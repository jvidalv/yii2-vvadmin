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
        $mapSources = self::getTypesAsKeys();

        foreach($sources as $source){
            switch($source->type){
                case self::T_LANGUAGE:
                    array_push($mapSources[self::T_LANGUAGE], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_SO:
                    array_push($mapSources[self::T_SO], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_LIBRARY:
                    array_push($mapSources[self::T_LIBRARY], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_FRAMEWORK:
                    array_push($mapSources[self::T_FRAMEWORK], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_OTHER:
                    array_push($mapSources[self::T_OTHER], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_SOURCE:
                    array_push($mapSources[self::T_SOURCE], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
                case self::T_PACKAGE:
                    array_push($mapSources[self::T_PACKAGE], ['name' => $source->name, 'version' => $source->version, 'url' => $source->url]);
                    break;
            }
        }

        return $mapSources;
    }
}