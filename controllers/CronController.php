<?php

namespace app\controllers;

use app\components\VController;
use app\models\Models;
use yii\filters\AccessControl;
use yii\web\Controller;

//Controlem tot lo que pasa a un beforeaction, tots han dextendre deste menos lo SiteController
class CronController extends VController
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
                    ],
                ],
            ],
        ];
    }

}
