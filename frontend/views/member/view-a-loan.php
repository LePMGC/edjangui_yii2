<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use kartik\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use common\models\User;
use common\models\Loan;
use common\models\LoanEndorse;
use common\models\Account;
use kartik\widgets\SwitchInput;
use yii\widgets\DetailView;

$this->title = Yii::t('app', 'View A Loan');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-cash-out">
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-12"> </div>
        <div class="col-md-5 col-sm-5 col-xs-12">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
            <div class="card card-primary">
                <div class="card-header"> 
                    <center> 
                        <h4><?= Html::encode($this->title) ?></h4> 
                    </center> 
                </div>
                <div class="card-body">
                    <!-- <center> <strong> <?= Yii::t('app', 'Loan Header')?> </strong> </center> -->
                    <?php
                        $modelLoanEndorse = LoanEndorse::find()->where(['loan' => $modelLoan->id])->orderBy(['id' => SORT_DESC])->one();
                        if(!is_null($modelLoanEndorse)){
                            $modelLoanEndorseUser = User::findOne($modelLoanEndorse->endorser);
                            $modelLoanEndorseUserName = $modelLoanEndorseUser->name;
                        }else
                            $modelLoanEndorseUserName = '';


                        echo DetailView::widget([
                            'model' => $modelLoan,
                            'attributes' => [
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('taker'),
                                    'value' => User::findOne($modelLoan->taker)->name,
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('amount_requested'),
                                    'value' => Yii::$app->formatter->asDecimal($modelLoan->amount_requested,0),
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('payment_method'),
                                    'value' => Loan::getAllPaymentMethods()[$modelLoan->payment_method],
                                ],
                                [ 
                                    'label' => $modelLoan->getAttributeLabel('phone_number'),
                                    'value' => $modelLoan->phone_number,
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
                                    'value' => Yii::$app->formatter->asDecimal($modelLoan->interest,0),
                                ],
                                [ 
                                    'label' => Yii::t('app', 'Loan Status'),
                                    'value' => $modelLoan->getStatusForFrontend(),
                                    'format' => 'raw',
                                ],
                                [ 
                                    'label' => Yii::t('app', 'Endorser'),
                                    'value' => $modelLoanEndorseUserName,
                                ],
                                [ 
                                    'label' => Yii::t('app', 'Endorse Request Status'),
                                    'value' => is_null($modelLoanEndorse) ? '' : LoanEndorse::getAllStatuses()[$modelLoanEndorse->status],
                                ]
                            ],
                        ]);
                    ?>      
                        <!-- <br/>
                        <center> <strong> <?= Yii::t('app', 'Loan Endorsers')?> </strong> </center>
                            <table class="table table-striped" style="font-size:13px" id="myTable">
                                <tr>
                                    <th width="39%"> <?= Yii::t('app', 'Member')?> </th>
                                    <th width="30%"> <?= Yii::t('app', 'Requested Amount')?> </th>
                                    <th width="30%"> <?= Yii::t('app', 'Status')?> </th>
                                </tr>
                                <?php
                                    $sumRequestedAmount = 0;
                                    foreach ($endorsersData as $endorsersDataItem) {
                                        if($endorsersDataItem['requested_endorse_amount'] > 0){
                                            echo "<tr>";
                                                echo "<td>".$endorsersDataItem['member']."</td>";
                                                echo "<td>".Yii::$app->formatter->asDecimal($endorsersDataItem['requested_endorse_amount'])."</td>";
                                                echo "<td>".$endorsersDataItem['status']."</td>";
                                            echo "</tr>";
                                            $sumRequestedAmount += $endorsersDataItem['requested_endorse_amount'];
                                        }
                                    }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th> <?= Yii::$app->formatter->asDecimal($sumRequestedAmount) ?> </th>
                                    </tr>
                                </tfoot>
                            </table> -->

                </div>
                <div class="card-footer">
                    <div class="form-group" >
                        <?= (($modelLoan->status == Loan::LOAN_REQUESTED) && $modelLoan->isEndorseRequestRejected()) ? Html::a(Yii::t('app', 'Edit'), Url::to(['/member/edit-a-loan', 'loanId' => $modelLoan->id]), ['class' => 'btn btn-success']) : ''; ?>
                        
                        <?= (($modelLoan->status == Loan::LOAN_REQUESTED) && !$modelLoan->isEndorseRequestApproved()) ? Html::a(Yii::t('app', 'Delete'), Url::to(['/member/delete-a-loan', 'loanId' => $modelLoan->id]), ['class' => 'btn btn-danger', "data-pjax" => "0", "data-method" => "post", "data-confirm" => "Are you sure to delete this Loan?"]) : ''; ?>

                        <?= Html::a(Yii::t('app', 'Cancel'), Url::to(['/site/index']), ['class' => 'btn btn-info']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-12"> 
            <div class="card card-success">
                <div class="card-header"> 
                    <center> 
                        <h4><?= Yii::t('app', 'Refunds') ?></h4>
                    </center> 
                </div>
                <div class="card-body">
                    <table class="table table-striped" style="font-size:13px" id="myTable">
                        <tr>
                            <th> <?= Yii::t('app', 'Date')?> </th>
                            <th> <?= Yii::t('app', 'Remain Before')?> </th>
                            <th> <?= Yii::t('app', 'Amount Given')?> </th>
                            <th> <?= Yii::t('app', 'Remain After')?> </th>
                        </tr>
                        <?php
                            $totalAmountGiven = 0;
                            foreach ($refundsData as $item) {
                                echo "<tr>";
                                    echo "<td>".Yii::$app->formatter->asDate($item['date'])."</td>";
                                    echo "<td>".Yii::$app->formatter->asDecimal($item['remain_before'])."</td>";
                                    echo "<td>".Yii::$app->formatter->asDecimal($item['amount_given'])."</td>";
                                    echo "<td>".Yii::$app->formatter->asDecimal($item['remain_after'])."</td>";
                                echo "</tr>";
                                $totalAmountGiven += $item['amount_given'];
                            }
                        ?>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th> <?= Yii::$app->formatter->asDecimal($totalAmountGiven) ?> </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
