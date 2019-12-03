<?php

namespace app\modules\api\models;

class User extends \app\models\User
{
    public function fields()
    {
        return [
            'id',
            'info' => function () {
                return $this->info ? [
                    'ca' => [
                        'information' => $this->info->information_ca,
                    ],
                    'es' => [
                        'information' => $this->info->information_es,
                    ],
                    'en' => [
                        'information' => $this->info->information_en,
                    ]
                ] : [];
            },
            'name' => function () {
                return $this->getFullName();
            },
            'image' => function () {
                return Media::img($this->id, Media::TBL_USER, [95, 95]);
            }
        ];
    }

}