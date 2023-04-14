<?php

use common\models\User;
use common\models\Account;
use common\models\Loan;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\BaseUrl;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Account History')?> </h4> </center> </div>
                    <div class="panel-body">
                        
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'kartik\grid\SerialColumn'],

                                [
                                    'attribute'=>'ah_date',
                                    'format' => 'Datetime',
                                    'width' => '20%'
                                ],
                                [
                                    'attribute' => 'ah_type',
                                    'value' => function ($model, $key, $index, $widget) { 
                                        switch ($model['ah_type']) {
                                            case 1:
                                                return 'CASH_IN';
                                                break;

                                            case 2:
                                                return 'INTEREST';
                                                break;

                                            case 3:
                                                return 'CASH_OUT';
                                                break;

                                            case 4:
                                                return 'PENALTIES';
                                                break;

                                            case 5:
                                                return 'CASH_IN';
                                                break;
                                            
                                            default:
                                                return '';
                                                break;
                                        };
                                    },
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' => [1 => 'CASH_IN', 2 => 'INTEREST', 3 => 'CASH_OUT', 'PENALTIES'],
                                    'filterWidgetOptions'=>[
                                        'pluginOptions'=>['allowClear'=>true],
                                    ],
                                    'filterInputOptions'=>['placeholder'=>Yii::t('app', 'All Types')],
                                    'format'=>'raw',
                                    'width' => '20%'
                                ],
                                [
                                    'attribute' => 'ah_balance_before',
                                    'format' => 'Decimal',
                                    'width' => '20%'
                                ],
                                [
                                    'attribute' => 'ah_amount',
                                    'format' => 'Decimal',
                                    'width' => '20%'
                                ],
                                [
                                    'attribute' => 'ah_balance_after',
                                    'format' => 'Decimal',
                                    'width' => '20%'
                                ],

                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'header' => '',
                                    'template' => '{view-ah-item}',
                                    'buttons' => [
                                        'view-ah-item' => function ($url, $model, $key) {
                                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#', [
                                                //'id' => 'activity-view-link',
                                                'class' => 'activity-view-link',
                                                'title' => Yii::t('yii', 'View'),
                                                'data-toggle' => 'modal',
                                                'data-target' => '#activity-modal',
                                                'data-id' => $model['ah_transaction_id'],
                                                'data-pjax' => '0',

                                            ]);
                                        },
                                    ],   
                                ],
                            ],
                            'showPageSummary' => true,
                            'pageSummaryRowOptions' => [
                                'class' => 'kv-page-summary primary',
                            ],
                        ]); ?>
                        
                    </div>
                    
                    <div class="panel-footer">
                        
                    </div>
                </div>                   
            </div>              
        </div>
    </div>
</div>

<?php $this->registerJs(
    "$('.activity-view-link').click(function() {
    $.get(
        'view-ah-item',         
        {
            ah_key: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#activity-modal').modal();
        }  
    );
});
    "
); ?>

<?php


?>

<?php Modal::begin([
    'id' => 'activity-modal',
    'title' => '<center> <h4 class="modal-title">Account History Item</h4> </center>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',

]); ?>

<div class="well">


</div>


<?php Modal::end(); ?>
