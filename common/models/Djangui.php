<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "djangui".
 *
 * @property int $id
 * @property int $association
 * @property string|null $name
 * @property float|null $amount
 * @property int|null $penalty_type
 * @property int|null $penalty_amount
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property User $updatedBy
 */
class Djangui extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'djangui';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['association'], 'required'],
            [['association', 'penalty_type', 'penalty_amount', 'created_by', 'updated_by', 'penalty_account'], 'integer'],
            [['amount'], 'number'],
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
            'amount' => Yii::t('app', 'Amount'),
            'penalty_type' => Yii::t('app', 'Penalty Type'),
            'penalty_amount' => Yii::t('app', 'Penalty Amount'),
            'penalty_account' => Yii::t('app', 'Penalty Account'),
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
    * Get list of all djanguis of current association and djangui
    * @return array(id => name_en)
    */
    public static function getAllDjanguis(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $djanguis = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('djangui')
                    ->where('association='.$modelCurrentUser->association)
                    ->orderBy('id DESC')
                    ->all();
        $results = array();
        foreach ($djanguis as $djangui) {
                $results[$djangui['id']] = $djangui['name'];
        }
        return $results;
    }

    /**
    * Get last Djangui model id
    */
    public static function getLastCreatedDjanguiId(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        
        //get last Djangui model
            $modelDjangui = Djangui::find()
                            ->where(['association' => $modelCurrentUser->association])
                            ->orderBy('id DESC')
                            ->one();
            if(!is_null($modelDjangui))
                return $modelDjangui->id;
            return 0;
    }


    /**
    * Get last Djangui model id
    */
    public static function getFirstCreatedDjanguiId(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        
        //get last Djangui model
            $modelDjangui = Djangui::find()
                            ->where(['association' => $modelCurrentUser->association])
                            ->orderBy('id ASC')
                            ->one();
            if(!is_null($modelDjangui))
                return $modelDjangui->id;
            return 0;
    }
}
