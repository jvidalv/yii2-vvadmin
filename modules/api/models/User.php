<?php

namespace app\modules\api\models;

class User extends \app\models\User
{
    public function fields()
    {
        return [
            'id',
            'name' => function(){
                return $this->getFullName();
            }
        ];
    }

}