<?php

use common\models\User;
use common\models\Account;
use common\models\DjanguiEpisode;
use common\models\Loan;
use common\models\CashIn;
use common\models\CashOut;
use common\models\InterestShare;
use common\models\DelayCharges;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <?php
        $t = explode("-", $ah_key);
        $ah_type = $t[0];
        $ah_transaction_id = $t[1];
        switch ($ah_type) {
            case 1:
                $modelCashIn = CashIn::findOne($ah_transaction_id);
                echo DetailView::widget([
                    'model' => $modelCashIn,
                    'attributes' => [
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('djangui_episode'),
                            'value' => DjanguiEpisode::findOne($modelCashIn->djangui_episode)->name,
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('transaction_amount'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashIn->transaction_amount)
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('djangui_contribution'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashIn->djangui_contribution)
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('bank_contribution'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashIn->bank_contribution)
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('balance_before'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashIn->balance_before)
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('balance_after'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashIn->balance_after)
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('created_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelCashIn->created_on),
                        ],
                        [ 
                            'label' => $modelCashIn->getAttributeLabel('updated_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelCashIn->updated_on),
                        ]
                    ],
                ]);
                break;

            case 2:
                $modelInterestShare = InterestShare::findOne($ah_transaction_id);
                $modelLoan = Loan::findOne($modelInterestShare->loan);
                echo DetailView::widget([
                    'model' => $modelInterestShare,
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
                        [ 
                            'label' => $modelInterestShare->getAttributeLabel('total_balance_at_loan'),
                            'value' => Yii::$app->formatter->asDecimal($modelInterestShare->total_balance_at_loan),
                        ],
                        [ 
                            'label' => $modelInterestShare->getAttributeLabel('balance_at_loan'),
                            'value' => Yii::$app->formatter->asDecimal($modelInterestShare->balance_at_loan),
                        ],
                        [ 
                            'label' => $modelInterestShare->getAttributeLabel('own_share'),
                            'value' => Yii::$app->formatter->asDecimal($modelInterestShare->own_share),
                        ],
                        [ 
                            'label' => $modelInterestShare->getAttributeLabel('balance_before'),
                            'value' => Yii::$app->formatter->asDecimal($modelInterestShare->balance_before),
                        ],
                        [ 
                            'label' => $modelInterestShare->getAttributeLabel('balance_after'),
                            'value' => Yii::$app->formatter->asDecimal($modelInterestShare->balance_after),
                        ],
                    ],
                ]);
                break;
            
            case 3:
                $modelCashOut = CashOut::findOne($ah_transaction_id);
                echo DetailView::widget([
                    'model' => $modelCashOut,
                    'attributes' => [
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('payment_method'),
                            'value' => CashOut::getAllPaymentMethods()[$modelCashOut->payment_method],
                        ],
                        'phone_number',
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('transaction_amount'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashOut->transaction_amount)
                        ],
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('balance_before'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashOut->balance_before)
                        ],
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('balance_after'),
                            'value' => Yii::$app->formatter->asDecimal($modelCashOut->balance_after)
                        ],
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('created_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelCashOut->created_on),
                        ],
                        [ 
                            'label' => $modelCashOut->getAttributeLabel('updated_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelCashOut->updated_on),
                        ]
                    ],
                ]);
                break;

                case 4:
                $modelDelayCharges = DelayCharges::findOne($ah_transaction_id);
                echo DetailView::widget([
                    'model' => $modelDelayCharges,
                    'attributes' => [
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('djangui_episode'),
                            'value' => DjanguiEpisode::findOne($modelDelayCharges->djangui_episode)->name
                        ],
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('charges'),
                            'value' => Yii::$app->formatter->asDecimal($modelDelayCharges->charges)
                        ],
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('balance_before'),
                            'value' => Yii::$app->formatter->asDecimal($modelDelayCharges->balance_before)
                        ],
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('balance_after'),
                            'value' => Yii::$app->formatter->asDecimal($modelDelayCharges->balance_after)
                        ],
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('created_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelDelayCharges->created_on),
                        ],
                        [ 
                            'label' => $modelDelayCharges->getAttributeLabel('updated_on'),
                            'value' => Yii::$app->formatter->asDateTime($modelDelayCharges->updated_on),
                        ]
                    ],
                ]);
                break;
            default:
                # code...
                break;
        }
    ?>
</div>
