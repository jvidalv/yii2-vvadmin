<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_info".
 *
 * @property int $id
 * @property int $user_id
 * @property string $information_ca
 * @property string $information_es
 * @property string $information_en
 */
class UserHasInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_has_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['information_ca', 'information_es', 'information_en'], 'string', 'max' => 250],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'information_ca' => Yii::t('app', 'Information Ca'),
            'information_es' => Yii::t('app', 'Information Es'),
            'information_en' => Yii::t('app', 'Information En'),
        ];
    }
}
