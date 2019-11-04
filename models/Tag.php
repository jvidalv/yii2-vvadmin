<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name_ca
 * @property string $name_es
 * @property string $name_en
 * @property int $priority Priority in wich they are rendered
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

     /**
     * @inheritdoc
     */
     public function behaviors()
     {
         return [
             [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name_en',
             ],
         ];
     }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ca', 'name_es', 'name_en'], 'required'],
            [['name_ca', 'name_es', 'name_en'], 'unique'],

            [['priority'], 'integer'],
            [['name_ca', 'name_es', 'name_en', 'slug'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ca' => Yii::t('app', 'name'),
            'name_es' => Yii::t('app', 'name'),
            'name_en' => Yii::t('app', 'name'),
            'priority' => Yii::t('app', 'priority'),
        ];
    }
}
