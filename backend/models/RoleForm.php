<?php
namespace backend\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Role;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions=[];

    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe']
        ];
    }
    public function attributeLabels()
    {
        return[
            'name'=>'角色',
            'description' =>'描述',
            'permissions'=>'权限'
        ];
    }
    //获取权限
    public static function getPermissionOptions(){
        $permissions = \Yii::$app->authManager->getPermissions();
        return ArrayHelper::map($permissions,'name','description');
    }

    public function addRole(){
        $authManager = \Yii::$app->authManager;
        if ($authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
            return false;
        }
        $role = $authManager->createRole($this->name);
        $role->description = $this->description;
        if($authManager->add($role)){
            foreach ($this->permissions as $permissionName){
                $permission = \Yii::$app->authManager->getPermission($permissionName);
                $authManager->addChild($role,$permission);
            }
            return true;
        }
    }
    public function loadData(Role $role){
        $this->name = $role->name;
        $this->description = $role->description;
        $permissions = \Yii::$app->authManager->getChildren($role->name);
        foreach ($permissions as $permission){
            $this->permissions[]=$permission->name;
        }
    }
    public function editRole($name){
        $authManager = \Yii::$app->authManager;
        if ($name != $this->name && $authManager->getRole($name)){
            $this->addError('name','角色已存在');
        }else{
            $role = $authManager->getRole($name);
            $role->name = $this->name;
            $role->description = $this->description;
            if ($authManager->update($name,$role)){
                $authManager->removeChildren($role);
                foreach ($this->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    $authManager->addChild($role,$permission);
                }
                return true;
            }
        }
        return false;
    }
}