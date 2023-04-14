<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_refund".
 *
 * @property int $id
 * @property int|null $loan
 * @property string|null $refund_date
 * @property float|null $amount_given
 * @property float|null $remain_before
 * @property float|null $remain_after
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property Loan $loan0
 * @property User $updatedBy
 */
class LoanRefund extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan', 'association', 'created_by', 'updated_by'], 'integer'],
            [['refund_date', 'created_on', 'updated_on'], 'safe'],
            [['amount_given', 'remain_before', 'remain_after'], 'number'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['loan'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::class, 'targetAttribute' => ['loan' => 'id']],
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
            'loan' => Yii::t('app', 'Loan'),
            'refund_date' => Yii::t('app', 'Refund Date'),
            'amount_given' => Yii::t('app', 'Amount Given'),
            'remain_before' => Yii::t('app', 'Remain Before'),
            'remain_after' => Yii::t('app', 'Remain After'),
            'association' => Yii::t('app', 'Association'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * Gets query for [[Association0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociation0()
    {
        return $this->hasOne(Association::class, ['id' => 'association']);
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
     * Gets query for [[Loan0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoan0()
    {
        return $this->hasOne(Loan::class, ['id' => 'loan']);
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
    * After a refund is saved, check if the the loan is completely refund, close it can compute the shares of everyone
    */
    public function computeInterestsSharingAndNotifyMembers(){
        if($this->remain_after == 0){
            //close the loan
            $modelLoan = Loan::findOne($this->loan);
            $modelLoan->status = Loan::LOAN_RETURNED;
            $modelLoan->save();
            $modelCurrentUser = User::findOne(['id' => $modelLoan->taker]);
            $modelUserAssociation = Association::findOne($modelCurrentUser->association);

            //Compute the interests of every one
            $modelInterestShares = LoanInterestShare::find()->where(['loan' => $this->loan])->all();
            foreach ($modelInterestShares as $modelInterestShare) {
                $modelAccount = Account::find()->where('owner='.$modelInterestShare->beneficiary.' and bank_account = '.$modelLoan->bank_account)->one();
                
                $modelInterestShare->own_share = $modelLoan->interest * $modelInterestShare->balance_at_loan / $modelInterestShare->total_balance_at_loan;
                $modelInterestShare->balance_before = $modelAccount->balance;

                //Add share in account
                $modelAccount->balance = $modelAccount->balance + $modelInterestShare->own_share;
                $modelAccount->save();

                $modelInterestShare->balance_after = $modelAccount->balance;
                $modelInterestShare->save();

                //Send the notifications
                $modelNotification = Notification::find()->where(['name' => 'interest_own_share_received'])->one();
                $modelUser = User::findOne(['id' => $modelInterestShare->beneficiary]);



                //SMS Notification
                $modelSmsNotification = new SmsNotification();
                $modelSmsNotification->phone_number = $modelUser->phone_number;
                //$modelSmsNotification->content = $modelNotification->sms_content;
                if(strstr($modelUser->language, "fr")){
                    $modelSmsNotification->content = $modelNotification->sms_content_fr;
                    Yii::$app->formatter->locale = 'fr-FR';
                }else{
                    $modelSmsNotification->content = $modelNotification->sms_content_en;
                    Yii::$app->formatter->locale = 'en-US';
                }
                $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelLoan->amount_requested), $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[LOAN_INTERESTS]", Yii::$app->formatter->asDecimal($modelLoan->interest), $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[INTERESTS_OWN_SHARE]", Yii::$app->formatter->asDecimal($modelInterestShare->own_share), $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[MEMBER_BANK_SOLDE]", Yii::$app->formatter->asDecimal($modelAccount->balance), $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
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
                $modelEmailNotification->subject = 'eDjangui - Interest Share Alert';
                $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelLoan->amount_requested, 2), $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[LOAN_INTERESTS]", Yii::$app->formatter->asDecimal($modelLoan->interest, 2), $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[INTERESTS_OWN_SHARE]", Yii::$app->formatter->asDecimal($modelInterestShare->own_share, 2), $modelEmailNotification->html_content); 
                $modelEmailNotification->html_content = str_replace("[MEMBER_BANK_SOLDE]", Yii::$app->formatter->asDecimal($modelAccount->balance, 2), $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[TOTAL_BANK_AT_LOAN]", Yii::$app->formatter->asDecimal($modelInterestShare->total_balance_at_loan, 2), $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[MEMBER_BANK_AT_LOAN]", Yii::$app->formatter->asDecimal($modelInterestShare->balance_at_loan, 2), $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
                $modelEmailNotification->sending_status = 0;
                $modelEmailNotification->save();

                //echo Date("Y-m-d H:i:s")." Interest Share Notification - message prepared for ".$modelUser->name." - SUCCESS \n";
            }
        }
    }


    /**
    * Send notification to loan taker once the refund is saved.
    */
    public function notifyTakerThatRefundIsSaved(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'taker_notification_on_loan_refund'])->one();
        $modelLoan = Loan::findOne($this->loan);
        $modelUser = User::findOne(['id' => $modelLoan->taker]);
        $modelLoanEndorse = LoanEndorse::find()->where(['loan' => $modelLoan->id])->one();
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
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelLoan->amount_requested), $modelSmsNotification->content);            
        $modelSmsNotification->content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($modelLoan->created_on, "php: M-d-Y"), $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[REFUND_AMOUNT]", $this->amount_given, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[REMAIN_AMOUNT_AFTER]", $this->remain_after, $modelSmsNotification->content);
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
        $modelEmailNotification->subject = 'eDjangui - Loan Refund';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal($modelLoan->amount_requested), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT_TAKEN]", Yii::$app->formatter->asDecimal($modelLoan->amount_received), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_INTERESTS]", Yii::$app->formatter->asDecimal($modelLoan->interest), $modelEmailNotification->html_content);             
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDate($modelLoan->taken_date, "php: M-d-Y"), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_REFUND_DATE]", Yii::$app->formatter->asDate($modelLoan->return_date, "php: M-d-Y"), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[REMAIN_AMOUNT_BEFORE]", Yii::$app->formatter->asDecimal($this->remain_before), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[REFUND_AMOUNT]", Yii::$app->formatter->asDecimal($this->amount_given), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[REMAIN_AMOUNT_AFTER]", Yii::$app->formatter->asDecimal($this->remain_after), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }
}
