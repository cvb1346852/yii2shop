<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    public $address_id;
    public static $_statusOptions = [0=>'已取消',1=>'待付款',2=>'待发货',3=>'待收货',4=>'完成'];
    public static $_delivery = [
        '1'=>['name'=>'普通快递送货上门','money'=>10,'intro'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
        '2'=>['name'=>'特快专递','money'=>40,'intro'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
        '3'=>['name'=>'加急快递送货上门','money'=>40,'intro'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
        '4'=>['name'=>'平邮','money'=>10,'intro'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
    ];
    public static $_pay = [
        '1'=>['name'=>'货到付款','intro'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        '2'=>['name'=>'在线支付','intro'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        '3'=>['name'=>'上门自提','intro'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        '4'=>['name'=>'邮局汇款','intro'=>'通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'delivery_id', 'payment_id', 'total'], 'required'],
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name', 'delivery_name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
            [['payment_name'], 'string', 'max' => 30],
            [['trade_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
            'address_id' => '地址id'
        ];
    }
    public function data($data){
        $this->member_id = Yii::$app->user->getId();
        $address = Address::findOne(['id'=>$data['address_id'],'member_id'=>$this->member_id]);
        $this->name = $address->name;
        $this->province = $address->province;
        $this->city = $address->city;
        $this->area = $address->area;
        $this->address = $address->detail_address;
        $this->tel = $address->phone;
        $this->delivery_id = $data['delivery'];
        $this->delivery_name = self::$_delivery[$data['delivery']]['name'];
        $this->delivery_price = self::$_delivery[$data['delivery']]['money'];
        $this->payment_id = $data['pay'];
        $this->payment_name = self::$_pay[$data['pay']]['name'];
        $this->total = $data['total'];
        $this->status = 1;
        $this->create_time = time();
    }
    public function getOrderGoods(){
        return $this->hasMany(OrderGoods::className(),['order_id'=>'id']);
    }
}
