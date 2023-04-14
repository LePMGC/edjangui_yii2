<?php

namespace app\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Account;
use common\models\BankAccount;
use common\models\Loan;
use common\models\LoanOption;
use common\models\LoanEndorse;
use common\models\DjanguiEpisode;
use common\models\DjanguiSeason;
use common\models\DjanguiMember;
use common\models\Parameter;


/**
 * ContactForm is the model behind the contact form.
 */
class LoanForm extends Model{
    public $amount;
    public $payment_method;
    public $phone_number;
    public $taker;
    public $endorser;
    public $loan_id;
    public $bank_account;
    public $loan_option;

    public $start_date;
    public $end_date;
    public $interest;

    public $former_loan_amount_to_refund;
    public $amount_taken;
    public $amount_to_cash_in;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['amount', 'payment_method', 'phone_number', 'endorser'], 'required'],
            
            [['amount'], 'number', 'min' => 0],
            [['phone_number'], 'isAValidPhoneNumber'],
            [['amount'], 'canLoanSuchAmount'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'amount' => Yii::t('app', 'Amount'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'endorser' => Yii::t('app', 'Endorser'),
            'taker' => Yii::t('app', 'Taker'),
            'interest' => Yii::t('app', 'Interest'),
            'start_date' => Yii::t('app', 'Taken Date'),
            'end_date' => Yii::t('app', 'Deadline'),
        ];
    }

    /**
    * @param string $attribute to validate
    * @param string $param
    * @return boolean
    */
    public function isAValidPhoneNumber($attribute, $params){
        if( (strlen($this->$attribute)!=9) || (!preg_match("/^6[0-9]{8}/", $this->$attribute) && !preg_match("/^22|33[0-9]{7}/", $this->$attribute))) {
            $this->addError($attribute, \Yii::t('app', 'This is not a valid phone number'));
            return false;
        }
        return true;
    }

    /**
    * @param string $attribute to validate
    * @param string $param
    * @return boolean
    */
    public function canLoanSuchAmount($attribute, $params){
        $takerBankAccount = Account::findOne(['owner' => $this->taker, 'bank_account' => $this->bank_account])->balance;
        $minBankSoldeForLoanTaker = BankAccount::findOne($this->bank_account)->min_balance_for_loan;

        if($takerBankAccount < $minBankSoldeForLoanTaker){
            $this->amount_to_cash_in = $minBankSoldeForLoanTaker - $takerBankAccount;
            if($this->amount < $this->amount_to_cash_in){
                $this->addError($attribute, \Yii::t('app', 'The requested amount should be more than').' '.$this->amount_to_cash_in.' '.\Yii::t('app', 'so that to allow you have the minimum required in you bank solde which is').' '.$minBankSoldeForLoanTaker);
                return false;
            }
        }

        return true;
    }

    /**
    * Create new loan and loan_endorse models, then save them.
    * @return boolean
    */
    public function performLoanRequest($isItANewLoan){
        $currentUser = User::findOne(Yii::$app->user->getId());

        $modelLoan = $isItANewLoan ? new Loan() : Loan::findOne($this->loan_id);
        $modelLoan->taker = $this->taker;
        $modelLoan->amount_requested = $this->amount;
        $modelLoan->amount_received = $this->amount_taken;
        $modelLoan->amount_to_cash_in = $this->amount_to_cash_in;
        $modelLoan->taken_date = $this->start_date;
        $modelLoan->return_date = $this->end_date;
        $modelLoan->interest = $this->interest;
        $modelLoan->status = Loan::LOAN_REQUESTED;
        $modelLoan->payment_method = $this->payment_method;
        $modelLoan->phone_number = $this->phone_number;
        $modelLoan->association = $currentUser->association;
        $modelLoan->loan_option = $this->loan_option;
        $modelLoan->bank_account = $this->bank_account;

        if($modelLoan->save()) {

            //check if the user have a former loan, reimburse it before creation the new one
            

            // send notification to loan requestors and admins
            $modelLoan->notifyAdminThatLoanIsRequested();            

            $modelLoanEndorse = new LoanEndorse();
            $modelLoanEndorse->loan = $modelLoan->id;
            $modelLoanEndorse->endorser = $this->endorser;
            $modelLoanEndorse->status = LoanEndorse::LOAN_ENDORSE_REQUESTED;
            $modelLoanEndorse->association = $currentUser->association;
            $modelLoanEndorse->save();

            //send notification to taker and endorser
            $modelLoan->notifyTakerThatLoanIsRequested();
            $modelLoanEndorse->sendNotificationToEndorser();
            return true;
        }

        return $modelLoan;
    }

    /**
    * Update existing loans and load model associated to this loanform
    * @return boolean
    */
    public function updateLoanRequest(){
        $modelLoan = Loan::findOne($this->loan_id);
        $modelLoan->taker = $this->taker;
        $modelLoan->amount = $this->amount;
        $modelLoan->taken_date = $this->start_date;
        $modelLoan->return_date = $this->end_date;
        $modelLoan->interest = $this->interest;
        $modelLoan->status = Loan::LOAN_REQUESTED;
        $modelLoan->payment_method = $this->payment_method;
        $modelLoan->phone_number = $this->phone_number;

        if($modelLoan->save()) {
            // send notification to loan requestors and admins
            $modelLoan->notifyAdminThatLoanIsRequested();            

            $modelLoanEndorse = new LoanEndorse();
            $modelLoanEndorse->loan = $modelLoan->id;
            $modelLoanEndorse->endorser = $this->endorser;
            $modelLoanEndorse->status = LoanEndorse::LOAN_ENDORSE_REQUESTED;
            $modelLoanEndorse->save();

            //send notification to taker and endorser
            $modelLoan->notifyTakerThatLoanIsRequested();
            $modelLoanEndorse->sendNotificationToEndorser();
            return true;
        }
        return false;
    }

    /**
    * Check if the current logged can chash out now.
    */
    public static function currentUserCanLoan(){
        if(strcmp(Yii::$app->session->get('loan_creation_mode'),'edit') == 0)
            return true;

        $currentUser = User::findOne(Yii::$app->user->getId());
        
        $modelAccount = Account::findOne(['owner' => $currentUser->id]);
        /*if(is_null($modelAccount) || $modelAccount->balance<=0){
            Yii::$app->session->set('currentUserCantLoanReason', 'A loan can\'t be granated to you since you are not participation in Bank.');
            return false;
        }*/

        $modelLoan = Loan::findOne(['taker' => $currentUser, 'status' => Loan::LOAN_REQUESTED]);
        if(!is_null($modelLoan)){
            Yii::$app->session->set('currentUserCantLoanReason', 'You can\'t request for a Loan now, you already have another one pending.');
            return false;    
        }else
            return true;
    }


    /**
    * Check if the current logged can chash out now.
    */
    public function generateLoanDetails(){

        //check if the member has a pending loan and recalculate the loan amount.
        $modelLoan = Loan::findOne(['taker' => $this->taker, 'status' => Loan::LOAN_GIVEN, 'bank_account' => $this->bank_account]);
        $this->amount_taken = $this->amount;
        if(!is_null($modelLoan)){            
            $this->former_loan_amount_to_refund = $modelLoan->getRemainingAmountToRefund();
            $this->amount = $this->amount + $this->former_loan_amount_to_refund;            
        }else
            $this->former_loan_amount_to_refund = 0;


        //Generate start and end date
        $modelLoanOption = LoanOption::find(['bank_account' => $this->bank_account])
                            ->where('min_amount <= '.$this->amount.' and '.$this->amount.' <= max_amount')
                            ->one();
        $this->start_date = date('Y-m-d');
        $this->end_date = date_create($this->start_date);
        $this->loan_option = $modelLoanOption->id;
        date_add($this->end_date, date_interval_create_from_date_string(($modelLoanOption->number_of_terms*$modelLoanOption->term_duration)." days"));
        $this->end_date = date_format($this->end_date, "Y-m-d");

        //Align end date with the choosen loan return day in loan option =======================================
        if($modelLoanOption->refund_deadline <> 100){
            if((7 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 37)){
                $firstDayOfRetrunMonth = date("Y-m-01", strtotime($this->end_date));
                $d1 = date_create($firstDayOfRetrunMonth);
                $d = date_add($d1, date_interval_create_from_date_string(($modelLoanOption->refund_deadline - 7)." days"));
                $this->end_date = date_format($d, "Y-m-d");

            }elseif ((38 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 79)) {
                if (($modelLoanOption->refund_deadline - 38) % 6  == 0) $rank = "first";
                if (($modelLoanOption->refund_deadline - 39) % 6  == 0) $rank = "second";
                if (($modelLoanOption->refund_deadline - 40) % 6  == 0) $rank = "third";
                if (($modelLoanOption->refund_deadline - 41) % 6  == 0) $rank = "fouth";

                if (($modelLoanOption->refund_deadline - 42) % 6  == 0) $rank = "last";
                if (($modelLoanOption->refund_deadline - 43) % 6  == 0) $rank = "last";

                if((38 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 43)) $day_of_week = "monday";
                if((44 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 49)) $day_of_week = "tuesday";
                if((50 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 55)) $day_of_week = "wednesday";
                if((56 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 61)) $day_of_week = "thursday";
                if((62 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 67)) $day_of_week = "friday";
                if((68 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 73)) $day_of_week = "saturday";
                if((74 <= $modelLoanOption->refund_deadline) && ($modelLoanOption->refund_deadline <= 79)) $day_of_week = "sunday";

                $monthOfReturnDate = $firstDayOfRetrunMonth = date("Y-m-01", strtotime($this->end_date->format('Y-m-d')));
                $this->end_date = date("Y-m-d", strtotime($rank." ".$day_of_week." ".$monthOfReturnDate));

                //get before last week day of given date
                if (($modelLoanOption->refund_deadline - 42) % 6  == 0){
                    $d2 = date_create($this->end_date);
                    date_sub($d2,date_interval_create_from_date_string("7 days"));
                    $this->end_date = date_format($d2,"Y-m-d");
                }
            }
        }
        //====================================================================================================
        

        //Geenrate interest amount
        $this->interest = $this->amount * $modelLoanOption->interest_rate / 100;
    }


    /**
    * Get the id of the last member who endorsed this loan
    **/
    public function getLastEndorserId(){
        $modelLoanEndorse = LoanEndorse::find()
            ->where(['loan' => $this->loan_id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return $modelLoanEndorse->endorser;
    }

    /**
    * Get the current member/user former loan amount to be refunded
    **/
    public function getFromerLoanAmountToBeRefund(){
       $modelFormerLoan = Loan::find()->where(['taker' => Yii::$app->user->getId(), 'status'=>Loan::LOAN_GIVEN])->one();
       if(!is_null($modelFormerLoan))
            $this->former_loan_amount_to_refund = $modelFormerLoan->getRemainingAmountToRefund();
        else
            $this->former_loan_amount_to_refund = 0;

        return $this->former_loan_amount_to_refund;
    }
}