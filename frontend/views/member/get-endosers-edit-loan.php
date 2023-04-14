<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\models\Loan;
use common\models\Account;
use common\models\CashOut;
use kartik\widgets\SwitchInput;

$this->title = Yii::t('app', 'Get Endorsers');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">    
        <div class="row">
            <div class="col-md-3 col-sm-12 col-xs-12"></div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h5> <?= Yii::t('app', 'Amount Requested')?> : <b> <?= Yii::$app->formatter->asDecimal($loan_amount) ?> </b> <br/> <?= Yii::t('app', 'Required Endorsement Amount')?> : <b> <?= Yii::$app->formatter->asDecimal($required_endorse_amount) ?> </b> </h5>
                        <?php
                            /*$currentUser = User::findOne(Yii::$app->user->getId());
                            $currentBalance = Yii::$app->formatter->asDecimal(Account::findOne(['owner' => $currentUser->id])->balance);
                            echo "<center> <h4>".Yii::t('app', "Bank Solde")." : XAF ".$currentBalance." </h4> </center>";*/
                        ?>      
                    </div>
                </div>              
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12"></div>
        </div>

    <div class="row">
        <div class="col-md-3 col-sm-12 col-xs-12"> </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-header"> 
                    <center> 
                        <h4><?= Html::encode($this->title) ?></h4> 
                    </center> 
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                        
                            <table class="table table-striped" style="font-size:13px" id="myTable">
                                <tr>
                                    <th width="1%"></th>
                                    <th width="39%"> <?= Yii::t('app', 'Member')?> </th>
                                    <th width="30%" class="hidden-xs hidden-sm"> <?= Yii::t('app', 'Available')?> </th>
                                    <th width="30%"> <?= Yii::t('app', 'Request')?> </th>
                                </tr>
                                <?php
                                    $sumBank = 0;
                                    $sumEndorsed = 0;
                                    $sumRequested = 0;
                                    $sumAvailableForEndorse = 0;
                                    $i = 0;
                                    foreach ($endorsersData as $endorsersDataItem) {
                                        //compute amount available for endorsement
                                        $amountAvailableForEndorse = $endorsersDataItem['bank_solde'] - $endorsersDataItem['current_amount_endorsed'];

                                        $sumBank += $endorsersDataItem['bank_solde'];
                                        $sumEndorsed += $endorsersDataItem['current_amount_endorsed'];
                                        $sumAvailableForEndorse += $amountAvailableForEndorse;
                                        $sumRequested += is_numeric($endorsersDataItem['requested_endorse_amount']) ? $endorsersDataItem['requested_endorse_amount'] : 0;
                                        
                                        
                                        echo "<tr data-toggle='collapse' data-target='#demo".$i."' class='accordion-toggle'>";
                                            echo "<td><a class='btn btn-default btn-xs'><span class='glyphicon glyphicon-eye-open'></span></a></td>";

                                            echo "<td>".$endorsersDataItem['member']."</td>";
                                            echo "<td class='hidden-xs hidden-sm'>".Yii::$app->formatter->asDecimal($amountAvailableForEndorse)."</td>";
                                            echo "<td>";
                                                echo Html::textInput('user-'.$endorsersDataItem['id'], ($endorsersDataItem['requested_endorse_amount'] > 0 ? $endorsersDataItem['requested_endorse_amount'] : ''), ["class" => "form-control", "autofocus" => "true", "aria-required" => "true", "size" => "10", "type" => "number", "min" => 0, "max" => $amountAvailableForEndorse, "onchange" => "sumInputs()"]);
                                            echo "</td>";
                                        echo "</tr>";

                                        echo "<tr> <td colspan='4' class='hiddenRow'>
                                            <div id='demo".$i."' class='accordian-body collapse'>".
                                                    '<span> <b>'.
                                                        Yii::t('app', 'Bank').': '.'</b>'. Yii::$app->formatter->asDecimal($endorsersDataItem['bank_solde']).
                                                    '</span> <br/>'.                                                
                                                '<span> <b>'.
                                                        Yii::t('app', 'Endorsed For This Loan').': '.'</b>'.Yii::$app->formatter->asDecimal($endorsersDataItem['requested_endorse_amount_for_this_loan']).
                                                ', '.$endorsersDataItem['requested_endorse_status_for_this_loan'].'</span> <br/>'.
                                                '<span> <b>'.
                                                        Yii::t('app', 'Total Endorsed Amount').': '.'</b>'.Yii::$app->formatter->asDecimal($endorsersDataItem['current_amount_endorsed']).
                                                '</span> <br/>'.
                                                '<span> <b>'.
                                                        Yii::t('app', 'Available').': '.'</b>'.Yii::$app->formatter->asDecimal($amountAvailableForEndorse).
                                                '</span>'.
                                            "</div>
                                        </td></tr>";

                                        $i = $i + 1;
                                    }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="hidden-xs hidden-sm"> </th>
                                        <th> <?= Yii::$app->formatter->asDecimal($sumAvailableForEndorse) ?> </th>
                                        <th id="totalRequestedAmount"> <?= $sumRequested?> </th>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <!-- Hidden input used to send the sum of requested amounts-->
                            <input id="sumRequestedAmounts" name="sumRequestedAmounts" type="hidden" value=<?= $sumRequested?> >
                </div>
                <div class="panel-footer">
                    <div class="form-group" >
                        <?= Html::a('< '.Yii::t('app', 'Loan Edit'), Url::to(['/member/edit-a-loan', 'loanId' => $loanId]), ['class' => 'btn btn-success']); ?>
                        <?= Html::submitButton(Yii::t('app', 'Confirm').' >', ['class' => 'btn btn-primary']) ?>                  
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12"> </div>
    </div>
</div>

<script type="text/javascript">
    window.sumInputs = function() {
        var inputs = document.getElementsByTagName('input'),
            result = document.getElementById('totalRequestedAmount'),
            sum = 0;            

        for(var i=0; i<inputs.length; i++) {
            var ip = inputs[i];

            if (ip.name && ip.name.indexOf("total") < 0 && ip.name.indexOf("sum") < 0) {
                sum += parseInt(ip.value) || 0;
            }

        }

        result.innerHTML = sum.toLocaleString('en');
        document.getElementById('sumRequestedAmounts').value = sum;
    }
</script>