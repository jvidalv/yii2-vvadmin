<?php

return [
    'class' => 'yii\db\Connection' ,
    'dsn' => YII_ENV_DEV ? 'mysql:host=localhost;dbname=vblogv' : 'mysql:host=bbdd.fempoble.app;dbname=ddb133032',
    'username' => YII_ENV_DEV ? 'root' : 'ddb133032',
    'password' => YII_ENV_DEV ? '' : 'XUq95x7mD;[2',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
