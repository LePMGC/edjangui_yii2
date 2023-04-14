<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cash_in".
 *
 * @property int $id
 * @property int|null $member
 * @property int|null $episode
 * @property int|null $bank_account
 * @property float|null $amount
 * @property float|null $balance_before
 * @property float|null $balance_after
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property Episode $episode0
 * @property User $member0
 * @property User $updatedBy
 */
class CashIn extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cash_in';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member', 'episode', 'bank_account', 'association', 'created_by', 'updated_by'], 'integer'],
            [['amount', 'balance_before', 'balance_after'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['episode'], 'exist', 'skipOnError' => true, 'targetClass' => Episode::class, 'targetAttribute' => ['episode' => 'id']],
            [['member'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['member' => 'id']],
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
            'member' => Yii::t('app', 'Member'),
            'episode' => Yii::t('app', 'Episode'),
            'bank_account' => Yii::t('app', 'Bank Account'),
            'amount' => Yii::t('app', 'Amount'),
            'balance_before' => Yii::t('app', 'Balance Before'),
            'balance_after' => Yii::t('app', 'Balance After'),
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
     * Gets query for [[Episode0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEpisode0()
    {
        return $this->hasOne(Episode::class, ['id' => 'episode']);
    }

    /**
     * Gets query for [[Member0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember0()
    {
        return $this->hasOne(User::class, ['id' => 'member']);
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
    * Check if the current cash_in operation is the last operation on the member account
    * @return Boolean
    */
    public function isLastOperationOnTheAccount(){
        $modelAccount = Account::findOne(['bank_account' => $this->bank_account, 'owner' => $this->member]);
        $timeCashInCreation = strtotime($this->created_on);
        $timeAccountLastUpdated = strtotime($modelAccount->updated_on);
        return ($timeAccountLastUpdated - $timeCashInCreation) < 60;
    }
}
