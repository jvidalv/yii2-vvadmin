<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Media;

/**
 * MediaSearch represents the model behind the search form of `app\models\Media`.
 */
class MediaSearch extends Media
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['titol', 'descripcio', 'path', 'file_name', 'general', 'es_imatge'], 'safe'],
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
        $query = Media::find();
        // add conditions that should always apply here
        $query->leftJoin('user', 'media.user_id = user.id');
        /* ordenacio per defecte */
        if(!isset($params['sort'])) $query->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        /* controlem llistat segons tipos de fitxer, si es null son tots */
        if($this->es_imatge == 'null') unset($this->es_imatge);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'es_imatge' => $this->es_imatge
        ]);

        $query->andFilterWhere(['or',
          ['like', 'media.titol', $this->general],
          ['like', 'media.descripcio', $this->general],
          ['like', 'media.path', $this->general],
          ['like', 'media.file_name', $this->general],
          ['like', 'user.nom', $this->general],
          ['like', 'user.cognoms', $this->general]]);

        return $dataProvider;
    }
}
