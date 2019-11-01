<?php

namespace app\modules\api\controllers;

use yii\rest\Controller;
use yii\web\Response;
use app\modules\api\components\ApiAuth;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
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
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return true;
    }
}
