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
    public $tags;

    /**
     * This variable is used to check if we are creating a new item for translation, and if so, we dont generate a new translations
     * @var [type]
     */
    public $translating = false;

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
            [['tags'], 'string', 'max' => 200],
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
            'language_id' => Yii::t('app', 'language'),
            'user_id' => Yii::t('app', 'user'),
            'media_id' => Yii::t('app', 'media'),
            'category_id' => Yii::t('app', 'category'),
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
        if (!$translations && $this->translating) {
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
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
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
        return $this->hasOne(ArticleHasTranslations::className(), ['article_' . $this->language->code . '_id' => 'id']);
    }

    public function getCreatedAt()
    {
        return Date('d-m-Y H:i', $this->created_at);
    }

    public function getUpdatedAt()
    {
        return Date('d-m-Y H:i', $this->updated_at);
    }

    public function getArticleHasTags()
    {
        return $this->hasMany(ArticleHasTags::className(), ['article_id' => 'tag_id']);
    }

    public function getTagss()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('article_has_tags', ['article_id' => 'id']);
    }

    public function getTagsString()
    {
        $tags = $this->tagss;
        return join(', ', array_map( function($val){return $val['name_'.$this->language->code]; }, $tags ?: []));
    }
}
