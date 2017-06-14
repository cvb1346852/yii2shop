<?php
namespace backend\models;

use yii\base\Model;
use yii\db\ActiveQuery;

class SearchForm extends Model{
    public $name;
    public $sn;
    public $category;

    public function rules()
    {
        return[
            [['name','sn','category'],'string']
        ];
    }
    public function search(ActiveQuery $models){
        $this->load(\Yii::$app->request->get());
        if ($this->name){
            $models->andwhere(['like','name',$this->name]);
        }
        if ($this->sn){
            $models->andwhere(['like','sn',$this->sn]);
        }
        if ($this->category){
            $models->andwhere(['like','goods_category_id',$this->category]);
        }
    }
}