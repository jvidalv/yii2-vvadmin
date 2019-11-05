<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_translations".
 *
 * @property int $id
 * @property int $article_ca
 * @property int $article_es
 * @property int $article_en
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
            [['article_ca', 'article_es', 'article_en'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_ca' => Yii::t('app', 'Article Ca ID'),
            'article_es' => Yii::t('app', 'Article Es ID'),
            'article_en' => Yii::t('app', 'Article En ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleCa()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_ca']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleEs()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_es']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleEn()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_en']);
    }


}