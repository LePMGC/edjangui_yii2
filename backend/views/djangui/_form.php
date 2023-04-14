<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\User;
use common\models\BankAccount;
use kartik\switchinput\SwitchInput;

/** @var yii\web\View $this */
/** @var common\models\Djangui $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="djangui-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'penalty_type')->widget(SwitchInput::classname(), [
                'pluginOptions'=>[
                    //'handleWidth'=>60,
                    'onText'=>'Fix Amount',
                    'offText'=>'Percentage'
                ]
        ]
    ); ?>

    <?= $form->field($model, 'penalty_amount')->textInput() ?>

    <?= 
        $form->field($model, 'penalty_account')->widget(Select2::classname(), [
            'data' => BankAccount::getAllBankAccountsOfCurrentAssociation(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Bank Account').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
