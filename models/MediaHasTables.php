<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media_has_tables".
 *
 * @property int $id
 * @property int $media_id
 * @property string $table_name
 * @property int $table_id
 */
class MediaHasTables extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media_has_tables';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'media_id', 'table_id'], 'integer'],
            [['table_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'table_name' => Yii::t('app', 'Table Name'),
            'table_id' => Yii::t('app', 'Table ID'),
        ];
    }
}
