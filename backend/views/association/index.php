<?php

use common\models\Association;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var common\models\AssociationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Associations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="association-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Association'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'country',
            'city',
            'admin_phone_number',
            'admin_email_address:email',
            [
                'attribute' => 'status',
                'value'=>function ($model, $key, $index, $widget) { 
                    return Association::getAllStatuses()[$model->status];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => Association::getAllStatuses(), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Statuses')],
                'format'=>'raw'
            ],
            'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Association $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
