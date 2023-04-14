<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use common\models\User;
use common\models\Account;
use common\models\LoanEndorse;
use common\models\DjanguiSeason;

/**
 * LoanEndoserSearch represents the model behind the search form of Account History Data.
 */
class LoanEndoserSearch extends Model
{
    public $member;
    public $bank_solde;
    public $current_total_djangui_contribution;
    public $current_amount_endorsed;
    public $requested_endorse_amount;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_solde', 'current_total_djangui_contribution', 'current_amount_endorsed', 'requested_endorse_amount'], 'number'],
            [['member'], 'integer',],
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
            'bank_solde' => Yii::t('app', 'Bank'),
            'current_total_djangui_contribution' => Yii::t('app', 'Djangui'),
            'current_amount_endorsed' => Yii::t('app', 'Endorsed'),
            'requested_endorse_amount' => Yii::t('app', 'Request'),
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
    * Get endorse data (available bank amount, already endorsed amount, ...) related to all the members Those data will be shown to the user asking a loan which requires endorsement
    */
    public function getEndosersData(){
        $currentUserId = Yii::$app->user->getId();
        $modelMembers = User::find()->where('id <> '.$currentUserId)->orderBy('name')->all();
        $results = array();
        $i = 0;
        foreach ($modelMembers as $modelMember) {
            $modelAccount = Account::findOne(['owner' => $modelMember->id]);
            if(!is_null($modelAccount) && (strcmp($modelMember->username, "admin") !=0)){
                $results[$modelMember->id] = array(
                    'id' => $modelMember->id,
                    'member' => $modelMember->name,
                    'bank_solde' => $modelAccount->balance,
                    'current_amount_endorsed' => LoanEndorse::getCurrentAmountEndorsedByUser($modelMember->id),
                    'requested_endorse_amount' => 0,
                );
            }
        }
        return $results;
    }

    /**
    * Get endorse data (available bank amount, already endorsed amount, ...) related to all the members. Those data will be shown to the user editing a loan which requires endorsement.
    */
    public function getEndosersDataEditLoan($loanId){
        $currentUserId = Yii::$app->user->getId();
        $modelMembers = User::find()->where('id <> '.$currentUserId)->orderBy('name')->all();
        $results = array();
        $i = 0;
        foreach ($modelMembers as $modelMember) {
            $modelAccount = Account::findOne(['owner' => $modelMember->id]);
            $modelLoanEndorse = LoanEndorse::findOne(['loan' => $loanId, 'endorser' => $modelMember->id]);
            $requtestedEndorseAmountForThisLoan = is_null($modelLoanEndorse) ? 0 : $modelLoanEndorse->endorse_amount;
            if(is_null($modelLoanEndorse))
                $requtestedEndorseStatusForThisLoan = '';
            else{
                if ($modelLoanEndorse->status == LoanEndorse::LOAN_ENDORSE_APPROVED) {
                    $requtestedEndorseStatusForThisLoan = '<strong class="text-success">'.Yii::t('app', 'APPROVED').'</strong>';
                }elseif ($modelLoanEndorse->status == LoanEndorse::LOAN_ENDORSE_REJECTED) {
                   $requtestedEndorseStatusForThisLoan = '<strong class="text-danger">'.Yii::t('app', 'REJECTED').'</strong>';
                }else
                    $requtestedEndorseStatusForThisLoan = LoanEndorse::getAllStatuses()[$modelLoanEndorse->status];
            }
            
            if(!is_null($modelAccount) && (strcmp($modelMember->username, "admin") !=0)){
                $currentAmountEndorsed = 0;
                //remove requested endorse amount for this loan from the user total endorse amount
                if(!is_null($modelLoanEndorse))
                    $currentAmountEndorsed = LoanEndorse::getCurrentAmountEndorsedByUser($modelMember->id) - (($modelLoanEndorse->status == LoanEndorse::LOAN_ENDORSE_REJECTED)? 0 : $requtestedEndorseAmountForThisLoan);

                $results[$modelMember->id] = array(
                    'id' => $modelMember->id,
                    'member' => $modelMember->name,
                    'bank_solde' => $modelAccount->balance,
                    'current_amount_endorsed' => $currentAmountEndorsed,
                    'requested_endorse_amount_for_this_loan' => $requtestedEndorseAmountForThisLoan,
                    'requested_endorse_status_for_this_loan' => $requtestedEndorseStatusForThisLoan,
                    'requested_endorse_amount' => $requtestedEndorseAmountForThisLoan,
                );
            }
        }
        return $results;
    }

    /**
    * Get endorse data (member, amount, status) related to an existing loan. This will be shown to the taker of the loan for viewing and/or editing of the loan.
    */
    public function getLoanEndosersData($loanId){
        $modelLoanEndorses = LoanEndorse::find()->where(['loan' => $loanId])->all();
        $results = array();
        $i = 0;
        foreach ($modelLoanEndorses as $modelLoanEndorse) {
            $results[$i++] = array(
                'id' => $modelLoanEndorse->endorser,
                'member' => User::findOne($modelLoanEndorse->endorser)->getName(),
                'requested_endorse_amount' => $modelLoanEndorse->endorse_amount,
                'status' => LoanEndorse::getAllStatuses()[$modelLoanEndorse->status],
            );
        }
        return $results;
    }
}
