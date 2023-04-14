<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Reset password';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">    

    <div class="row">
        <div class="col-lg-4"> </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header"> 
                    <center> 
                        <h2><?= Html::encode($this->title) ?></h2> 
                        <p>Please choose your new password:</p>
                    </center> 
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                    <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-4"> </div>
    </div>
</div>
