<?php

use common\models\User;
use common\models\Account;
use common\models\Loan;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Loans')?> </h4> </center> </div>
                    <div class="panel-body">
                        <?php
                            //if(count($dataProvider->getModels())==0){
                            //    echo Yii::t('app', "You have no loans");
                            //}else{
                                Pjax::begin();
                                    
                                    echo GridView::widget([
                                        'dataProvider' => $dataProvider,
                                        'filterModel' => $searchModel,
                                        'columns' => [
                                            ['class' => 'kartik\grid\SerialColumn'],

                                            //'id', 
                                            [
                                                'attribute'=>'taker', 
                                                'vAlign'=>'middle',
                                                'width'=>'25%',
                                                'value'=>function ($model, $key, $index, $widget) { 
                                                    return User::findOne($model->taker)->name;
                                                },
                                                'filterType'=>GridView::FILTER_SELECT2,
                                                'filter'=>ArrayHelper::map(User::find()->orderBy('name')->asArray()->all(), 'id', 'name'), 
                                                'filterWidgetOptions'=>[
                                                    'pluginOptions'=>['allowClear'=>true],
                                                ],
                                                'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Users')],
                                                'format'=>'raw'
                                            ],                                           
                                            [
                                                'attribute' => 'amount_requested',
                                                'pageSummary' => true,
                                                'format' => 'Decimal',
                                            ],
                                            [
                                                'attribute' => 'taken_date',
                                                'filter' => false,
                                                'format' => 'Date',
                                            ],
                                            [
                                                'attribute' => 'return_date',
                                                'filter' => false,
                                                'format' => 'Date',
                                            ],
                                            [
                                                'attribute' => 'interest',
                                                'pageSummary' => true,
                                                'format' => 'Decimal',
                                            ],
                                            [
                                                'label' => Yii::t('app','To Refund'),
                                                'value'=>function ($model, $key, $index, $widget) { 
                                                    return $model->getAmountToDisplayInFrontend();
                                                },
                                            ],
                                            [
                                                'attribute' => 'status',
                                                'width'=>'20%',
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
                                            //'created_by',
                                            //'created_on',
                                            //'updated_by',
                                            //'updated_on',

                                            /*[
                                                'class' => 'kartik\grid\ActionColumn',
                                                'template' => '{view} {refunds} {interest-shares} {update} {delete}',
                                                'buttons' => [
                                                    'refunds' => function ($url, $model, $key) {
                                                        return '<a href="'.BaseUrl::to(['refunds', 'id' => $model->id]).'" title="'.\Yii::t('app', "View Refunds").'" data-pjax="0"><span class="glyphicon glyphicon-usd"></span></a>';
                                                    },
                                                    'interest-shares' => function ($url, $model, $key) {
                                                        return '<a href="'.BaseUrl::to(['interest-shares', 'id' => $model->id]).'" title="'.\Yii::t('app', "Interest Shares").'" data-pjax="0"><span class="glyphicon glyphicon-user"></span></a>';
                                                    },
                                                ],   
                                            ],*/
                                        ],
                                        'showPageSummary' => true,
                                        'pageSummaryRowOptions' => [
                                            'class' => 'kv-page-summary primary',
                                        ],
                                    ]);
                                Pjax::end();
                            //}
                        ?>
                    </div>
                    
                    <div class="panel-footer">
                        
                    </div>
                </div>                   
            </div>              
        </div>
    </div>
</div>
