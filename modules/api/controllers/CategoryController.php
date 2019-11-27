<?php

namespace app\modules\api\controllers;


use app\modules\api\models\Category;

class CategoryController extends ApiController
{
    public function actionAll(){
        return Category::find()->all();
    }
}