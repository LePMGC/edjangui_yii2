<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int|null $owner
 * @property int|null $bank_account
 * @property float|null $balance
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property BankAccount $bankAccount
 * @property User $createdBy
 * @property User $owner0
 * @property User $updatedBy
 */
class Account extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner', 'bank_account', 'association', 'created_by', 'updated_by'], 'integer'],
            [['balance'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['owner'], 'unique', 'targetAttribute' => ['association', 'bank_account', 'owner']],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['bank_account'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::class, 'targetAttribute' => ['bank_account' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner' => 'id']],
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
            'owner' => Yii::t('app', 'Owner'),
            'bank_account' => Yii::t('app', 'Bank Account'),
            'balance' => Yii::t('app', 'Balance'),
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
     * Gets query for [[BankAccount]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccount()
    {
        return $this->hasOne(BankAccount::class, ['id' => 'bank_account']);
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
     * Gets query for [[Owner0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner0()
    {
        return $this->hasOne(User::class, ['id' => 'owner']);
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
