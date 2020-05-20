<?php

namespace frontend\controllers;

use shop\entities\Blog\Category as BlogCategory;
use shop\entities\Blog\Post\Post;
use shop\entities\Page;
use shop\entities\Shop\Category as ShopCategory;
use shop\entities\Shop\Product\Product;
use shop\readModels\Blog\CategoryReadRepository as BlogCategoryReadRepository;
use shop\readModels\Blog\PostReadRepository;
use shop\readModels\PageReadRepository;
use shop\readModels\Shop\CategoryReadRepository as ShopCategoryReadRepository;
use shop\readModels\Shop\ProductReadRepository;
use shop\services\sitemap\IndexItem;
use shop\services\sitemap\MapItem;
use shop\services\sitemap\Sitemap;
use yii\caching\Dependency;
use yii\caching\TagDependency;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class SitemapController extends Controller
{
    const ITEMS_PER_PAGE = 100;

    private $_sitemap;
    private $_pages;
    private $_blogCategories;
    private $_posts;
    private $_shopCategories;
    private $_products;

    public function __construct(
        $id,
        $module,
        Sitemap $sitemap,
        PageReadRepository $pages,
        BlogCategoryReadRepository $blogCategories,
        PostReadRepository $posts,
        ShopCategoryReadRepository $shopCategories,
        ProductReadRepository $products,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->_sitemap = $sitemap;
        $this->_pages = $pages;
        $this->_blogCategories = $blogCategories;
        $this->_posts = $posts;
        $this->_shopCategories = $shopCategories;
        $this->_products = $products;
    }

    public function actionBlogCategories(): Response
    {
        return $this->_renderSitemap('sitemap-blog-categories', function () {
            return $this->_sitemap->generateMap(\array_map(function (BlogCategory $category) {
                return new MapItem(
                    Url::to(['/blog/posts/category', 'slug' => $category->slug], true),
                    null,
                    MapItem::WEEKLY
                );
            }, $this->_blogCategories->getAll()));
        });
    }

    public function actionBlogPosts($start = 0): Response
    {
        return $this->_renderSitemap(['sitemap-blog-posts', $start], function () use ($start) {
            return $this->_sitemap->generateMap(\array_map(function (Post $post) {
                return new MapItem(
                    Url::to(['/blog/post/post', 'id' => $post->id], true),
                    null,
                    MapItem::DAILY
                );
            }, $this->_posts->getAllByRange($start, self::ITEMS_PER_PAGE)));
        });
    }

    public function actionBlogPostsIndex(): Response
    {
        return $this->_renderSitemap('sitemap-blog-posts-index', function (){
            return $this->_sitemap->generateIndex(\array_map(function ($start) {
                return new IndexItem(Url::to(['blog-posts', 'start' => $start * self::ITEMS_PER_PAGE], true));
            }, \range(0, (int)($this->posts->count() / self::ITEMS_PER_PAGE))));
        });
    }

    public function actionIndex(): Response
    {
        return $this->_renderSitemap('sitemap-index', function () {
            return $this->_sitemap->generateIndex([
                new IndexItem(Url::to(['pages'], true)),
                new IndexItem(Url::to(['blog-categories'], true)),
                new IndexItem(Url::to(['blog-posts-index'], true)),
                new IndexItem(Url::to(['shop-categories'], true)),
                new IndexItem(Url::to(['shop-products-index'], true)),
            ]);
        });
    }

    public function actionPages(): Response
    {
        return $this->_renderSitemap('sitemap-pages', function () {
            return $this->_sitemap->generateMap(\array_map(function (Page $page) {
                return new MapItem(
                    Url::to(['/page/view', 'id' => $page->id], true),
                    null,
                    MapItem::WEEKLY
                );
            }, $this->_pages->getAll()));
        });
    }

    public function actionShopCategories(): Response
    {
        return $this->_renderSitemap('sitemap-blog-categories', function () {
            return $this->_sitemap->generateMap(\array_map(function (ShopCategory $category) {
                return new MapItem(
                    Url::to(['/shop/catalog/category', 'id' => $category->id], true),
                    null,
                    MapItem::WEEKLY
                );
            }, $this->_shopCategories->getAll()));
        }, new TagDependency(['tags' => ['categories']]));
    }

    public function actionShopProducts($start = 0): Response
    {
        return $this->_renderSitemap(['sitemap-shop-products', $start], function () use ($start) {
            return $this->_sitemap->generateMap(\array_map(function (Product $product) {
                return new MapItem(
                    Url::to(['/shop/catalog/product', 'id' => $product->id], true),
                    null,
                    MapItem::DAILY
                );
            }, $this->_products->getAllByRange($start, self::ITEMS_PER_PAGE)));
        }, new TagDependency(['tags' => ['products']]));
    }

    public function actionShopProductsIndex(): Response
    {
        return $this->_renderSitemap('sitemap-shop-products-index', function (){
            return $this->_sitemap->generateIndex(\array_map(function ($start) {
                return new IndexItem(Url::to(['shop-products', 'start' => $start * self::ITEMS_PER_PAGE], true));
            }, \range(0, (int)($this->_products->count() / self::ITEMS_PER_PAGE))));
        }, new TagDependency(['tags' => ['products']]));
    }

    private function _renderSitemap($key, callable $callback, Dependency $dependency = null): Response
    {
        return \Yii::$app->response->sendContentAsFile(\Yii::$app->cache->getOrSet($key, $callback, null, $dependency), Url::canonical(), [
            'mimeType' => 'application/xml',
            'inline' => true
        ]);
    }
}