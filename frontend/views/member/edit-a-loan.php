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
use common\models\BankAccount;
use common\models\CashOut;
use kartik\select2\Select2;
use kartik\widgets\SwitchInput;

$this->title = 'Edit Loan Request';
//$this->params['breadcrumbs'][] = $this->title;
$totalAcoumtAvailableForLoan = round(Loan::getTotalAmountAvailableForLoan());
?>
<div class="member-cash-out">    
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">              
            </div>
            <div class="col-lg-4"></div>
        </div>

    <div class="row">
        <div class="col-lg-4"> </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header"> 
                    <center> 
                        <h3><?= Html::encode($this->title) ?></h3>
                    </center> 
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'ask-loan-form']); ?>

                    <?=
                        $form->field($model, 'bank_account')->widget(Select2::classname(), [
                            'data' => BankAccount::getAllBankAccountsWithLoanAllowed(),
                            'options' => [
                                'placeholder' => 'Select a Bank Account ...',
                                'onChange' => 'setMaxAllowedLoanAmount(this.value)'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>

                    <?= $form->field($model, 'amount')->textInput([
                        'autofocus' => true,
                        'type' => 'number',
                        'step' => '0.0.1',
                        'max' => $totalAcoumtAvailableForLoan,
                        ])
                    ?>

                    <?= $form->field($model, 'endorser')->widget(Select2::classname(), [
                                'data' => LoanEndorse::getAllPossibleLoanEndorsers(),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select a Member').' ...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                        ])?>
                    
                    <br/>
                    <?php
                        echo Html::Label($model->getAttributeLabel('payment_method')); 
                        \yii\bootstrap4\BootstrapPluginAsset::register($this);
                        echo Html::activeRadioButtonGroup($model, 'payment_method', [CashOut::CASH_OUT_MOBILE_PAYMENT => Yii::t('app', 'Mobile Payment'), CashOut::CASH_OUT_CASH_PAYMENT => Yii::t('app', 'Cash Payment')]);
                    ?>
                    <br/> <br/>
                    <?= $form->field($model, 'phone_number')->textInput(['autofocus' => true]) ?>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Request'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Yii::t('app', 'Cancel'), Url::to(['/site/index']), ['class' => 'btn btn-info']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-4"> </div>
    </div>
</div>
