<?php

?>
    <!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><?=\yii\helpers\Html::a(\yii\helpers\Html::img('@web/images/logo.png'),['member/index'])?></h2>
        <div class="flow fr">
            <ul>
                <li class="cur">1.我的购物车</li>
                <li>2.填写核对订单信息</li>
                <li>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->

<div style="clear:both;"></div>


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
        <tbody id="tb">
        <?php   foreach ($models as $model):  ?>
        <tr data-goods_id="<?=$model['id']?>">
            <td class="col1"><a href="goods-content.html?id=<?=$model['id']?>"><?=\yii\helpers\Html::img(Yii::$app->params['backUp'].$model['logo'])?></a>  <strong><a href="goods-content.html?id=<?=$model['id']?>"><?=$model['name']?></a></strong></td>
            <td class="col3">￥<span><?=$model['shop_price']?></span></td>
            <td class="col4">
                <a href="javascript:;" class="reduce_num"></a>
                <input type="text" name="amount" class="amount" value="<?=$model['amount']?>" class="amount"/>
                <a href="javascript:;" class="add_num"></a>
            </td>
            <td class="col5">￥<span><?=number_format($model['amount']*$model['shop_price'],2,'.','')?></span></td>
            <td class="col6"><a href="javascript:;" class="del-goods">删除</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td id="zero"><?=$models?'':'空空哒';?></td>
            <td colspan="6">购物金额总计： <strong>￥ <span id="total"><?=number_format($sum,2,'.','')?></span></strong></td>
        </tr>
        </tfoot>
    </table>
    <div class="cart_btn w990 bc mt10">
        <?=\yii\helpers\Html::a('继续购物',['member/index'],['class'=>'continue'])?>
        <?=$models ? \yii\helpers\Html::a('结算',['goods/flow2'],['class'=>'checkout']) : \yii\helpers\Html::a('逛逛',['member/index'],['class'=>'checkout'])?>
    </div>
</div>
<!-- 主体部分 end -->
<?php
/**
 * @var $this \yii\web\view
 */
$url = \yii\helpers\Url::to(['goods/edit-cart']);
$token = Yii::$app->request->csrfToken;
$herf = \yii\helpers\Url::to(['goods/flow2']);
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
    $('.amount').change(function() {
      var goods_id = $(this).closest('tr').attr('data-goods_id');
      var amount = $(this).parent().find('input').val();
      var data = {'goods_id':goods_id,'amount':amount,'_csrf-frontend':'$token'};
      $.post('$url',data,function() { 
      });
    });
    //删除商品
    $('.del-goods').click(function() {
      if(confirm('是否删除该商品')){
          var goods_id = $(this).closest('tr').attr('data-goods_id');
          var amount = 0;
          var data = {'goods_id':goods_id,'amount':amount,'_csrf-frontend':'$token'};
          $.post('$url',data,function() {
            
          });
          var sum = $('#total').text();//总价格
          var del_one = $(this).closest('tr').find('.col5 span').text();//需删除行的小计
          sum = sum - del_one;
          $('#total').html(sum);//修改总价格
          $(this).closest('tr').remove();//最后删除行
          if($('#tb tr').length == 0){
            $('.checkout').attr('href','/member/index.html');
            $('.checkout').html('逛逛');
            $('#zero').html('空空哒')
          }
      }  
    });
    //
    /*$('.checkout').click(function() {
      if($('tbody tr').length==0){
          alert('请先加入商品');
      }else{
          $(location).attr('href', '$herf');
      }
    })*/
JS

));