<?php

namespace app\modules\api\controllers;

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
}