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
    <tr data-lft="<?=$model->lft?>" data-rgt="<?=$model->rgt?>" data-tree="<?=$model->tree?>">
        <td><?=$model->id?></td>
        <td><?=str_repeat(' — ',$model->depth).$model->name?></td>
        <td><?=$model->depth?></td>
        <td><?=$model->parent_id ==0 ? '最上级' : $model->category->name?><span class="xiala glyphicon glyphicon-chevron-down" style="float: right"></span></td>
        <td><?=$model->intro?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-info btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$model->id],['class'=>'btn btn-danger btn-xs'])?>
        </td>

    </tr>
    <?php endforeach;?>
</table>
<?php
$js = <<<JS
    $('.xiala').click(function() {
      var tr = $(this).closest('tr');
      var tree = parseInt(tr.attr('data-tree')) ;
      var lft = parseInt(tr.attr('data-lft'));
      var rgt = parseInt(tr.attr('data-rgt'));
      var show = $(this).hasClass('glyphicon-chevron-up');
      
      $(this).toggleClass('glyphicon-chevron-up');
      $(this).toggleClass('glyphicon-chevron-down');
      
      $('tr').each(function(i,v) {
          // console.debug(this);
        if ($(v).attr('data-tree')==tree && $(v).attr('data-lft')>lft && $(v).attr('data-rgt')<rgt){
           show ? $(v).show() : $(v).hide();
        }
      })   
      
    })
JS;
$this->registerJs($js);
