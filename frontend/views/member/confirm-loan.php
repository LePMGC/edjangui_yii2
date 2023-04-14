<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use common\models\User;
use common\models\Loan;
use common\models\LoanEndorse;
use common\models\Account;
use kartik\widgets\SwitchInput;
use yii\widgets\DetailView;

$this->title = Yii::t('app', 'Confirm Loan');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12"> </div>
        <div class="col-md-4 col-sm-12 col-xs-12">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
            <div class="panel panel-primary">
                <div class="panel-header"> 
                    <center> 
                        <h4><?= Html::encode($this->title) ?></h4> 
                    </center> 
                </div>
                <div class="panel-body">
                    <center> <strong> </strong> </center>
                    <?php                   
                        echo DetailView::widget([
                            'model' => $modelLoanForm,
                            'attributes' => [
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('taker'),
                                    'value' => User::findOne($modelLoanForm->taker)->name,
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('amount'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoanForm->amount,0),
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('interest'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoanForm->interest,0),
                                ],
                                [ 
                                    'label' => Yii::t('app','Amount to Receive'),
                                    'value' => Yii::$app->formatter->asDecimal(($modelLoanForm->amount - (float)$modelLoanForm->amount_to_cash_in),0),
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('amount_to_cash_in'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoanForm->amount_to_cash_in,0),
                                ],
                                
                                'phone_number', 

                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('payment_method'),
                                    'value' => ($modelLoanForm->payment_method == Loan::LOAN_MOBILE_PAYMENT) ? 'MOBILE PAYMENT' : 'CASH PAYMENT',
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('start_date'),
                                    'value' => Yii::$app->formatter->asDate($modelLoanForm->start_date),
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('end_date'),
                                    'value' => Yii::$app->formatter->asDate($modelLoanForm->end_date),
                                ],
                                [ 
                                    'label' => $modelLoanForm->getAttributeLabel('endorser'),
                                    'value' => User::findOne($modelLoanForm->endorser)->name,
                                ],
                            ],
                        ]);
                    ?>

                </div>
                <div class="panel-footer">
                    <div class="form-group" >
                        <?= Html::a('< '.Yii::t('app', 'Loan Request'), Url::to(['/member/ask-a-loan', 'action' => 'UPDATE']), ['class' => 'btn btn-success']); ?>
                        <?= Html::submitButton(Yii::t('app', 'Confirm Loan'), ['class' => 'btn btn-primary', 'id' => 'confirm-button', 'name' => 'confirm-button']) ?>                  
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-4 col-sm-12 col-xs-12"> </div>
    </div>
</div>
