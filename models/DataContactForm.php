<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_contact_form".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property string $date
 */
class DataContactForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_contact_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'message'], 'required'],
            [['date'], 'safe'],
            [['name', 'email'], 'string', 'max' => 100],
            [['message'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'message' => 'Message',
            'date' => 'Date',
        ];
    }
}
