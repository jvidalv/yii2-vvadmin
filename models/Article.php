<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // We generate the translation log if it does not still
        $translations = $this->translations;
        if (!$translations) {
            $translation = new ArticleHasTranslations();
            $translation->setAttributes([
                'article_' . Yii::$app->language . '_id' => $this->id,
            ]);
            $translation->save();
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Category::className(), ['id' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasOne(ArticleHasTranslations::className(), ['article_' . Yii::$app->language . '_id' => 'id']);
    }

    public function getCreatedAt()
    {
        return Date('d-m-Y H:i', $this->created_at);

        }

        public function getUpdatedAt()
    {
        return Date('d-m-Y H:i', $this->updated_at);
        
        }
}
