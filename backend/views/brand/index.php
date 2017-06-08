<?php
/* @var $this yii\web\View */
?>
<h1>品牌列表</h1>
    <p><?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>['btn btn-success']])?></p>
<table class="table table-bordered">
    <tr>
        <td>id</td>
        <td>名称</td>
        <td>简介</td>
        <td>图片</td>
        <td>排序</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
    <?php foreach ($brands as $brand):?>
    <tr>
        <td><?=$brand->id?></td>
        <td><?=$brand->name?></td>
        <td><?=$brand->intro?></td>
        <td><?=\yii\bootstrap\Html::img($brand->logo,['width'=>100])?></td>
        <td><?=$brand->sort?></td>
        <td><?=\backend\models\Brand::$statusOptions[$brand->status];?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brand->id],['class'=>['btn btn-info btn-xs']])?>
            <?=\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$brand->id],['class'=>['btn btn-danger btn-xs']])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>

<?php
   echo \yii\widgets\LinkPager::widget([
            'pagination'=>$page,
            'nextPageLabel'=>'下一页',
            'prevPageLabel'=>'上一页',
    ]);

