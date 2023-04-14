<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\CashInSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cash-in-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'member') ?>

    <?= $form->field($model, 'episode') ?>

    <?= $form->field($model, 'bank_account') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'balance_before') ?>

    <?php // echo $form->field($model, 'balance_after') ?>

    <?php // echo $form->field($model, 'association') ?>

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
