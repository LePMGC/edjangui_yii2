<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "association".
 *
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $city
 * @property string $admin_phone_number
 * @property string $admin_email_address
 * @property int $status
 * @property int $created_by
 * @property string $created_on
 * @property int $updated_by
 * @property string $updated_on
 *
 * @property BankAccount[] $bankAccounts
 * @property User $createdBy
 * @property LoanInterestShare[] $loanInterestShares
 * @property LoanOptionTerm[] $loanOptionTerms
 * @property LoanOption[] $loanOptions
 * @property Loan[] $loans
 * @property User $updatedBy
 */
class Association extends BaseModel
{
    //Loan Status
    const ASSOCIATION_CREATED = 0;
    const ASSOCIATION_ACTIVATED = 10;
    const ASSOCIATION_TEMPORARY_BLOCKED = 20;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'association';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'country', 'city', 'admin_phone_number', 'admin_email_address'], 'required'],
            [['created_by', 'updated_by', 'status'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'admin_phone_number', 'admin_email_address'], 'string', 'max' => 256],
            [['country', 'city'], 'string', 'max' => 45],
            [['admin_phone_number'], 'unique'],
            [['admin_email_address'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'name' => Yii::t('app', 'Name'),
            'country' => Yii::t('app', 'Country'),
            'city' => Yii::t('app', 'City'),
            'admin_phone_number' => Yii::t('app', 'Admin Phone Number'),
            'admin_email_address' => Yii::t('app', 'Admin Email Address'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * Gets query for [[BankAccounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccounts()
    {
        return $this->hasMany(BankAccount::class, ['association' => 'id']);
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
     * Gets query for [[LoanInterestShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanInterestShares()
    {
        return $this->hasMany(LoanInterestShare::class, ['association' => 'id']);
    }

    /**
     * Gets query for [[LoanOptionTerms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptionTerms()
    {
        return $this->hasMany(LoanOptionTerm::class, ['association' => 'id']);
    }

    /**
     * Gets query for [[LoanOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptions()
    {
        return $this->hasMany(LoanOption::class, ['association' => 'id']);
    }

    /**
     * Gets query for [[Loans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loan::class, ['association' => 'id']);
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
    * Get all possible status of an association
    * @return array
    */
    public static function getAllStatuses(){
        return [
            0 => Yii::t('app', 'CREATED'),
            10 => Yii::t('app', 'ACTIVATED'),
            20 => Yii::t('app', 'TEMPORARY_BLOCKED'),
        ];
    }

     /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {

            $this->status = $this->className()::ASSOCIATION_CREATED;
        }

        return parent::beforeSave($insert);
    }


    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->isNewRecord) {
            
        }

        return parent::afterSave($insert, $changedAttributes);
    } 


    /**
    /* Create admin user and send notification
    */
    public function createAdminUserAndNotify(){
        //Create Admin user account
            $modelLastUser = User::find()->where('id>0')->orderBy("id desc")->limit(1)->one();
                $adminUserModel = new User();
                $adminUserModel->name = "admin".$modelLastUser->id;
                $adminUserModel->username = $adminUserModel->name;
                $adminUserModel->setPassword('admin123');
                $adminUserModel->email_address = $this->admin_email_address;
                $adminUserModel->phone_number = $this->admin_phone_number;
                $adminUserModel->language = "en_us";
                $adminUserModel->status = User::STATUS_DELETED;
                $adminUserModel->association = $this->id;
                $adminUserModel->generateAuthKey();
                $adminUserModel->created_at = strtotime(Date('Y-m-d H:i:s'));
                $adminUserModel->updated_at = strtotime(Date('Y-m-d H:i:s'));

                if ($adminUserModel->save()){
                    $this->admin_user_id = $adminUserModel->id;
                    $this->save();
                }
                else
                    print_r($adminUserModel->getErrors());

            if($adminUserModel->save()){
                //send notification to admin of the association
                $modelNotification = Notification::find()->where(['name' => 'association_admin_notification_on_association_creation'])->one();
                $modelUser = $adminUserModel;

                //SMS Notification
                $modelSmsNotification = new SmsNotification();
                $modelSmsNotification->phone_number = $modelUser->phone_number;

                if(strstr($modelUser->language, "fr")){
                        $modelSmsNotification->content = $modelNotification->sms_content_fr;
                        Yii::$app->formatter->locale = 'fr-FR';
                    }else{
                        $modelSmsNotification->content = $modelNotification->sms_content_en;
                        Yii::$app->formatter->locale = 'en-US';
                    }
                $modelSmsNotification->content = str_replace("[ASSOCIATION_ADMIN_NAME]", $this->name, $modelSmsNotification->content);
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
                $modelEmailNotification->subject = 'eDjangui - Association Creation';
                $modelEmailNotification->html_content = str_replace("[ASSOCIATION_ADMIN_NAME]", $this->name, $modelEmailNotification->html_content); 
                $modelEmailNotification->html_content = str_replace("[ASSOCIATION_NAME]", $this->name, $modelEmailNotification->html_content); 
                

                $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", Yii::$app->params['adminPhone'], $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", Yii::$app->params['adminEmail'], $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
                $modelEmailNotification->sending_status = 0;
                $modelEmailNotification->save();
            }
    }


    /**
    /* Notifiy Admin User that the association have been activated
    */
    public function sendCredentialsToAdminUser(){
        //Create Admin user account
           $adminUserModel = User::findOne($this->admin_user_id);

                //send notification to admin of the association
                $modelNotification = Notification::find()->where(['name' => 'association_admin_notification_on_association_activation'])->one();
                $modelUser = $adminUserModel;

                //SMS Notification
                $modelSmsNotification = new SmsNotification();
                $modelSmsNotification->phone_number = $modelUser->phone_number;

                if(strstr($modelUser->language, "fr")){
                        $modelSmsNotification->content = $modelNotification->sms_content_fr;
                        Yii::$app->formatter->locale = 'fr-FR';
                    }else{
                        $modelSmsNotification->content = $modelNotification->sms_content_en;
                        Yii::$app->formatter->locale = 'en-US';
                    }
                $modelSmsNotification->content = str_replace("[ASSOCIATION_ADMIN_NAME]", $this->name, $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[ASSOCIATION_NAME]", $this->name, $modelSmsNotification->content);
                $modelSmsNotification->content = str_replace("[ASSOCIATION_ADMIN_USERNAME]", $adminUserModel->username, $modelSmsNotification->content);
                 $modelSmsNotification->content = str_replace("[ASSOCIATION_ADMIN_PASSWORD]", "admin123", $modelSmsNotification->content);
                 $modelSmsNotification->content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelSmsNotification->content);

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
                $modelEmailNotification->subject = 'eDjangui - Association Creation';
                $modelEmailNotification->html_content = str_replace("[ASSOCIATION_ADMIN_NAME]", $this->name, $modelEmailNotification->html_content); 
                $modelEmailNotification->html_content = str_replace("[ASSOCIATION_NAME]", $this->name, $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[ASSOCIATION_ADMIN_USERNAME]", $adminUserModel->username, $modelEmailNotification->html_content);
                 $modelEmailNotification->html_content = str_replace("[ASSOCIATION_ADMIN_PASSWORD]", "admin123", $modelEmailNotification->html_content);
                

                $modelEmailNotification->html_content = str_replace("[ADMIN_PHONE_NUMBER]", Yii::$app->params['adminPhone'], $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[ADMIN_EMAIL_ADDRESS]", Yii::$app->params['adminEmail'], $modelEmailNotification->html_content);
                $modelEmailNotification->html_content = str_replace("[PLATFORM_URL]", Yii::$app->params['platform_url'], $modelEmailNotification->html_content);
                $modelEmailNotification->sending_status = 0;
                $modelEmailNotification->save();
    }
}
