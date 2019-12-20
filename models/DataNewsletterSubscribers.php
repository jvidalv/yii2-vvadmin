<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_newsletter_subscribers".
 *
 * @property int $id
 * @property string $language_id
 * @property string $email
 * @property int $active
 * @property string $date
 */
class DataNewsletterSubscribers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_newsletter_subscribers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['active'], 'integer'],
            [['date'], 'safe'],
            [['language_id'], 'string', 'max' => 3],
            [['email'], 'string', 'max' => 70],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language_id' => 'Language ID',
            'email' => 'Email',
            'active' => 'Active',
            'date' => 'Date',
        ];
    }
}
