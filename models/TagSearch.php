<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tag;

/**
 * TagSearch represents the model behind the search form of `app\models\Tag`.
 */
class TagSearch extends Tag
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'priority'], 'integer'],
            [['name_ca', 'name_es', 'name_en'], 'safe'],
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
        $query = Tag::find();

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
            'priority' => $this->priority,
        ]);

        $query->andFilterWhere(['like', 'name_ca', $this->name_ca])
            ->andFilterWhere(['like', 'name_es', $this->name_es])
            ->andFilterWhere(['like', 'name_en', $this->name_en]);

        return $dataProvider;
    }
}
