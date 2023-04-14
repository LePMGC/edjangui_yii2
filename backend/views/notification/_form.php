<?php

use yii\helpers\Html;
use kartik\checkbox\CheckboxX;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, /*'readOnly' => true*/]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sms_content_en')->textarea(['maxlength' => true, 'rows' => 6]) ?>
    
    <?= $form->field($model, 'sms_content_fr')->textarea(['maxlength' => true, 'rows' => 6]) ?>

    <?= $form->field($model, 'email_content_en')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'email_content_fr')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'send_email')->widget(CheckboxX::classname(), ['pluginOptions'=>['threeState'=>false]]); ?>

    <?= $form->field($model, 'send_sms')->widget(CheckboxX::classname(), ['pluginOptions'=>['threeState'=>false]]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
