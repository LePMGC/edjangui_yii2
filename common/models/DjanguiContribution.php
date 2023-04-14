<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "djangui_contribution".
 *
 * @property int $id
 * @property int|null $member
 * @property int|null $episode
 * @property int $djangui
 * @property int|null $expected_amount
 * @property float|null $contribution_amount
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property User $createdBy
 * @property Djangui $djangui0
 * @property Episode $episode0
 * @property User $member0
 * @property User $updatedBy
 */
class DjanguiContribution extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'djangui_contribution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member', 'episode', 'djangui', 'expected_amount', 'association', 'created_by', 'updated_by'], 'integer'],
            [['djangui'], 'required'],
            [['contribution_amount'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['djangui'], 'exist', 'skipOnError' => true, 'targetClass' => Djangui::class, 'targetAttribute' => ['djangui' => 'id']],
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
            'djangui' => Yii::t('app', 'Djangui'),
            'expected_amount' => Yii::t('app', 'Expected Amount'),
            'contribution_amount' => Yii::t('app', 'Contribution Amount'),
            'association' => Yii::t('app', 'Association'),
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
     * Gets query for [[Djangui0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDjangui0()
    {
        return $this->hasOne(Djangui::class, ['id' => 'djangui']);
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
}
