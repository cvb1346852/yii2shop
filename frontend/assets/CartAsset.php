<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CartAsset extends AssetBundle
{
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/cart.css',
        'style/fillin.css',
        'style/success.css',
        'style/footer.css',

    ];
    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/cart1.js',
        'js/cart2.js',
    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

