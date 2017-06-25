<?php

?>
<!-- 主体部分 start -->
<div class="mycart w990 mt10 bc">
    <h2><span>我的购物车</span></h2>
    <table>
        <thead>
        <tr>
            <th class="col1">商品名称</th>
            <th class="col3">单价</th>
            <th class="col4">数量</th>
            <th class="col5">小计</th>
            <th class="col6">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php   foreach ($models as $model):  ?>
        <tr data-goods_id="<?=$model['id']?>">
            <td class="col1"><a href="goods-content.html?id=<?=$model['id']?>"><?=\yii\helpers\Html::img(Yii::$app->params['backUp'].$model['logo'])?></a>  <strong><a href="goods-content.html?id=<?=$model['id']?>"><?=$model['name']?></a></strong></td>
            <td class="col3">￥<span><?=$model['shop_price']?></span></td>
            <td class="col4">
                <a href="javascript:;" class="reduce_num"></a>
                <input type="text" name="amount" value="<?=$model['amount']?>" class="amount"/>
                <a href="javascript:;" class="add_num"></a>
            </td>
            <td class="col5">￥<span><?=number_format($model['amount']*$model['shop_price'],2,'.','')?></span></td>
            <td class="col6"><a href="javascript:;" class="del-goods">删除</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <?=$models?'':'<td>空空哒</td>';?>
            <td colspan="6">购物金额总计： <strong>￥ <span id="total"><?=number_format($sum,2,'.','')?></span></strong></td>
        </tr>
        </tfoot>
    </table>
    <div class="cart_btn w990 bc mt10">
        <a href="" class="continue">继续购物</a>
        <a href="" class="checkout">结 算</a>
    </div>
</div>
<!-- 主体部分 end -->
<?php
/**
 * @var $this \yii\web\view
 */
$url = \yii\helpers\Url::to(['goods/edit-cart']);
$token = Yii::$app->request->csrfToken;
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        //增减商品数量
    $('.reduce_num,.add_num').click(function() {
      var goods_id = $(this).closest('tr').attr('data-goods_id');
      var amount = $(this).parent().find('input').val();
      var data = {'goods_id':goods_id,'amount':amount,'_csrf-frontend':'$token'};
      $.post('$url',data,function() {
        
      });
    });
    //删除商品
    $('.del-goods').click(function() {
      var goods_id = $(this).closest('tr').attr('data-goods_id');
      var amount = 0;
      var data = {'goods_id':goods_id,'amount':amount,'_csrf-frontend':'$token'};
      $.post('$url',data,function() {
        
      });
      var sum = $('#total').text();//总价格
      var del_one = $(this).closest('tr').find('.col5 span').text();//删除行的小计
      sum = sum - del_one;
      $('#total').html(sum);//修改总价格
      $(this).closest('tr').remove();//最后删除行
    })
JS

));