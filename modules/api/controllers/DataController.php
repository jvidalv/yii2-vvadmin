<?php

namespace app\modules\api\controllers;

use app\modules\api\models\DataContactForm;
use app\modules\api\models\DataNewsletterSubscribers;
use Yii;
use app\modules\api\models\DataUserCookies;

/**
 * Class DataController
 * @package app\modules\api\controllers
 */
class DataController extends ApiController
{
    /**
     * @return bool
     */
    public function actionUserCookies()
    {
        $data = new DataUserCookies();
        $data->setAttributes(
            [
                'data' => json_encode(getallheaders()),
            ]
        );
        return $data->save();
    }

    /**
     * Adds a new email to the newsletter list
     * @return bool
     */
    public function actionNewsletterSubscriber()
    {
        $data = new DataNewsletterSubscribers();
        $data->setAttributes(
            [
                'language_id' =>  Yii::$app->request->get('language_id'),
                'email' => Yii::$app->request->get('email'),
            ]
        );
        return $data->save();
    }

    /**
     * Data that comes from contact form
     * @return bool
     */
    public function actionContactForm()
    {
        $data = new DataContactForm();
        $data->setAttributes(
            [
                'name' => Yii::$app->request->post('name'),
                'email' => Yii::$app->request->post('email'),
                'message' => Yii::$app->request->post('message'),
            ]
        );
        return $data->save();
    }
}