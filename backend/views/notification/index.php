<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Notification'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'description:ntext',
            //'sms_content',
            //'email_content:ntext',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'send_sms', 
                'vAlign'=>'middle',
                'trueLabel' => Yii::t('app', 'Yes'),
                'falseLabel' => Yii::t('app', 'No'),
            ],
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'send_email', 
                'vAlign'=>'middle',
                'trueLabel' => Yii::t('app', 'Yes'),
                'falseLabel' => Yii::t('app', 'No'),
            ],
            //'crested_by',
            //'created_on',
            //'updated_by',
            //'updated_on',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
