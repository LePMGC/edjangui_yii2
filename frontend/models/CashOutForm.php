<?php

namespace app\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Account;
use common\models\CashOut;


/**
 * ContactForm is the model behind the contact form.
 */
class CashOutForm extends Model{
    public $amount;
    public $payment_method;
    public $phone_number;
    public $member;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['amount', 'payment_method', 'phone_number'], 'required'],
            
            [['amount'], 'number', 'min' => 0],
            [['phone_number'], 'isAValidPhoneNumber'],
            [['amount'], 'canCashOutSuchAmount'],
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
    public function canCashOutSuchAmount($attribute, $params){
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentBalance = Account::findOne(['owner' => $currentUser->id])->balance;
        
        //check requested amount is less than availabe bank solde
        if($this->amount > $currentBalance) {
            $this->addError($attribute, \Yii::t('app', 'You can\'t withdraw more than your current balance which is ').$currentBalance);
            return false;
        }

        return true;
    }

    /**
    * Create a new cash out request model and save it.
    * @return boolean
    */
    public function performCashOutRequest(){
        $modelCashOut = new CashOut();
        $modelCashOut->user = $this->member;
        $modelCashOut->transaction_amount = $this->amount;
        $modelCashOut->phone_number = $this->phone_number;
        $modelCashOut->status = CashOut::CASH_OUT_REQUESTED;
        
        $currentUser = User::findOne(Yii::$app->user->getId());

        $modelCashOut->balance_before = Account::findOne(['owner' => $currentUser->id])->balance;
        //$modelCashOut->balance_after = Account::findOne(['owner' => $currentUser->id])->balance - $this->amount;
        if($modelCashOut->save()) {
            $modelCashOut->sendEmailtoAdminAndMember();
            return true;
        }
        return false;
    }

    /**
    * Check if the current logged can chash out now.
    */
    public static function currentUserCanCashOut(){
        $currentUser = User::findOne(Yii::$app->user->getId());
        $modelCashOut = CashOut::findOne(['member' => $currentUser, 'status' => CashOut::CASH_OUT_REQUESTED]);
        if(!is_null($modelCashOut)){
            //$this->addError($attribute, \Yii::t('app', 'You can\'t request for a Cash Out now, you already have another one pending.'));
            return false;    
        }else
            return true;
    }
}
