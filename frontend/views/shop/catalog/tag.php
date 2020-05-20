<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\DataProviderInterface */
/* @var $tag shop\entities\Shop\Tag */

$this->title = 'Products with tag ' . $tag->name;

$this->params['breadcrumbs'][] = ['label' => 'Catalog', 'url' => ['index']];
$this->params['breadcrumbs'][] = $tag->name;
?>

<h1>Products with tag &laquo;<?= Html::encode($tag->name) ?>&raquo;</h1>

<hr />

<?= $this->render('_list', [
    'dataProvider' => $dataProvider
]) ?>


