<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmailNotification;

/**
 * EmailNotificationSearch represents the model behind the search form of `common\models\EmailNotification`.
 */
class EmailNotificationSearch extends EmailNotification
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sending_status', 'created_by', 'updated_by'], 'integer'],
            [['subject', 'html_content', 'text_content', 'send_to', 'sent_on', 'created_on', 'updated_on'], 'safe'],
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
        $query = EmailNotification::find();

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
            'sending_status' => $this->sending_status,
            'sent_on' => $this->sent_on,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
            'updated_by' => $this->updated_by,
            'updated_on' => $this->updated_on,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'html_content', $this->html_content])
            ->andFilterWhere(['like', 'text_content', $this->text_content])
            ->andFilterWhere(['like', 'send_to', $this->send_to]);

        $query->orderBy('id DESC');

        return $dataProvider;
    }
}
