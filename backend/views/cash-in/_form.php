<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Season;
use common\models\Parameter;
use common\models\Episode;
use common\models\BankAccount;
use kartik\select2\Select2;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\CashIn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cash-in-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
        if($model->isNewRecord) 
        {
            echo $form->field($model, 'episode')->widget(Select2::classname(), [
                //'data' => Episode::getAllEpisodes(Parameter::findOne(['name' => 'current_season'])->value),
                'data' => Episode::getAllEpisodes(Season::getCurrentSeasonId()),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Season').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);

            echo $form->field($model, 'bank_account')->widget(Select2::classname(), [
                'data' => BankAccount::getAllBankAccountsOfCurrentAssociation(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a bank account').' ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
         
            echo $form->field($model, 'member')->widget(Select2::classname(), [
                    'data' => User::getAllUsers(),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Select a Member').' ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
            ]);
        }
        else
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [ 
                            'label' => $model->getAttributeLabel('member'),
                            'value' => User::findOne($model->member)->name,
                        ],
                [ 
                            'label' => $model->getAttributeLabel('episode'),
                            'value' => Episode::findOne($model->episode)->name,
                        ],
                [
                    'label' => $model->getAttributeLabel('bank_account'),
                    'value' => BankAccount::findOne($model->bank_account)->name,
                ],
            ]
        ]);
    ?>

    <?= $form->field($model, 'amount')->textInput(
        [
            'onChange' => 'setContributionAmount(this.value)',
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>