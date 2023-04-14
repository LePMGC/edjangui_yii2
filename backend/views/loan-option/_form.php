<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\BankAccount;
use common\models\LoanOption;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var common\models\LoanOption $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="loan-option-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-6">
             <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

             <?= $form->field($model, 'bank_account')->widget(Select2::classname(), [
                'data' => BankAccount::getAllBankAccountsWithLoanAllowed(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Bank Account').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>

            <?= $form->field($model, 'min_amount')->textInput() ?>

            <?= $form->field($model, 'max_amount')->textInput() ?>

            <?= $form->field($model, 'interest_rate')->textInput() ?>

            <?= $form->field($model, 'postpone_option')->widget(Select2::classname(), [
                'data' => LoanOption::getListOfAllPostponeOptions(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Loan Option').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>

            <?= $form->field($model, 'postpone_capital')->widget(Select2::classname(), [
                'data' => LoanOption::getListOfAllPostponeCapital(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Loan Capital').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>

        </div>
        <div class="col-6">
            <?= $form->field($model, 'number_of_terms')->textInput() ?>

            <?= $form->field($model, 'term_duration')->widget(Select2::classname(), [
                'data' => LoanOption::getListOfAllTermDurations(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Term Duration').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>

            <?= $form->field($model, 'refund_deadline')->widget(Select2::classname(), [
                'data' => LoanOption::getListOfAllRefundDayTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Refund Day Type').' ...',
                    //'onChange' => 'setListOfRefundDayRanks(this.value)'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>
        </div>
    </div>

   
    <div class="row">
        <div class="col-6">
        </div>
        <div class="col-6">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>