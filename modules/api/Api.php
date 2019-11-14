<?php

namespace app\modules\api;

use yii\base\Module;

/**
 * api module definition class
 */
class Api extends Module
{

    public $controllerNamespace = 'app\modules\api\controllers';
    public $defaultRoute = 'api';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
