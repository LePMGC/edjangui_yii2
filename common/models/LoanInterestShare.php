<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_interest_share".
 *
 * @property int $id
 * @property int $association
 * @property int $beneficiary
 * @property float|null $balance_at_loan
 * @property float|null $total_balance_at_loan
 * @property float|null $own_share
 * @property float|null $balance_before
 * @property float|null $balance_after
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $beneficiary0
 * @property User $createdBy
 * @property User $updatedBy
 */
class LoanInterestShare extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_interest_share';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['association', 'beneficiary'], 'required'],
            [['association', 'beneficiary', 'created_by', 'updated_by'], 'integer'],
            [['balance_at_loan', 'total_balance_at_loan', 'own_share', 'balance_before', 'balance_after'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['beneficiary'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['beneficiary' => 'id']],
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
            'beneficiary' => Yii::t('app', 'Beneficiary'),
            'balance_at_loan' => Yii::t('app', 'Balance At Loan'),
            'total_balance_at_loan' => Yii::t('app', 'Total Balance At Loan'),
            'own_share' => Yii::t('app', 'Own Share'),
            'balance_before' => Yii::t('app', 'Balance Before'),
            'balance_after' => Yii::t('app', 'Balance After'),
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
     * Gets query for [[Beneficiary0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBeneficiary0()
    {
        return $this->hasOne(User::class, ['id' => 'beneficiary']);
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
}
