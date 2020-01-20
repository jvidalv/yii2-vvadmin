<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "article_has_translations".
 * @property int $id
 * @property int $article_ca
 * @property int $article_es
 * @property int $article_en
 * @property int $time_to_read
 * @property int $state
 * @property int $claps
 * @property string $category_id
 * @property ActiveQuery $articleEs
 * @property ActiveQuery $articleCa
 * @property ActiveQuery $category
 * @property ActiveQuery $articleEn
 * @property string $date
 */
class ArticleHasTranslations extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_translations';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_ca', 'article_es', 'article_en', 'state', 'featured', 'category_id', 'time_to_read', 'claps'], 'integer'],
            [['date'], 'safe'],
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
            'time_to_read' => Yii::t('app', 'time to read'),
            'claps' => Yii::t('app', 'claps'),
            'featured' => Yii::t('app', 'featured'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getArticleCa()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_ca']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArticleEs()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_es']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArticleEn()
    {
        return $this->hasOne(Article::className(), ['id' => 'article_en']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Has all translations
     * @return bool
     */
    public function hasAllTranslations()
    {
        return $this->article_ca && $this->article_es && $this->article_en;
    }

}
