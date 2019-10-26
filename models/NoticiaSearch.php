<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Noticia;

/**
 * NoticiaSearch represents the model behind the search form of `app\models\Noticia`.
 */
class NoticiaSearch extends Noticia
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'super',  'updated_at', 'created_at', 'borrat'], 'integer'],
            [['titol', 'user_id', 'general', 'data_publicacio', 'data_publicacio_string','capcalera', 'cos', 'imatge', 'slug'], 'safe'],
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
        $query = Noticia::find();
        // add conditions that should always apply here
        $query->leftJoin('user', 'noticia.user_id = user.id');
        if(!isset($params['sort'])) $query->orderBy('created_at DESC');


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
            'super' => $this->super,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'borrat' => $this->borrat ? $this->borrat : 0,
        ]);


        $query->andFilterWhere(['or',
          ['like', 'titol', $this->general],
          ['like', 'capcalera', $this->general],
          ['like', 'cos', $this->general],
          ['like', 'data_publicacio', $this->general],
          ['like', 'data_publicacio_string', $this->general],
          ['like', 'user.nom', $this->general],
          ['like', 'user.cognoms', $this->general]]);

        return $dataProvider;
    }

    /*
    *  RETORNA EL NUMERO EN @string DE NOTICIES
    */
    public function numeroTotal($params){
      $query = Noticia::find();

      $query->leftJoin('user', 'noticia.user_id = user.id');


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
          'super' => $this->super,
          'updated_at' => $this->updated_at,
          'created_at' => $this->created_at,
          'borrat' => $this->borrat,
      ]);

      $query->andFilterWhere(['like', 'titol', $this->titol])
          ->andFilterWhere(['like', 'capcalera', $this->capcalera])
          ->andFilterWhere(['like', 'cos', $this->cos])
          ->andFilterWhere(['like', 'imatge', $this->imatge])
          ->andFilterWhere(['like', 'data_publicacio', $this->data_publicacio]);

      $query->andFilterWhere(['like', 'user.nom', $this->user_id]);

      return $query->count();

    }
}
