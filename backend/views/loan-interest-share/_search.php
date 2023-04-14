<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LoanInterestShareSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-interest-share-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'association') ?>

    <?= $form->field($model, 'beneficiary') ?>

    <?= $form->field($model, 'balance_at_loan') ?>

    <?= $form->field($model, 'total_balance_at_loan') ?>

    <?php // echo $form->field($model, 'own_share') ?>

    <?php // echo $form->field($model, 'balance_before') ?>

    <?php // echo $form->field($model, 'balance_after') ?>

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
