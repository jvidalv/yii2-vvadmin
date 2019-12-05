<?php

namespace app\models;

use DOMDocument;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Url;

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
            [['user_id', 'state', 'word_count', 'updated_at', 'created_at', 'translation_of', 'word_count', 'continuation'], 'integer'],
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
            $this->parseArticleTags();
            // Parses content
            $this->parseArticleContent();
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
     * Parses tag input and stores in the relational table
     */
    private function parseArticleTags()
    {
        if ($this->tags_form) {
            $tags = explode(',', $this->tags_form);
            array_map(function (ArticleHasTags $val) {
                $val->delete();
            }, ArticleHasTags::findAll(['article_id' => $this->id]));

            foreach ($tags as $tag) {

                $tag = trim($tag);
                $tagf = Tag::findOne(['name_' . $this->language->code => $tag]);
                if (!$tagf) {
                    $tagf = new Tag();
                    $tagf->setAttributes([
                        'name_ca' => $tag,
                        'name_es' => $tag,
                        'name_en' => $tag,
                        'priority' => 9,
                    ]);
                    $tagf->save();
                }

                $tagr = new ArticleHasTags();
                $tagr->setAttributes([
                    'tag_id' => $tagf->id,
                    'article_id' => $this->id,
                ]);

                $tagr->save();
            }
        }
    }

    /**
     * Parses the content article and apply all the modifications
     * @return void
     */
    private function parseArticleContent()
    {
        libxml_use_internal_errors(true);
        $content = new DOMDocument();
        $content->loadHTML($this->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        if (count(libxml_get_errors())) {
            foreach (libxml_get_errors() as $error) {
                switch ($error->code) {
                    case 513:
                        $this->addError('content', Yii::t('app', 'ID repetead at line {line}', ['line' => $error->line]));
                }
            }
            libxml_clear_errors();
            return;
        };

        array_map(function (ArticleHasAnchors $val) {
            $val->delete();
        }, ArticleHasAnchors::findAll(['article_id' => $this->id]));

        $links = $content->getElementsByTagName('a');
        foreach ($links as $link) {
            if (strpos($link->getAttribute('id'), "anchor-") !== false) {
                $anchor = new ArticleHasAnchors();
                $anchor->setAttributes([
                    'article_id' => $this->id,
                    'anchor_id' => $link->getAttribute('id'),
                    'content' => $link->parentNode->nodeValue
                ]);
                $anchor->save();
            };
        }

        $images = $content->getElementsByTagName('img');
        foreach ($images as $img) {
            if (strpos($img->getAttribute('id'), 'img-') === false) {
                $data = $img->getAttribute('src');

                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $tempPath = Media::PATH_TO_TEMPORARY . time() . '.png';
                if (file_put_contents($tempPath, $data)) {
                    $con = Yii::$app->db->beginTransaction();

                    try {

                        $imgC = getimagesize($tempPath);

                        if (isset($imgC[0]) && isset($imgC[1])) {
                            if ($imgC[0] > 1200 || $imgC[1] > 1200) {
                                $this->addError('size', Yii::t('app', 'image in line {line} is to big (max 1200px widht, height)', ['line' => $img->getLineNo()]));
                                throw new Exception(Yii::t('app', 'Error validating size, too big!'));
                            }
                        } else {
                            $this->addError('image', Yii::t('app', 'image in line {line} is not an image?', ['line' => $img->getLineNo()]));
                            throw new Exception(Yii::t('app', 'error validating image'));
                        }

                        $table_name = 'article_has_media';
                        Media::generateFoldersByTableName($table_name);

                        $newA = new ArticleHasMedia();
                        $newA->article_id = $this->id;
                        $newA->save(false);

                        $newM = new Media();
                        $newM->setAttributes([
                            'path' => 'uploads/' . date("Y") . '/' . date("m") . '/' . $table_name . '/',
                            'file_name' => $table_name . '-' . $newA->id . '.png',
                            'titol' => $img->getAttribute('title') ?: null,
                            'user_id' => Yii::$app->user->identity->id,
                            'es_imatge' => 1,
                        ]);
                        $newM->save();

                        $newA->media_id = $newM->id;
                        $newA->save();

                        $newT = new MediaHasTables();
                        $newT->setAttributes([
                            'media_id' => $newM->id,
                            'table_name' => $table_name,
                            'table_id' => $newA->id,
                        ]);
                        $newT->save();

                        file_put_contents($newM->path . $newM->file_name, $data);
                        unlink($tempPath);

                        $img->setAttribute('id', 'img-' . $newA->id);

                        // todo cambiar a la api pa q xuto en public i privat
                        $sizes = [
                            $img->getAttribute('width') && $img->getAttribute('width') < 1201 ? $img->getAttribute('width') : $imgC[0],
                            $img->getAttribute('height') && $img->getAttribute('height') < 1201 ? $img->getAttribute('height') : $imgC[1],
                        ];

                        $img->setAttribute('src', Url::base(true) . '/' . Media::img($newA->id, $table_name, $sizes));

                        $con->commit();
                    } catch (Exception $e) {
                        $this->addError('exception', $e);
                        $con->rollback();
                    }


                }
            }
        }
        // Save the modifications in dom
        $this->content = $content->saveHTML();
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
        return $this->hasOne(ArticleHasContinuation::className(), ['continuation_id' => 'id']);
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
}
