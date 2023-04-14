<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanInterestShare;

/**
 * LoanInterestShareSearch represents the model behind the search form of `common\models\LoanInterestShare`.
 */
class LoanInterestShareSearch extends LoanInterestShare
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'loan', 'beneficiary', 'bank_account', 'association', 'created_by', 'updated_by'], 'integer'],
            [['balance_at_loan', 'total_balance_at_loan', 'own_share', 'balance_before', 'balance_after'], 'number'],
            [['created_on', 'updated_on'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LoanInterestShare::find();

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

        $modelCurrentUser = User::findOne(\Yii::$app->getUser()->getId());
        $this->association = $modelCurrentUser->association;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'loan' => $this->loan,
            'beneficiary' => $this->beneficiary,
            'balance_at_loan' => $this->balance_at_loan,
            'total_balance_at_loan' => $this->total_balance_at_loan,
            'own_share' => $this->own_share,
            'bank_account' => $this->bank_account,
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
            'association' => $this->association,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        return $dataProvider;
    }
}
