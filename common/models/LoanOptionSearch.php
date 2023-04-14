<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanOption;

/**
 * LoanOptionSearch represents the model behind the search form of `common\models\LoanOption`.
 */
class LoanOptionSearch extends LoanOption
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'association', 'bank_account','number_of_terms', 'term_duration', 'postpone_option', 'postpone_capital', 'created_by', 'updated_by', 'refund_deadline'], 'integer'],
            [['name', 'created_on', 'updated_on'], 'safe'],
            [['min_amount', 'max_amount', 'interest_rate'], 'number'],
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
        $query = LoanOption::find();

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
            'bank_account' => $this->bank_account,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'interest_rate' => $this->interest_rate,
            'number_of_terms' => $this->number_of_terms,
            'term_duration' => $this->term_duration,
            'postpone_option' => $this->postpone_option,
            'postpone_capital' => $this->postpone_capital,
            'refund_deadline' => $this->refund_deadline,
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
