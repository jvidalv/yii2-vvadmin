<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Category;

/**
 * Class CategoryController
 * @package app\modules\api\controllers
 */
class CategoryController extends ApiController
{
    public function actionAll()
    {
        return Category::find()->all();
    }
}