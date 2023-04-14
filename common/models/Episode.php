<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "episode".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $season
 * @property int|null $rank
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $meeting_date
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property Season $season0
 * @property User $updatedBy
 */
class Episode extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'episode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['season', 'rank', 'association', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date',  'meeting_date',  'created_on', 'updated_on'], 'safe'],
            [['meeting_date'], 'required'],
            [['name'], 'string', 'max' => 45],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['season'], 'exist', 'skipOnError' => true, 'targetClass' => Season::class, 'targetAttribute' => ['season' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
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
            'season' => Yii::t('app', 'Season'),
            'rank' => Yii::t('app', 'Rank'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'meeting_date' => Yii::t('app', 'Meeting Date'),
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
     * Gets query for [[Season0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeason0()
    {
        return $this->hasOne(Season::class, ['id' => 'season']);
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
    * Get list of all Episodes of current association and djangui
    * @return array(id => name_en)
    */
    public static function getAllEpisodes($season){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $episodes = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('episode')
                    ->where('season='.$season)
                    ->andWhere('association='.$modelCurrentUser->association)
                    ->orderBy('created_on')
                    ->all();
        $results = array();
        foreach ($episodes as $episode) {
                $results[$episode['id']] = $episode['name'];
        }
        return $results;
    }

    /**
    * get current episode Id
    */
    public static function getCurrentEpisode(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $modelEpisode = Episode::find()
                            ->where('start_date <="'.date('Y-m-d').'"')
                            ->andWhere('end_date >="'.date('Y-m-d').'"')
                            ->andWhere('association='.$modelCurrentUser->association)
                            ->one();
        if(!is_null($modelEpisode))
            return $modelEpisode->id;
        else return 0;
    }

    /**
    * get current episode name
    */
    public static function getCurrentEpisodeName(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $modelEpisode = Episode::find()
                            ->where('start_date <="'.date('Y-m-d').'"')
                            ->andWhere('end_date >="'.date('Y-m-d').'"')
                            ->andWhere('association='.$modelCurrentUser->association)
                            ->one();
        if(!is_null($modelEpisode))
            return $modelEpisode->name;
        else return 0;
    }
}
