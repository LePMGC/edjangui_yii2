<?php

use common\models\Loan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\User;
/** @var yii\web\View $this */
/** @var common\models\LoanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Loans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-index">

    <!-- <p>
        <?= Html::a(Yii::t('app', 'Create Loan'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'taker', 
                'vAlign'=>'middle',
                'width'=>'25%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return User::findOne($model->taker)->name;
                },
                'filterType'=>GridView::FILTER_SELECT2,
                //'filter'=>ArrayHelper::map(User::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                'filter' => User::getAllUsers(),
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Members')],
                'format'=>'raw'
            ],
            [
                'attribute' => 'amount_requested',
                'pageSummary' => true,
                'format' => 'Decimal',
            ],
            //'amount_received',
            //'amount_to_cash_in',
            'taken_date',
            'return_date',
            [
                'attribute' => 'interest',
                'pageSummary' => true,
                'format' => 'Decimal',
            ],
            [
                'attribute' => 'status',
                //'width'=>'20%',
                'value'=>function ($model, $key, $index, $widget) { 
                    return Loan::getAllStatuses()[$model->status];
                },
                'filterType'=>GridView::FILTER_SELECT2,
                'filter' => Loan::getAllStatuses(), 
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Statuses')],
                'format'=>'raw'
            ],
            //'payment_method',
            //'phone_number',
            //'association',
            //'loan_option',
            //'created_by',
            //'created_on',
            //'updated_by',
            //'updated_on',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Loan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
        'showPageSummary' => true,
        'pageSummaryRowOptions' => [
            'class' => 'kv-page-summary primary',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
