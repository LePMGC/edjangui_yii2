<?php
use yii\helpers\Html;
use yii\helpers\BaseUrl;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->name." - ".Yii::t('app', 'Sign In');

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<br/> <br/> <br/> <br/>

<div class="row">
    <div class="col-lg-4"> </div>
    <div class="col-lg-4">
        <strong>
            <?= Yii::$app->name ?> - BACKEND
        </strong>
        <br/>
        <div class="card card-primary">
            <div class="card-header"> 
                <center> <p class="login-box-msg"><?= Yii::t('app', 'Sign in to start your session')?> </p> </center>
            </div>
            <div class="card-body">
                <div class="login-box">
                <!-- /.login-logo -->
                <div class="login-box-body">
                    
                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

                    <?= $form
                        ->field($model, 'username', $fieldOptions1)
                        ->label(false)
                        ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

                    <?= $form
                        ->field($model, 'password', $fieldOptions2)
                        ->label(false)
                        ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'rememberMe')->checkbox() ?>
                        </div>

                        <div class="col-md-2"> </div>

                        <!-- /.col -->
                        <div class="col-md-4">
                            <?= Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                        </div>
                        <!-- /.col -->
                    </div>


                    <?php ActiveForm::end(); ?>

                    <a href=<?= BaseUrl::to(['/site/reset-password'])?> ><?= Yii::t('app', 'I forgot my password')?> </a><br>
                </div>
                <!-- /.login-box-body -->
            </div><!-- /.login-box -->

            </div>
        </div>
    </div>
    <div class="col-lg-4"> </div>
</div>