<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\widgets\Blog\LastPostsWidget;
use frontend\widgets\Shop\FeaturedProductsWidget;

\frontend\assets\OwlCarouselAsset::register($this);

?>
<?php $this->beginContent('@frontend/views/layouts/main.php') ?>

<div class="row">
    <div id="content" class="col-sm-12">
        <div id="slideshow0" class="owl-carousel" style="opacity: 1;">
            <div class="item">
                <a href="index.php?route=product/product&amp;path=57&amp;product_id=49"><img
                            src="http://static.shop.test/cache/banners/iPhone6-1140x380.jpg"
                            alt="iPhone 6" class="img-responsive"/></a>
            </div>
            <div class="item">
                <img src="http://static.shop.test/cache/banners/MacBookAir-1140x380.jpg"
                     alt="MacBookAir" class="img-responsive"/>
            </div>
        </div>
        <h3>Featured</h3>

        <?= FeaturedProductsWidget::widget([
            'limit' => 4,
        ]) ?>

        <h3>Last Posts</h3>

        <?= LastPostsWidget::widget([
            'limit' => 4,
        ]) ?>

        <div id="carousel0" class="owl-carousel">
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/nfl-130x100.png" alt="NFL"
                     class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/redbull-130x100.png"
                     alt="RedBull" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/sony-130x100.png" alt="Sony"
                     class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/cocacola-130x100.png"
                     alt="Coca Cola" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/burgerking-130x100.png"
                     alt="Burger King" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/canon-130x100.png" alt="Canon"
                     class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/harley-130x100.png"
                     alt="Harley Davidson" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/dell-130x100.png" alt="Dell"
                     class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/disney-130x100.png"
                     alt="Disney" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/starbucks-130x100.png"
                     alt="Starbucks" class="img-responsive"/>
            </div>
            <div class="item text-center">
                <img src="http://static.shop.test/cache/manufacturers/nintendo-130x100.png"
                     alt="Nintendo" class="img-responsive"/>
            </div>
        </div>
        <?= $content ?>
    </div>
</div>

<?php $this->registerJs('
$(\'#slideshow0\').owlCarousel({
    items: 1,
    loop: true,
    autoplay:true,
    autoplayTimeout:3000,
    autoplayHoverPause:true,
    nav: true,
    navText: [\'<i class="fa fa-chevron-left fa-5x"></i>\', \'<i class="fa fa-chevron-right fa-5x"></i>\'],
    dots: true
});') ?>

<?php $this->registerJs('
$(\'#carousel0\').owlCarousel({
    items: 6,
    loop: true,
    autoplay:true,
    autoplayTimeout:3000,
    autoplayHoverPause:true,
    nav: true,
    navText: [\'<i class="fa fa-chevron-left fa-5x"></i>\', \'<i class="fa fa-chevron-right fa-5x"></i>\'],
    dots: true
});') ?>

<?php $this->endContent() ?>