<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanOptionTerm;

/**
 * LoanOptionTermSearch represents the model behind the search form of `common\models\LoanOptionTerm`.
 */
class LoanOptionTermSearch extends LoanOptionTerm
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'association', 'loan_option', 'rank','created_by', 'updated_by'], 'integer'],
            [['name', 'created_on', 'updated_on'], 'safe'],
            [['amount_to_refund', 'percentage'], 'number'],
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
        $query = LoanOptionTerm::find();

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
            'amount_to_refund' => $this->amount_to_refund,
            'percentage' => $this->percentage,
            'loan_option' => $this->loan_option,
            'rank' => $this->rank,
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
