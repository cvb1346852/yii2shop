<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/login.css',
        'style/footer.css',
    ];
    public $js = [

    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
