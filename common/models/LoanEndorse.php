<?php

namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * This is the model class for table "loan_endorse".
 *
 * @property int $id
 * @property int|null $loan
 * @property int|null $endorser
 * @property float|null $endorse_amount
 * @property float|null $bank_at_endorse
 * @property float|null $djangui_at_endorse
 * @property int|null $status
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property User $endorser0
 * @property Loan $loan0
 * @property User $updatedBy
 */
class LoanEndorse extends BaseModel
{
    //Loan Endorse Status
    const LOAN_ENDORSE_REQUESTED = 0;
    const LOAN_ENDORSE_APPROVED = 10;
    const LOAN_ENDORSE_REJECTED = 20;

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_endorse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan', 'endorser', 'status', 'association', 'created_by', 'updated_by'], 'integer'],
            [['endorse_amount', 'bank_at_endorse', 'djangui_at_endorse'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['endorser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['endorser' => 'id']],
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
            'endorser' => Yii::t('app', 'Endorser'),
            'endorse_amount' => Yii::t('app', 'Endorse Amount'),
            'bank_at_endorse' => Yii::t('app', 'Bank At Endorse'),
            'djangui_at_endorse' => Yii::t('app', 'Djangui At Endorse'),
            'status' => Yii::t('app', 'Status'),
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
     * Gets query for [[Endorser0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEndorser0()
    {
        return $this->hasOne(User::class, ['id' => 'endorser']);
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
    * Return the number of loan endorse requested send to given user and not yet approved/rejected
    */
    public static function getNumberOfNewEndorseRequests($memberId){
        $rows = (new \yii\db\Query())
            ->select(['count(id) as total_new_loan_endorse_requests'])
            ->from('loan_endorse')
            ->where('endorser = '.$memberId.' and status = '.LoanEndorse::LOAN_ENDORSE_REQUESTED)
            ->all();

        $totalRequets = (is_null($rows[0])) ? 0 : $rows[0]['total_new_loan_endorse_requests'];
        return $totalRequets;
    }

    /**
    * Get all possible status of a user
    * @return array
    */
    public static function getAllStatuses(){
        return [
            0 => Yii::t('app', 'REQUESTED'),
            10 => Yii::t('app', 'APPROVED'),
            20 => Yii::t('app', 'REJECTED'),
        ];
    }

     /**
    * Get the list of the users who can endorse a loan at the time the function is called.
    */
    public static function getAllPossibleLoanEndorsers($bankAccountId=0){
        $currentUser = User::findOne(Yii::$app->user->getId());
        if($bankAccountId==0)
            $modelBankAccount = BankAccount::find()->where(['association' => $currentUser->association, 'loan_allowed' => 1])->orderBy('id ASC')->one();
        else
            $modelBankAccount = BankAccount::findOne($bankAccountId);

        $minBalanceForLoan = is_null($modelBankAccount) ? 0 : $modelBankAccount->min_balance_for_loan;
        $loan_endorsers = (new \yii\db\Query())
           ->select(['id', 'name'])
           ->from('user')
           ->where('id>1')
           ->andWhere('id <> '.Yii::$app->user->getId())
           //->andWhere('id in (select owner from account where balance > '.$minBalanceForLoan.')')
           ->andWhere('association = '.$currentUser->association)
           //->andWhere('id <> 2')
           //->andWhere('id not in (select endorser from loan_endorse inner join loan on loan.id = loan_endorse.loan where loan.status <>20)')
           ->andWhere('id not in (select admin_user_id from association)')
           ->all();
        $results = array();
        foreach ($loan_endorsers as $loan_endorse) {
            $results[$loan_endorse['id']] = $loan_endorse['name'];
        }
        if(Yii::$app->user->getId() == 13)
            $results[2] = 'Ghislain PENKA MAGOUA';
        return $results;
    }

        /**
    * Send notification to the user to who the endorse requet is addressed.
    */
    public function sendNotificationToEndorser(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'loan_endorse_request_notification'])->one();
        $modelUser = User::findOne(['id' => $this->endorser]);
        $modelBankAccount = Account::find()->where(['owner' => $modelUser->id])->one();
        $modelLoanTaker = User::findOne(Loan::findOne($this->loan)->taker);

        $endorseApprovalLink = Html::a('Approve <span class="glyphicon glyphicon-ok"></span>', Yii::$app->UrlManager->createAbsoluteUrl(['member/approve-endorse-request', 'endorseId' => $this->id]));

        $endorseRejectionLink = Html::a('Reject <span class="glyphicon glyphicon-remove">', Yii::$app->UrlManager->createAbsoluteUrl(['member/reject-endorse-request', 'endorseId' => $this->id]));
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUser->phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content;
        if(strstr($modelUser->language, "fr")){
                $modelSmsNotification->content = utf8_encode($modelNotification->sms_content_fr);
                Yii::$app->formatter->locale = 'fr-FR';
            }else{
                $modelSmsNotification->content = $modelNotification->sms_content_en;
                Yii::$app->formatter->locale = 'en-US';
            }
        $modelSmsNotification->content = str_replace("[LOAN_TAKER]", $modelLoanTaker->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested), $modelSmsNotification->content);
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
        $modelEmailNotification->subject = 'eDjangui - '.Yii::t('app', 'Loan Endorse Request');
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKER]", $modelLoanTaker->name, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested, 2), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKEN_DATE]", Yii::$app->formatter->asDateTime(Loan::findOne($this->loan)->created_on, "php: Y-M-d H:i:s"), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ENDORSE_REQUEST_APPROVAL_LINK]", $endorseApprovalLink, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ENDORSE_REQUEST_REJECTION_LINK]", $endorseRejectionLink, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }

    /**
    * Send notification to the loan taker when endorse request is approved.
    */
    public function sendNotificationToLoanTakerOnApprovalOfEndorseRequest(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'loan_endorse_request_approved_notification'])->one();
        $modelUser = User::findOne(['id' => $this->endorser]);
        $modelLoan = Loan::findOne(['id' => $this->loan]);
        $modelLoanTaker = User::findOne(['id' => $modelLoan->taker]);
        $modelUserAssociation = Association::findOne($modelUser->association);
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUserAssociation->admin_phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content;
        if(strstr($modelUser->language, "fr")){
            $modelSmsNotification->content = $modelNotification->sms_content_fr;
            Yii::$app->formatter->locale = 'fr-FR';
        }else{
            $modelSmsNotification->content = $modelNotification->sms_content_en;
            Yii::$app->formatter->locale = 'en-US';
        }
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested), $modelSmsNotification->content);
        $modelSmsNotification->status = 0;
        $modelSmsNotification->save();

        //Email Notification
        $modelEmailNotification = new EmailNotification();
        //$modelEmailNotification->send_to = $modelUser->email_address;
        if(strstr($modelUser->language, "fr")){
            $modelEmailNotification->html_content = $modelNotification->email_content_fr;   
            Yii::$app->formatter->locale = 'fr-FR';
        }else{
            $modelEmailNotification->html_content = $modelNotification->email_content_en;
            Yii::$app->formatter->locale = 'en-US';
        }
        //$modelEmailNotification->html_content = $modelNotification->email_content;
        $modelEmailNotification->subject = 'eDjangui - Loan Endorse Request Status';
        $modelEmailNotification->html_content = str_replace("[LOAN_TAKER]", $modelLoanTaker->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested, 2), $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->send_to = $modelLoanTaker->email_address;
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }


    /**
    * Send notification to the loan taker when endorse request is rejected.
    */
    public function sendNotificationToLoanTakerOnRejectionOfEndorseRequest(){
        //build the messages to be sent
        $modelNotification = Notification::find()->where(['name' => 'loan_endorse_request_rejected_notification'])->one();
        $modelUser = User::findOne(['id' => $this->endorser]);
        $modelLoan = Loan::findOne(['id' => $this->loan]);
        $modelLoanTaker = User::findOne(['id' => $modelLoan->taker]);
        $modelUserAssociation = Association::findOne($modelUser->association);

        //Get comment
        $modelComment = LoanEndorseComment::find()->where(['loan_endorse' => $this->id])->orderBy(['id'=> SORT_DESC])->one();
        $rejectionComment = is_null($modelComment) ? '' : $modelComment->comment;
                    
        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $modelUserAssociation->admin_phone_number;
        //$modelSmsNotification->content = $modelNotification->sms_content;
        if(strstr($modelUser->language, "fr")){
            $modelSmsNotification->content = $modelNotification->sms_content_fr;
            Yii::$app->formatter->locale = 'fr-FR';
        }else{
            $modelSmsNotification->content = $modelNotification->sms_content_en;
            Yii::$app->formatter->locale = 'en-US';
        }
        $modelSmsNotification->content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelSmsNotification->content);
        $modelSmsNotification->content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested), $modelSmsNotification->content);            
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
        $modelEmailNotification->subject = 'eDjangui - Loan Endorse Request Status';
        $modelEmailNotification->html_content = str_replace("[MEMBER_NAME]", $modelUser->name, $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[LOAN_AMOUNT]", Yii::$app->formatter->asDecimal(Loan::findOne($this->loan)->amount_requested, 2), $modelEmailNotification->html_content); 
        $modelEmailNotification->html_content = str_replace("[REJECTION_COMMENT]", $rejectionComment, $modelEmailNotification->html_content);
         $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", $modelUserAssociation->admin_phone_number, $modelEmailNotification->html_content);
        $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", $modelUserAssociation->admin_email_address, $modelEmailNotification->html_content);
        $modelEmailNotification->send_to = $modelLoanTaker->email_address;
        $modelEmailNotification->sending_status = 0;
        $modelEmailNotification->save();
    }


    /**
    * Return data on all the endorse requests sent to the current user.
    */
    public static function getEndorseRequestsData($memberId){
        $modelLoanEndorsers = LoanEndorse::find()
                            ->where(['endorser' => $memberId])
                            ->orWhere(['in', 'loan', Loan::find()->select(['id'])->from('loan')->where(['taker' => $memberId])])
                            ->orderBy('id DESC')
                            ->all();
        $results = array();
        $i = 0;
        foreach ($modelLoanEndorsers as $modelLoanEndorser) {

            //Build the status
            switch ($modelLoanEndorser->status) {
                case LoanEndorse::LOAN_ENDORSE_REQUESTED:
                    if($modelLoanEndorser->endorser != Loan::findOne($modelLoanEndorser->loan)->taker)
                        $status = '<strong class="text-primary">'.Yii::t('app', 'REQUESTED').'</strong>'.
                            '&nbsp'.
                            (($modelLoanEndorser->endorser == Yii::$app->user->getId()) ? Html::a('<span class="text-success glyphicon glyphicon-ok"></span>', Yii::$app->UrlManager->createAbsoluteUrl(['member/approve-endorse-request', 'endorseId' => $modelLoanEndorser->id])) : '')
                            .'&nbsp'.
                            (($modelLoanEndorser->endorser == Yii::$app->user->getId()) ? Html::a('<span class="text-danger glyphicon glyphicon-remove"></span>', Yii::$app->UrlManager->createAbsoluteUrl(['member/reject-endorse-request', 'endorseId' => $modelLoanEndorser->id])) : '');
                    else
                        $status = '<strong class="text-primary">'.Yii::t('app', 'REQUESTED').'</strong>';
                    break;

                case LoanEndorse::LOAN_ENDORSE_APPROVED:
                    $status = '<strong class="text-success">'.Yii::t('app', 'APPROVED').'</strong>';
                    break;

                case LoanEndorse::LOAN_ENDORSE_REJECTED:
                    $status = '<strong class="text-danger">'.Yii::t('app', 'REJECTED').'</strong>';
                    break;
                
                default:
                    # code...
                    break;
            }

            //build the comments
            $comments = '';
            $modelComments = LoanEndorseComment::find()->where(['loan_endorse' => $modelLoanEndorser->id])->all();
            foreach ($modelComments as $modelComment) {
                $comments = $comments.'<strong>'.Yii::t('app', 'Comment By').' : '.User::findOne($modelComment->author)->getName().'</strong> <br/>'.
                            '<p>'.$modelComment->comment.'</p> <br/>';
            }

            //build the result item
            $results[$i++] = array(
                'id' => $modelLoanEndorser->id,
                'loan_requestor' => User::findOne(Loan::findOne($modelLoanEndorser->loan)->taker)->getFirstName(),
                'loan_endorser' => User::findOne($modelLoanEndorser->endorser)->getFirstName(),
                'loan_amount' => Loan::findOne($modelLoanEndorser->loan)->amount_requested,
                'endorse_amount' => $modelLoanEndorser->endorse_amount,
                'requested_date' => $modelLoanEndorser->created_on,
                'status' => $status,
                'comments' => $comments,
                'show_approve_reject_buttons' => ($modelLoanEndorser->endorser == Yii::$app->user->getId()) && ($modelLoanEndorser->status == LoanEndorse::LOAN_ENDORSE_REQUESTED)
            );
        }

        return $results;
    }

}
