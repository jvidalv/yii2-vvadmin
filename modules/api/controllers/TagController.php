<?php

namespace app\modules\api\controllers;


use app\modules\api\models\Tag;

/**
 * Class TagController
 * @package app\modules\api\controllers
 */
class TagController extends ApiController
{
    public function actionAll()
    {
        return Tag::find()->all();
    }
}