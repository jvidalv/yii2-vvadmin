<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'role', 'created_at', 'updated_at'], 'integer'],
            [['general', 'nom', 'cognoms', 'telefon', 'email', 'dni', 'adreca', 'username', 'password', 'authKey', 'password_reset_token', 'imatge'], 'safe'],
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
        $query = User::find();
        // add conditions that should always apply here
        if (!isset($params['sort'])) $query->orderBy('created_at DESC');

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
            'role' => $this->role,
        ]);

        $query->andFilterWhere(['or',
            ['like', 'nom', $this->general],
            ['like', 'cognoms', $this->general],
            ['like', 'telefon', $this->general],
            ['like', 'adreca', $this->general],
            ['like', 'email', $this->general],
            ['like', 'username', $this->general]
        ]);


        return $dataProvider;
    }
}
