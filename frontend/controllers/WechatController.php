<?php
namespace frontend\controllers;

use yii\web\Controller;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller{
    //关闭csrf跨站验证
    public $enableCsrfValidation = false;
    //用于与微信通信
    public function actionIndex(){

        $app = new Application(\Yii::$app->params['wechat']);
        $response = $app->server->serve();
// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
    }

}