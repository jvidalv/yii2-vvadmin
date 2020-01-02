<?php

namespace app\models;

use app\components\ArticleParser;
use DOMDocument;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Url;
use yii\imagine\Image;

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
 * @property string $tagsString
 * @property string|false $updatedAt
 * @property \yii\db\ActiveQuery $user
 * @property \yii\db\ActiveQuery $category
 * @property string|false $dateF
 * @property \yii\db\ActiveQuery $translations
 * @property float $timeToRead
 * @property string|false $createdAt
 * @property \yii\db\ActiveQuery $articleHasTags
 * @property \yii\db\ActiveQuery $tags
 * @property \yii\db\ActiveQuery $continuationA
 * @property \yii\db\ActiveQuery $language
 * @property \yii\db\ActiveQuery $articleHasAnchors
 * @property int $created_at
 */
class Article extends ActiveRecord
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
     * If its the next serie of certain article
     */
    public $continuation;
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
            [['date'], 'default', 'value' => function () {
                return Date('Y-m-d H:i:s');
            }],
            [['user_id', 'state', 'word_count', 'updated_at', 'created_at', 'translation_of', 'word_count', 'continuation'], 'integer'],
            ['state', 'default', 'value' => 0],
            [['language_id'], 'string', 'max' => 2],
            [['date'], 'safe'],
            [['content'], 'string'],
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
            'continuation' => Yii::t('app', 'continuation of'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->date = Date('Y-m-d H:i', strtotime($this->date));

        if (!$this->isNewRecord) {
            // Parse tags
            if($this->tags_form = Yii::$app->request->post()['Article']['tags']){
                $this->parseArticleTags();
            }
            // Parse article content
            if($this->content) {
                $parser = new ArticleParser($this->id, $this->content);
                $parser->insertAnchors();
                $parser->parseImatges();
                if (!$parser->errors) {
                    $this->content = $parser->getContent();
                } else {
                    $this->addErrors($parser->errors);
                }
            }
            // End article parsing
        }

        return !$this->errors && parent::beforeSave($insert);
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

        // For series of articles, if we set the property first we delete the historical
        if ($this->continuation) {
            if ($ocon = $this->continuationA) {
                $ocon->delete();
            }
            $continuation = new ArticleHasContinuation(['origen_id' => $this->continuation, 'continuation_id' => $this->id]);
            $continuation->save();
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
     * @throws Exception
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
     * Parses tags select and stores
     * Also deletes all the tags
     */
    private function parseArticleTags()
    {
        array_map(function (ArticleHasTags $val) {
            $val->delete();
        }, ArticleHasTags::findAll(['article_id' => $this->id]));

        foreach ($this->tags_form as $tag) {
            $tagr = new ArticleHasTags();
            $tagr->setAttributes([
                'tag_id' => $tag,
                'article_id' => $this->id,
            ]);
            $tagr->save();
        }
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
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['code' => 'language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContinuationA()
    {
        return $this->hasOne(ArticleHasContinuation::className(), ['origen_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
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
     * @return ActiveQuery
     */
    public function getArticleHasTags()
    {
        return $this->hasMany(ArticleHasTags::className(), ['article_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArticleHasAnchors()
    {
        return $this->hasMany(ArticleHasAnchors::className(), ['article_id' => 'id'])->orderBy('id asc');
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
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
        return ceil($this->word_count / 200);
    }

    /**
     * Is featured?
     * @return bool
     */
    public function isFeatured()
    {
        return (bool)$this->translations->featured;
    }

    /**
     * Returns locale as expected from setlocale funcion
     * @return string
     */
    public function getLocale()
    {
        switch($this->language_id){
            case Language::LANG_CA:
                return 'ca_ES';
            case Language::LANG_ES:
                return 'es_ES';
            case Language::LANG_EN:
            default:
                return 'en_EN';
        }
    }
}
