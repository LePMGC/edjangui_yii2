<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\BankAccount $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bank-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?> <br/>

    <?=  $form->field($model, 'loan_allowed')->checkbox() ?> <br/>

    <?=  $form->field($model, 'cash_in_allowed')->checkbox() ?> <br/>

    <?=  $form->field($model, 'cash_out_allowed')->checkbox() ?> <br/>

    <?= $form->field($model, 'fix_cash_in_amount')->textInput() ?>

    <?= $form->field($model, 'min_balance_for_loan')->textInput() ?>

    <?= $form->field($model, 'min_cash_in_amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
