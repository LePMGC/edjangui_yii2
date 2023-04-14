<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loan_endorse_comment".
 *
 * @property int $id
 * @property int|null $author
 * @property int|null $loan_endorse
 * @property string|null $comment
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $author0
 * @property User $createdBy
 * @property LoanEndorse $loanEndorse
 * @property User $updatedBy
 */
class LoanEndorseComment extends BaseModel 
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_endorse_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author', 'loan_endorse', 'association', 'created_by', 'updated_by'], 'integer'],
            [['comment'], 'string'],
            [['created_on', 'updated_on'], 'safe'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['loan_endorse'], 'exist', 'skipOnError' => true, 'targetClass' => LoanEndorse::class, 'targetAttribute' => ['loan_endorse' => 'id']],
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
            'author' => Yii::t('app', 'Author'),
            'loan_endorse' => Yii::t('app', 'Loan Endorse'),
            'comment' => Yii::t('app', 'Comment'),
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
     * Gets query for [[Author0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor0()
    {
        return $this->hasOne(User::class, ['id' => 'author']);
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
     * Gets query for [[LoanEndorse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLoanEndorse()
    {
        return $this->hasOne(LoanEndorse::class, ['id' => 'loan_endorse']);
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
