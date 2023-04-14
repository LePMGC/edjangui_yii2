<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountHistory represents the model behind the search form of Account History Data.
 */
class AccountHistory extends Model
{
    public $ah_date;
    public $ah_type;
    public $ah_amount;
    public $ah_balance_before;
    public $ah_balance_after;
    public $member;
    public $ah_transaction_id;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ah_amount', 'ah_balance_before', 'ah_balance_after'], 'number'],
            [['ah_date'], 'safe'],
            [['member', 'ah_type', 'ah_transaction_id'], 'integer',],
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
            'ah_date' => Yii::t('app', 'Date'),
            'ah_type' => Yii::t('app', 'Type'),
            'ah_amount' => Yii::t('app', 'Amount'),
            'ah_balance_before' => Yii::t('app', 'Balance Before'),
            'ah_balance_after' => Yii::t('app', 'Balance After'),
            'ah_transaction_id' => Yii::t('app', 'Transaction Id'),
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params){
        
         $query1 = (new \yii\db\Query())
            ->select(["created_on as ah_date", new \yii\db\Expression("1 as ah_type"), "amount as ah_amount", "balance_after as ah_balance_after", "balance_before as ah_balance_before", "id as ah_transaction_id"])
            ->from('cash_in')
            ->where('member='.$this->member)
            ->andWhere('amount > 0');

        $query2 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("2 as ah_type"), "own_share as ah_amount", "balance_after as ah_balance_after", "balance_before as ah_balance_before", "id as ah_transaction_id"])
            ->from('loan_interest_share')
            ->where('beneficiary='.$this->member)
            ->andWhere('not(own_share is null)');

        $query3 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("3 as ah_type"), "amount as ah_amount", "balance_after as ah_balance_after", "balance_before as ah_balance_before", "id as ah_transaction_id"])
            ->from('cash_out')
            ->where('member='.$this->member);

        /*$query4 = (new \yii\db\Query())
            ->select(["created_on as ah_date", new \yii\db\Expression("4 as ah_type"), "charges as ah_amount", "balance_after as ah_balance_after", "balance_before as ah_balance_before", "id as ah_transaction_id"])
            ->from('delay_charges')
            ->where('user='.$this->member);*/

        /*$query5 = (new \yii\db\Query())
            ->select(["updated_on as ah_date", new \yii\db\Expression("5 as ah_type"), "amount as ah_amount", "balance_after as ah_balance_after", "balance_before as ah_balance_before", "id as ah_transaction_id"])
            ->from('instant_cash_in')
            ->where('account='.$this->member);*/

        $unionQuery = $query1->union($query2, true)->union($query3, true)/*->union($query5, true)*/;
        //$query6 = $query5->union($query3, true);
        //$unionQuery = $query6->union($query5, true);
        
        $query = (new \yii\db\Query())
            ->select('ah_date, ah_type, ah_amount, ah_balance_before, ah_balance_after, ah_transaction_id')
            ->from([$unionQuery]);
            

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'ah_date' => $this->ah_date,
            'ah_type' => $this->ah_type,
            'ah_amount' => $this->ah_amount,
            'ah_balance_before' => $this->ah_balance_before,
            'ah_balance_after' => $this->ah_balance_after,
        ]);

        $query->andFilterWhere(['like', 'ah_date', $this->ah_date]);
        $query->orderBy('ah_date DESC');

        $dataProviderKeys = array();
        $i = 0;
        $dataProviderModels = $dataProvider->getModels();
        foreach ($dataProviderModels as $dataProviderModel) {
            $dataProviderKeys[$i++] = $dataProviderModel['ah_type'].'-'.$dataProviderModel['ah_transaction_id'];
        }

        $dataProvider->setKeys($dataProviderKeys);
        //print_r($dataProvider->getKeys());
        return $dataProvider;
    }
}
