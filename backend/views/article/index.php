<?php
/* @var $this yii\web\View */
?>
<h2>文章列表</h2>

<p>
    <?=\yii\bootstrap\Html::a('添加',['article/add'],['class'=>['btn btn-success']])?>
</p>
<table class="table table-bordered table-striped">
    <tr>
        <th>id</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($articles as $article):?>
        <tr>
            <td><?=$article->id?></td>
            <td><?=\yii\bootstrap\Html::a($article->name,['article/detail','id'=>$article->id])?></td>
            <td width="550"><?=$article->intro?></td>
            <td><?=$article->articleCategory->name?></td>
            <td><?=$article->sort?></td>
            <td><?=\backend\models\Article::$statusOptions[$article->status];?></td>
            <td><?=date('Y-m-d H:i:s',$article->create_time);?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$article->id],['class'=>['btn btn-info btn-xs']])?>
                <?=\yii\bootstrap\Html::a('删除',['article/delete','id'=>$article->id],['class'=>['btn btn-danger btn-xs']])?>
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