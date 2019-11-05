<?php

namespace app\controllers;

use Yii;

class MainController extends TopController
{
    /**
     * @param $action
     * @return bool|void
     * @throws \yii\web\BadRequestHttpException
     */
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
