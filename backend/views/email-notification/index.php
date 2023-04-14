<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EmailNotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Email Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-notification-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('app', 'Create Email Notification'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'html_content:ntext',
            'text_content:ntext',
            [
                'attribute' => 'sending_status',
                'format' => 'Boolean',
            ],
            [
                'attribute' => 'send_to',
                'format' => 'Email',
            ],
            [
                'attribute' => 'sent_on',
                'format' => 'Datetime',
            ],
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
