<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\models\Loan;
use common\models\LoanEndorse;
use common\models\Account;
use kartik\widgets\SwitchInput;
use yii\widgets\DetailView;

$this->title = Yii::t('app', 'Confirm Loan');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">
    <div class="row">
        <div class="col-md-3 col-sm-12 col-xs-12"> </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
            <div class="panel panel-primary">
                <div class="panel-header"> 
                    <center> 
                        <h4><?= Html::encode($this->title) ?></h4> 
                    </center> 
                </div>
                <div class="panel-body">
                    <center> <strong> <?= Yii::t('app', 'Loan Header')?> </strong> </center>
                    <?php                        
                        echo DetailView::widget([
                            'model' => $modelLoan,
                            'attributes' => [
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('taker'),
                                    'value' => User::findOne($modelLoan->taker)->name,
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('amount'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoan->amount),
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('payment_method'),
                                    'value' => Loan::getAllPaymentMethods()[$modelLoan->payment_method],
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('phone_number'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoan->phone_number),
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('taken_date'),
                                    'value' => Yii::$app->formatter->asDateTime($modelLoan->taken_date),
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('return_date'),
                                    'value' => Yii::$app->formatter->asDateTime($modelLoan->return_date),
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('interest'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoan->interest),
                                ],
                            ],
                        ]);
                    ?>      
                        <br/>
                        <center> <strong> <?= Yii::t('app', 'Loan Endorsers')?> </strong> </center>
                            <table class="table table-striped" style="font-size:13px" id="myTable">
                                <tr>
                                    <th width="39%"><?= Yii::t('app', 'Member')?> </th>
                                    <th width="30%"><?= Yii::t('app', 'Amount Requested')?> </th>
                                </tr>
                                <?php
                                    $sumRequestedAmount = 0;
                                    foreach ($endorsersData as $endorsersDataItem) {
                                        if($endorsersDataItem['requested_endorse_amount'] > 0){
                                            echo "<tr>";
                                                echo "<td>".User::findOne($endorsersDataItem['id'])->name."</td>";
                                                echo "<td>".Yii::$app->formatter->asDecimal($endorsersDataItem['requested_endorse_amount'])."</td>";
                                            echo "</tr>";
                                            $sumRequestedAmount += $endorsersDataItem['requested_endorse_amount'];
                                        }
                                    }
                                    if(Yii::$app->session->get('own_endorse_amount') > 0){
                                        echo "<tr>";
                                            echo "<td>"."You"."</td>";
                                            echo "<td>".Yii::$app->formatter->asDecimal(Yii::$app->session->get('own_endorse_amount'))."</td>";
                                        echo "</tr>";
                                        $sumRequestedAmount += Yii::$app->session->get('own_endorse_amount');
                                    }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th> <?= Yii::$app->formatter->asDecimal($sumRequestedAmount) ?> </th>
                                    </tr>
                                </tfoot>
                            </table>

                </div>
                <div class="panel-footer">
                    <div class="form-group" >
                        <?= Html::a('< '.Yii::t('app', 'Get Endorsers'), Url::to(['/member/get-endosers']), ['class' => 'btn btn-success']); ?>
                        <?= Html::submitButton(Yii::t('app', 'Confirm Loan'), ['class' => 'btn btn-primary', 'id' => 'confirm-button', 'name' => 'confirm-button']) ?>                  
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12"> </div>
    </div>
</div>
