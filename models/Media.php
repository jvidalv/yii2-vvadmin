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
 * @property User $user
 */
class Media extends \yii\db\ActiveRecord
{
    /* camp per a filtrar */
    const MINIATURA = 'miniatura-';

    /* camp per pujar fitxers */
    const THUMB65 = 'thumb-very-little-';

    /* prefix de les imatges pujades */
    const THUMB150 = 'thumb-little-';
    const THUMB250 = 'thumb-';
    const THUMB750 = 'thumb-long-';
    const USER = 'user';
    const PDF = '/images/pdf-default.png';
    // tipos de objectes
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
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx, pptx, pdf, txt, doc, dot, docx, jpeg, jpg, png', 'maxFiles' => 10],
            [['titol', 'path', 'file_name', 'tipo'], 'string', 'max' => 500],
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
            'titol' => Yii::t('app', 'Títol'),
            'descripcio' => Yii::t('app', 'Descripció'),
            'file_name' => Yii::t('app', 'Nom del fitxer'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    // guardem al objecte concret, espere una id y el tipo de OBJECTE
    public function guardarObjecte($id, $tipo)
    {
        switch ($tipo) {
            case static::USER:
                $user = User::findOne($id);
                $user->media_id = $this->id;
                return $user->save();;
                break;
        }
        return false;
    }

    /**
     * @param $id
     * @param $tipo
     * @return bool
     */
    public function esborrarMedia($id, $tipo)
    {
        switch ($tipo) {
            case static::USER:
                $user = User::findOne($id);
                $user->media_id = null;
                return $user->save();
                break;
        }
        return false;
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
                case 65:
                    $string = $string . $this::THUMB65;
                    break;
                case 150:
                    $string = $string . $this::THUMB150;
                    break;
                case 250:
                    $string = $string . $this::THUMB250;
                    break;
                case 750:
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
