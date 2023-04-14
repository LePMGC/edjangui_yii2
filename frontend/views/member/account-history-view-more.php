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
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Account History')?> </h4> </center> </div>
                    <div class="panel-body">
                        <?php
                            $accountHistoryData = $model->getAccountHistoryData();
                            if(count($accountHistoryData)==0){
                                echo "Your Account History is empty";
                            }else{
                                echo '<table class="table table-striped" style="font-size:13px">';
                                    echo '<tr>';
                                        echo '<th>'.Yii::t('app', 'Date').'</th>';
                                        echo '<th>'.Yii::t('app', 'Type').'</th>';
                                        echo '<th>'.Yii::t('app', 'Amount').'</th>';
                                        echo '<th>'.Yii::t('app', 'Balance After').'</th>';
                                    echo '</tr>';
                                    foreach ($accountHistoryData as $accountHistoryDataItem) {
                                        //Build date format, display year only if it is different than current year.
                                        if(strcmp(date('Y', strtotime($accountHistoryDataItem['ah_date'])), Date('Y')) == 0)
                                            $dateFormatTemplate = "php: M d";
                                        else
                                            $dateFormatTemplate = "php: Y M d";

                                         echo '<tr>';
                                            echo '<td>'.Yii::$app->formatter->asDateTime($accountHistoryDataItem['ah_date'], $dateFormatTemplate).'</td>';
                                            echo '<td>'.$accountHistoryDataItem['ah_type'].'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($accountHistoryDataItem['ah_amount']).'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($accountHistoryDataItem['ah_balance_after']).'</td>';
                                        echo '</tr>';
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
