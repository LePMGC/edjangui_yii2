<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_notification".
 *
 * @property int $id
 * @property string|null $content
 * @property string|null $phone_number
 * @property int|null $status
 * @property int|null $response_code
 * @property string|null $description
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class SmsNotification extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'response_code', 'created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['content'], 'string', 'max' => 160],
            [['phone_number'], 'string', 'max' => 9],
            [['description'], 'string', 'max' => 256],
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
            'content' => Yii::t('app', 'Content'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'status' => Yii::t('app', 'Status'),
            'response_code' => Yii::t('app', 'Response Code'),
            'description' => Yii::t('app', 'Description'),
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
