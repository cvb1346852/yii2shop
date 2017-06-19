<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $label
 * @property string $url
 * @property integer $parent_id
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'parent_id'], 'required'],
            [['parent_id', 'sort'], 'integer'],
            [['label'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 255],
            ['label','unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => '菜单名称',
            'url' => '地址/路由',
            'parent_id' => '上级菜单',
            'sort' => '排序',
        ];
    }
    public static function getParentId(){
        $category = self::find()->where(['parent_id'=>0])->asArray()->all();
        $category = array_merge([['label'=>'最上级','id'=>0]],$category);
//        var_dump($category);exit;
        return ArrayHelper::map($category,'id','label');

    }
    public function sort(){
        $models = [];
        $first = self::find()->where(['parent_id'=>0])->all();
        foreach ($first as $model){
            $models[]=$model;
//            $children = self::find()->where(['parent_id'=>$model->id])->all();
            foreach ($model->children as $child){
                $models[]=$child;
            }
        }
        return $models;
    }
    public function getParentLabel(){
        return $this->hasOne(self::className(),['id'=>'parent_id']);
    }
    public function getChildren(){
        return $this->hasMany(self::className(),['parent_id'=>'id']);
    }
}
