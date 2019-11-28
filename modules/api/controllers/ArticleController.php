<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Article;
use app\modules\api\models\ArticleHasTranslations;
use Faker\Factory;
use yii\rest\ActiveController;
use yii\rest\Controller;

class ArticleController extends ApiController
{
    public function actionAll(){
        return ArticleHasTranslations::find()->all();
    }
}