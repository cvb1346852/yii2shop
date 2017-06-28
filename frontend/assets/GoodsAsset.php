<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class GoodsAsset extends AssetBundle
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
        'style/home.css',
        'style/address.css',
        'style/goods.css',
        'style/list.css',
        'style/common.css',
        'style/order.css',
        'style/bottomnav.css',
        'style/footer.css',
        'style/jqzoom.css',
    ];
    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/header.js',
        'js/home.js',
        'js/list.js',
        'js/goods.js',
        'js/jqzoom-core.js',
    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

