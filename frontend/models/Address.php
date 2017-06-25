<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $member_id
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail_address
 * @property string $phone
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'province', 'city', 'area', 'phone'], 'required'],
            [['detail_address'], 'string'],
            [['status'], 'integer'],
            [['name', 'province'], 'string', 'max' => 20],
            [['city', 'area'], 'string', 'max' => 30],
            [['phone'], 'string','min'=>11, 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收 货 人 :',
            'province' => '省级',
            'city' => '市级',
            'area' => '区县',
            'detail_address' => '详细地址:',
            'phone' => '电 话 :',
            'status' => '设置为默认地址',
        ];
    }
    public function addressDefault(){
        $address_default = self::findOne(['status'=>1]);
        if ($address_default){
            $address_default->status = 0;
            $address_default->save();
        }
    }
    public function getProName(){
       return $this->hasOne(Locations::className(),['id'=>'province']);
    }
    public function getCityName(){
        return $this->hasOne(Locations::className(),['id'=>'city']);
    }
    public function getAreaName(){
        return $this->hasOne(Locations::className(),['id'=>'area']);
    }
}
