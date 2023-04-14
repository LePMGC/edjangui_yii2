<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EmailNotification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'html_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'text_content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sending_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'send_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sent_on')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_on')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'updated_on')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
