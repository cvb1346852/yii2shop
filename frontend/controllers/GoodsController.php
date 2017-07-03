<?php
namespace frontend\controllers;

use backend\models\Brand;
use backend\models\GoodsCategory;
use frontend\components\SphinxClient;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use frontend\models\SearchForm;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use backend\models\Goods;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class GoodsController extends Controller{

    public $layout='goods';
    public function actionList(){
        $goodses = Goods::find();
        if($keyword = \Yii::$app->request->get('keyword')){
            $cl = new SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $info = $keyword;
            $res = $cl->Query($info, 'goods');
            if (!isset($res['matches'])){
                $goodses->where(['id'=>0]);
            }else{
                $ids = ArrayHelper::map($res['matches'],'id','id');
                $goodses->where(['in','id',$ids]);
            }
        }
        /*$page = new Pagination([
            'totalCount'=>$goodses->count(),
            'pageSize' => 3,
        ]);*/

        $goodses = $goodses->all();
        $brands = Brand::find()->all();

        $keywords = array_keys($res['words']);
        $options = array(
            'before_match' => '<span style="color:red;">',
            'after_match' => '</span>',
            'chunk_separator' => '...',
            'limit' => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
        );
//关键字高亮
        foreach ($goodses as $index => $item) {
            $name = $cl->BuildExcerpts([$item->name], 'goods', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
            $goodses[$index]->name = $name[0];
        }
        return $this->render('goods-list',['goodses'=>$goodses,'brands'=>$brands]);
    }
    public function actionGoodsList($lft,$rgt){
        $categorys = GoodsCategory::find()->where(['>=','lft',$lft])->andWhere(['<=','rgt',$rgt])->all();
        $goodses = [];
        foreach ($categorys as $category){
            $goods = Goods::findAll(['goods_category_id'=>$category->id]);
            if ($goods){
                $goodses = array_merge($goods,$goodses);
            }
        }
        $brands = Brand::find()->all();
        return $this->render('goods-list',['goodses'=>$goodses,'brands'=>$brands]);
    }
    public function actionGoodsContent($id){
        $goods = Goods::findOne(['id'=>$id]);
        return $this->render('goods-content',['goods'=>$goods]);
    }
    public function actionAddCart(){
        $this->layout='cart';
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if ($goods == null){
            throw new NotFoundHttpException('商品已下架');
        }
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie){
                $cart = unserialize($cookie->value);
            }else{
                $cart = [];
            }
            if (isset($cart[$goods->id])){
                $cart[$goods->id] += $amount;
            }else{
                $cart[$goods->id] = $amount;
            }
            $cookie = new Cookie([
                'name'=>'cart','value'=>serialize($cart)
            ]);
            $cookies = \Yii::$app->response->cookies;
            $cookies->add($cookie);
        }else{//登录状态
            $member_id = \Yii::$app->user->getId();
            if ($cart = Cart::findOne(['goods_id'=>$goods->id,'member_id'=>$member_id])){
                $cart->amount += $amount;
                $cart->save();
            }else{
                $cart = new Cart();
                $cart->amount = $amount;
                $cart->goods_id = $goods_id;
                $cart->member_id = \Yii::$app->user->getId();
                if ($cart->validate()){
                    $cart->save();
                }
            }
        }
        return $this->redirect(['goods/cart']);
    }
    public function actionCart(){
        $this->layout='cart';
        $sum = 0;
        $models = [];
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie == null){
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
            }
            foreach ($cart as $k=>$num){
                $goods = Goods::findOne(['id'=>$k])->attributes;
                $goods['amount'] = $num;
                $sum += $num*($goods['shop_price']);
                $models[] = $goods;
            }
        }else{
            $member_id = \Yii::$app->user->getId();
            $carts = Cart::findAll(['member_id'=>$member_id]);
            foreach ($carts as $cart){
                $goods = Goods::findOne(['id'=>$cart->goods_id])->attributes;
                $goods['amount'] = $cart->amount;
                $sum += $goods['amount']*$goods['shop_price'];
                $models[] = $goods;
            }
        }
        return $this->render('cart',['models'=>$models,'sum'=>$sum]);
    }
    public function actionEditCart(){
        $this->layout='cart';
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if ($goods == null){
            throw new NotFoundHttpException('商品已下架');
        }
        if (\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            $cart = unserialize($cookie);
            //amount不为0就修改，为0就删除这个商品的cookie
            if ($amount){
                $cart[$goods->id] = $amount;
            }else{
                if (key_exists($goods_id,$cart)){
                    unset($cart[$goods_id]);
                }
            }
            $cookie = new Cookie([
                'name'=>'cart','value'=>serialize($cart)
            ]);
            $cookies = \Yii::$app->response->cookies;
            $cookies->add($cookie);
        }else{//登录状态
            $member_id = \Yii::$app->user->getId();
            $cart = Cart::findOne(['goods_id'=>$goods->id,'member_id'=>$member_id]);
            if ($amount){
                $cart->amount= $amount;
                $cart->save();
            }else{
                $cart->delete();
            }
        }
    }
    //购物车结算
    public function actionFlow2(){
        $this->layout='cart';
        if (\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }else{
            $member = \Yii::$app->user->identity;
            $n=0;//商品个数
            $sum=0;//商品总价
            $models = [];
            foreach ($member->cart as $cart){
                $goods = Goods::findOne(['id'=>$cart->goods_id])->attributes;
                $goods['amount'] = $cart->amount;
                $n += $cart->amount;
                $sum += $cart->amount*$goods['shop_price'];
                $models[] = $goods;
            }
            return $this->render('flow2',['models'=>$models,'member'=>$member,'n'=>$n,'sum'=>$sum]);
        }
    }
    //提交订单
    public function actionFlow3(){
        $this->layout='cart';
        $order = new Order();
        $data = \Yii::$app->request->post();
//        var_dump($data);exit;
        $order->data($data);
        if ( $order->validate()){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $order->save();
                $id = $order->id;
                $carts = Cart::findAll(['member_id'=>$order->member_id]);
                if ($carts == null){
                    throw new Exception('请先添加商品到购物车');
                }
                foreach ($carts as $cart){
                    $amount = $cart->amount;
                    $goods = Goods::findOne(['id'=>$cart->goods_id]);
                    if ($goods == null){
                        throw new Exception('商品以售空');
                    }
                    if ($goods->stock > $amount){//判断库存是否足够
                        $order_goods = new OrderGoods();
                        $order_goods->data($goods,$amount,$id);
                        if($order_goods->validate()){
                            $order_goods->save();
                            $goods->stock = $goods->stock - $amount;//修改库存
                            $goods->save(false);
                            $cart->delete();//删除购物车
                        }else{
                            throw new Exception($order_goods->getErrors());
                        };
                    }else{
                       throw new Exception('库存不够了');
                    }
                }
                $transaction->commit();//执行事务
//                var_dump('订单提交成功，我们将及时为您处理');exit;
                return $this->render('flow3');
            }catch (Exception $e){
                $transaction->rollBack();//回滚
                return $this->redirect(['goods/cart']);
            }
        }
    }

    public function actionOrderList(){
        $models = Order::findAll(['member_id'=>\Yii::$app->user->getId()]);
        return $this->render('order-list',['models'=>$models]);
    }

    public function actionClean(){//超时未支付订单  待支付状态1超过1小时==》已取消0
        set_time_limit(0);
        while (1) {
            /*$orders = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-3600*24])->all();
            foreach ($orders as $order){
                $order->status = 0;
                $order->save();
                foreach ($order->orderGoods as $model){
                    $goods = Goods::findOne(['id'=>$model->goods_id]);
                    $goods->stock += $model->amount;
                    $goods->save();
                }
                echo "ID".$order->id." has been clean...\n";
            }
            echo 2222,"\n";
            sleep(5);*/
        }
    }
}