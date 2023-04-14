<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BankAccount;

/**
 * BankAccountSearch represents the model behind the search form of `common\models\BankAccount`.
 */
class BankAccountSearch extends BankAccount
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'association', 'loan_allowed', 'cash_in_allowed', 'cash_out_allowed', 'fix_cash_in_amount', 'created_by', 'updated_by'], 'integer'],
            [['name', 'created_on', 'updated_on'], 'safe'],
            [['min_balance_for_loan', 'min_cash_in_amount'], 'number'],
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
        $query = BankAccount::find();

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
            'association' => $this->association,
            'loan_allowed' => $this->loan_allowed,
            'cash_in_allowed' => $this->cash_in_allowed,
            'cash_out_allowed' => $this->cash_out_allowed,
            'fix_cash_in_amount' => $this->fix_cash_in_amount,
            'min_balance_for_loan' => $this->min_balance_for_loan,
            'min_cash_in_amount' => $this->min_cash_in_amount,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
