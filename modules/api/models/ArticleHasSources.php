<?php


namespace app\modules\api\models;

use Yii;

class ArticleHasSources extends \app\models\ArticleHasSources
{
    /**
     * Ready to be api consumed
     * @param $sources
     * @return array
     */
    public static function mapSourcesToLabels($sources)
    {
        $mapSources = self::getTypesLabelsAsKeys();

        foreach($sources as $source){
            switch($source->type){
                case self::T_LANGUAGE:
                    array_push($mapSources[Yii::t('app', 'languages')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_SO:
                    array_push($mapSources[Yii::t('app', 'OS')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_LIBRARY:
                    array_push($mapSources[Yii::t('app', 'library')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_FRAMEWORK:
                    array_push($mapSources[Yii::t('app', 'framework')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_OTHER:
                    array_push($mapSources[Yii::t('app', 'other')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_SOURCE:
                    array_push($mapSources[Yii::t('app', 'source')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_PACKAGE:
                    array_push($mapSources[Yii::t('app', 'package')], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
            }
        }

        return $mapSources;
    }
}