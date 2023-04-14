<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Episode;

/**
 * EpisodeSearch represents the model behind the search form of `common\models\Episode`.
 */
class EpisodeSearch extends Episode
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'season', 'rank', 'meeting_date', 'association', 'created_by', 'updated_by'], 'integer'],
            [['name', 'start_date', 'end_date', 'created_on', 'updated_on'], 'safe'],
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
        $query = Episode::find();

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
            'season' => $this->season,
            'rank' => $this->rank,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'meeting_date' => $this->meeting_date,
            'association' => $this->association,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        //$query->orderBy('id DESC');

        return $dataProvider;
    }
}
