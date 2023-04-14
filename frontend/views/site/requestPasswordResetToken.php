<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Request password reset';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <div class="row">
        <div class="col-lg-3"> </div>
        <div class="col-lg-6">
            <div class="card card-primary">
                <div class="card-header"> 
                    <center> 
                        <h2><?= Html::encode($this->title) ?></h2> 
                        <p>&nbsp Please fill out your email. A link to reset password will be sent there. &nbsp </p>
                    </center> 
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                    <?= $form->field($model, 'email_address')->textInput(['autofocus' => true]) ?>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-lg-3"> </div>
    </div>
</div>
