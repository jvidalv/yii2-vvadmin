<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property int $user_id
 * @property string $titol
 * @property string $descripcio
 *
 * @property string $url
 * @property string $fullPath
 * @property User $user
 */
class Media extends \yii\db\ActiveRecord
{
    /* TABLES */
    const TBL_USER = 'user';
    const TBL_ARTICLE = 'article';
    const TBL_MEDIA = 'media';
    const TBL_ARTICLE_HAS_MEDIA = 'article_has_media';

    /* OUTPUTS */
    const OP_IMAGE = 'image';
    const OP_URL = 'path';

    /* PATHS */
    const PATH_TO_DEFAULTS = 'images/defaults/';
    const PATH_TO_TEMPORARY = 'images/temporary/';

    /* filters */
    public $general;

    /* preview quant no es imatge */
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media';
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
                'attribute' => 'file_name',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            ['table', 'default', 'value' => self::TBL_MEDIA],
            [['user_id', 'borrat', 'es_imatge', 'table_id'], 'integer'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx, pptx, pdf, txt, doc, dot, docx, jpeg, jpg, png', 'maxFiles' => 10],
            [['titol', 'path', 'file_name', 'table'], 'string', 'max' => 500],
            [['descripcio'], 'string', 'max' => 1000],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'titol' => Yii::t('app', 'title'),
            'table' => Yii::t('app', 'origin'),
            'descripcio' => Yii::t('app', 'description'),
            'path' => Yii::t('app', 'path to file'),
            'file_name' => Yii::t('app', 'filename'),
        ];
    }

    /**
     * Returns and image base64 or an url (default) depending on the parameters
     * todo method image base 64
     * @param $table_id
     * @param $table
     * @param array $size
     * @param string $output
     * @return string
     */
    public static function img($table_id, $table, $size = [], $output = self::OP_URL)
    {
        $media = Media::findOne(['table' => $table, 'table_id' => $table_id]);

        if($media){
            return $media->getFullPath($size);
        }

        return self::img_fallback($table, $output);
    }

    /**
     * Fallback in case image does not exist
     * @param $table
     * @param string $output
     * @return string
     */
    public static function img_fallback($table, $output = self::OP_URL)
    {
        switch ($table) {
            case MEDIA::TBL_ARTICLE:
                return 'images/defaults/65-article.png';
            case MEDIA::TBL_USER:
                return 'images/defaults/user.png';
            default:
                return 'images/defaults/404.jpg';
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getFullPath($size = [])
    {
        return $size && count($size) === 2 ?
            $this->path . $size[0] . '-' . $size[1] . '-' . $this->file_name
            :
            $this->path . $this->file_name;
    }

    /**
     * @param $id
     * @param $table
     * @return bool
     */
    public function guardarObjecte($id, $table)
    {
        switch ($table) {
            case static::TBL_USER:
                $user = User::findOne($id);
                $user->media_id = $this->id;
                return $user->save();
            case static::TBL_ARTICLE:
                $article = Article::findOne($id);
                $article->media_id = $this->id;
                return $article->save();
        }

        return false;
    }

    /**
     * @param $id
     * @param $tipo
     * @return bool
     */
    public static function esborrarMedia($id, $table)
    {
        switch ($table) {
            case static::TBL_USER:
                $model = User::findOne($id);
                break;
            case static::TBL_ARTICLE:
                $model = Article::findOne($id);
                break;
        }

        $model->media_id = null;
        return $model->save();
    }

    /* retorne imatge segons parametre - 150 - 250 - 750 */
    public function getUrlImatge($tipo = 'normal')
    {

        if ($this->es_imatge) {

            $string = $this->path;

            switch ($tipo) {
                case 'miniatura':
                    $string = $string . $this::MINIATURA;
                    break;
                case $this::THUMB65:
                    $string = $string . $this::THUMB65;
                    break;
                case $this::THUMB150:
                    $string = $string . $this::THUMB150;
                    break;
                case  $this::THUMB250:
                    $string = $string . $this::THUMB250;
                    break;
                case $this::THUMB500:
                    $string = $string . $this::THUMB500;
                    break;
                case $this::THUMB750:
                    $string = $string . $this::THUMB750;
                    break;
            }

            return $string . $this->file_name;

        }

        return $this::PDF;
    }

    /* retorne url */
    public function getUrl()
    {
        return $this->path . $this->file_name;
    }

}
