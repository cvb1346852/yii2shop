<?php
namespace console\controllers;

use backend\models\Goods;
use frontend\models\Order;
use yii\console\Controller;

class OrderController extends Controller{

    public function actionClean(){
        set_time_limit(0);
        while (1){
            $orders = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-3600*24])->all();
            foreach ($orders as $order){
                /*$order->status = 0;
                $order->save();
                foreach ($order->orderGoods as $model){
                    $goods = Goods::findOne(['id'=>$model->goods_id]);
                    $goods->stock += $model->amount;
                    $goods->save();
                }*/
                echo "ID".$order->id." has been clean...\n";
            }
            sleep(1);
        }
    }
}