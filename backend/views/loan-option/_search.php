<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\LoanOptionSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-option-search">

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

    <?= $form->field($model, 'min_amount') ?>

    <?= $form->field($model, 'max_amount') ?>

    <?php // echo $form->field($model, 'interest_rate') ?>

    <?php // echo $form->field($model, 'number_of_terms') ?>

    <?php // echo $form->field($model, 'term_duration') ?>

    <?php // echo $form->field($model, 'postpone_option') ?>

    <?php // echo $form->field($model, 'postpone_capital') ?>

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
