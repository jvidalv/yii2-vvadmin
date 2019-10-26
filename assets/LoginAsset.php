<?php

namespace app\assets;

use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/CoolAdmin/css/font-face.css',
        'themes/CoolAdmin/css/theme.css',
        'css/login.css',
        'css/site.css',
    ];
    public $js = [
        'js/login.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
