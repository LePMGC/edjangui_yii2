<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\User;
use common\models\BankAccount;

/** @var yii\web\View $this */
/** @var common\models\Account $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= 
        $form->field($model, 'owner')->widget(Select2::classname(), [
            'data' => User::getAllMembersOfCurrentAssociation(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Member').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
        ]);
    ?>

    <?= 
        $form->field($model, 'bank_account')->widget(Select2::classname(), [
            'data' => BankAccount::getAllBankAccountsOfCurrentAssociation(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Bank Account').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
        ]);
    ?>

    <?= $form->field($model, 'balance')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
