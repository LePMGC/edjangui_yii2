<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\models\Account;
use common\models\CashOut;
use kartik\widgets\SwitchInput;

$this->title = 'Cash Out';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">    
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <?php
                            $currentUser = User::findOne(Yii::$app->user->getId());
                            $currentBalance = Yii::$app->formatter->asDecimal(Account::findOne(['owner' => $currentUser->id])->balance);
                            echo "<center> <h4>".Yii::t('app', "Bank Solde")." : XAF ".$currentBalance." </h4> </center>";
                        ?>      
                    </div>
                </div>              
            </div>
            <div class="col-lg-4"></div>
        </div>

    <div class="row">
        <div class="col-lg-4"> </div>
        <div class="col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-header"> 
                    <center> 
                        <h3><?= Html::encode($this->title) ?></h3> 
                        <p><?= Yii::t('app', 'Fill the form to request the cash out')?> </p>
                    </center> 
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    
                    <?= $form->field($model, 'amount')->textInput(['autofocus' => true]) ?>
                    <br/>
                    <?php
                        echo Html::Label($model->getAttributeLabel('payment_method')); 
                        \yii\bootstrap\BootstrapPluginAsset::register($this);
                        echo Html::activeRadioButtonGroup($model, 'payment_method', [CashOut::CASH_OUT_MOBILE_PAYMENT => 'Mobile Payment', CashOut::CASH_OUT_CASH_PAYMENT => 'Cash Payment']);
                    ?>
                    <br/> <br/>
                    <?= $form->field($model, 'phone_number')->textInput(['autofocus' => true]) ?>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton('Request', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-4"> </div>
    </div>
</div>
