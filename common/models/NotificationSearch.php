<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Notification;

/**
 * NotificationSearch represents the model behind the search form of `common\models\Notification`.
 */
class NotificationSearch extends Notification
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['name', 'description', 'sms_content_en', 'sms_content_fr', 'email_content_en', 'email_content_fr', 'send_email', 'send_sms', 'created_on', 'updated_on'], 'safe'],
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
        $query = Notification::find();

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
            'id' => $this->id,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'sms_content_en', $this->sms_content_en])
            ->andFilterWhere(['like', 'sms_content_fr', $this->sms_content_fr])
            ->andFilterWhere(['like', 'email_content_en', $this->email_content_en])
            ->andFilterWhere(['like', 'email_content_fr', $this->email_content_fr])
            ->andFilterWhere(['like', 'send_email', $this->send_email])
            ->andFilterWhere(['like', 'send_sms', $this->send_sms]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
