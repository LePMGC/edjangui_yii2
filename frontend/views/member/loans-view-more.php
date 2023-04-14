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
                    <div class="panel-header"> <center> <h4> <?= Yii::t('app', 'Loans')?> </h4> </center> </div>
                    <div class="panel-body">
                        <?php
                            $modelLoans = Loan::find()->where(['association' => $model->association])->orderBy('created_on DESC')->all();
                            if(count($modelLoans)==0){
                                echo "You have no loans";
                            }else{
                                echo '<table class="table table-striped" style="font-size:13px">';
                                    echo '<tr>';
                                        echo '<th>'.Yii::t('app', 'Date').'</th>';
                                        echo '<th>'.Yii::t('app', 'Taker').'</th>';
                                        echo '<th>'.Yii::t('app', 'Amount').'</th>';
                                        echo '<th>'.Yii::t('app', 'Status').'</th>';
                                    echo '</tr>';
                                    foreach ($modelLoans as $modelLoan) {
                                        $loanTakerFirstName = User::findOne($modelLoan->taker)->name;
                                        $arr = explode(' ',trim($loanTakerFirstName));
                                        $loanTakerFirstName = $arr[0];

                                        if(strcmp(date('Y', strtotime($modelLoan->taken_date)), Date('Y')) == 0)
                                            $dateFormatTemplate = "php:  M d";
                                        else
                                            $dateFormatTemplate = "php:  Y M d";

                                        echo '<tr>';
                                            echo '<td>'.Yii::$app->formatter->asDateTime($modelLoan->taken_date, $dateFormatTemplate).'</td>';
                                            echo '<td>'.$loanTakerFirstName.'</td>';
                                            echo '<td>'.Yii::$app->formatter->asDecimal($modelLoan->getAmountToDisplayInFrontend(),0).'</td>';
                                            echo '<td>'.$modelLoan->getStatusForFrontend().'</td>';
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
