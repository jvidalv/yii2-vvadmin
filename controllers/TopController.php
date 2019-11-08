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
     * Sets language session
     */
    private function setLanguage()
    {
        if (Yii::$app->user->isGuest && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langFound = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($langFound, ['ca', 'es', 'en'])) {
                Yii::$app->language = $langFound;
            } else {
                Yii::$app->language = 'en';
            }
        } else {
            Yii::$app->language = Yii::$app->user->isGuest ? 'en' : Yii::$app->user->identity->language->code;
        }
    }
}
