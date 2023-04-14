<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LoanInterestShare $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-interest-share-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'association')->textInput() ?>

    <?= $form->field($model, 'beneficiary')->textInput() ?>

    <?= $form->field($model, 'balance_at_loan')->textInput() ?>

    <?= $form->field($model, 'total_balance_at_loan')->textInput() ?>

    <?= $form->field($model, 'own_share')->textInput() ?>

    <?= $form->field($model, 'balance_before')->textInput() ?>

    <?= $form->field($model, 'balance_after')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_on')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'updated_on')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
