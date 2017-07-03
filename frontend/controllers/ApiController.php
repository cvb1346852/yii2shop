<?php
namespace frontend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use frontend\models\UpdatePwdForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller{
    public $enableCsrfValidation = false;
    public function init()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init();
    }

    //用户
    public function actionMemberRegister(){
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model = new Member();
            $model->username = $request->post('username');
            $model->password_1 = $request->post('password');
            $model->email = $request->post('email');
            $model->tel = $request->post('tel');
            $model->code = $request->post('code');
            if ($model->validate()){
                $model->save(false);
                return ['status'=>'1', 'msg' => '','username'=>$model->username,'create_at'=>$model->create_at];
            }else{
                return ['status'=>'-1', 'msg' => $model->getErrors(),];
            }
        }else{
            return ['status'=>'-1', 'msg' => '请使用post请求',];
        }
    }
    public function actionMemberLogin(){
        $request = \Yii::$app->request;
        if($request->isPost){
            $model = new LoginForm();
            $model->username = $request->post('username');
            $model->password = $request->post('password');
            $model->rememberMe = $request->post('rememberMe',0);
            if ($model->validate()){
                $model->addCart();
                return ['status'=>'1', 'msg' => '','username'=>$model->username];
            }else{
                return ['status'=>'-1', 'msg' => $model->getErrors(),];
            }
        }else{
            return ['status'=>'-1', 'msg' => '请使用post请求',];
        }
    }
    public function actionLoginOut(){
        $r = \Yii::$app->user->logout();
        if ($r){
            return ['status'=>'1', 'msg' => '','data'=>true];
        }
    }
    public function actionUpdatePwd(){
        $request = \Yii::$app->request;
        if (\Yii::$app->user->isGuest){
            return ['status'=>'-1', 'msg' => '请先登录'];
        }else{
            if($request->isPost){
                $model = new UpdatePwdForm();
                $model->id = \Yii::$app->user->id;
                $model->old_password = $request->post('old_password');
                $model->password_1 = $request->post('password_1');
                $model->password_2 = $request->post('password_2');
                if ($model->validate()){
                    return ['status'=>'1', 'msg' => '','data'=>true];
                }else{
                    return ['status'=>'-1', 'msg' => $model->getErrors()];
                }
            }else{
                return ['status'=>'-1', 'msg' => '请使用post请求',];
            }
        }
    }
    public function actionMember(){
        if (\Yii::$app->user->isGuest){
            return ['status'=>'-1', 'msg' => '请先登录'];
        }else{
            $model = \Yii::$app->user->identity->toArray();
            unset($model['auth_key']);
            unset($model['password_hash']);
            return ['status'=>'1', 'msg' => '','data'=>$model];
        }
    }
    //收获地址
    public function actionAddAddress(){
        if (\Yii::$app->user->isGuest){
            return ['status'=>'-1', 'msg' => '请先登录'];
        }else{
            $request = \Yii::$app->request;
            if ($request->isPost){
                $model = new Address();
                $model->name = $request->post('name');
                $model->member_id = \Yii::$app->user->id;
                $model->province = $request->post('province');
                $model->city = $request->post('city');
                $model->area = $request->post('area');
                $model->detail_address = $request->post('detail_address');
                $model->phone = $request->post('phone');
                $model->status = $request->post('status');
                if ($model->validate()){
                    if ($model->status){//如果选为默认
                        $deflut = Address::findOne(['status'=>1,'member_id'=>\Yii::$app->user->id]);
                        if ($deflut != null){//如果之前有默认地址，把其状态改为0
                            $deflut->status = 0;
                            $deflut->save();
                        }
                    }
                    $model->save();

                    return ['status'=>'1', 'msg' => '','data'=>['name'=>$model->name,'phone'=>$model->phone]];
                }else{
                    return ['status'=>'-1', 'msg' => $model->getErrors()];
                }
            }else{
                return ['status'=>'-1', 'msg' => '请使用post请求',];
            }
        }
    }
    public function actionEditAddress(){
        if (\Yii::$app->user->isGuest){
            return ['status'=>'-1', 'msg' => '请先登录'];
        }else{
            $request = \Yii::$app->request;
            if ($request->isPost){
                $model = Address::findOne(['id'=>$request->post('id'),'member_id'=>\Yii::$app->user->id]);
                if ($model == null){
                    return ['status'=>'-1', 'msg' => '地址未找到',];
                }
                $model->name = $request->post('name');
                $model->province = $request->post('province');
                $model->city = $request->post('city');
                $model->area = $request->post('area');
                $model->area = $request->post('area');
                $model->detail_address = $request->post('detail_address');
                $model->phone = $request->post('phone');
                $old_status = $model->status;
                $model->status = $request->post('status');
                if ($model->validate()){
                    if ($old_status == 0 && $model->status == 1){
                        $deflut = Address::findOne(['status'=>1,'member_id'=>\Yii::$app->user->id]);
                        if ($deflut != null){
                            $deflut->status = 0;
                            $deflut->save();
                        }
                    }
                    $model->save();
                    return ['status'=>'1', 'msg' => '','data'=>true];
                }else{
                    return ['status'=>'-1', 'msg' => $model->getErrors()];
                }
            }else{
                return ['status'=>'-1', 'msg' => '请使用post请求',];
            }
        }
    }
    public function actionDeleteAddress(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $model = Address::findOne(['id' => $request->get('id'), 'member_id' => \Yii::$app->user->id]);
            if ($model == null) {
                return ['status' => '-1', 'msg' => '地址未找到',];
            }
            $model->delete();
            return ['status'=>'1', 'msg' => '','data'=>true];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    public function actionListAddress(){
        $models = Address::find()->where(['member_id' => \Yii::$app->user->id])->asArray()->all();
        if ($models == null){
            return ['status' => '-1', 'msg' => '还没有收获地址',];
        }else{
            return ['status'=>'1', 'msg' => '','data'=>$models];
        }
    }
    //商品分类
    public function actionGoodsCategory(){
        $models = GoodsCategory::find()->orderBy(['tree'=>SORT_ASC,'lft'=>SORT_ASC])->all();
        return ['status'=>'1', 'msg' => '','data'=>$models];
    }
    public function actionChildrenCategory(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $model = GoodsCategory::findOne(['id'=>$request->get('id')]);
            $models = $model->children()->orderBy(['lft'=>SORT_ASC])->all();
            return ['status'=>'1', 'msg' => '','data'=>$models];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    public function actionParentCategory(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $model = GoodsCategory::findOne(['id'=>$request->get('id')]);
            $models = $model->parents()->orderBy(['lft'=>SORT_ASC])->all();
            return ['status'=>'1', 'msg' => '','data'=>$models];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    //商品
    public function actionCateGoods(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $size = $request->get('size',2);
            $page = $request->get('page',1);
            $start = $size*($page-1);
            $keyword = $request->get('keyword');
            $model = GoodsCategory::findOne(['id'=>$request->get('cate_id')]);
            $query = Goods::find();
            if ($model->depth != 2){//不为第三级
                $categorys =$model->leaves()->all();
                $ids = ArrayHelper::map($categorys,'id','id');
                $query->andWhere(['in','goods_category_id',$ids]);
            }else{//第三级
                $query = $query->andWhere(['goods_category_id'=>$model->id]);
            }
            if ($keyword){//搜索
                $query = $query->andWhere(['like','name',$keyword]);
            }
            $total = $query->count();
            $goodses = $query->offset($start)->limit($size)->all();
            return ['status'=>'1', 'msg' => '','data'=>[
                'size' => $size,
                'page' => $page,
                'total' => $total,
                'goodses' => $goodses
            ]];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    public function actionBrandGoods(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $goodses = Goods::find()->where(['brand_id'=>$request->get('brand_id')])->asArray()->all();
            return ['status'=>'1', 'msg' => '','data'=>$goodses];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    //文章
    public function actionArticleCategory(){
        $models = ArticleCategory::find()->all();
        return ['status'=>'1', 'msg' => '','data'=>$models];
    }
    public function actionCateArticle(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $articles = Article::findAll(['article_category_id'=>$request->get('category_id')]);
            return ['status'=>'1', 'msg' => '','data'=>$articles];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    public function actionArticleOneCategory(){
        $request = \Yii::$app->request;
        if ($request->isGet) {
            $article = Article::findOne(['id'=>$request->get('id')]);
            return ['status'=>'1', 'msg' => '','data'=>['name'=>$article->articleCategory->name]];
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    public function actionAddCart(){
        $request = \Yii::$app->request;
        if ($request->isPost){
            $goods_id = $request->post('goods_id');
            $amount = $request->post('amount');
            $goods = Goods::findOne(['id'=>$goods_id]);
            if ($goods == null){
                return ['status'=>'-1', 'msg' => '商品已下架',];
            }
            if (\Yii::$app->user->isGuest){
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                if ($cookie){
                    $cart = unserialize($cookie->value);
                }else{
                    $cart = [];
                }
                if (isset($cart[$goods_id])){
                    $cart[$goods_id] += $amount;
                }else{
                    $cart[$goods_id] = $amount;
                }
                $cookie = new Cookie([
                    'name'=>'cart','value'=>serialize($cart)
                ]);
                $cookies = \Yii::$app->response->cookies;
                $cookies->add($cookie);
            }else{
                $member_id = \Yii::$app->user->id;
                $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
                if ($model == null){
                    $cart = new Cart();
                    $cart->goods_id = $goods_id;
                    $cart->member_id = $member_id;
                    $cart->amount = $amount;
                    $cart->save();
                }else{
                    $model->amount += $amount;
                    $model->save();
                }
            }
            return ['status'=>'1', 'msg' => '','rs'=>true];
        }else{
            return ['status'=>'-1', 'msg' => '请使用post请求',];
        }
    }
    public function actionEditCart(){
        $request = \Yii::$app->request;
        if ($request->isPost){
            $goods_id = $request->post('goods_id');
            $amount = $request->post('amount');
            if (\Yii::$app->user->isGuest){
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                $cart = unserialize($cookie->value);
                if (isset($cart[$goods_id])){
                    if ($amount){
                        $cart[$goods_id] = $amount;
                    }else{
                        unset($cart[$goods_id]);
                    }
                    $cookie = new Cookie([
                        'name'=>'cart','value'=>serialize($cart)
                    ]);
                    $cookies = \Yii::$app->response->cookies;
                    $cookies->add($cookie);
                    if($amount){
                        return ['status'=>'1', 'msg' => '','rs'=>'商品修改成功'];
                    }else{
                        return ['status'=>'1', 'msg' => '','rs'=>'商品删除成功'];
                    }
                }else{
                    return ['status'=>'-1', 'msg' => '商品已从购物车删除',];
                }
            }else{
                $member_id = \Yii::$app->user->id;
                $cart = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
                if ($cart){
                    if ($amount){
                        $cart->amount = $amount;
                        $cart->save();
                        return ['status'=>'1', 'msg' => '','rs'=>'商品修改成功'];
                    }else{
                        $cart->delete();
                        return ['status'=>'1', 'msg' => '','rs'=>'商品删除成功'];
                    }
                }else{
                    return ['status'=>'-1', 'msg' => '商品已从购物车删除',];
                }
            }
        }else{
            return ['status'=>'-1', 'msg' => '请使用post请求',];
        }
    }
    public function actionCleanCart(){
        $request = \Yii::$app->request;
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if (!$cookie){//如果购物车为空
                return ['status'=>'-1', 'msg' => '购物车为空',];
            }
            $cookies->remove($cookie);
            return ['status'=>'1', 'msg' => '','rs'=>'购物车清除成功'];
        }else{
            $member_id = \Yii::$app->user->id;
            $carts = Cart::findAll(['member_id'=>$member_id]);
            if ($carts){
                foreach ($carts as $cart){
                    $cart->delete();
                }
                return ['status'=>'1', 'msg' => '','rs'=>'购物车清除成功'];
            }else{
                return ['status'=>'-1', 'msg' => '购物车为空',];
            }
        }
    }
    public function actionCart(){
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie){//如果购物车为空
                $cart = unserialize($cookie->value);
            }else{
                return ['status'=>'1', 'msg' => '','data'=>''];
            }
            $goodses = [];

            foreach ($cart as $k=>$amount){
                $goods = Goods::findOne(['id'=>$k])->toArray();
                $goods['amount'] = $amount;
                $goodses[] = $goods;
            }
            return ['status'=>'1', 'msg' => '','data'=>$goodses];
        }else{
            $member_id = \Yii::$app->user->id;
            $carts = Cart::findAll(['member_id'=>$member_id]);
            $goodses = [];
            if ($carts){
                foreach ($carts as $cart){
                    $goods = Goods::findOne(['id'=>$cart->goods_id])->toArray();
                    $goods['amount'] = $cart->amount;
                    $goodses[] = $goods;
                }
                return ['status'=>'1', 'msg' => '','data'=>$goodses];
            }else{
                return ['status'=>'1', 'msg' => '','data'=>''];
            }
        }
    }
    public function actionDelivery(){
        $delivery = Order::$_delivery;
        return ['status'=>'1', 'msg' => '','data'=>$delivery];
    }
    public function actionPay(){
        $pay = Order::$_pay;
        return ['status'=>'1', 'msg' => '','data'=>$pay];
    }
    public function actionAddOrder(){
        $request = \Yii::$app->request;
        if ($request->isPost){
            $data['address_id'] = $request->post('address_id');
            $data['pay'] = $request->post('pay');
            $data['delivery'] = $request->post('delivery');
            $data['total'] = $request->post('total');
            if ($data['address_id'] == null){
                return ['status'=>'-1', 'msg' => ['address_id'=>['地址id不能为空']]];
            }
            if ($data['pay'] == null){
                return ['status'=>'-1', 'msg' => ['pay'=>['支付方式id不能为空']]];
            }
            if ($data['delivery'] == null){
                return ['status'=>'-1', 'msg' => ['delivery'=>['邮寄方式id不能为空']]];
            }
            $order = new Order();
            $order->data($data);
            if ($order->validate()){
                $transaction = \Yii::$app->db->beginTransaction();//开启事务
                try{
                    $order->save();
                    $carts = Cart::findAll(['member_id'=>\Yii::$app->user->id]);
                    if ($carts == null){
                        throw new Exception('请先添加商品到购物车');
                    }
                    foreach ($carts as $cart){
                        $goods = Goods::findOne(['id'=>$cart->goods_id]);
                        if ($goods == null){
                            throw new Exception('商品已下架');
                        }
                        if ($goods->stock < $cart->amount){
                            throw new Exception('商品库存不足');
                        }
                        $model = new  OrderGoods();
                        $model->data($goods,$cart->amount,$order->id);
                        if ($model->validate()){
                            $model->save();
                            $goods->stock -= $cart->amount;//修改商品库存
                            $goods->save(false);
                            $cart->delete();//删除购物车里对应商品
                        }else{
                            $errors = $model->getErrors();
                            $msg = '';
                            foreach ($errors as $error){//取出错误信息中的字符串
                                $msg = $error[0];
                            }
                            throw new Exception($msg);
                        }
                    }
                    $transaction->commit();
                    return ['status'=>'1', 'msg' => '','data'=>true];
                }catch (Exception $e){
                    $transaction->rollBack();
                    return ['status'=>'-1', 'msg' => $e->getMessage()];
                }
            }else{
                return ['status'=>'-1', 'msg' => $order->getErrors()];
            }
        }else{
            return ['status'=>'-1', 'msg' => '请使用post请求',];
        }
    }
    //订单列表
    public function actionListOrder(){
        $models =[];
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
        foreach ($orders as $order){
            $order_goods = OrderGoods::find()->where(['order_id'=>$order['id']])->asArray()->all();
            $order['order_goods'] = $order_goods;
            $models[]= $order;
        }
        return ['status'=>'1', 'msg' => '','data'=>$models];
    }
    //取消订单
    public function actionCancelOrder(){
        $request = \Yii::$app->request;
        if ($request->isGet){
            $id = $request->get('id');
            $order = Order::findOne(['id'=>$id,'member_id'=>\Yii::$app->user->id]);
            if ($order != null){
                $order->status = 0;
                $order->save(false);
                $order_goodses = OrderGoods::findAll(['order_id'=>$order->id]);
                foreach ($order_goodses as $order_goods){
                    $goods = Goods::findOne(['id'=>$order_goods->goods_id]);
                    $goods->stock += $order_goods->amount;
                    $goods->save(false);
                    $order_goods->delete();
                }
                return ['status'=>'1', 'msg' => '','data'=>true];
            }else{
                return ['status'=>'-1', 'msg' => '订单不存在',];
            }
        }else{
            return ['status'=>'-1', 'msg' => '请使用get请求',];
        }
    }
    //验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
//                'captchaAction' => 'site/captcha',
                'minLength'=>4,//验证码最小长度
                'maxLength'=>4,//最大长度
            ],
        ];
    }
    //上传图片
    public function actionUpload(){
        $img = UploadedFile::getInstanceByName('img');
        if ($img){
            $fileName = '/upload/'.uniqid().'.'.$img->extension;
            $result = $img->saveAs(\Yii::getAlias('@webroot').$fileName,0);
            if($result){
                return ['status'=>'1','msg'=>'','data'=>$fileName];
            }
            return ['status'=>'-1','msg'=>$img->error];
        }else{
            ['status'=>'-1','msg'=>'没有文件上传'];
        }
    }
    //短信
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            return ['status'=>'-1','msg'=>'电话号码不正确'];
        }
        //检查上次发送时间是否超过1分钟
        $value = \Yii::$app->cache->get('time_tel_'.$tel);
        $s = time()-$value;
        if($s <60){
            return ['status'=>'-1','msg'=>'请'.(60-$s).'秒后再试'];
        }

        $code = rand(1000,9999);
        //$result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        $result = 1;
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            \Yii::$app->cache->set('time_tel_'.$tel,time(),5*60);
            //echo 'success'.$code;
            return ['status'=>'1','msg'=>''];
        }else{
            return ['status'=>'-1','msg'=>'短信发送失败'];
        }
    }
}