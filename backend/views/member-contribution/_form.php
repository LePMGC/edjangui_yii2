<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use common\models\Episode;
use common\models\Season;
use common\models\DjanguiContribution;
use common\models\Parameter;
use common\models\User;
use common\models\CashIn;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\Episode */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="member-contribution">

    <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="card border-primary">
                    <div class="card-header"> <?= Yii::t('app', 'Member') ?> </div>
                        <div class="card-body text-primary">
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
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="card border-primary">
                    <div class="card-header"> <?= Yii::t('app', 'Djangui') ?> </div>
                        <div class="card-body text-primary">

                            <?php
                                foreach ($model->djangui_contributions as $djangui_contribution) {
                                    $modelDjanguiContribution = DjanguiContribution::findOne($djangui_contribution['djangui_contribution_id']);
                                    echo "<div class='form-group field-djangui_contribution-amount required'>";
                                    echo "<label class='control-label' for='djangui_contribution-amount'>".Yii::t("app", "Djangui")." - ".$djangui_contribution['djangui_name']."</label>";

                                    echo BaseHtml::textInput("djangui_contribution_".$djangui_contribution['djangui'], 
                                            $djangui_contribution['djangui_contribution'], 
                                            [
                                                'class' => 'form-control',
                                                'disabled' => is_null($modelDjanguiContribution) ? false : true /*!$modelDjanguiContribution->canBeUpdated()*/,
                                                'id' => "djangui_contribution_".$djangui_contribution['djangui'],
                                                'type' => "number"
                                            ]);                        
                                    echo "<div class='help-block'></div>";
                                    echo "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="card border-primary">
                    <div class="card-header"> <?= Yii::t('app', 'Banque') ?> </div>
                        <div class="card-body text-primary">
                            <?php
                                foreach ($model->bank_contributions as $bank_contribution) {
                                    $modelCashIn = CashIn::findOne($bank_contribution['cash_in_id']);
                                    echo "<div class='form-group field-cashin-amount required'>";
                                    echo "<label class='control-label' for='cashin-amount'>".Yii::t("app", "Bank")." - ".$bank_contribution['bank_account_name']."</label>";
                                    echo BaseHtml::textInput("bank_contribution_".$bank_contribution['bank_account'],
                                        $bank_contribution['bank_contribution'],
                                        [
                                            'class' => 'form-control', 
                                            'disabled' => is_null($modelCashIn) ? false : !$modelCashIn->canBeUpdated(),
                                            'type' => "number"
                                        ]
                                    );                        
                                    echo "<div class='help-block'></div>";
                                    echo "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    <?= BaseHtml::hiddenInput('get_expected_djangui_contribution_amount_url', Url::to(['/djangui-contribution/get-expected-djangui-contribution-amount', 'userId' => ""]), 
        ['id' => 'get_expected_djangui_contribution_amount_url']) ?>


    <?= BaseHtml::hiddenInput('expected_djangui_contribution_amount', 0, ['id' => 'expected_djangui_contribution_amount']) ?>

    <br/>
    <br/>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    function setExpectedDjanguiContributionAmount(userId) {
        var Httpreq = new XMLHttpRequest(); // a new request
        var urlToGetExpectedDjanguiContribution = document.getElementById("get_expected_djangui_contribution_amount_url").value + userId;

        Httpreq.open("GET", urlToGetExpectedDjanguiContribution, false);
        Httpreq.send(null);
        var json_obj = JSON.parse(Httpreq.responseText);
        console.log("json responseText: "+json_obj.expected_djangui_contribution_amounts);

        var expected_djangui_contribution_amounts = json_obj.expected_djangui_contribution_amounts.split("#");
        for (i = 0; i < expected_djangui_contribution_amounts.length; i++) { 
            if(expected_djangui_contribution_amounts[i].length>0){
                var t = expected_djangui_contribution_amounts[i].split("=");
                if(t[1]==0){
                    document.getElementById(t[0]).value = t[1];
                    document.getElementById(t[0]).disabled  = true;
                }else{
                    document.getElementById(t[0]).disabled  = false;
                    document.getElementById(t[0]).value = t[1];
                }
            }
        }

        //document.getElementById("membercontribution-djangui_contribution").value = json_obj.expected_djangui_contribution;
        //document.getElementById("expected_djangui_contribution_amount").value = json_obj.expected_djangui_contribution;
        //document.getElementById("cashincotisationwithaccountform-account_amount").value = json_obj.xpected_amount;
    }

    function setContributionAmount(transactionAmount){
        document.getElementById("cashin-bank_contribution").value = transactionAmount -  document.getElementById("cashin-djangui_contribution").value;
    }
</script>

