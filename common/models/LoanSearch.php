<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Loan;

/**
 * LoanSearch represents the model behind the search form of `common\models\Loan`.
 */
class LoanSearch extends Loan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'taker', 'status', 'payment_method', 'association', 'loan_option', 'created_by', 'updated_by'], 'integer'],
            [['amount_requested', 'amount_received', 'amount_to_cash_in', 'interest'], 'number'],
            [['taken_date', 'return_date', 'phone_number', 'created_on', 'updated_on'], 'safe'],
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
        $query = Loan::find();

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
            'taker' => $this->taker,
            'amount_requested' => $this->amount_requested,
            'amount_received' => $this->amount_received,
            'amount_to_cash_in' => $this->amount_to_cash_in,
            'interest' => $this->interest,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'association' => $this->association,
            'loan_option' => $this->loan_option,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'phone_number', $this->phone_number]);
        $query->andFilterWhere(['like', 'taken_date', $this->taken_date]);
        $query->andFilterWhere(['like', 'return_date', $this->return_date]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
