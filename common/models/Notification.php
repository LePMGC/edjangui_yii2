<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $sms_content_en
 * @property string|null $sms_content_fr
 * @property string|null $email_content_en
 * @property string|null $email_content_fr
 * @property string|null $send_email
 * @property string|null $send_sms
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class Notification extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sms_content_en', 'sms_content_fr', 'email_content_en', 'email_content_fr'], 'string'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'description'], 'string', 'max' => 256],
            [['send_email', 'send_sms'], 'string', 'max' => 1],
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
            'description' => Yii::t('app', 'Description'),
            'sms_content_en' => Yii::t('app', 'Sms Content En'),
            'sms_content_fr' => Yii::t('app', 'Sms Content Fr'),
            'email_content_en' => Yii::t('app', 'Email Content En'),
            'email_content_fr' => Yii::t('app', 'Email Content Fr'),
            'send_email' => Yii::t('app', 'Send Email'),
            'send_sms' => Yii::t('app', 'Send Sms'),
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
