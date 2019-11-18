<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'admin',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'ca',
    'components' => [
        'assetManager' => [
            'bundles' => [
                'dosamigos\switchinput\SwitchAsset' => [
                    'depends' => ['yii\bootstrap4\BootstrapPluginAsset'],
                ],
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false, // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '/' => 'site/index',
                '/login' => 'site/login',
                'logout' => 'site/logout',

                'noticies' => 'noticia/index',
                'noticia/<slug>-<id:\d+>' => 'noticia/update',

                'media' => 'media/index',
                'categories' => 'category/index',
                'tags' => 'tag/index',

                'articles' => 'article/index',
                'articles/<id:\d+>/<slug>' => 'article/update',

                'users' => 'user/index',
                'settings/<id:\d+>/<slug>' => 'user/update',

            ],
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-mm-dd',
            'datetimeFormat' => 'yyyy-mm-dd HH:mm',
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2kAgxOvLysGvkSFRVBfWHPKxjuoV4yLL',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'timeout' => 60 * 60 * 24 * 14,
            'writeCallback' => function () {
                return [
                    'user_id' => Yii::$app->user->id,
                    'last_write' => time(),
                ];
            },
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                    'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
                ],
            ],
        ],

    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Api',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
