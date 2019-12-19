<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_user_cookies".
 *
 * @property int $id
 * @property resource $data
 * @property string $date
 */
class DataUserCookies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_user_cookies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Data'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
