<?php

?>

<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><?=\yii\helpers\Html::a(\yii\helpers\Html::img('@web/images/logo.png'),['member/index'])?></h2>
        <div class="flow fr flow2">
            <ul>
                <li>1.我的购物车</li>
                <li class="cur">2.填写核对订单信息</li>
                <li>3.成功提交订单</li>
            </ul>
        </div>
    </div>
</div>
<!-- 页面头部 end -->

<div style="clear:both;"></div>

<!-- 主体部分 start -->
<div class="fillin w990 bc mt15">
    <div class="fillin_hd">
        <h2>填写并核对订单信息</h2>
    </div>

    <div class="fillin_bd">
        <form action="flow3.html" method="post" id="form">
            <input name="_csrf-frontend" type="hidden" id="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>"><!--跨域问题-->
            <input type="hidden" id="total" name="total" value="">
        <!-- 收货人信息  start-->
        <div class="address">
            <h3>收货人信息</h3>
            <div class="address_info">
                <?php
                    foreach ($member->address as $addres):
                ?>
                <p>
                    <input type="radio" value="<?=$addres->id?>" name="address_id" <?=$addres->status ? 'checked' : '';?>/><?=$addres->name,' ',$addres->phone,' ',$addres->province,' ',$addres->city,' ',$addres->area,' ',$addres->detail_address?><?=$addres->status ? ' (默认地址)' : '';?>
                </p>
                <?php endforeach; ?>
            </div>


        </div>
        <!-- 收货人信息  end-->

        <!-- 配送方式 start -->
        <div class="delivery">
            <h3>送货方式 </h3>


            <div class="delivery_select">
                <table>
                    <thead>
                    <tr>
                        <th class="col1">送货方式</th>
                        <th class="col2">运费</th>
                        <th class="col3">运费标准</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach (\frontend\models\Order::$_delivery as $k=>$delivery):
                            if ($k==1){
                            $fare = number_format($delivery['money'],2);//默认运费
                            $total = $fare+$sum;//总额
                        }
                        ?>
                    <tr class="<?= $k==1 ? 'cur':''?>" data-fare="<?=number_format($delivery['money'],2)?>">
                        <td>
                            <input type="radio" value="<?=$k?>" name="delivery"  <?= $k==1 ? 'checked':''?>/><?=$delivery['name']?>
                        </td>
                        <td>￥<?=number_format($delivery['money'],2)?></td>
                        <td><?=$delivery['intro']?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- 配送方式 end -->

        <!-- 支付方式  start-->
        <div class="pay">
            <h3>支付方式 </h3>


            <div class="pay_select">
                <table>
                    <?php foreach (\frontend\models\Order::$_pay as $k=>$pay):?>
                    <tr class="<?= $k==1 ? 'cur':''?>">
                        <td class="col1"><input type="radio" name="pay" value="<?=$k?>"/><?=$pay['name']?></td>
                        <td class="col2"><?=$pay['intro']?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

            </div>
        </div>
        <!-- 支付方式  end-->
        </form>
        <!-- 发票信息 start-->
        <div class="receipt none">
            <h3>发票信息 </h3>


            <div class="receipt_select ">
                <form action="">
                    <ul>
                        <li>
                            <label for="">发票抬头：</label>
                            <input type="radio" name="type" checked="checked" class="personal" />个人
                            <input type="radio" name="type" class="company"/>单位
                            <input type="text" class="txt company_input" disabled="disabled" />
                        </li>
                        <li>
                            <label for="">发票内容：</label>
                            <input type="radio" name="content" checked="checked" />明细
                            <input type="radio" name="content" />办公用品
                            <input type="radio" name="content" />体育休闲
                            <input type="radio" name="content" />耗材
                        </li>
                    </ul>
                </form>

            </div>
        </div>
        <!-- 发票信息 end-->

        <!-- 商品清单 start -->
        <div class="goods">
            <h3>商品清单</h3>
            <table>
                <thead>
                <tr>
                    <th class="col1">商品</th>
                    <th class="col3">价格</th>
                    <th class="col4">数量</th>
                    <th class="col5">小计</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($models as $model):?>
                <tr>
                    <td class="col1"><a href="goods-content.html?id=<?=$model['id']?>"><?=\yii\helpers\Html::img(Yii::$app->params['backUp'].$model['logo'])?></a>  <strong><a href="goods-content.html?id=<?=$model['id']?>"><?=$model['name']?></a></strong></td>
                    <td class="col3">￥<?=$model['shop_price']?></td>
                    <td class="col4"> <?=$model['amount']?></td>
                    <td class="col5"><span>￥<?=number_format($model['amount']*$model['shop_price'],2,'.','')?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <ul>
                            <li>
                                <span><?=$n?> 件商品，总商品金额：</span>
                                <em>￥<?=number_format($sum,2,'.','')?></em>
                            </li>
                            <li>
                                <span>返现：</span>
                                <em>-￥0.00</em>
                            </li>
                            <li>
                                <span>运费：</span>
                                <em id="fare">￥<?=$fare?></em>
                            </li>
                            <li>
                                <span>应付总额：</span>
                                <em class="total">￥<?=number_format($total,2)?></em>
                            </li>
                        </ul>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- 商品清单 end -->

    </div>

    <div class="fillin_ft">
        <a href="javascript:;" id="button"><span>提交订单</span></a>
        <p>应付总额：<strong class="total">￥<?=number_format($total,2)?>元</strong></p>

    </div>
</div>
<!-- 主体部分 end -->

<?php
/**
 * @var $this \yii\web\view
 * */
$url = \yii\helpers\Url::to(['goods/flow3']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $('#total').val('$total');//初始总额
    $('input[name=delivery]').click(function() {
      var fare = $(this).closest('tr').attr('data-fare');
      $('#fare').html('￥'+fare);
      var total = parseInt('$sum')+parseInt(fare) ;
      $('.total').html('￥'+total+'.00');
      $('#total').val(total);
    });
    $('#button').click(function() {
       if($('.pay_select input:checked').length == 0){
           alert('请先选择支付方式');
       }else if($('.address_info input:checked').length == 0){
           alert('请先选择收货地址');
       }else {
           if($('tbody tr').length == 0){
               alert('请先选择商品');
           }else{
               $('#form').submit();
           }
           
       }
    })
    
JS

));
