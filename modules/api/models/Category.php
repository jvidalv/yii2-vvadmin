<?php

namespace app\modules\api\models;

class Category extends \app\models\Category
{
    public function fields()
    {
        return [
            'id',
            'code',
            'color_hex',
            'ca' => function ($model) {
                return ['name' => $model->name_ca, 'description' => $model->description_ca];
            },
            'es' => function ($model) {
                return ['name' => $model->name_es, 'description' => $model->description_es];
            },
            'en' => function ($model) {
                return ['name' => $model->name_en, 'description' => $model->description_en];
            },
            'image' => function ($model) {
                return  Media::img($model->id, Media::TBL_CATEGORY, [100, 100]);
            }

        ];
    }
}