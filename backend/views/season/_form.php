<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use common\models\Episode;
use common\models\Season;
use common\models\LoanOption;

/** @var yii\web\View $this */
/** @var common\models\Season $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="season-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>  

    <?=  
       $form->field($model, 'periodicity')->widget(Select2::classname(), [
            'data' => Season::getPeriodicities(1),
            'options' => ['placeholder' => Yii::t('app', 'Select a Periodicity').' ...'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]); 
    ?>

    <?= $form->field($model, 'meeting_day')->widget(Select2::classname(), [
                'data' => LoanOption::getListOfAllRefundDayTypes(),
                'options' => [
                    'placeholder' => Yii::t('app', 'Select a Meeting Day').' ...',
                    //'onChange' => 'setListOfRefundDayRanks(this.value)'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                ]);
                ?>

    <?= $form->field($model, 'start_date')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter Start date ...'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>

     <?= $form->field($model, 'end_date')->widget(DatePicker::classname(), [
        'options' => ['placeholder' => 'Enter End date ...'],
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>