<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_has_sources".
 *
 * @property int $id
 * @property int $article_id
 * @property string $type language - library - framework - package - source
 * @property string $name
 * @property string $version
 * @property string $url
 * @property int $visible
 */
class ArticleHasSources extends \yii\db\ActiveRecord
{
    const T_LANGUAGE = 'language';
    const T_LIBRARY = 'library';
    const T_FRAMEWORK = 'framework';
    const T_PACKAGE = 'package';
    const T_SOURCE = 'source';
    const T_SO = 'so';
    const T_OTHER = 'other';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_has_sources';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required', 'whenClient' => '() => false'],
            [['article_id', 'visible'], 'integer'],
            [['type', 'version'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_id' => Yii::t('app', 'Article ID'),
            'type' => Yii::t('app', 'type'),
            'name' => Yii::t('app', 'name'),
            'version' => Yii::t('app', 'version'),
            'url' => Yii::t('app', 'url'),
            'visible' => Yii::t('app', 'visible'),
        ];
    }

    /**
     * All types of sources
     * @return array
     */
    public static function getTypes()
    {
        return [self::T_LANGUAGE => Yii::t('app', 'languages'), self::T_SO => Yii::t('app', 'OS'), self::T_LIBRARY => Yii::t('app', 'library'), self::T_FRAMEWORK => Yii::t('app', 'framework'), self::T_PACKAGE => Yii::t('app', 'package'), self::T_SOURCE => Yii::t('app', 'source'), self::T_OTHER => Yii::t('app', 'other')];
    }

    /**
     * Returns an array of arrays with labels as keys for the api to consume
     * @return array
     */
    public static function getTypesLabelsAsKeys()
    {
        return [Yii::t('app', 'languages') => [], Yii::t('app', 'OS') => [], Yii::t('app', 'library') => [], Yii::t('app', 'framework') => [], Yii::t('app', 'package') => [], Yii::t('app', 'source') => [], Yii::t('app', 'other') => []];
    }


}
