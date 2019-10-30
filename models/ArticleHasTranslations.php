<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_translations".
 *
 * @property int $id
 * @property int $article_ca_id
 * @property int $article_es_id
 * @property int $article_en_id
 */
class ArticleHasTranslations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_translations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_ca_id', 'article_es_id', 'article_en_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_ca_id' => Yii::t('app', 'Article Ca ID'),
            'article_es_id' => Yii::t('app', 'Article Es ID'),
            'article_en_id' => Yii::t('app', 'Article En ID'),
        ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCa()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_ca_id']);
    }

      /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleEs()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_es_id']);
    }

      /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleEn()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_en_id']);
    }


}
