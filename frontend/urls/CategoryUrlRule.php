<?php

namespace frontend\urls;

use shop\entities\Shop\Category;
use shop\readModels\Shop\CategoryReadRepository;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\web\UrlNormalizerRedirectException;
use yii\web\UrlRuleInterface;

class CategoryUrlRule extends BaseObject implements UrlRuleInterface
{
    public $prefix = 'catalog';

    private $_repository;
    private $_cache;

    public function __construct(CategoryReadRepository $repository, Cache $cache, $config = [])
    {
        parent::__construct($config);
        $this->_repository = $repository;
        $this->_cache = $cache;
    }

    public function createUrl($manager, $route, $params)
    {
        if ($route == 'shop/catalog/category') {
            if (empty($params['id'])) {
                throw new InvalidArgumentException('Empty id.');
            }
            $id = $params['id'];

            $url = $this->_cache->getOrSet(['category_route', 'id' => $id], function () use ($id) {
                if (!$category = $this->_repository->find($id)) {
                    return null;
                }
                return $this->_getCategoryPath($category);
            }, null, new TagDependency(['tags' => ['categories']]));

            if (!$url) {
                throw new InvalidArgumentException('Undefined id.');
            }

            $url = $this->prefix . '/' . $url;
            unset($params['id']);
            if (!empty($params) && ($query = \http_build_query($params)) !== '') {
                $url .= '?' . $query;
            }

            return $url;
        }
        return false;
    }

    public function parseRequest($manager, $request)
    {
        if (\preg_match('#^' . $this->prefix . '/(.*[a-z])$#is', $request->pathInfo, $matches)) {
            $path = $matches['1'];

            $result = $this->_cache->getOrSet(['category_route', 'path' => $path], function () use ($path) {
                if (!$category = $this->_repository->findBySlug($this->_getPathSlug($path))) {
                    return ['id' => null, 'path' => null];
                }
                return ['id' => $category->id, 'path' => $this->_getCategoryPath($category)];
            }, null, new TagDependency(['tags' => ['categories']]));

            if (empty($result['id'])) {
                return false;
            }

            if ($path != $result['path']) {
                throw new UrlNormalizerRedirectException(['shop/catalog/category', 'id' => $result['id']], 301);
            }

            return ['shop/catalog/category', ['id' => $result['id']]];
        }
        return false;
    }

    private function _getCategoryPath(Category $category): string
    {
        $chunks = ArrayHelper::getColumn($category->getParents()->andWhere(['>', 'depth', 0])->all(), 'slug');
        $chunks[] = $category->slug;
        return \implode('/', $chunks);
    }

    private function _getPathSlug($path): string
    {
        $chunks = \explode('/', $path);
        return \end($chunks);
    }
}