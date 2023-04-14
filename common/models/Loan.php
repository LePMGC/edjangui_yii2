<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "loan".
 *
 * @property int $id
 * @property int|null $taker
 * @property float|null $amount_requested
 * @property float|null $amount_received
 * @property float|null $amount_to_cash_in
 * @property string|null $taken_date
 * @property string|null $return_date
 * @property float|null $interest
 * @property int|null $status
 * @property int|null $payment_method
 * @property string|null $phone_number
 * @property int|null $association
 * @property int|null $loan_option
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property User $createdBy
 * @property LoanOption $loanOption
 * @property User $taker0
 * @property User $updatedBy
 */
class Loan extends BaseModel
{

    //Loan Status
    const LOAN_REQUESTED = 0;
    const LOAN_GIVEN = 10;
    const LOAN_RETURNED = 20;
    const LOAN_REJECTED = 30;

    const LOAN_MOBILE_PAYMENT = 1;
    const LOAN_CASH_PAYMENT = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['taker', 'status', 'payment_method', 'association', 'loan_option', 'created_by', 'updated_by'], 'integer'],
            [['amount_requested', 'amount_received', 'amount_to_cash_in', 'interest'], 'number'],
            [['taken_date', 'return_date', 'created_on', 'updated_on'], 'safe'],
            [['phone_number'], 'string', 'max' => 45],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['loan_option'], 'exist', 'skipOnError' => true, 'targetClass' => LoanOption::class, 'targetAttribute' => ['loan_option' => 'id']],
            [['taker'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['taker' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'taker' => Yii::t('app', 'Taker'),
            'amount_requested' => Yii::t('app', 'Amount Requested'),
            'amount_received' => Yii::t('app', 'Amount Received'),
            'amount_to_cash_in' => Yii::t('app', 'Amount To Cash In'),
            'taken_date' => Yii::t('app', 'Taken Date'),
            'return_date' => Yii::t('app', 'Return Date'),
            'interest' => Yii::t('app', 'Interest'),
            'status' => Yii::t('app', 'Status'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'association' => Yii::t('app', 'Association'),
            'loan_option' => Yii::t('app', 'Loan Option'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[LoanOption]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOption()
    {
        return $this->hasOne(LoanOption::class, ['id' => 'loan_option']);
    }

    /**
     * Gets query for [[Taker0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaker0()
    {
        return $this->hasOne(User::class, ['id' => 'taker']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
    * Get all possible status of a user
    * @return array
    */
    public static function getAllStatuses(){
        return [
            0 => Yii::t('app', 'LOAN_REQUESTED'),
            10 => Yii::t('app', 'LOAN_GIVEN'),
            20 => Yii::t('app', 'LOAN_RETURNED'),
        ];
    }

    /**
    * Get all possible payment method of a user
    * @return array
    */
    public static function getAllPaymentMethods(){
        return [
            1 => Yii::t('app', 'MOBILE'),
            2 => Yii::t('app', 'CASH'),
        ];
    }

    /**
    * Check if the loan endorse request status is approved
    * @return boolean
    */
    public function isEndorseRequestApproved(){
        $modelLoanEndorse = LoanEndorse::find()
            ->where(['loan' => $this->id])
            ->orderBy(['id' => SORT_DESC])
            ->one(); 
        if(is_null($modelLoanEndorse)) return false;
        return ($modelLoanEndorse->status == LoanEndorse::LOAN_ENDORSE_APPROVED);
    }
	
	
	/**
    * Get amount to be displayed in the frontend. In fact for a loan which already refuneded 0 will be displayed and remaining amount to be refunded will be displayed for a loan not yet refunded
    **/
    public function getAmountToDisplayInFrontend(){
        if ($this->status == Loan::LOAN_REQUESTED){
            return $this->getRemainingAmountToRefund();
        }
        elseif($this->status == Loan::LOAN_RETURNED){
            return 0;
        }elseif ($this->status == Loan::LOAN_GIVEN) {
            return $this->getRemainingAmountToRefund();
        }
    }
	
	/**
    * Get remaining amount to be refunded for a loan
    * @return integer
    */
    public function getRemainingAmountToRefund(){
         $rows = (new \yii\db\Query())
            ->select(['sum(amount_given) as total_given'])
            ->from('loan_refund')
            ->where('loan='.$this->id)
            ->all();
        $totalGiven = (is_null($rows[0])) ? 0 : $rows[0]['total_given'];
        return $this->amount_requested + $this->interest - $totalGiven;
    }
	
	/**
    * get short text of a status for display in frontend views
    * @return string
    */
    public function getStatusForFrontend(){
        $resultArray = array(
            Loan::LOAN_REQUESTED => Yii::t('app', 'ASKED'),
            Loan::LOAN_GIVEN => Yii::t('app', 'RECEIVED'),
            Loan::LOAN_RETURNED => Yii::t('app', 'REFUNDED'),
        );
        if(Yii::$app->user->getId() == $this->taker){
            return Html::a($resultArray[$this->status], Yii::$app->UrlManager->createAbsoluteUrl(['member/view-a-loan', 'loanId' => $this->id]));
        }
        return $resultArray[$this->status];
    }

    /**
    * Get amount already refunded for a loan
    * @return integer
    */
    public function getAmountAlreadyRefund(){
         $rows = (new \yii\db\Query())
            ->select(['sum(amount_given) as total_given'])
            ->from('loan_refund')
            ->where('loan='.$this->id)
            ->all();
        $totalGiven = (is_null($rows[0])) ? 0 : $rows[0]['total_given'];
        return $totalGiven;
    }


    /**
    * Get total amount available for loan from all the accounts
    */
    public static function getTotalAmountAvailableForLoan(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $modelBankAccounts = BankAccount::find()->where(['association' => $modelCurrentUser->association, 'loan_allowed' => 1])->all();
        $totalAmountAvailableForLoan = 0;
        foreach ($modelBankAccounts as $modelBankAccount) {
            $totalAmountAvailableForLoan += $modelBankAccount->getTotalAmountAvailableForLoan();
        }
        return $totalAmountAvailableForLoan;
    }


    /**
    * Send notification to admin when a user has requested a loan. Admin should be notified so that he can take action
    */
    public function notifyAdminThatLoanIsRequested(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'admin_notification_on_loan_request'])->one();
        $modelUser = User::findOne(['id' => $this->taker]);
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUserAssociation->admin_phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content_en;
        if(strstr($modelUser->language, "fr")){
                $modelSmsNotification->content = $modelNotification->sms_content_fr;
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelSmsNotification->content = $modelNotification->sms_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelSmsNotification->content);            
        $modelSmsNotification->content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelSmsNotification->content);
        $modelSmsNotification->status = 0;
        $modelSmsNotification->save();

        //Email Notification
        $modelEmailNotification = new EmailNotification();
        $modelEmailNotification->send_to = $modelUserAssociation->admin_phone_number;
        //$modelEmailNotification->html_content = $modelNotification->email_content;
        if(strstr($modelUser->language, "fr")){
                $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelEmailNotification->html_content = $modelNotification->email_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelEmailNotification->subject = 'eDjangui - Loan Request';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT_TAKEN]", Yii::$app->formatter->asDecimal($this->amount_received), $modelEmailNotification->html_content);             
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelEmailNotification->html_content);
         $modelEmailNotification->html_content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }

    /**
    * Send notification to loan requestor once he has raised the request.
    */
    public function notifyTakerThatLoanIsRequested(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_loan_request'])->one();
        $modelUser = User::findOne(['id' => $this->taker]);
        $modelLoanEndorse = LoanEndorse::find()->where(['loan' => $this->id])->one();
        $endorserName = (User::findOne($modelLoanEndorse->endorser))->name;
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUserAssociation->admin_phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content_en;
        if(strstr($modelUser->language, "fr")){
                $modelSmsNotification->content = $modelNotification->sms_content_fr;
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelSmsNotification->content = $modelNotification->sms_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelSmsNotification->content);            
        $modelSmsNotification->content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelSmsNotification->content);
        $modelSmsNotification->status = 0;
        $modelSmsNotification->save();

        //Email Notification
        $modelEmailNotification = new EmailNotification();
        $modelEmailNotification->send_to = $modelUser->email_address;
        //$modelEmailNotification->html_content = $modelNotification->email_content;
        if(strstr($modelUser->language, "fr")){
                $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelEmailNotification->html_content = $modelNotification->email_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelEmailNotification->subject = 'eDjangui - Loan Request';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT_TAKEN]", Yii::$app->formatter->asDecimal($this->amount_received), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_INTERESTS]", Yii::$app->formatter->asDecimal($this->interest), $modelEmailNotification->html_content);             
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->taken_date, "php: M-d-Y"), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_REFUND_DATE]", Yii::$app->formatter->asDate($this->return_date, "php: M-d-Y"), $modelEmailNotification->html_content);
         $modelEmailNotification->html_content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[LOAN_ENDORSER]", $endorserName, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }

    /**
    * Send notification to admin when a user has requested a loan. Admin should be notified so that he can take action
    */
    public function notifyAdminThatLoanIsEdited(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'admin_notification_on_loan_request'])->one();
        $modelUser = User::findOne(['id' => $this->taker]);
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = Yii::$app->params['adminPhone'];
        $modelSmsNotification->content = $modelNotification->sms_content;
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelSmsNotification->content);            
        $modelSmsNotification->content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelSmsNotification->content);
        $modelSmsNotification->status = 0;
        $modelSmsNotification->save();

        //Email Notification
        $modelEmailNotification = new EmailNotification();
        $modelEmailNotification->send_to = $modelUserAssociation->admin_email_address;
        $modelEmailNotification->html_content = $modelNotification->email_content;
        $modelEmailNotification->subject = 'eDjangui - Loan Edited';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT_TAKEN]", Yii::$app->formatter->asDecimal($this->amount_received), $modelEmailNotification->html_content);             
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelEmailNotification->html_content);
         $modelEmailNotification->html_content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }


     /**
    * Get all the refunds which have been made for the loan
    */
    public function getLoanRefundsData(){
        $results = array();
        $modelLoanRefunds = LoanRefund::find()->where(['loan' => $this->id])->all();
        $i = 0;
        foreach ($modelLoanRefunds as $modelLoanRefund) {
            $results[$i++] = array(
                'date' => $modelLoanRefund->refund_date,
                'remain_before' => $modelLoanRefund->remain_before,
                'amount_given' => $modelLoanRefund->amount_given,
                'remain_after' => $modelLoanRefund->remain_after,
            );
        }
        return $results;
    }


    /**
    * Check if the loan endorse request status is rejected
    * @return boolean
    */
    public function isEndorseRequestRejected(){
        $modelLoanEndorse = LoanEndorse::find()
            ->where(['loan' => $this->id])
            ->orderBy(['id' => SORT_DESC])
            ->one(); 
        if(is_null($modelLoanEndorse)) return false;
        return ($modelLoanEndorse->status == LoanEndorse::LOAN_ENDORSE_REJECTED);
    }

        /**
    * When a loan a created, we need to set the interest amount to be paid and how it will be shared amoung the members
    * @return void
    */
    public function initializeInterestsAndShare(){
        if($this->validate()){
            //initialize the interests share for each memebers
            $rows = (new \yii\db\Query())
                ->select(['sum(balance) as total_balance'])
                ->from('account')
                ->where(['bank_account' => $this->bank_account])
                ->all();
            $totalBalance = (is_null($rows[0])) ? 0 : $rows[0]['total_balance'];
            $modelAccounts = Account::find()->where('balance > 0 and bank_account = '.$this->bank_account)->all();
            $modelCurrentUser = User::findOne($this->taker);

            foreach ($modelAccounts as $modelAccount) {
                $modelLoanInterestShare = new LoanInterestShare();
                $modelLoanInterestShare->loan = $this->id;
                $modelLoanInterestShare->beneficiary = $modelAccount->owner;
                $modelLoanInterestShare->balance_at_loan = $modelAccount->balance;
                $modelLoanInterestShare->total_balance_at_loan = $totalBalance;
                $modelLoanInterestShare->association = $modelCurrentUser->association;
                $modelLoanInterestShare->save();
            }
            return true;
        } return false;
    }



    /**
    * Send notification to loan requestor once he has raised the request.
    */
    public function notifyTakerThatLoanIsProcessed(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_loan_process'])->one();
        $modelUser = User::findOne(['id' => $this->taker]);
        $modelLoanEndorse = LoanEndorse::find()->where(['loan' => $this->id])->one();
        $endorserName = (User::findOne($modelLoanEndorse->endorser))->name;
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUserAssociation->admin_phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content_en;
        if(strstr($modelUser->language, "fr")){
                $modelSmsNotification->content = $modelNotification->sms_content_fr;
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelSmsNotification->content = $modelNotification->sms_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelSmsNotification->content);            
        $modelSmsNotification->content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->created_on, "php: M-d-Y"), $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelSmsNotification->content);
        $modelSmsNotification->status = 0;
        $modelSmsNotification->save();

        //Email Notification
        $modelEmailNotification = new EmailNotification();
        $modelEmailNotification->send_to = $modelUser->email_address;
        //$modelEmailNotification->html_content = $modelNotification->email_content;
        if(strstr($modelUser->language, "fr")){
                $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelEmailNotification->html_content = $modelNotification->email_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelEmailNotification->subject = 'eDjangui - Loan Request';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_requested), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT_TAKEN]", Yii::$app->formatter->asDecimal($this->amount_received), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_INTERESTS]", Yii::$app->formatter->asDecimal($this->interest), $modelEmailNotification->html_content);             
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($this->taken_date, "php: M-d-Y"), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_REFUND_DATE]", Yii::$app->formatter->asDate($this->return_date, "php: M-d-Y"), $modelEmailNotification->html_content);
         $modelEmailNotification->html_content = str_replace("[PAYMENT_METHOD]", Loan::getAllPaymentMethods()[$this->payment_method], $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[PAYMENT_NUMBER]", $this->phone_number, $modelEmailNotification->html_content);
          $modelEmailNotification->html_content = str_replace("[LOAN_ENDORSER]", $endorserName, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }


    /**
    * This method allow a user who has created a loan to delete it. This will be possible only if the loan is still in the state ASKED.
    * @return boolean
    */
    public function deleteTheLoan(){
        //Delete all the associated endorse requests and comments
        $modelLoanEndorseComments = LoanEndorseComment::find()
            ->where('loan_endorse in (select id from loan_endorse where loan = '.$this->id.')')
            ->all();
        foreach ($modelLoanEndorseComments as $modelLoanEndorseComment) {
            $modelLoanEndorseComment->delete();
        }

        $modelLoanEndorses = LoanEndorse::find()->where(['loan' => $this->id])->all();
        foreach ($modelLoanEndorses as $modelLoanEndorse) {
            $modelLoanEndorse->delete();
        }

        //Delete all the associated interest share
        $modelLoanInterestShares = LoanInterestShare::find()->where(['loan' => $this->id])->all();
        foreach ($modelLoanInterestShares as $modelLoanInterestShare) {
            $modelLoanInterestShare->delete();
        }

        //Delete all the associated refunds
        $modelLoanRefunds = LoanRefund::find()->where(['loan' => $this->id])->all();
        foreach ($modelLoanRefunds as $modelLoanRefund) {
            $modelLoanRefund->delete();
        }

        //Delete the loan itself
        return $this->delete();
    }
}