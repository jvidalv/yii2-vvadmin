<?php

namespace app\models;

use Yii;

use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "noticia".
 *
 * @property int $id
 * @property int $user_id
 * @property int $super
 * @property string $titol
 * @property string $capcalera
 * @property resource $cos
 * @property string $imatge
 * @property string $slug
 * @property int $data_publicacio
 * @property int $updated_at
 * @property int $created_at
 *
 * @property User $user
 */
class Noticia extends \yii\db\ActiveRecord
{
    /* camp per a filtrar */
    public $general;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'noticia';
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
               'attribute' => 'titol',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id',  'titol', 'data_publicacio'], 'required'],
            [['user_id', 'super', 'updated_at', 'created_at', 'borrat'], 'integer'],
            [['cos'], 'string'],
            [['titol'], 'string', 'max' => 500],
            [['capcalera'], 'string', 'max' => 1000],
            [['data_publicacio'], 'safe'],
            [['imatge'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxWidth' => 1024, 'maxHeight' => 1024, 'maxSize' => 1024 * 1024],
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
            'user_id' => 'Usuari',
            'super' => 'Super',
            'titol' => 'Títol',
            'capcalera' => 'Capcalera',
            'cos' => 'Cos',
            'imatge' => 'Imatge',
            'slug' => 'Slug',
            'data_publicacio' => 'Data de publicació',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /* CUSTOM BEFORE SAVE by Josep
    * Status = true creant now registre, false, actualitzant
    *
    */
    public function beforeSave($status){
      parent::beforeSave ($status);

      /* controlem format dates */
      if($this->data_publicacio) {
        $this->data_publicacio_string = strftime("%A, %a, %e de %B del %G a les %H:%M", strtotime($this->data_publicacio));
      }

      return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return data_publicacio formatada
     */
    public function getDataPublicacio()
    {
        return strftime("%a, %e de %B del %G a les %H:%M", strtotime($this->data_publicacio));
    }
}
