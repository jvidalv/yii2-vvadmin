<?php

namespace app\modules\api\controllers;


use app\modules\api\models\Tag;

class TagController extends ApiController
{
    public function actionAll(){
        return Tag::find()->all();
    }
}