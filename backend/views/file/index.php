<?php

use mihaildev\elfinder\ElFinder;

/* @var $this yii\web\View */

$this->title = 'Files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-index">

    <?= ElFinder::widget([
        'frameOptions' => ['style' => 'width: 100%; height: 640px; border: 0;']
    ]); ?>

</div>
