<?php
/* @var $this yii\web\View */
?>
<h2>文章类型列表</h2>

<p>
    <?=\yii\bootstrap\Html::a('添加',['article-category/edit'],['class'=>['btn btn-success']])?>
</p>
<table class="table table-bordered table-striped">
    <tr>
        <td>id</td>
        <td>名称</td>
        <td>简介</td>
        <td>排序</td>
        <td>状态</td>
        <td>类型</td>
        <td>操作</td>
    </tr>
    <?php foreach ($categorys as $category):?>
    <tr>
        <td><?=$category->id?></td>
        <td><?=$category->name?></td>
        <td><?=$category->intro?></td>
        <td><?=$category->sort?></td>
        <td><?=\backend\models\ArticleCategory::$statusOptions[$category->status]; ?></td>
        <td><?=$category->is_help==1?'帮助型':'非帮助型';?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$category->id],['class'=>['btn btn-info btn-xs']])?>
            <?=\yii\bootstrap\Html::a('删除',['article-category/delete','id'=>$category->id],['class'=>['btn btn-danger btn-xs']])?>
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

