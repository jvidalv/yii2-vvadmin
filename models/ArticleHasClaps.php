<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_claps".
 *
 * @property int $id
 * @property int $article_has_translations_id
 * @property string $voter_ip
 */
class ArticleHasClaps extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_claps';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_has_translations_id'], 'integer'],
            [['voter_ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_has_translations_id' => Yii::t('app', 'Article Has Translations ID'),
            'voter_ip' => Yii::t('app', 'Voter Ip'),
        ];
    }
}
