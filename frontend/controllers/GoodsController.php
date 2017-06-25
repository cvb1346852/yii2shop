<?php
namespace frontend\controllers;

use backend\models\Brand;
use frontend\models\Cart;
use yii\web\Controller;
use backend\models\Goods;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class GoodsController extends Controller{

    public $layout='goods';

    public function actionGoodsList($id){
        $goodses = Goods::find()->where(['goods_category_id'=>$id])->all();
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
                $cart = unserialize($cookie);
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
                $cart = unserialize($cookie);
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
                $sum += $goods['amount']*($goods['shop_price']);
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
}