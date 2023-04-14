<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\db\Expression;


/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $name
 * @property string $auth_key
 * @property string|null $password_hash
 * @property string|null $password_reset_token
 * @property string $email_address
 * @property int $status
 * @property int|null $role
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $phone_number
 * @property string|null $verification_code
 * @property string|null $language
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association[] $associations
 * @property Association[] $associations0
 * @property BankAccount[] $bankAccounts
 * @property BankAccount[] $bankAccounts0
 * @property LoanInterestShare[] $loanInterestShares
 * @property LoanInterestShare[] $loanInterestShares0
 * @property LoanInterestShare[] $loanInterestShares1
 * @property LoanOptionTerm[] $loanOptionTerms
 * @property LoanOptionTerm[] $loanOptionTerms0
 * @property LoanOption[] $loanOptions
 * @property LoanOption[] $loanOptions0
 * @property Loan[] $loans
 * @property Loan[] $loans0
 * @property Loan[] $loans1
 * @property Loan[] $loans2
 */
class User extends BaseModel implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_PHONE_NUMBER_NOT_VERIFIED = 20;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_key', 'email_address', 'status', 'created_at', 'updated_at'], 'required'],
            [['status', 'role', 'created_at', 'updated_at', 'created_by', 'updated_by', 'association'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['username', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 45],
            [['auth_key'], 'string', 'max' => 32],
            [['email_address'], 'string', 'max' => 100],
            [['phone_number'], 'string', 'max' => 9],
            [['verification_code'], 'string', 'max' => 4],
            [['language'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'name' => Yii::t('app', 'Name'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email_address' => Yii::t('app', 'Email Address'),
            'status' => Yii::t('app', 'Status'),
            'role' => Yii::t('app', 'Role'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'verification_code' => Yii::t('app', 'Verification Code'),
            'language' => Yii::t('app', 'Language'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }

    /**
     * Gets query for [[Associations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociations()
    {
        return $this->hasMany(Association::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[Associations0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssociations0()
    {
        return $this->hasMany(Association::class, ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[BankAccounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccounts()
    {
        return $this->hasMany(BankAccount::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[BankAccounts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccounts0()
    {
        return $this->hasMany(BankAccount::class, ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[LoanInterestShares]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanInterestShares()
    {
        return $this->hasMany(LoanInterestShare::class, ['beneficiary' => 'id']);
    }

    /**
     * Gets query for [[LoanInterestShares0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanInterestShares0()
    {
        return $this->hasMany(LoanInterestShare::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[LoanInterestShares1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanInterestShares1()
    {
        return $this->hasMany(LoanInterestShare::class, ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[LoanOptionTerms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptionTerms()
    {
        return $this->hasMany(LoanOptionTerm::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[LoanOptionTerms0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptionTerms0()
    {
        return $this->hasMany(LoanOptionTerm::class, ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[LoanOptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptions()
    {
        return $this->hasMany(LoanOption::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[LoanOptions0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOptions0()
    {
        return $this->hasMany(LoanOption::class, ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[Loans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loan::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[Loans0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoans0()
    {
        return $this->hasMany(Loan::class, ['loan_endorser' => 'id']);
    }

    /**
     * Gets query for [[Loans1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoans1()
    {
        return $this->hasMany(Loan::class, ['member' => 'id']);
    }

    /**
     * Gets query for [[Loans2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoans2()
    {
        return $this->hasMany(Loan::class, ['updated_by' => 'id']);
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by phone_number
     *
     * @param string $phone_number
     * @return static|null
     */
    public static function findByPhoneNumber($phone_number)
    {
        return static::findOne(['phone_number' => $phone_number, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email_address
     *
     * @param string $email_address
     * @return static|null
     */
    public static function findByEmailAddress($email_address)
    {
        return static::findOne(['email_address' => $email_address, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
    * Generate a verification code (to be sent to user via SMS) and assign it to current user model
    *
    */
    public function generateVerificationCode(){
        $this->verification_code = BaseModel::generatePIN();
    }


    /**
    * Get list of all Users
    * @return array(id => name_en)
    */
    public static function getAllUsers(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $users = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('user')
                    ->where('association='.$modelCurrentUser->association)
                    ->andWhere('id not in (select admin_user_id from association)')
                    ->orderBy('name')
                    ->all();
        $results = array();
        foreach ($users as $user) {
                $results[$user['id']] = $user['name'];
        }
        return $results;
    }

    /**
    * Get all possible status of a user
    * @return array
    */
    public static function getAllStatuses(){
        return [
            0 => 'STATUS_DELETED',
            10 => 'STATUS_ACTIVE',
            20 => 'STATUS_PHONE_NUMBER_NOT_VERIFIED',
        ];
    }

    /**
    * Get Account History Data For display in Frontend. This will return the asked number of lines or all the lines if the numberOfLines is 0
    * @param integer
    * @return array
    */
    public function getAccountHistoryData($numerOfLines=0){
        $bank_account = isset($_SESSION['account_history_bank_account']) ? $_SESSION['account_history_bank_account'] : 0;

        $query1 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("1 as ah_type"), "amount as ah_amount", "balance_after as ah_balance_after"])
            ->from('cash_in')
            ->where(['member' => $this->id, 'bank_account' => $bank_account])
            ->andWhere('amount > 0');

        $query2 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("2 as ah_type"), "own_share as ah_amount", "balance_after as ah_balance_after"])
            ->from('loan_interest_share')
            ->where('beneficiary='.$this->id)
            ->andWhere('not(own_share is null)')
            ->andWhere("loan in (select id from loan where bank_account = ".$bank_account.")");

        $query3 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("3 as ah_type"), "amount as ah_amount", "balance_after as ah_balance_after"])
            ->from('cash_out')
            ->where(['member' => $this->id, 'bank_account' => $bank_account]);

        /*$query4 = (new \yii\db\Query())
            ->select(["created_on as ah_date", new \yii\db\Expression("4 as ah_type"), "charges as ah_amount", "balance_after as ah_balance_after"])
            ->from('delay_charges')
            ->where('user='.$this->id);*/

        /*$query5 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("5 as ah_type"), "transaction_amount as ah_amount", "balance_after as ah_balance_after"])
            ->from('instant_cash_in')
            ->where('account='.$this->id);*/

        /*$query4 = $query1->union($query2, true);
        $unionQuery = $query4->union($query3, true);*/

        $unionQuery = $query1->union($query2, true)->union($query3, true)/*->union($query5, true)*/;

        $rows = (new \yii\db\Query())
            ->select('ah_date, ah_type, ah_amount, ah_balance_after')
            ->from([$unionQuery])
            ->orderBy('ah_date DESC');

        if($numerOfLines!=0)    $rows = $rows->limit($numerOfLines);
        $rows = $rows->all();

        $results = array();
        $i = 0;
        foreach ($rows as $row) {
            switch ($row['ah_type']) {
                case 1: $ah_type = Yii::t('app', 'CASH_IN'); break;
                case 2: $ah_type = Yii::t('app', 'INTEREST'); break;
                case 3: $ah_type = Yii::t('app', 'CASH_OUT'); break;
                case 4: $ah_type = Yii::t('app', 'PENALTIES'); break;
                case 5: $ah_type = Yii::t('app', 'CASH_IN'); break;
                default:
                    # code...
                    break;
            }
            $results[$i++] = array(
                'ah_date' => $row['ah_date'],
                'ah_type' => $ah_type,
                'ah_amount' => $row['ah_amount'],
                'ah_balance_after' => $row['ah_balance_after'],
            );
        }
        //print_r($rows);
        return $results;
    }

    /**
    * Get total amount cashed into bank by a user
    * @return float
    */
    public function getTotalCashIn(){
        $rows = (new \yii\db\Query())
            ->select('sum(amount) as total_bank_cash_in')
            ->from('cash_in')
            ->where('user='.$this->id)
            ->all();
        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_bank_cash_in']) ? 0 : $rows[0]['total_bank_cash_in']);
    }

    /**
    * Get total amount cashed into bank by a user during the on going season
    * @return float
    */
    public function getTotalCashInOfCurrentSeason(){
        $currentDjanguiSeasonId = Parameter::findOne(['name' => 'current_season'])->value;

        $rows = (new \yii\db\Query())
            ->select('sum(amount) as total_bank_cash_in')
            ->from('cash_in')
            ->where('user='.$this->id)
            ->andWhere('djangui_episode in (select id from djangui_episode where djangui_season = '.$currentDjanguiSeasonId.')')
            ->all();
        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_bank_cash_in']) ? 0 : $rows[0]['total_bank_cash_in']);
    }

    /**
    * Get total amount received as interest for a user bank acount
    * @return float
    */
    public function getTotalInterests(){
        $rows = (new \yii\db\Query())
            ->select('sum(own_share) as total_interests')
            ->from('loan_interest_share')
            ->where('beneficiary='.$this->id)
            ->all();
        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_interests']) ? 0 : $rows[0]['total_interests']);
    }

    /**
    * Get total amount received as interest for a user bank acount during the on going season
    * @return float
    */
    public function getTotalInterestOfCurrentSeason(){
        $currentDjanguiSeasonId = Parameter::findOne(['name' => 'current_season'])->value;
        $modelDjanguiSeason = DjanguiSeason::findOne($currentDjanguiSeasonId);

        $rows = (new \yii\db\Query())
            ->select('sum(own_share) as total_interests')
            ->from('loan_interest_share')
            ->where('beneficiary='.$this->id)
            ->andWhere('date(created_on) BETWEEN "'.$modelDjanguiSeason->start_date.'" AND "'.$modelDjanguiSeason->end_date.'"')
            ->all();
        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_interests']) ? 0 : $rows[0]['total_interests']);
    }

    /**
    * Get total amount cashed out from bank by a user
    * @return float
    */
    public function getTotalCashOut(){
        $rows = (new \yii\db\Query())
            ->select('sum(amount) as total_bank_cash_out')
            ->from('cash_out')
            ->where('user='.$this->id)
            ->all();

        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_bank_cash_out']) ? 0 : $rows[0]['total_bank_cash_out']);
    }

    /**
    * Get total amount cashed out from bank by a user during the on going season
    * @return float
    */
    public function getTotalCashOutOfCurrentSeason(){
        $currentDjanguiSeasonId = Parameter::findOne(['name' => 'current_season'])->value;
        $modelDjanguiSeason = DjanguiSeason::findOne($currentDjanguiSeasonId);
        
        $rows = (new \yii\db\Query())
            ->select('sum(amount) as total_bank_cash_out')
            ->from('cash_out')
            ->where('user='.$this->id)
            ->andWhere('date(created_on) BETWEEN "'.$modelDjanguiSeason->start_date.'" AND "'.$modelDjanguiSeason->end_date.'"')
            ->all();

        return is_null($rows[0]) ? 0 : (is_null($rows[0]['total_bank_cash_out']) ? 0 : $rows[0]['total_bank_cash_out']);
    }

    /**
    * Get the time when this user will collect for current djangui
    * @return date
    */
    public function getCurrentDjanguiCollectionEpisode(){
        $currentDjanguiSeasonId = Parameter::findOne(['name' => 'current_season'])->value;
        $modelDjanguiMember = DjanguiMember::findOne(['djangui_season' => $currentDjanguiSeasonId, 'user' => $this->id]);
        if(is_null($modelDjanguiMember))
            return '';
        $modelDjanguiEpisode = DjanguiEpisode::findOne($modelDjanguiMember->collecting_episode);
        return $modelDjanguiEpisode->name;
    }

    /**
    * Get the name of the current user.
    * @return date
    */
    public function getName(){
        $memberId = Yii::$app->user->getId();
        if(!is_null($memberId))
            if($memberId == $this->id) return Yii::t('app', 'You');
        return $this->name;
    }


    /**
    * Get the first name of the current user.
    * @return date
    */
    public function getFirstName(){
        $memberId = Yii::$app->user->getId();
        if(!is_null($memberId))
            if($memberId == $this->id) return Yii::t('app', 'You');
        $t = explode(" ", $this->name);
        return $t[0];
    }


    /**
    * Get the first name of the current user.
    * @return date
    */
    public function getFirstAndSecondNames(){
        $t = explode(" ", $this->name);
        return (count($t) > 1) ? $t[0]." ".$t[1] : $t[0];
    }

    /**
    * Generate Password ans send to the new user
    */
    public function generateAndSendCredentialsToUser(){
        // Generate usermae
        $words = explode(" ", $this->name);
        $username = "";

        foreach ($words as $w) {
          $username .= $w[0];
        }

        $this->username = strtolower($username).$this->id;

        // Generate password
        $currenUserPassword = User::generateStrongPassword();
        $this->setPassword($currenUserPassword);
        $this->generateAuthKey();
        $this->save();

        //Set language and account status
        $this->language = 'en-UK';
        $this->status = User::STATUS_ACTIVE;

        $modelEmailNotification = new EmailNotification();
        $modelEmailNotification->subject = 'e-Djangui Support : Your Account Details';
        $modelEmailNotification->html_content = '<p> Hello '.$this->name.',</p> 
            <p> An user account have been created for you on e-Djangui Online.</p>
            <p> See below the access details : </p>
            <p>     - Url : '.Yii::$app->params['platform_url'].'</p>
            <p>     - Username : '.$this->username.'</p>
            <p>     - Password : '.$currenUserPassword.'</p>
            <br/>
            <p>Best Regards///</p>';

        $modelEmailNotification->text_content = 'Hello '.$this->name.', 
            An user account have been created for you on e-Djangui Online.
            See below the access details :
                    - Url : '.Yii::$app->params['platform_url'].'</p>
                    - Username : '.$this->username.'
                    - Password : '.$currenUserPassword.'
            Best Regards///';

            $modelEmailNotification->send_to = $this->email_address;
            $modelEmailNotification->sending_status = 0;
            $modelEmailNotification->save();

        //SMS Notification
        $modelSmsNotification = new SmsNotification();
        $modelSmsNotification->phone_number = $this->phone_number;
        $names = explode(" ", $this->name);
        $modelSmsNotification->content = "Hello ".$names[0].", this is your user account for e-Djangui Online. Username=".$this->username.", Password=".$currenUserPassword.", Url=".Yii::$app->params['platform_url'].", Thanks";
        $modelSmsNotification->status = 0;
                $modelSmsNotification->save();
    }


    /**
    * Generate Password ans send to the new user
    */
    public function saveNewUser(){
        $this->status = User::STATUS_ACTIVE;
        $this->created_at = strtotime(Date('Y-m-d H:i:s'));
        $this->updated_at = strtotime(Date('Y-m-d H:i:s'));
        $this->language = "en_us";
        $this->generateAuthKey();
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $this->association = $modelCurrentUser->association;

        $result = $this->save();
        $this->generateAndSendCredentialsToUser();

        return $result;
    }


    /**
    * Get list of all Users of the same association of current admin user
    * @return array(id => name_en)
    */
    public static function getAllMembersOfCurrentAssociation(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $users = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('user')
                    ->where('association='.$modelCurrentUser->association)
                    ->orderBy('name')
                    ->all();
        $results = array();
        foreach ($users as $user) {
                $results[$user['id']] = $user['name'];
        }
        return $results;
    }
    
}