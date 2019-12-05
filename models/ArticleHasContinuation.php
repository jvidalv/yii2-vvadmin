<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_continuation".
 *
 * @property int $id
 * @property int $origen_id
 * @property int $continuation_id
 */
class ArticleHasContinuation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_continuation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['origen_id', 'continuation_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'origen_id' => Yii::t('app', 'Origen ID'),
            'continuation_id' => Yii::t('app', 'Continuation ID'),
        ];
    }
}
