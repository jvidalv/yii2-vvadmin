<?php


namespace app\modules\api\controllers;


use app\modules\api\models\Article;
use app\modules\api\models\ArticleHasTranslations;
use app\modules\api\models\User;
use Faker\Factory;
use Yii;
use yii\rest\ActiveController;
use yii\rest\Controller;

class UserController extends ApiController
{
    public function actionAll(){
        return User::find()->with('info')->all();
    }
}