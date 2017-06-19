<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'loginUrl'=>'@web/user/login',
            'identityClass' => 'backend\models\User',//验证登陆需要修改为自己有符合接口的类
            'enableAutoLogin' => true,//基于cookie自动登录，需要true
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'qiniu'=>[
            'class' =>\backend\components\Qiniu::className(),
            'accessKey'=>'CLerOU3r5b3ngp3vgjM0n441FOnHziQyGG-lnLi_',
            'secretKey'=>'SNzf1dqdsNBfd7uveVq6EGxWggDMxNh_z6QzdCLW',
            'bucket'=>'yiishop',
            'domain'=>'http://or9rfsq68.bkt.clouddn.com',
        ],
    ],
    'params' => $params,
];
