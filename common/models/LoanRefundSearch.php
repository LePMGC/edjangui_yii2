<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LoanRefund;

/**
 * LoanRefundSearch represents the model behind the search form of `common\models\LoanRefund`.
 */
class LoanRefundSearch extends LoanRefund
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'loan', 'association', 'created_by', 'updated_by'], 'integer'],
            [['refund_date', 'created_on', 'updated_on'], 'safe'],
            [['amount_given', 'remain_before', 'remain_after'], 'number'],
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
        $query = LoanRefund::find();

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
            'refund_date' => $this->refund_date,
            'amount_given' => $this->amount_given,
            'remain_before' => $this->remain_before,
            'remain_after' => $this->remain_after,
            'association' => $this->association,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        return $dataProvider;
    }
}
