<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\BankAccount;
use common\models\Account;
use common\models\User;
use common\models\Djangui;
use common\models\DjanguiMember;
use common\models\Episode;
use common\models\Season;
use common\models\CashOut;
use common\models\Loan;

/**
 * ContactForm is the model behind the contact form.
 */
class Member extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            //'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Get all bank account of the current member with its balances
     *
     * @param interger member
     * @return array (name, balance)
     */
    public static function getAllAccountsOfMember($member){
        $modelMember = User::findOne($member);
        $modelBankAccounts = BankAccount::find()->where(['association' => $modelMember->association])->all();
        $results = array();
        $i = 0;
        foreach ($modelBankAccounts as $modelBankAccount) {
            $modelAccount = Account::find()->where(['association' => $modelMember->association, 'owner' => $member, 'bank_account' => $modelBankAccount->id])->one();
            $results[$i++] = array(
                'name' => $modelBankAccount->name,
                'balance' => is_null($modelAccount) ? 0 : $modelAccount->balance,
            );
        }

        return $results;
    }

    /**
     * Get all djangui of the current member with the collection episode
     *
     * @param interger member
     * @return array (name, episode)
     */
    public static function getAllDjanguisOfMember($member){
        $modelMember = User::findOne($member);
        $modelDjanguis = Djangui::find()->where(['association' => $modelMember->association])->all();
        $currentSeasonId = Season::getCurrentSeasonId();
        $results = array();
        $i = 0;
        foreach ($modelDjanguis as $modelDjangui) {
            $modelDjanguiMember = DjanguiMember::find()->where(['association' => $modelMember->association, 'member' => $member, 'djangui' => $modelDjangui->id, 'season' => $currentSeasonId])->one();
            if(!is_null($modelDjanguiMember)){
                $modelEpisode = Episode::findOne($modelDjanguiMember->collecting_episode);
                $results[$i++] = array(
                    'name' => $modelDjangui->name,
                    'collecting_episode' => $modelEpisode->name,
                );
            }
        }

        return $results;
    }


    /**
     * Get total cash_in of the member, total of the current season and the one of all the time
     *
     * @param interger member
     * @return array (name, episode)
     */
    public static function getTotalCashInAmounts($member){
        $modelMember = User::findOne($member);
        $currentSeasonId = Season::getCurrentSeasonId();

        $rows = (new \yii\db\Query())
                    ->select(['sum(amount) as total_cash_in'])
                    ->from('cash_in')
                    ->where(['association' => $modelMember->association, 'member' => $member])
                    ->all();

        $totalCashInAllTheTime = 0;
        if(!is_null($rows))
            if(!is_null($rows[0]))
                if(!is_null($rows[0]['total_cash_in']))
                    $totalCashInAllTheTime = $rows[0]['total_cash_in'];

        $rows = (new \yii\db\Query())
                    ->select(['sum(amount) as total_cash_in'])
                    ->from('cash_in')
                    ->where(['association' => $modelMember->association, 'member' => $member])
                    ->andWhere('episode in (select id from episode where season = '.$currentSeasonId.')')
                    ->all();

        $totalCashInCurrentSeason = 0;
        if(!is_null($rows))
            if(!is_null($rows[0]))
                if(!is_null($rows[0]['total_cash_in']))
                    $totalCashInCurrentSeason = $rows[0]['total_cash_in'];

        return array(
            'current_season' => $totalCashInCurrentSeason,
            'all_the_time' => $totalCashInAllTheTime
        );
    }


    /**
     * Get total cash_out of the member, total of the current season and the one of all the time
     *
     * @param interger member
     * @return array (name, episode)
     */
    public static function getTotalCashOutAmounts($member){
        $modelMember = User::findOne($member);
        $currentSeasonId = Season::getCurrentSeasonId();
        if($currentSeasonId > 0){
            $modelCurrentSeaon = Season::findOne($currentSeasonId);

            $rows = (new \yii\db\Query())
                        ->select(['sum(amount) as total_cash_out'])
                        ->from('cash_out')
                        ->where(['association' => $modelMember->association, 'member' => $member, 'status' => CashOut::CASH_OUT_GIVEN])
                        ->all();

            $totalCashOutAllTheTime = 0;
            if(!is_null($rows))
                if(!is_null($rows[0]))
                    if(!is_null($rows[0]['total_cash_out']))
                        $totalCashOutAllTheTime = $rows[0]['total_cash_out'];

            $rows = (new \yii\db\Query())
                        ->select(['sum(amount) as total_cash_out'])
                        ->from('cash_out')
                        ->where(['association' => $modelMember->association, 'member' => $member])
                        ->andWhere("'".$modelCurrentSeaon->start_date."' <= created_on and created_on <= '".$modelCurrentSeaon->end_date."'")
                        ->all();

            $totalCashOutCurrentSeason = 0;
            if(!is_null($rows))
                if(!is_null($rows[0]))
                    if(!is_null($rows[0]['total_cash_out']))
                        $totalCashOutCurrentSeason = $rows[0]['total_cash_out'];

            return array(
                'current_season' => $totalCashOutCurrentSeason,
                'all_the_time' => $totalCashOutAllTheTime
            );
        }else
            return array(
                'current_season' => 0,
                'all_the_time' => 0
            );
    }


    /**
     * Get total interest share of the member, total of the current season and the one of all the time
     *
     * @param interger member
     * @return array (name, episode)
     */
    public static function getTotalInterestShares($member){
        $modelMember = User::findOne($member);
        $currentSeasonId = Season::getCurrentSeasonId();
        if($currentSeasonId > 0){
            $modelCurrentSeaon = Season::findOne($currentSeasonId);

            $rows = (new \yii\db\Query())
                        ->select(['sum(own_share) as total_interest_share'])
                        ->from('loan_interest_share')
                        ->innerJoin('loan','loan_interest_share.loan=loan.id')
                        ->where(['loan_interest_share.association' => $modelMember->association, 'beneficiary' => $member, 'loan.status' => Loan::LOAN_RETURNED])
                        ->all();

            $totalInterestShareAllTheTime = 0;
            if(!is_null($rows))
                if(!is_null($rows[0]))
                    if(!is_null($rows[0]['total_interest_share']))
                        $totalInterestShareAllTheTime = $rows[0]['total_interest_share'];

            $rows = (new \yii\db\Query())
                        ->select(['sum(own_share) as total_interest_share'])
                        ->from('loan_interest_share')
                        ->innerJoin('loan','loan_interest_share.loan=loan.id')
                        ->where(['loan_interest_share.association' => $modelMember->association, 'beneficiary' => $member, 'loan.status' => Loan::LOAN_RETURNED])
                        ->andWhere("'".$modelCurrentSeaon->start_date."'<= loan_interest_share.created_on and loan_interest_share.created_on <= '".$modelCurrentSeaon->end_date."'")
                        ->all();

            $totalInterestShareCurrentSeason = 0;
            if(!is_null($rows))
                if(!is_null($rows[0]))
                    if(!is_null($rows[0]['total_interest_share']))
                        $totalInterestShareCurrentSeason = $rows[0]['total_interest_share'];

            return array(
                'current_season' => $totalInterestShareCurrentSeason,
                'all_the_time' => $totalInterestShareAllTheTime
            );
        }else
            return array(
                'current_season' => 0,
                'all_the_time' => 0
            );
    }
}
