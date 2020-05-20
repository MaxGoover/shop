<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\DataProviderInterface */
/* @var $category shop\entities\Shop\Category */

$this->title = 'Catalog';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_subcategories', [
    'category' => $category
]) ?>

<?= $this->render('_list', [
    'dataProvider' => $dataProvider
]) ?>


