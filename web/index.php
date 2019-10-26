<?php
// Comprobem si local o produccio
$local = $_SERVER["REMOTE_ADDR"] === "127.0.0.1" || $_SERVER["REMOTE_ADDR"] === "::1" ? true : false;
defined('YII_DEBUG') or define('YII_DEBUG', $local);
defined('YII_ENV') or define('YII_ENV', $local ? 'dev' : 'prod');

// Idioma per defecta catala
setlocale(LC_TIME,'ca_ES', 'Catalan_Spain', 'Catalan');

// Activar HTTPS
$_SERVER['HTTPS']='on';

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
