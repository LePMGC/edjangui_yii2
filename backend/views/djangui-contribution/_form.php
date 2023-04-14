<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Episode;
use common\models\User;
use kartik\select2\Select2;
use common\models\Season;
use common\models\Djangui;

/** @var yii\web\View $this */
/** @var common\models\DjanguiContribution $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="djangui-contribution-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'member')->widget(Select2::classname(), [
                                'data' => User::getAllUsers(),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select a Member').' ...',
                                    'onChange' => 'setExpectedDjanguiContributionAmount(this.value)'
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                ]);
                            ?>

    <?= $form->field($model, 'episode')->widget(Select2::classname(), [
                                //'data' => Episode::getAllEpisodes(Parameter::findOne(['name' => 'current_season'])->value),
                                'data' => Episode::getAllEpisodes(Season::getCurrentSeasonId()),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select an Episode').' ...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                ]);
                            ?>
	
	<?= $form->field($model, 'djangui')->widget(Select2::classname(), [
                                //'data' => Episode::getAllEpisodes(Parameter::findOne(['name' => 'current_season'])->value),
                                'data' => Djangui::getAllDjanguis(),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'Select a Djangui').' ...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                                ]);
                            ?>

    <?= $form->field($model, 'expected_amount')->textInput() ?>

    <?= $form->field($model, 'contribution_amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
