<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_photo".
 *
 * @property integer $id
 * @property string $photo
 * @property integer $goods_id
 */
class GoodsPhoto extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'photo' => '图片',
            'goods_id' => '所属商品',
        ];
    }

}
