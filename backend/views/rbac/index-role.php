<?php
/* @var $this yii\web\View */
?>
<h1>角色列表</h1>

<p>
    <?=\yii\bootstrap\Html::a('添加',['rbac/add-role'],['class'=>'btn btn-info'])?>
</p>

<table class="table table-bordered">
    <tr>
        <td>角色</td>
        <td>描述</td>
        <td>权限</td>
        <td>操作</td>
    </tr>
    <?php foreach ($roles as $role):?>
    <tr>
        <td><?=$role->name?></td>
        <td><?=$role->description?></td>
        <td><?php
                 foreach (Yii::$app->authManager->getChildren($role->name) as $child){
                     echo $child->description.'、';
                 }
            ?>
        </td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['rbac/edit-role','name'=>$role->name],['class'=>'btn btn-warning btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['rbac/del-role','name'=>$role->name],['class'=>'btn btn-danger btn-xs'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>