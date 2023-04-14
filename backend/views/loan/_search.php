<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LoanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'taker') ?>

    <?= $form->field($model, 'amount_requested') ?>

    <?= $form->field($model, 'amount_received') ?>

    <?= $form->field($model, 'amount_to_cash_in') ?>

    <?php // echo $form->field($model, 'taken_date') ?>

    <?php // echo $form->field($model, 'return_date') ?>

    <?php // echo $form->field($model, 'interest') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'payment_method') ?>

    <?php // echo $form->field($model, 'phone_number') ?>

    <?php // echo $form->field($model, 'association') ?>

    <?php // echo $form->field($model, 'loan_option') ?>

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
