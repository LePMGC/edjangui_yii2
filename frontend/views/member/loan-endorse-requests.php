<?php

use common\models\User;
use common\models\Account;
use common\models\Loan;
use common\models\LoanEndorse;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
rmrevin\yii\fontawesome\AssetBundle::register($this);
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Loan Endorse Requests')?> </h4> </center> </div>
                    <div class="panel-body">
                        <?php                           
                            if(count($endorseRequestsData)==0){
                                echo Yii::t('app', "You have no Loan Endorse Requests");
                            }else{
                                echo '<table class="table table-striped" style="font-size:13px">';
                                    $i = 0;
                                    foreach ($endorseRequestsData as $endorseRequestsDataItem) {
                                        //Build date format, display year only if it is different than current year.
                                        if(strcmp(date('Y', strtotime($endorseRequestsDataItem['requested_date'])), Date('Y')) == 0)
                                            $dateFormatTemplate = "php: M d";
                                        else
                                            $dateFormatTemplate = "php: Y M d";


                                        echo "<tr>";
                                            echo "<td>";
                                                echo "<strong> Date : </strong>".Yii::$app->formatter->asDateTime($endorseRequestsDataItem['requested_date'], $dateFormatTemplate);
                                                echo "<br/>";
                                                echo "<strong>".Yii::t('app', 'Requestor')." : </strong>".$endorseRequestsDataItem['loan_requestor'];
                                                echo "<br/>";
                                                echo "<strong>".Yii::t('app', 'Amount')." : </strong>".$endorseRequestsDataItem['loan_amount'];
                                            echo "</td>"; 

                                            echo "<td>";
                                                echo "<strong>".Yii::t('app', 'Endorser')." : </strong>".$endorseRequestsDataItem['loan_endorser'];
                                                echo "<br/>";
                                                echo "<strong>".Yii::t('app', 'Status')." : </strong>".$endorseRequestsDataItem['status'];
                                                echo "<br/>";
                                                if($endorseRequestsDataItem['show_approve_reject_buttons']){
                                                    echo Html::a('<i class="fa fa-fw fa-check fa-lg text-success"></i>', Url::to(['member/approve-endorse-request','endorseId' => $endorseRequestsDataItem['id']]));
                                                    echo "&nbsp &nbsp";
                                                    echo Html::a('<i class="fa fa-fw fa-ban fa-lg text-danger"></i>', Url::to(['member/reject-endorse-request','endorseId' => $endorseRequestsDataItem['id']]));
                                                }
                                            echo "</td>";                                          
                                        echo "</tr>";

                                        $i = $i + 1;
                                    }
                                echo '</table>';
                            }
                        ?>
                    </div>
                    
                    <div class="panel-footer">
                        
                    </div>
                </div>                   
            </div>              
        </div>
    </div>
</div>
