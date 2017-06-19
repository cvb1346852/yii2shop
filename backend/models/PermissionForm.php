<?php
namespace backend\models;

use yii\base\Model;
use yii\rbac\Permission;

class PermissionForm extends Model{
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name','description'],'required'],

        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'权限名',
            'description'=>'权限描述'
        ];
    }

    public function addPermission(){
        $authManager = \Yii::$app->authManager;
        if (\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
            return false;
        }
        //创建权限
        $permission = $authManager->createPermission($this->name);
        $permission->description = $this->description;
        //保存数据
         return $authManager->add($permission);
    }
    public function loadData(Permission $permission){
        $this->name = $permission->name;
        $this->description = $permission->description;
    }
    public function editPermission($name){
        $authManager = \Yii::$app->authManager;
        if ($name != $this->name){
            if($authManager->getPermission($this->name)){
                $this->addError('name','权限已存在');
                return false;
            }
        }
        $permission = $authManager->createPermission($this->name);
        $permission->description = $this->name;
        $permission->description = $this->description;
        return $authManager->update($name,$permission);
    }
}