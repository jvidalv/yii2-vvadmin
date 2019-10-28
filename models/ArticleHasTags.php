<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_tags".
 *
 * @property int $id
 * @property int $article_id
 * @property int $tag_id
 */
class ArticleHasTags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'tag_id'], 'required'],
            [['article_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_id' => Yii::t('app', 'Article ID'),
            'tag_id' => Yii::t('app', 'Tag ID'),
        ];
    }
}
