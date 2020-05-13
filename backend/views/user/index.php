<?php

use shop\entities\User\User;
use shop\helpers\UserHelper;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\forms\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'created_at:datetime',
                    [
                        'attribute' => 'username',
                        'format' => 'raw',
                        'value' => function (User $model) {
                            return Html::a(Html::encode($model->username), ['view', 'id' => $model->id]);
                        },
                    ],
                    'email:email',
                    [
                        'attribute' => 'status',
                        'filter' => UserHelper::statusList(),
                        'format' => 'raw',
                        'value' => function (User $model) {
                            return UserHelper::statusLabel($model->status);
                        },
                    ],
                    ['class' => ActionColumn::class],
                ],
            ]); ?>
        </div>
    </div>

</div>
