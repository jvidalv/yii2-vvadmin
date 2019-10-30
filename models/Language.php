<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 */
class Language extends \yii\db\ActiveRecord
{

    const LANG_CA = 'ca';
    const LANG_ES = 'es';
    const LANG_EN = 'en';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 30],
            [['code'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
        ];
    }

    public static function isCA()
    {
        return Yii::$app->language === self::LANG_CA;
    }

    public static function isES()
    {
        return Yii::$app->language === self::LANG_ES;
    }

     public static function isEN()
    {
        return Yii::$app->language === self::LANG_ES;
    }
}
