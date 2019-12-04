<?php

namespace app\modules\api\models;

class Tag extends \app\models\Tag
{
    public $ca, $es, $en;

    public function fields()
    {
        return [
            'id',
            'slug',
            'ca' => 'name_ca',
            'es' => 'name_es',
            'en' => 'name_en',
        ];
    }
}