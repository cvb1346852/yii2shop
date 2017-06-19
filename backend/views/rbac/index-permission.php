<?php
/* @var $this yii\web\View */
?>
<h1>权限列表</h1>

<p>
    <?=\yii\bootstrap\Html::a('添加',['rbac/add-permission'],['class'=>'btn btn-info'])?>
</p>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <td>权限名称</td>
        <td>权限描述</td>
        <td>操作</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($permissions as $permission): ?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td><?=\yii\bootstrap\Html::a('修改',['rbac/edit-permission','name'=>$permission->name],['class'=>'btn btn-warning btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['rbac/del-permission','name'=>$permission->name],['class'=>'btn btn-danger btn-xs'])?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$this->registerCssFile('@web/css/datetables.css');
$this->registerJsFile('@web/js/datetables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({

});');