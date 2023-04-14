<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'username',
            'name',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'email_address:email',
            'phone_number',
            [
                'attribute' => 'status',
                'value'=>function ($model, $key, $index, $widget) { 
                    return User::getAllStatuses()[$model->status];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => User::getAllStatuses(), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Statuses')],
                'format'=>'raw'
            ],
            //'role',
            //'created_at',
            //'updated_at',
            //'verification_code',
            //'language',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
