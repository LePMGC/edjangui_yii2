<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "season".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $periodicity
 * @property int|null $current_episode
 * @property int|null $meeting_day
 * @property int|null $loan_return_day
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property User $createdBy
 * @property Episode[] $episodes
 * @property User $updatedBy
 */
class Season extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'season';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'start_date', 'end_date', 'periodicity', 'meeting_day'], 'required'],
            [['start_date', 'end_date', 'created_on', 'updated_on'], 'safe'],
            [['periodicity', 'current_episode', 'meeting_day', 'association', 'created_by', 'updated_by'], 'integer'],
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
            'name' => Yii::t('app', 'Name'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'periodicity' => Yii::t('app', 'Periodicity'),
            'current_episode' => Yii::t('app', 'Current Episode'),
            'meeting_day' => Yii::t('app', 'Meeting Day'),
            'loan_return_day' => Yii::t('app', 'Loan Return Day'),
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
     * Gets query for [[Episodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEpisodes()
    {
        return $this->hasMany(Episode::class, ['season' => 'id']);
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
    * 
    * @return array
    */
    public static function getPeriodicities(){
        return array(
            0 => Yii::t('app', 'Weekly'),
            1 => Yii::t('app', 'Monthly'),
        );
    }


    /**
    * Generate episodes of the new crated season
    */
    public function generateEpisodes(){
        if($this->periodicity == 1){
            $listOfMonths = $this->getAllMonthsBetween2Dates($this->start_date, $this->end_date);
            $i = 1;
            foreach ($listOfMonths as $month) {
                $modelEpisode = new Episode();
                $modelEpisode->name = $month['name'];
                $modelEpisode->season = $this->id;
                $modelEpisode->rank = $i++;
                $modelEpisode->start_date = $month['start_date'];
                $modelEpisode->end_date = $month['end_date'];
                if((7 <= $this->meeting_day) && ($this->meeting_day <= 37)){
                    $d1 = date_create($month['start_date']);
                    $d = date_add($d1, date_interval_create_from_date_string(($this->meeting_day - 7)." days"));
                    $modelEpisode->meeting_date = date_format($d, "Y-m-d");
                }elseif ((38 <= $this->meeting_day) && ($this->meeting_day <= 79)) {
                    if (($this->meeting_day - 38) % 6  == 0) $rank = "first";
                    if (($this->meeting_day - 39) % 6  == 0) $rank = "second";
                    if (($this->meeting_day - 40) % 6  == 0) $rank = "third";
                    if (($this->meeting_day - 41) % 6  == 0) $rank = "fouth";

                    if (($this->meeting_day - 42) % 6  == 0) $rank = "last";
                    if (($this->meeting_day - 43) % 6  == 0) $rank = "last";

                    if((38 <= $this->meeting_day) && ($this->meeting_day <= 43)) $day_of_week = "monday";
                    if((44 <= $this->meeting_day) && ($this->meeting_day <= 49)) $day_of_week = "tuesday";
                    if((50 <= $this->meeting_day) && ($this->meeting_day <= 55)) $day_of_week = "wednesday";
                    if((56 <= $this->meeting_day) && ($this->meeting_day <= 61)) $day_of_week = "thursday";
                    if((62 <= $this->meeting_day) && ($this->meeting_day <= 67)) $day_of_week = "friday";
                    if((68 <= $this->meeting_day) && ($this->meeting_day <= 73)) $day_of_week = "saturday";
                    if((74 <= $this->meeting_day) && ($this->meeting_day <= 79)) $day_of_week = "sunday";

                    $modelEpisode->meeting_date = date("Y-m-d", strtotime($rank." ".$day_of_week." ".$month['name']));

                    //get before last week day of given date
                    if (($this->meeting_day - 42) % 6  == 0){
                        $d2 = date_create($modelEpisode->meeting_date);
                        date_sub($d2,date_interval_create_from_date_string("7 days"));
                        $modelEpisode->meeting_date = date_format($d2,"Y-m-d");
                    }
                }
                $modelEpisode->association = $this->association;
                $modelEpisode->save();
            }
        }
    }

    /**
    * Get list of all Seasons of current association and djangui
    * @return array(id => name_en)
    */
    public static function getAllSeasons(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $seasons = $rows = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('season')
                    ->where('association='.$modelCurrentUser->association)
                    ->orderBy('id DESC')
                    ->all();
        $results = array();
        foreach ($seasons as $season) {
                $results[$season['id']] = $season['name'];
        }
        return $results;
    }

    /**
    * Get last season model id
    */
    public static function getCurrentSeasonId(){
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        
        //get last season model
            $modelSeason = Season::find()
                            ->where(['association' => $modelCurrentUser->association])
                            ->orderBy('id DESC')
                            ->one();
            if(!is_null($modelSeason))
                return $modelSeason->id;
            return 0;
    }

}
