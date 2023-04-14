<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Association $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="association-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= 
        (!$model->isNewRecord) ? 
        $form->field($model, 'status')->dropdownList([
            0 => 'CREATED', 
            10 => 'ACTIVATED',
            20 => 'TEMPORARY_BLOCKED'
        ])
        : ""
    ?>

    <?= $form->field($model, 'admin_phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_email_address')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
