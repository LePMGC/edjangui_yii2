<?php

namespace app\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Episode;
use common\models\DjanguiContribution;
use common\models\Djangui;
use common\models\BankAccount;
use common\models\CashIn;
use yii\data\ArrayDataProvider;


/**
 * MemberContribution is the model behind the Member Contribution form.
 */
class MemberContribution extends Model{
    public $episode;
    public $member;
    public $djangui_contribution;
    public $djangui_contributions;
    public $bank_contributions;
    public $bank_contributions_html;
    public $djangui_contributions_html;
    public $id;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // episode, member are required
            [['episode', 'member'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'episode' => Yii::t('app', 'Episode'),
            'member' => Yii::t('app', 'Member'),
            'djangui_contribution' => Yii::t('app', 'Djangui Contribution'),
        ];
    }


    public function getMemberContributionsData($params){
        //print_r($params);
        $this->load($params);
        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        ////echo "<br/>";
        //print_r($this->getAttributes());

        if(!empty($this->episode))
            $listOfEpisodes = Episode::find()
                        ->where(['association' => $modelCurrentUser->association])
                        ->andWhere(['id' => $this->episode])
                        ->orderBy('start_date DESC')
                        ->all();
        else
            $listOfEpisodes = Episode::find()
                        ->where(['association' => $modelCurrentUser->association])
                        ->orderBy('start_date DESC')
                        ->all();
        //echo "<br/> <br/>";
        //print_r($listOfEpisodes[0]->getAttributes());

        $memberContributionModels = array();
        foreach ($listOfEpisodes as $episode) {

            //get members who have sent djangui contribution or bank contribution for that episode
            if(!empty($this->member))
                $listOfMembers = User::find()
                            ->where('id = '.$this->member)
                            ->andWhere("id > 1 and ( id in (select member from djangui_contribution where episode = ".$episode->id.") or id in (select member from cash_in where episode = ".$episode->id.") )")
                            ->andWhere('association='.$modelCurrentUser->association)
                            ->all();
            else
                $listOfMembers = User::find()
                            ->where("id > 1 and ( id in (select member from djangui_contribution where episode = ".$episode->id.") or id in (select member from cash_in where episode = ".$episode->id.") )")
                            ->andWhere('association='.$modelCurrentUser->association)
                            ->all();

            //echo "<br/> <br/>";
            //print_r($listOfMembers->createCommand()->sql);

            foreach ($listOfMembers as $member) {
                $memberContributionModels[$episode->id."_".$member->id]['episode'] = $episode->name;
                $memberContributionModels[$episode->id."_".$member->id]['member'] = $member->name;
                
                //load bank contributions
                $bankAccountModels = BankAccount::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
                $bankContributionsHtml = "";
                foreach ($bankAccountModels as $bankAccountModel) {
                    $rows = (new \yii\db\Query())
                        ->select(['sum(amount) as total_cash_in_amount'])
                        ->from('cash_in')
                        ->where(['association' => $modelCurrentUser->association, 'bank_account' => $bankAccountModel->id, 'episode' => $episode->id, 'member' => $member->id])
                        ->all();

                    $memberCahsIn = CashIn::find()->where(['member' => $member->id, 'bank_account' => $bankAccountModel->id, 'episode' => $episode->id, 'association' => $modelCurrentUser->association])->one();

                    if(!is_null($rows) && !is_null($rows[0]) && $rows[0]['total_cash_in_amount']>0)
                        $bankContributionsHtml = $bankContributionsHtml.$bankAccountModel->name." : ".Yii::$app->formatter->asDecimal($rows[0]['total_cash_in_amount'])."<br/>";

                    //$memberContributionModels[$episode->id."_".$member->id]['bank_account_'.$bankAccountModel->id] = is_null($memberCahsIn) ? 0 : $memberCahsIn->amount;
                }
                $memberContributionModels[$episode->id."_".$member->id]['bank_contributions'] = $bankContributionsHtml;

                //load djangui contributions
                $djanguiModels = Djangui::find()->where('id > 0')->andWhere(['association' => $modelCurrentUser->association])->all();
                $djanguiContributionsHtml = "";
                foreach ($djanguiModels as $djanguiModel) {
                    $memberDjanguiContribution = DjanguiContribution::find()->where(['member' => $member->id, 'djangui' => $djanguiModel->id, 'episode' => $episode->id, 'association' => $modelCurrentUser->association])->one();

                    if(!is_null($memberDjanguiContribution))
                        $djanguiContributionsHtml = $djanguiContributionsHtml.$djanguiModel->name." : ".Yii::$app->formatter->asDecimal($memberDjanguiContribution->contribution_amount)."<br/>";
                    //$memberContributionModels[$episode->id."_".$member->id]['djangui_'.$djanguiModel->id] = is_null($memberDjanguiContribution) ? 0 : $memberDjanguiContribution->contribution_amount;
                }
                $memberContributionModels[$episode->id."_".$member->id]['djangui_contributions'] = $djanguiContributionsHtml;
            }
        }

        //echo "<br/> <br/>";
        //print_r($memberContributionModels);

        return new ArrayDataProvider([
            'allModels' => $memberContributionModels,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]); 

    }


    public static function findOne($id){
        $t = explode("_", $id);
        $episode_id = $t[0];
        $member_id = $t[1];
        $model = new MemberContribution();
        $model->episode = $episode_id;
        $model->member = $member_id;
        $model->id = $id;

        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());

        $modelDjanguiContributions = DjanguiContribution::find()->where(['episode' => $episode_id, 'member' => $member_id, 'association' => $modelCurrentUser->association])->all();
        //$model->djangui_contribution = is_null($modelDjanguiContribution) ? 0 : $modelDjanguiContribution->contribution_amount;

        $modelBankContributions = CashIn::find()->where(['episode' => $episode_id, 'member' => $member_id, 'association' => $modelCurrentUser->association])->all();
        
        //find djangui contributions
        $i = 0;
        $model->djangui_contributions_html = "";
        foreach ($modelDjanguiContributions as $modelDjanguiContribution) {
            $djanguiName = Djangui::findOne($modelDjanguiContribution->djangui)->name;
            $model->djangui_contributions[$i++] = array(
                'djangui' => $modelDjanguiContribution->djangui,
                'djangui_name' => $djanguiName,
                'djangui_contribution' => $modelDjanguiContribution->contribution_amount,
                'djangui_contribution_id' => $modelDjanguiContribution->id,
            );

            $model->djangui_contributions_html = $model->djangui_contributions_html.$djanguiName." : ".Yii::$app->formatter->asDecimal($modelDjanguiContribution->contribution_amount)."<br/>";
        }

        //find bank contributions
        $i = 0;
        $model->bank_contributions_html = "";
        foreach ($modelBankContributions as $modelBankContribution) {
            $bankAccountName = BankAccount::findOne($modelBankContribution->bank_account)->name;
            $model->bank_contributions[$i++] = array(
                'bank_account' => $modelBankContribution->bank_account,
                'bank_account_name' => $bankAccountName,
                'bank_contribution' => $modelBankContribution->amount,
                'cash_in_id' => $modelBankContribution->id,
            );

            $model->bank_contributions_html = $model->bank_contributions_html.$bankAccountName." : ".Yii::$app->formatter->asDecimal($modelBankContribution->amount)."<br/>";
        }

        return $model;
    }
}
