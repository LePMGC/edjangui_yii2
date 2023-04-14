<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "djangui_member".
 *
 * @property int $id
 * @property int|null $member
 * @property int|null $season
 * @property int|null $djangui
 * @property int|null $collecting_episode
 * @property int|null $association
 * @property int|null $created_by
 * @property string|null $created_on
 * @property int|null $updated_by
 * @property string|null $updated_on
 *
 * @property Association $association0
 * @property Episode $collectingEpisode
 * @property User $createdBy
 * @property Djangui $djangui0
 * @property User $member0
 * @property Season $season0
 * @property User $updatedBy
 */
class DjanguiMember extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'djangui_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member', 'season', 'djangui', 'collecting_episode', 'association', 'created_by', 'updated_by'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['association'], 'exist', 'skipOnError' => true, 'targetClass' => Association::class, 'targetAttribute' => ['association' => 'id']],
            [['collecting_episode'], 'exist', 'skipOnError' => true, 'targetClass' => Episode::class, 'targetAttribute' => ['collecting_episode' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['djangui'], 'exist', 'skipOnError' => true, 'targetClass' => Djangui::class, 'targetAttribute' => ['djangui' => 'id']],
            [['season'], 'exist', 'skipOnError' => true, 'targetClass' => Season::class, 'targetAttribute' => ['season' => 'id']],
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
            'season' => Yii::t('app', 'Season'),
            'djangui' => Yii::t('app', 'Djangui'),
            'collecting_episode' => Yii::t('app', 'Collecting Episode'),
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
     * Gets query for [[CollectingEpisode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollectingEpisode()
    {
        return $this->hasOne(Episode::class, ['id' => 'collecting_episode']);
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
     * Gets query for [[Member0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember0()
    {
        return $this->hasOne(User::class, ['id' => 'member']);
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
    *  Get the number of names the current given member has in current djandui season
    */
    public static function getMemberNumberOfNames($memberId, $djanguiId){
        $currentDjanguiSeasonId = Season::getCurrentSeasonId();

        //$modelDjanguiMembers = DjanguiMember::find()->where(['djangui_season' => $currentDjanguiSeasonId, 'user' => $memberId])->all();
        $number_of_names = (new \yii\db\Query())
                    ->select(['count(id) as number_of_names'])
                    ->from('djangui_member')
                    ->where(['season' => $currentDjanguiSeasonId, 'djangui' => $djanguiId, 'member' => $memberId])
                    ->all();
        return (is_null($number_of_names)) ? 0 : $number_of_names[0]['number_of_names'];
    }

    /**
    * Get the first occurence (id of table djangui_member) of names for a user who have more than one name in the djangui season.
    * @return Integer
    */
    public static function getMemberFirstOccurenceOfNames($memberId){
        $currentDjanguiSeasonId = Season::getCurrentSeasonId();
        $first_occurence_of_names = (new \yii\db\Query())
            ->select(['djangui_member.id as first_occurence'])
            ->from('djangui_member')
            ->innerJoin('episode','djangui_member.collecting_episode = episode.id')
            ->where(['djangui_member.member' => $memberId])
            ->andWhere(['djangui_member.season' => $currentDjanguiSeasonId])
            ->orderBy(['episode.rank' => SORT_ASC])
            ->all();

        return (is_null($first_occurence_of_names)) ? 0 : $first_occurence_of_names[0]['first_occurence'];
    }

    /**
    * Check if this model member (user) is also collecting on the given episode
    */
    public function isCollectingOnEpisode($episodeId){
        $modelMember = DjanguiMember::find()->where(['member' => $this->member, 'collecting_episode' => $episodeId])->one();
        return !is_null($modelMember);
    }
}
