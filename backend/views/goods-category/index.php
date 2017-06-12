<?php

/* @var $this yii\web\View */
?>
<h2>商品分类</h2>

<p>
    <?=\yii\bootstrap\Html::a('添加',['goods-category/add'],['class'=>'btn btn-success'])?>
</p>
<table class="table table-bordered table-striped">
    <tr>
        <td>id</td>
        <td>名称</td>
        <td>层级</td>
        <td>上级分类</td>
        <td>简介</td>
        <td>操作</td>
    </tr>
    <?php foreach ($models as $model): ?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=str_repeat(' — ',$model->depth).$model->name?></td>
        <td><?=$model->depth?></td>
        <td><?=$model->parent_id ==0 ? '最上级' : $model->category->name?></td>
        <td><?=$model->intro?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-info btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$model->id],['class'=>'btn btn-danger btn-xs'])?>
        </td>

    </tr>
    <?php endforeach;?>
</table>