<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * To control session related things
 */
class TopController extends Controller
{

    public function init()
    {
        parent::init();
        $app = \Yii::$app;
        $this->setLanguage();
    }

    /**
     * Checks for language and assings the right value
     */
    private function setLanguage()
    {
        $session = Yii::$app->session;

        if (Yii::$app->user->isGuest) {
            $langFound = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($langFound, ['ca', 'es', 'en'])) {
                Yii::$app->language = $langFound;
            } else {
                Yii::$app->language = 'en';
            }
        } else {
            Yii::$app->language = Yii::$app->user->identity->language->code;
        }

    }
}
