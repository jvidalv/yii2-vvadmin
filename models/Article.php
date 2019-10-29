<?php

namespace app\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $language_id
 * @property int $user_id
 * @property int $media_id
 * @property int $category_id
 * @property string $date
 * @property string $title
 * @property string $resume
 * @property string $content
 * @property int $state 0 = draft, 1 = private, 2 = public
 * @property string $slug
 * @property int $updated_at
 * @property int $created_at
 */
class Article extends \yii\db\ActiveRecord
{

    public $general;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
     public function behaviors()
     {
         return [
             TimestampBehavior::className(),
             [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
             ],
         ];
     }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['language_id', 'user_id', 'title'], 'required'],
            [['language_id', 'user_id', 'media_id', 'category_id', 'state', 'updated_at', 'created_at'], 'integer'],
            [['date'], 'safe'],
            [['content'], 'string'],
            [['title', 'slug'], 'string', 'max' => 120],
            [['resume'], 'string', 'max' => 180],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'language_id' => Yii::t('app', 'Language ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'date' => Yii::t('app', 'Date'),
            'title' => Yii::t('app', 'Title'),
            'resume' => Yii::t('app', 'Resume'),
            'content' => Yii::t('app', 'Content'),
            'state' => Yii::t('app', 'State'),
            'slug' => Yii::t('app', 'Slug'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }
}
