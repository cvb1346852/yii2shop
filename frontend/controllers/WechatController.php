<?php
namespace frontend\controllers;


use backend\models\Goods;
use EasyWeChat\Message\News;
use frontend\models\Address;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller{
    public $layout=false;
    //关闭csrf跨站验证
    public $enableCsrfValidation = false;
    //用于与微信通信
    public function actionIndex(){
        $app = new Application(\Yii::$app->params['wechat']);
        $app->server->setMessageHandler(function ($message) {
            switch ($message->MsgType){
                case 'text'://文本消息
                    switch ($message->Content){
                        case '成都':
                            $xml = simplexml_load_file('http://flash.weather.com.cn/wmaps/xml/sichuan.xml');
                            foreach ($xml as $city){
                                if ($city['cityname'] == '成都'){
                                    return '成都的天气是：'.$city['stateDetailed'];
                                }
                            }
                            break;
                        case '注册':
                            $url = Url::to(['member/register'],true);
                            return '注册：'.$url;
                            break;
                        case '活动':
                            $news1 = new News([
                                'title'       => '开心到飞起',
                                'description' => '开心到飞起',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://img3.imgtn.bdimg.com/it/u=1242602247,1983187764&fm=11&gp=0.jpg',
                            ]);
                            $news2 = new News([
                                'title'       => '容易吗我',
                                'description' => '图文信息的描述...',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://img4.imgtn.bdimg.com/it/u=635986344,1401803788&fm=26&gp=0.jpg',
                            ]);
                            return [$news1,$news2];
                            break;
                        case '解除绑定':
                            $openid = $message->FromUserName;
                            $member = Member::findOne(['openid'=>$openid]);
                            if ($member == null){
                                return '请先绑定 ->'.Url::to(['wechat/login'],true);//$this->redirect(['wechat/login']);
                            }else{
                                Member::updateAll(['openid'=>''],'id='.$member->id);
                                return '解绑成功';
                            }
                            break;
                        case '帮助':
                            return '您可以发送 优惠、解除绑定 等信息';
                            break;
                        case '优惠':
                            $goodses = Goods::findAll(['status'=>2]);
                            $chuxiao = [];
                            foreach ($goodses as $k=>$goods){
                                $k = new News([
                                    'title'       => $goods->name,
                                    'description' => $goods->name,
                                    'url'         => 'http://jp.xxshop.xin/goods/goods-content.html?id='.$goods->id,
                                    'image'       => 'http://jph.xxshop.xin'.$goods->logo,
                                ]);
                                $chuxiao[]=$k;
                            }
                            return $chuxiao;
                            break;

                    }
                    return $message->Content;
                    break;
                case 'event'://事件   //事件的类型   $message->Event  //事件的key值  $message->EventKey
                    if ($message->Event == 'CLICK'){
                        if ($message->EventKey == 'jx'){
                            $goodses = Goods::findAll(['status'=>2]);
                            $chuxiao = [];
                            foreach ($goodses as $k=>$goods){
                                $k = new News([
                                    'title'       => $goods->name,
                                    'description' => $goods->name,
                                    'url'         => 'http://jp.xxshop.xin/goods/goods-content.html?id='.$goods->id,
                                    'image'       => 'http://jph.xxshop.xin'.$goods->logo,
                                ]);
                                $chuxiao[]=$k;
                            }
                           /* $news1 = new News([
                                'title'       => '开心到飞起',
                                'description' => '开心到飞起',
                                'url'         => Url::to(['goods/']),
                                'image'       => 'http://img3.imgtn.bdimg.com/it/u=1242602247,1983187764&fm=11&gp=0.jpg',
                            ]);
                            $news2 = new News([
                                'title'       => '容易吗我',
                                'description' => '图文信息的描述...',
                                'url'         => 'http://www.baidu.com',
                                'image'       => 'http://img4.imgtn.bdimg.com/it/u=635986344,1401803788&fm=26&gp=0.jpg',
                            ]);*/
                            return $chuxiao;
                        }
                    }
                    return $message->Event.'事件,key值为：'.$message->EventKey;
                    break;
            }
        });
        $response = $app->server->serve();
// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
    }

    public function actionSetMenu(){
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $button = [
            [
                'type' => 'click',
                'name' => '促销商品',
                'key' => 'jx',
            ],
            [
                "type" => "view",
                "name" => "在线商城",
                "url"  => Url::to(['member/index'],true)
            ],
            [
                'name' => '个人中心',
                'sub_button' => [
                    [
                        "type" => "view",
                        "name" => "绑定账户",
                        "url"  => Url::to(['wechat/login'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/order'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "收货地址",
                        "url"  => Url::to(['wechat/address'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "修改密码",
                        "url"  => Url::to(['wechat/edit-pwd'],true)
                    ],
                    /*[
                        "type" => "view",
                        "name" => "openid",
                        "url"  => Url::to(['wechat/user'],true)
                    ],*/
                ]
            ]
        ];
        $menu->add($button);
        $menus = $menu->all();
        var_dump($menus);
    }

    //个人中心
    public function actionUser()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        var_dump($openid);
    }
    //授权回调
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
//        var_dump($user->getId());
        //将openid放入session
        \Yii::$app->session->set('openid',$user->getId());
        return $this->redirect([\Yii::$app->session->get('redirect')]);
    }

    public function actionLogin(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }

        $request = \Yii::$app->request;
        if ($request->isPost){
            $member = Member::findOne(['username'=>$request->post('usernmae')]);
            if ($member != null && \Yii::$app->security->validatePassword($request->post('password'),$member->password_hash)){
                Member::updateAll(['openid'=>$openid],'id='.$member->id);
                if (\Yii::$app->session->get('redirect')){
                    return $this->redirect([\Yii::$app->session->get('redirect')]);
                }
                echo '成功';exit;
            }else{
                echo '失败';exit;
            }
        }
        return $this->render('login');
    }
    //订单
    public function actionOrder(){
        $openid = \Yii::$app->session->get('openid');
        \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权

            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }

        $member = Member::findOne(['openid'=>$openid]);
        if ($member){
            $models = Order::findAll(['member_id'=>$member->id]);
            $this->layout='goods';
            return $this->render('../goods/order-list',['models'=>$models]);
            var_dump($order);
        }else{
            return $this->redirect(['wechat/login']);
        }
    }
    //地址
    public function actionAddress(){
        $openid = \Yii::$app->session->get('openid');
        \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }

        $member = Member::findOne(['openid'=>$openid]);
        if ($member){
            $model = new Address();
            $addresses = Address::findAll(['member_id'=>$member->id]);
            $this->layout='goods';
            return $this->render('../address/index',['addresses'=>$addresses,'model'=>$model]);
            var_dump($address);
        }else{
            return $this->redirect(['wechat/login']);
        }
    }
    //修改密码
    public function actionEditPwd(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        $request = \Yii::$app->request;
        if ($request->isPost){
            $member = Member::findOne(['openid'=>$openid]);
            if ($request->post('pwd_1') == $request->post('pwd_2')){
                if ($member && \Yii::$app->security->validatePassword($request->post('old_pwd'),$member->password_hash)){
                    Member::updateAll(['password_hash'=>\Yii::$app->security->generatePasswordHash($request->post('pwd_1'))],'id='.$member->id);
                    echo '密码修改成功';exit;
                }else{
                    echo '旧密码错误';exit;
                }
            }else{
                echo '确认密码必须与新密码一致';exit;
            }
        }
        return $this->render('edit-pwd');
    }
}