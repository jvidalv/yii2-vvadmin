<?php


namespace app\modules\api\controllers;


use app\modules\api\models\User;

class UserController extends ApiController
{
    public function actionAll()
    {
        return User::find()->with('info')->all();
    }
}