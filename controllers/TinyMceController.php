<?php


namespace app\controllers;

use app\models\Media;
use Yii;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class TinyMceController extends \yii\base\Controller
{

    public function actionUploadImageFromTiny()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $getParams = Yii::$app->request->get();
        $postParams = Yii::$app->request->post();

        return ['location' => 'eioooooooo'];
    }

}