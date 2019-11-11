<?php


namespace app\controllers;

use Yii;
use yii\base\Controller;
use yii\web\Response;

class TinyMceController extends Controller
{
    public function beforeAction($action)
    {
        if(!parent::beforeAction($action)){
            return false;
        };
        var_dump($_SERVER['HTTP_ORIGIN'] );die();
    }

    public function actionUploadImageFromTiny()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $getParams = Yii::$app->request->get();
        $postParams = Yii::$app->request->post();

        return ['location' => 'eioooooooo'];
    }

}