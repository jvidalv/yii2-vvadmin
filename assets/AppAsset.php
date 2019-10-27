<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/CoolAdmin/css/font-face.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css',
        'themes/CoolAdmin/vendor/mdi-font/css/material-design-iconic-font.min.css',
        'css/theme.css',
        'css/site.css'
    ];
    public $js = [
        'themes/CoolAdmin/vendor/bootstrap-4.1/popper.min.js',
        'themes/CoolAdmin/vendor/bootstrap-4.1/bootstrap.min.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
