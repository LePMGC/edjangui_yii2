<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\models\User;
use common\models\Account;
use common\models\LoanEndorse;
use common\models\Season;
use common\models\Episode;
use common\models\Parameter;
use common\models\DjanguiMember;
use common\models\Djangui;
use common\models\DjanguiContribution;
use common\models\CashIn;
use xstreamka\mobiledetect\Device;

/**
 * EpisodeContributionSearch represents the model behind the search form of Account History Data.
 */
class EpisodeContributionSearch extends Model
{
    public $episode;
    public $member;
    public $bank_contribution;
    public $djangui_contribution;
    public $contributed_on;
    public $collecting_episode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_contribution', 'djangui_contribution'], 'number'],
            [['episode', 'collecting_episode'], 'integer',],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'episode' => Yii::t('app', 'Djangui Episode'),
            'bank_contribution' => Yii::t('app', 'Bank'),
            'djangui_contribution' => Yii::t('app', 'Djangui'),
            'member' => Yii::t('app', 'Member'),
        ];
    }

    /**
     * Creates data provider instance
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params){
        
         $dataProvider = new ArrayDataProvider([
            'allModels' => array(['member' => 'Ghislain', 'bank_solde' => 137817, 'current_total_djangui_contribution' => 201000, 'requested_endorse_amount' => 0]),
            /*'sort' => [
                'attributes' => ['id', 'username', 'email'],
            ],*/
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
    }

    /**
    *
    */
    public static function getCurrentEpisodeContributionsData($djanuiId=0){
        $currentSeasonModel = Season::findOne(Season::getCurrentSeasonId());
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        if($djanuiId==0){
            $firstDjanguiModel = Djangui::find()->where(['association' => $modelCurrentUser->association])->orderBy('id DESC')->one();
            if(is_null($firstDjanguiModel))
                return array();
            else{
                $djanuiId = $firstDjanguiModel->id;
            }
        }

        $modelMembers = DjanguiMember::find()->where(['season' => $currentSeasonModel->id, 'association' => $modelCurrentUser->association, 'djangui' => $djanuiId])->orderBy('collecting_episode')->all();

        $modelCurrentEpisode = Episode::find()
            ->where(['season' => $currentSeasonModel->id])
            ->andWhere('curdate() between start_date and end_date')
            ->one();

        if(is_null($modelCurrentEpisode)){
            $modelCurrentEpisode = Episode::find()->where(['season' => $currentSeasonModel->id, 'association' => $modelCurrentUser->association])->one();
        }

        $results = array();
        $i = 0;
        $modelDjangui = Djangui::findOne($djanuiId);
        foreach ($modelMembers as $modelMember) {
            $modelDjanguiContribution = DjanguiContribution::findOne(['member' => $modelMember->member, 'episode' => $modelCurrentEpisode->id, 'association' => $modelCurrentUser->association]);
            if(is_null($modelDjanguiContribution)){
                $results[$i++] = array(
                    'member' => Device::$isPhone ? User::findOne($modelMember->member)->getFirstName() : User::findOne($modelMember->member)->name,
                    'djangui_contribution' => 0,
                    'collecting_episode' => Episode::findOne([$modelMember->collecting_episode])->name,
                    'collecting_episode_id' => $modelMember->collecting_episode,
                    'current_episode_id' => $modelCurrentEpisode->id,
                );
            }else{
                //check if the memeber have more than one name
                if(DjanguiMember::getMemberNumberOfNames($modelMember->member, $djanuiId) > 1){
                    //display djangui and back contributions on the first name
                    if(DjanguiMember::getMemberFirstOccurenceOfNames($modelMember->member) == $modelMember->id){
                        $results[$i++] = array(
                            'member' => Device::$isPhone ? User::findOne($modelMember->member)->getFirstName() : User::findOne($modelMember->member)->name,
                            'djangui_contribution' => $modelDjangui->amount,
                            'collecting_episode' => Episode::findOne([$modelMember->collecting_episode])->name,
                            'collecting_episode_id' => $modelMember->collecting_episode,
                            'current_episode_id' => $modelCurrentEpisode->id,
                        );
                    }else{
                        //display only djangui ocntribution on the second name
                        $results[$i++] = array(
                            'member' => Device::$isPhone ? User::findOne($modelMember->member)->getFirstName() : User::findOne($modelMember->member)->name,
                            'djangui_contribution' => $modelDjangui->amount,
                            'collecting_episode' => Episode::findOne([$modelMember->collecting_episode])->name,
                            'collecting_episode_id' => $modelMember->collecting_episode,
                            'current_episode_id' => $modelCurrentEpisode->id,
                        );
                    }
                }else{
                    $results[$i++] = array(
                        'member' => Device::$isPhone ? User::findOne($modelMember->member)->getFirstName() : User::findOne($modelMember->member)->name,
                        'djangui_contribution' => $modelDjangui->amount,
                        'collecting_episode' => Episode::findOne([$modelMember->collecting_episode])->name,
                        'collecting_episode_id' => $modelMember->collecting_episode,
                        'current_episode_id' => $modelCurrentEpisode->id,
                    );
                }
            }
        }
        
        return $results;
    }
}
