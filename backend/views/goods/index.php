<?php
/* @var $this yii\web\View */
?>
<h1>goods/index</h1>

<p>
    <?=\yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-success']) ?>
    <?php
        $form = \yii\bootstrap\ActiveForm::begin([
            'method'=>'get',
            'action'=>\yii\helpers\Url::to(['goods/index']),
            'options'=>['class'=>'form-inline']
        ]);
            echo $form->field($search,'name')->textInput(['placeholder'=>'商品名称'])->label(false);
            echo $form->field($search,'sn')->textInput(['placeholder'=>'商品货号'])->label(false);
            echo $form->field($search,'category')->textInput(['placeholder'=>'商品分类'])->label(false);
            echo \yii\bootstrap\Html::submitButton('搜索');
        \yii\bootstrap\ActiveForm::end();
    ?>
</p>
<table class="table table-bordered table-striped">
    <tr>
        <td>id</td>
        <td>商品名称</td>
        <td>货号</td>
        <td>LOGO图片</td>
        <td>商品分类</td>
        <td>品牌分类</td>
        <td>市场价格</td>
        <td>商品价格</td>
        <td>库存</td>
        <td>是否在售</td>
        <td>状态</td>
        <td>排序</td>
        <td>操作</td>
    </tr>
    <?php foreach ($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=\yii\bootstrap\Html::a($model->name,['goods/intro','id'=>$model->id]) ?></td>
        <td><?=$model->sn?></td>
        <td><?=\yii\bootstrap\Html::img($model->logo,['width'=>50])?></td>
        <td><?=$model->goodsCategory->name?></td>
        <td><?=$model->brandCategory->name?></td>
        <td><?=$model->market_price?></td>
        <td><?=$model->shop_price?></td>
        <td><?=$model->stock?></td>
        <td><?=\backend\models\Goods::$saleOptions[$model->is_on_sale];?></td>
        <td><?=\backend\models\Goods::$statusOptions[$model->status];?></td>
        <td><?=$model->sort?></td>
        <td>
            <?=\yii\bootstrap\Html::a('相册',['goods-photo/index','id'=>$model->id],['class'=>'btn btn-primary btn-xs']) ?>
            <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$model->id],['class'=>'btn btn-info btn-xs']) ?>
            <?=\yii\bootstrap\Html::a('删除',['goods/delete','id'=>$model->id],['class'=>'btn btn-danger btn-xs']) ?>

        </td>
    </tr>
    <?php endforeach;?>
</table>

<?php
echo \yii\widgets\LinkPager::widget([
        'pagination' => $page,
        'nextPageLabel' => '下一页',
        'prevPageLabel' => '上一页'
]);