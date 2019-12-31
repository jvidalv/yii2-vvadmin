<?php

namespace app\modules\api\controllers;

use app\modules\api\components\ApiAuth;
use Exception;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Class ApiController
 * @package app\modules\api\controllers
 */
class ApiController extends Controller
{
    /**
     * @param $action
     * @return bool|string
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        $response = true;

        try {
            parent::beforeAction($action);
        } catch (Exception $e){
            throw new HttpException(500, $e->getMessage(), 100);
        }

        return $response;
    }

    /**
     * @return array
     */
    public static function allowedDomains() {
        return [
            '*'
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => '\yii\filters\Cors',
            'cors' => [
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => ApiAuth::className(),
        ];
        return $behaviors;
    }

    /**
     * @return string
     */
    public function actionIndex(){
        return 'It works! Check my blog at https://vblogv.io.';
    }

}
