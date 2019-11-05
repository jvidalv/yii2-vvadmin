<?php

namespace app\modules\api\components;

use yii\filters\auth\AuthMethod;

/**
 * @author Josep Vidal
 */
class ApiAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        return true;
    }
}
