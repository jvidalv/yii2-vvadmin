<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_log".
 *
 * @property int $id
 * @property int $article_id
 * @property int $user_id
 * @property int $media_id
 * @property int $category_id
 * @property string $date
 * @property string $title
 * @property string $resume
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 */
class ArticleLog extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id', 'user_id', 'category_id', 'title', 'created_at', 'updated_at'], 'required'],
            [['article_id', 'user_id', 'media_id', 'category_id', 'created_at', 'updated_at'], 'integer'],
            [['date'], 'safe'],
            [['content'], 'string'],
            [['title', 'resume'], 'string', 'max' => 120],
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
            'user_id' => Yii::t('app', 'User ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'date' => Yii::t('app', 'Date'),
            'title' => Yii::t('app', 'Title'),
            'resume' => Yii::t('app', 'Resume'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
