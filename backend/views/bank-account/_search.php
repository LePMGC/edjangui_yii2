<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\BankAccountSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bank-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'association') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'loan_allowed') ?>

    <?= $form->field($model, 'cash_in_allowed') ?>

    <?php // echo $form->field($model, 'cash_out_allowed') ?>

    <?php // echo $form->field($model, 'fix_cash_in_amount') ?>

    <?php // echo $form->field($model, 'min_balance_for_loan') ?>

    <?php // echo $form->field($model, 'min_cash_in_amount') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_on') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_on') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
