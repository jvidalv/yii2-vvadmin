<?php

namespace app\modules\api\controllers;

use app\modules\api\components\ApiAuth;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => ApiAuth::className(),
        ];
        return $behaviors;
    }

    public function actionIndex(){
        return 'It works! Check my blog at https://vblogv.io.';
    }
}
