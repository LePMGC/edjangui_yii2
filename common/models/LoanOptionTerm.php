<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_option_term".
 *
 * @property int $id
 * @property int $association
 * @property string|null $name
 * @property float|null $amount_to_refund
 * @property float|null $percentage
 * @property int|null $loan_option
 * @property int|null $rank
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property LoanOption $loanOption
 * @property User $updatedBy
 */
class LoanOptionTerm extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_option_term';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['association', 'loan_option', 'rank', 'created_by', 'updated_by'], 'integer'],
            [['amount_to_refund', 'percentage'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['loan_option'], 'exist', 'skipOnError' => true, 'targetClass' => LoanOption::class, 'targetAttribute' => ['loan_option' => 'id']],
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
            'amount_to_refund' => Yii::t('app', 'Amount To Refurn'),
            'percentage' => Yii::t('app', 'Percentage'),
            'loan_option' => Yii::t('app', 'Loan Option'),
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
     * Gets query for [[LoanOption]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanOption()
    {
        return $this->hasOne(LoanOption::class, ['id' => 'loan_option']);
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
