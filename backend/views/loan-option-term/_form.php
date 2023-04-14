<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LoanOptionTerm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-option-term-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'association')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount_to_refurn')->textInput() ?>

    <?= $form->field($model, 'percentage')->textInput() ?>

    <?= $form->field($model, 'loan_option')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_on')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'updated_on')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
