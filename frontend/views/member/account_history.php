<?php

use common\models\User;
use common\models\Account;
use common\models\Loan;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Detailled Account History')?> </h4> </center> </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($model, 'name')->textInput() ?>
                        <?= $form->field($model, 'phone_number')->textInput() ?>
                        <?= $form->field($model, 'email_address')->textInput() ?>
                    </div>
                    
                    <div class="panel-footer">
                        
                    </div>
                </div>                   
            </div>              
        </div>
    </div>
</div>
