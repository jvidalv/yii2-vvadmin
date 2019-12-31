<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => YII_ENV_DEV ? 'mysql:host=localhost;dbname=vblogv' : 'mysql:host=104.248.36.173;dbname=vvlog',
    'username' => YII_ENV_DEV ? 'root' : 'vidal',
    'password' => YII_ENV_DEV ? '' : 'vvlog2020',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
