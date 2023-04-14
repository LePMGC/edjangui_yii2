<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Loan;
use common\models\LoanRefund;
use common\models\User;
use common\models\LoanEndorse;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\BaseUrl;
use yii\helpers\Url;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var common\models\Loan $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="loan-view">
    <p>
        <?php 
                if(($model->status == Loan::LOAN_REQUESTED) && $model->isEndorseRequestApproved())
                    echo Html::a(Yii::t('app', 'Process'), ['process-loan-request', 'id' => $model->id], ['class' => 'btn btn-success']) 
            ?>
            <!-- <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?> -->
            <?= ($model->status == Loan::LOAN_REQUESTED || $model->status == Loan::LOAN_REJECTED) ? 
                    Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]) 
                    : ""
            ?>
    </p>


    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'id',
                    [ 
                                                'label' => $model->getAttributeLabel('taker'),
                                                'value' => User::findOne($model->taker)->name,
                                            ],
                    [ 
                                                'label' => $model->getAttributeLabel('amount_requested'),
                                                'value' => is_null($model->amount_requested) ? '' : Yii::$app->formatter->asDecimal($model->amount_requested),
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('amount_received'),
                                                'value' => is_null($model->amount_received) ? '' : Yii::$app->formatter->asDecimal($model->amount_received),
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('amount_to_cash_in'),
                                                'value' => is_null($model->amount_to_cash_in) ? '' : Yii::$app->formatter->asDecimal($model->amount_to_cash_in),
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('taken_date'),
                                                'value' => Yii::$app->formatter->asDate($model->taken_date),
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('return_date'),
                                                'value' => Yii::$app->formatter->asDate($model->return_date),
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('interest'),
                                                'value' => is_null($model->interest) ? '' : Yii::$app->formatter->asDecimal($model->interest),
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('payment_method'),
                                                'value' => Loan::getAllPaymentMethods()[$model->payment_method],
                                            ],
                ],
            ]) ?>
        </div>
        <div class="col-lg-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    
                                    [ 
                                                'label' => $model->getAttributeLabel('phone_number'),
                                                'value' => $model->phone_number,
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('status'),
                                                'value' => Loan::getAllStatuses()[$model->status],
                                            ],
                                    [ 
                                                'label' => $model->getAttributeLabel('created_by'),
                                                'value' => User::findOne($model->created_by)->name,
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('created_on'),
                                                'value' => Yii::$app->formatter->asDateTime($model->created_on),
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('updated_by'),
                                                'value' => User::findOne($model->updated_by)->name,
                                            ],
                                            [ 
                                                'label' => $model->getAttributeLabel('updated_on'),
                                                'value' => Yii::$app->formatter->asDateTime($model->updated_on),
                                            ],
                                            [ 
                                                'label' => $modelLoanEndorser->getAttributeLabel('endorser'),
                                                'value' => User::findOne($modelLoanEndorser->endorser)->name,
                                            ],
                                            [ 
                                                'label' => Yii::t('app', 'Endorse Status'),
                                                'value' => LoanEndorse::getAllStatuses()[$modelLoanEndorser->status],
                                            ]
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-info">
                <div class="card-header"> <center> <strong> <?= Yii::t('app', 'Refunds') ?> </strong> </center>  
                    <?php
                        if ($model->status == Loan::LOAN_GIVEN) {
                            if($currentAction == "VIEW"){
                                echo Html::a(Yii::t('app', '+Refund'), ['loan/view', 'id' => $model->id, 'currentAction' => 'ADD'], ['class' => 'btn btn-primary']);
                            }else{                          
                                echo Html::a(Yii::t('app', 'Refunds'), ['loan/view', 'id' => $model->id, 'currentAction' => 'VIEW'], ['class' => 'btn btn-primary']);
                            }
                        }
                    ?>
                </div>
                <div class="card-body">
                    <?php
                        if(($currentAction == "UPDATE") || ($currentAction == "ADD")){
                            $form = ActiveForm::begin();

                            echo $form->field($modelLoanRefund, 'refund_date')->widget(DatePicker::classname(), [
                                'options' => ['placeholder' => 'Enter a date ...'],
                                'pluginOptions' => [
                                    'autoclose'=>true
                                ]
                            ]);

                            echo $form->field($modelLoanRefund, 'remain_before')->textInput(['readOnly' => true]);
                            
                            echo $form->field($modelLoanRefund, 'amount_given')->textInput();
                            
                            echo '<div class="form-group">';
                            echo Html::submitButton($modelLoanRefund->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $modelLoanRefund->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                            echo '</div>';
                            ActiveForm::end();

                        }else{
                            Pjax::begin(); ?>    
                                <?= GridView::widget([
                                'dataProvider'=>$dataProviderLoanRefunds,
                                //'filterModel'=>$searchModel,
                                // parameters from the demo form
                                'bordered'=>true,
                                //'striped'=>true,
                                'condensed'=>true,
                                'responsive'=>true,
                                'hover'=>true,
                                'showPageSummary'=>true,
                                'layout' => '{items}{pager}',
                                //'exportConfig'=>$exportConfig,
                                'columns' => [
                                    ['class' => 'kartik\grid\SerialColumn'],

                                    [
                                        'attribute' => 'refund_date',
                                        'format' => 'Date',
                                    ],                            
                                    [
                                        'attribute' => 'remain_before',
                                        'format' => 'Decimal',
                                    ],
                                    [
                                        'attribute' => 'amount_given',
                                        'pageSummary' => true,
                                        'format' => 'Decimal',
                                    ],
                                    [
                                        'attribute' => 'remain_after',
                                        'format' => 'Decimal',
                                    ],

                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => ($model->status==Loan::LOAN_GIVEN) ? '{update} {delete}' : '',
                                        'urlCreator' => function ($action, LoanRefund $model, $key, $index, $column) {
                                            return Url::toRoute(['view', 'id' => $model->loan, 'currentAction' => strtoupper($action), 'loanRefundId' => $model->id]);
                                         }
                                    ],
                                ],
                                'showPageSummary' => true,
                                'pageSummaryRowOptions' => [
                                    'class' => 'kv-page-summary primary',
                                ],
                        ]); Pjax::end(); 
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <p> </p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-primary">
                <div class="card-header"> <center> <strong> <?= Yii::t('app', 'Interest Share') ?> </strong> </center>  </div>
                <div class="card-body">
                    <?php Pjax::begin(); ?>
                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProviderInterestShare,
                        //'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],

                            //'id',
                            /*[
                                'attribute'=>'loan', 
                                'vAlign'=>'middle',
                                'width'=>'27%',
                                'value'=>function ($model, $key, $index, $widget) { 
                                    $modelLoan = Loan::findOne($model->loan);
                                    $takerName =  User::findOne($modelLoan->taker)->name;
                                    return $takerName.'|'.Yii::$app->formatter->asDecimal($modelLoan->amount_requested).'|'.Yii::$app->formatter->asDate($modelLoan->taken_date);
                                },
                                'format'=>'raw'
                            ],*/
                            [
                                'attribute' => 'beneficiary', 
                                'vAlign'=>'middle',
                                //'width'=>'15%',
                                'value'=>function ($model, $key, $index, $widget) { 
                                    return User::findOne($model->beneficiary)->name;
                                    //return $model->beneficiary;
                                },
                                'format'=>'raw'
                            ],
                            [
                                'attribute' => 'balance_at_loan',
                                'pageSummary' => true,
                                'format' => 'Decimal',
                            ],
                            [
                                'attribute' => 'total_balance_at_loan',
                                //'summary' => true,
                                'format' => 'Decimal',
                            ],
                            [
                                'attribute' => 'own_share',
                                'pageSummary' => true,
                                'format' => 'Decimal',
                            ],
                            [
                                'attribute' => 'balance_before',
                                'pageSummary' => true,
                                'format' => 'Decimal',
                            ],
                            [
                                'attribute' => 'balance_after',
                                'pageSummary' => true,
                                'format' => 'Decimal',
                            ],
                            
                            //'created_by',
                            //'created_on',
                            //'updated_by',
                            //'updated_on',

                            //['class' => 'kartik\grid\ActionColumn'],
                        ],
                        'showPageSummary' => true,
                        'pageSummaryRowOptions' => [
                            'class' => 'kv-page-summary primary',
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>


    

</div>
