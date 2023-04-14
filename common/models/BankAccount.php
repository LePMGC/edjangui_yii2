<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bank_account".
 *
 * @property int $id
 * @property int $association
 * @property string|null $name
 * @property int|null $loan_allowed
 * @property int|null $cash_in_allowed
 * @property int|null $cash_out_allowed
 * @property int|null $fix_cash_in_amount
 * @property float|null $min_balance_for_loan
 * @property float|null $min_cash_in_amount
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property User $updatedBy
 */
class BankAccount extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['association'], 'required'],
            [['association', 'loan_allowed', 'cash_in_allowed', 'cash_out_allowed', 'fix_cash_in_amount', 'created_by', 'updated_by'], 'integer'],
            [['min_balance_for_loan', 'min_cash_in_amount'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
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
            'association' => Yii::t('app', 'Association'),
            'name' => Yii::t('app', 'Name'),
            'loan_allowed' => Yii::t('app', 'Loan Allowed'),
            'cash_in_allowed' => Yii::t('app', 'Cash In Allowed'),
            'cash_out_allowed' => Yii::t('app', 'Cash Out Allowed'),
            'fix_cash_in_amount' => Yii::t('app', 'Fix Cash In Amount'),
            'min_balance_for_loan' => Yii::t('app', 'Min Balance For Loan'),
            'min_cash_in_amount' => Yii::t('app', 'Min Cash In Amount'),
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
    * Get list of all bank accounts on which loans are allowed
    * @return array(id => name_en)
    */
    public static function getAllBankAccountsWithLoanAllowed(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $modelBankAccounts = BankAccount::find()
                ->where('association='.$modelCurrentUser->association)
                ->andWhere('loan_allowed=1')
                ->orderBy('name')
                ->all();

        $results = array();
        foreach ($modelBankAccounts as $modelBankAccount) {
            $results[$modelBankAccount->id] = $modelBankAccount->name.' : '.Yii::$app->formatter->asDecimal($modelBankAccount->getTotalAmountAvailableForLoan());
        }
        return $results;
    }


    /**
    * Get list of all bank accounts of current assocaition
    * @return array(id => name_en)
    */
    public static function getAllBankAccountsOfCurrentAssociation(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $users = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('bank_account')
                    ->where('association='.$modelCurrentUser->association)
                    ->orderBy('name')
                    ->all();
        $results = array();
        foreach ($users as $user) {
                $results[$user['id']] = $user['name'];
        }
        return $results;
    }

    /**
    *
    */
    public static function getDetaultBankAccountIdForLoan(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $modelBankAccount = BankAccount::find()->where(['association' => $modelCurrentUser->association, 'loan_allowed' => 1])->orderBy('id ASC')->one();
        return is_null($modelBankAccount) ? 0 : $modelBankAccount->id;
    }

    /**
    * This function will compute the total amount which are currently available for a loan.
    * This amount is the difference between sum of accounts balances and sum of amount remaining from loan.
    * @return float
    */
    public function getTotalAmountAvailableForLoan(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $rows = (new \yii\db\Query())
            ->select(['sum(balance) as total_balance'])
            ->from('account')
            ->where('bank_account in (select id from bank_account where loan_allowed =1)')
            ->andWhere(['bank_account' => $this->id, 'association' => $modelCurrentUser->association])
            ->all();

        $totalBalance = (is_null($rows[0])) ? 0 : $rows[0]['total_balance'];

        $results = $totalBalance;
        $modelLoans = Loan::find()->where(['bank_account' => $this->id])->all();
        foreach ($modelLoans as $modelLoan) {
            if($modelLoan->status != Loan::LOAN_RETURNED)
                $results = $results - $modelLoan->amount_requested + $modelLoan->getAmountAlreadyRefund();
        }
        
        if(strcmp(Yii::$app->session->get('current_loan_mode'),'edit') == 0){
            $modelLoan = Loan::findOne(Yii::$app->session->get('current_loan_id'));
            $currentEditedLoanAmount = $modelLoan->amount_requested;
        }else{
            $currentEditedLoanAmount = 0;
        }

        return $results + $currentEditedLoanAmount;
    }
}
