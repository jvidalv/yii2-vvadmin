<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;

//Controlem tot lo que pasa a un beforeaction, tots han dextendre deste menos lo SiteController
class MainController extends TopController
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

    public function init()
    {
        parent::init();
        $app = \Yii::$app;
    }

    // custom before action, controlem login i permisos
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login'])->send();
        }

        return true;
    }

}
