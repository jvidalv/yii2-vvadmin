<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name_ca
 * @property string $name_es
 * @property string $name_en
 * @property string $description_ca
 * @property string $description_es
 * @property string $description_en
 * @property int $priority
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ca', 'name_es', 'name_en', 'code'], 'required'],
            [['code'], 'unique'],
            [['priority'], 'integer'],
            [['name_ca', 'name_es', 'name_en'], 'string', 'max' => 30],
            [['description_ca', 'description_es'], 'string', 'max' => 220],
            [['description_en'], 'string', 'max' => 22],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'code'),
            'name_ca' => Yii::t('app', 'Name Ca'),
            'name_es' => Yii::t('app', 'Name Es'),
            'name_en' => Yii::t('app', 'Name En'),
            'description_ca' => Yii::t('app', 'Description Ca'),
            'description_es' => Yii::t('app', 'Description Es'),
            'description_en' => Yii::t('app', 'Description En'),
            'priority' => Yii::t('app', 'Priority'),
        ];
    }
}
