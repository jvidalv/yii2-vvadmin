<?php

namespace app\assets;

use yii\web\AssetBundle;

class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',
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
