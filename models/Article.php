<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $language_id
 * @property int $user_id
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
    public $tags_form;
    public $imatge;

    /**
     * This variable is used to check if we are creating a new item for translation, and if so, we dont generate a new translations
     * @var [type]
     */
    public $translation_of = 0;

    /**
     * Tags and attributes constants for blog content
     */
    const TAGS_ANCHOR = 'anchor', TAGS_IMAGE = 'image';

    /**
     * STATES
     * 0 -> Draft, 1 -> Published, 2 -> Private
     */
    const STATE_DRAFT = 0, STATE_PUBLISHED = 1, STATE_PRIVATE = 2;

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
            [['language_id', 'user_id', 'title', 'category_id'], 'required'],
            [['user_id', 'state', 'word_count', 'updated_at', 'created_at', 'translation_of', 'word_count'], 'integer'],
            ['state', 'default', 'value' => 0],
            [['language_id'], 'string', 'max' => 2],
            [['date'], 'safe'],
            [['content'], 'string'],
            [['tags_form'], 'string', 'max' => 200],
            [['title', 'slug'], 'string', 'max' => 120],
            [['resume'], 'string', 'max' => 180],
            [['imatge'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 250],
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
            'category_id' => Yii::t('app', 'category'),
            'date' => Yii::t('app', 'publish date'),
            'title' => Yii::t('app', 'title'),
            'resume' => Yii::t('app', 'resume'),
            'content' => Yii::t('app', 'content'),
            'state' => Yii::t('app', 'state'),
            'slug' => Yii::t('app', 'slug'),
            'word_count' => Yii::t('app', 'number of words'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->date = Date('Y-m-d H:i', strtotime($this->date));
        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @return bool|void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // If this is true then we are saving and element that has an origin, and in such cases we assign them a translation before contiue
        if ($this->translation_of) {

            $translation = ArticleHasTranslations::findOne(['article_' . Article::findOne($this->translation_of)->language_id => $this->translation_of]);
            $translation->setAttributes([
                'article_' . $this->language_id => $this->id,
            ]);

            $translation->save();
        }

        // We generate the translation log if it does not exist
        if (!$this->translations) {

            $translation = new ArticleHasTranslations();
            $translation->setAttributes([
                'article_' . $this->language_id => $this->id,
            ]);

            $translation->save();
        }

        try {
            // We dont update other translations of a recent created translation :d
            if (!$this->translation_of) {
                $this->updateTranslations();
            }
        } catch (Exception $e) {
            $this->addError('system', Yii::t('app', 'error while updating translations'));
        }

        return true;
    }

    /**
     * Update common fields between all translations
     * @return int
     * @throws \yii\db\Exception
     */
    private function updateTranslations()
    {
        $trans = $this->getTranslations()->one();
        Yii::$app->db->createCommand()->update('article_has_translations',
            ['category_id' => $this->category_id, 'date' => $this->date, 'state' => $this->state, 'time_to_read' => $this->getTimeToRead()],
            ['article_' . $this->language_id => $this->id])
            ->execute();
        return Yii::$app->db->createCommand()->update('article',
            ['category_id' => $this->category_id, 'date' => $this->date, 'state' => $this->state],
            ['in', 'id', [$trans->article_ca, $trans->article_es, $trans->article_en]])
            ->execute();
    }

    /**
     * All states than an article can be in
     * @return array
     */
    public static function getStates()
    {
        return [
            self::STATE_DRAFT => Yii::t('app', 'draft'),
            self::STATE_PUBLISHED => Yii::t('app', 'published'),
            self::STATE_PRIVATE => Yii::t('app', 'private')
        ];
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
        return $this->hasOne(Language::className(), ['code' => 'language_id']);
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
        return $this->hasOne(ArticleHasTranslations::className(), ['article_' . $this->language->code => 'id']);
    }

    /**
     * @return false|string
     */
    public function getCreatedAt()
    {
        return Date('d-m-Y H:i', $this->created_at);
    }

    /**
     * @return false|string
     */
    public function getUpdatedAt()
    {
        return Date('d-m-Y H:i', $this->updated_at);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleHasTags()
    {
        return $this->hasMany(ArticleHasTags::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleHasAnchors()
    {
        return $this->hasMany(ArticleHasAnchors::className(), ['article_id' => 'id'])->orderBy('id asc');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('article_has_tags', ['article_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getTagsString()
    {
        return join(', ', array_map(function ($val) {
            return $val['name_' . $this->language->code];
        }, $this->tags ?: []));
    }

    /**
     * @return false|string
     */
    public function getDateF()
    {
        return Date('d-m-Y H:i', strtotime($this->date));
    }

    /**
     * Returns time to read an article in minutes
     * @return float
     */
    public function getTimeToRead()
    {
        return ceil($this->word_count/200 );
    }
}
