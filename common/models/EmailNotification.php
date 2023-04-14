<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "email_notification".
 *
 * @property int $id
 * @property string|null $subject
 * @property string|null $html_content
 * @property string|null $text_content
 * @property int|null $sending_status
 * @property string|null $send_to
 * @property string|null $sent_on
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 */
class EmailNotification extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['html_content', 'text_content'], 'string'],
            [['sending_status', 'created_by', 'updated_by'], 'integer'],
            [['sent_on', 'created_on', 'updated_on'], 'safe'],
            [['subject', 'send_to'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject' => Yii::t('app', 'Subject'),
            'html_content' => Yii::t('app', 'Html Content'),
            'text_content' => Yii::t('app', 'Text Content'),
            'sending_status' => Yii::t('app', 'Sending Status'),
            'send_to' => Yii::t('app', 'Send To'),
            'sent_on' => Yii::t('app', 'Sent On'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }
}
