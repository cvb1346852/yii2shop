<?php
/* @var $this yii\web\View */
?>
<h1>菜单列表</h1>

<p>
    <?=\yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-success'])?>
</p>
<table class="table table-bordered  table-condensed">
    <thead>
    <tr class="warning">
        <td>id</td>
        <td>菜单名称</td>
        <td>地址/路由</td>
        <td>上级菜单</td>
        <td>排序</td>
        <td>操作</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($models as $model): ?>
    <tr class="<?=$model->parent_id ? '':'info';?>">
        <td><?=$model->id?></td>
        <td><?php
                if ($model->parent_id != 0){
                   echo ' — ',$model->label;
                }else{
                    echo $model->label;
            }
            ?>
        </td>
        <td><?=$model->url?></td>
        <td><?=$model->parent_id ? $model->parentLabel->label:'最上级';?></td>
        <td><?=$model->sort?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-info btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$model->id],['class'=>'btn btn-danger btn-xs'])?>
        </td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
$this->registerCssFile('@web/css/datetables.css');
$this->registerJsFile('@web/js/datetables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({
    "ordering": false
});');
