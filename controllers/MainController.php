<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

//Controlem tot lo que pasa a un beforeaction, tots han dextendre deste menos lo SiteController
class MainController extends Controller
{
      /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    function init()
    {
        parent::init();
        $app = \Yii::$app;
    }

    // custom before action, controlem login i permisos
    function beforeAction($action){
      if (!parent::beforeAction($action)) {
        return false;
      }

      if(Yii::$app->user->isGuest){
        return $this->redirect(['site/login'])->send();
      }

      return true;
    }
}
