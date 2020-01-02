<?php

namespace app\models;

use Exception;
use Imagine\Image\ImageInterface;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\imagine\Image;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property int $user_id
 * @property string $titol
 * @property string $descripcio
 * @property string $path
 * @property string $file_name
 * @property string $url
 * @property string $fullPath
 * @property User $user
 */
class Media extends ActiveRecord
{
    /* TABLES */
    const TBL_USER = 'user';
    const TBL_ARTICLE = 'article';
    const TBL_MEDIA = 'media';
    const TBL_CATEGORY = 'category';
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
            [['user_id', 'borrat', 'es_imatge'], 'integer'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xlsx, pptx, pdf, txt, doc, dot, docx, jpeg, jpg, png', 'maxFiles' => 10],
            [['titol', 'path', 'file_name'], 'string', 'max' => 500],
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
    public static function img($table_id, $table, $size, $output = self::OP_URL)
    {
        $media = Media::find()->alias('m')
            ->leftJoin('media_has_tables as mt', 'mt.media_id = m.id')
            ->where(['mt.table_name' => $table, 'mt.table_id' => $table_id])->orderBy('id desc')
            ->one();

        $size = Media::limit_size($size);

        if ($media) {
            if(is_file($media->getFullPath($size))) {
                return $media->getFullPath($size);
            } else if (file_exists($media->getFullPath())) {
                return Media::generate_image($media->file_name, $media->path, $size);
            }
        }

        switch ($table) {
            case Media::TBL_USER:
                return Media::generate_image('user.png', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY);
            case Media::TBL_ARTICLE:
                return Media::generate_image('article.jpg', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY);
            default:
                return Media::generate_image('404.jpg', Media::PATH_TO_DEFAULTS, $size, Media::PATH_TO_TEMPORARY);
        }
    }

    /**
     * Creates and image with the specified $size, preppends the size to the filename
     * Has a fallback that always returns 404 if something went wrong
     * @param $filename
     * @param $path
     * @param $size
     * @param $savepath
     * @return bool|ImageInterface
     */
    public static function generate_image($filename, $path, $size, $savepath = false)
    {
        $newFileName = join('', $size) . $filename;
        $savepath = $savepath ?: $path;

        try {
            if ($size[0] !== null && $size[1] !== null) {
                Image::thumbnail($path . $filename, $size[0], $size[1])
                    ->save($savepath . $newFileName, ['quality' => 100]);
            }
        } catch (Exception $e) {
            return self::generate_image('404.jpg', Media::PATH_TO_DEFAULTS, $size, $savepath);
        }

        return $savepath . $newFileName;
    }

    /**
     * Limit the size of photos so they don't trick the server into megaimages
     * @param array $size
     * @return array|bool
     */
    public static function limit_size($size)
    {
        if (count($size) === 2 && isset($size[0]) && isset($size[1])) {
            foreach ($size as $i => $s) {
                $s = abs($s);
                $s = is_numeric($s) ? $s : 250;
                $s = $s < 1200 ? $s : 250;
                $size[$i] = $s;
            }
            return $size;
        }

        return [null, null];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param array $size
     * @return string
     */
    public function getFullPath($size = [null, null])
    {
        return $this->path . join('', $size) . $this->file_name;
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

    /**
     * Generates the following tables based on a table name
     * @param $table_name
     */
    public static function generateFoldersByTableName($table_name)
    {
        if (!is_dir('uploads/' . date("Y") . '/')) mkdir('uploads/' . date("Y"), 0755); // carpeta any
        if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/')) mkdir('uploads/' . date("Y") . '/' . date("m"), 0755); // carpeta mes
        if (!is_dir('uploads/' . date("Y") . '/' . date("m") . '/' . $table_name . '/')) mkdir('uploads/' . date("Y") . '/' . date("m") . '/' . $table_name, 0755); // carpeta tipo
    }

}
