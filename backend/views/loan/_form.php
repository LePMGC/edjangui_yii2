<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Loan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'taker')->textInput() ?>

    <?= $form->field($model, 'amount_requested')->textInput() ?>

    <?= $form->field($model, 'amount_received')->textInput() ?>

    <?= $form->field($model, 'amount_to_cash_in')->textInput() ?>

    <?= $form->field($model, 'taken_date')->textInput() ?>

    <?= $form->field($model, 'return_date')->textInput() ?>

    <?= $form->field($model, 'interest')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'payment_method')->textInput() ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'association')->textInput() ?>

    <?= $form->field($model, 'loan_option')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
