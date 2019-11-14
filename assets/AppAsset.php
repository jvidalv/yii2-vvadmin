<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package app\assets
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //  'themes/CoolAdmin/css/font-face.css',
        'themes/CoolAdmin/vendor/mdi-font/css/material-design-iconic-font.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css',
        'css/theme.css',
        'css/site.css'
    ];
    public $js = [
        //'https://code.jquery.com/jquery-3.3.1.slim.min.js',
        // 'themes/CoolAdmin/vendor/bootstrap-4.1/popper.min.js',
        // 'themes/CoolAdmin/vendor/bootstrap-4.1/bootstrap.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
       // 'yii\bootstrap4\BootstrapAsset',
    ];
}
