<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Login';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <div class="row">
        <div class="col-lg-4"> </div>
        <div class="col-lg-4">
            <div class="card card-primary">
                <div class="card-header"> <center> <h1><?= Html::encode($this->title) ?></h1> </center> </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeHolder' => 'Username or Phone Number or Email Address']) ?>

                        <?= $form->field($model, 'password')->passwordInput() ?>

                        <?= $form->field($model, 'rememberMe')->checkbox() ?>

                        <div style="color:#999;margin:1em 0">
                            If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                        </div>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-lg-4"> </div>
    </div>
</div>