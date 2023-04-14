<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Djangui;

/**
 * DjanguiSearch represents the model behind the search form of `common\models\Djangui`.
 */
class DjanguiSearch extends Djangui
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'association', 'penalty_type', 'penalty_amount', 'created_by', 'updated_by', 'penalty_account'], 'integer'],
            [['name', 'created_on', 'updated_on'], 'safe'],
            [['amount'], 'number'],
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
        $query = Djangui::find();

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
            'amount' => $this->amount,
            'penalty_type' => $this->penalty_type,
            'penalty_amount' => $this->penalty_amount,
            'penalty_account' => $this->penalty_account,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
