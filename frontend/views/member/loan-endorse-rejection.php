<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use common\models\User;
use common\models\Loan;
use common\models\Account;
use common\models\CashOut;
use kartik\widgets\SwitchInput;
use yii\widgets\DetailView;

$this->title = 'Loan Endorse Request Rejection';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">    
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <?=
                                DetailView::widget([
                                    'model' => $modelLoanEndose,
                                    'attributes' => [
                                        [ 
                                            'label' => 'Loan Taker',
                                            'value' => User::findOne(Loan::findOne($modelLoanEndose->loan)->taker)->getName(),
                                        ],
                                        [ 
                                            'label' => 'Loan Amount',
                                            'value' => Yii::$app->formatter->asDecimal(Loan::findOne($modelLoanEndose->loan)->amount_requested, 2)
                                        ],
                                        [ 
                                            'label' => $modelLoanEndose->getAttributeLabel('loan_endorser'),
                                            'value' => User::findOne($modelLoanEndose->endorser)->getName(),
                                        ],/*
                                        [ 
                                            'label' => $modelLoanEndose->getAttributeLabel('endorse_amount'),
                                            'value' => Yii::$app->formatter->asDecimal($modelLoanEndose->endorse_amount, 2)
                                        ],*/
                                    ],
                                ]);
                        ?>      
                    </div>
                </div>              
            </div>
            <div class="col-lg-3"></div>
        </div>

    <div class="row">
        <div class="col-lg-3"> </div>
        <div class="col-lg-6">
            <div class="panel panel-primary">
                <div class="panel-header"> 
                    <center> 
                        <h4><?= Html::encode($this->title) ?></h4> 
                        <p>Fill the form to give rejection reason</p>
                    </center> 
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'loan-endorse-rejection']); ?>
                    
                    <?= $form->field($model, 'comment')->textArea(['autofocus' => true]) ?>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton('Reject', ['class' => 'btn btn-primary']) ?>
                        <?php 
                            echo Html::a(Yii::t('app', 'Cancel'), Url::to(['/site/index']), ['class' => 'btn btn-default']);
                        ?>                        
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-3"> </div>
    </div>
</div>
