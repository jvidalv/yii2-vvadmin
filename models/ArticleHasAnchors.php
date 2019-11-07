<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_anchors".
 *
 * @property int $id
 * @property int $article_id
 * @property string $anchor_id
 * @property string $content
 */
class ArticleHasAnchors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_anchors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id'], 'integer'],
            [['anchor_id'], 'string', 'max' => 50],
            [['content'], 'string', 'max' => 150],
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
            'anchor_id' => Yii::t('app', 'Anchor ID'),
            'content' => Yii::t('app', 'Content'),
        ];
    }
}
