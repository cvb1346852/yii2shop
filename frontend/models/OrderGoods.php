<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order_goods".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $goods_id
 * @property string $goods_name
 * @property string $logo
 * @property string $price
 * @property integer $amount
 * @property string $total
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id'], 'required'],
            [['order_id', 'goods_id', 'amount'], 'integer'],
            [['price', 'total'], 'number'],
            [['goods_name', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'goods_id' => '商品id',
            'goods_name' => '商品名称',
            'logo' => '商品图片',
            'price' => '商品价格',
            'amount' => '商品数量',
            'total' => '小计',
        ];
    }
    public function data($goods,$amount,$id){
        $this->order_id = $id;
        $this->goods_id = $goods->id;
        $this->goods_name = $goods->name;
        $this->logo = 'html://admin.yiishop.com'.$goods->logo;
        $this->price = $goods->shop_price;
        $this->amount = $amount;
        $this->total = $this->price * $this->amount;
    }
}
