<?php

use yii\helpers\Html;
?>
<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 我的订单</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><?=Html::a('我的订单',['goods/order-list'])?></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">账户信息</a></dd>
                <dd><b>.</b><a href="">账户余额</a></dd>
                <dd><b>.</b><a href="">消费记录</a></dd>
                <dd><b>.</b><a href="">我的积分</a></dd>
                <dd><b>.</b><?=\yii\helpers\Html::a('收货地址',['address/index'])?></dd>
            </dl>

            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">返修/退换货</a></dd>
                <dd><b>.</b><a href="">取消订单记录</a></dd>
                <dd><b>.</b><a href="">我的投诉</a></dd>
            </dl>
        </div>
    </div>
    <!-- 左侧导航菜单 end -->

    <!-- 右侧内容区域 start -->
    <div class="content fl ml10">
        <div class="address_hd">
            <h3>收货地址薄</h3>
            <?php
                foreach ($addresses as $address){
                    echo '<dl>';
                    echo '<dt>',$address->id,'.',$address->name ,' ',$address->province,' ',$address->city,' ',$address->area,' ',$address->detail_address,' ',$address->phone,' ',$address->status?'(默认地址)':'','</dt>';
                    echo '<dd>';
                    echo \yii\helpers\Html::a('修改 ',['address/edit','id'=>$address->id]);
                    echo \yii\helpers\Html::a('删除 ',['address/delete','id'=>$address->id]);
                    if(!$address->status){
                        echo \yii\helpers\Html::a('设为默认地址',['address/default','id'=>$address->id]);
                    }
                    echo '</dd>';
                    echo '</dl>';
                }
            ?>
            <!--<dl>
                <dt>1.许坤 北京市 昌平区 仙人跳区 仙人跳大街 17002810530 </dt>
                <dd>
                    <a href="">修改</a>
                    <a href="">删除</a>
                    <a href="">设为默认地址</a>
                </dd>
            </dl>
            <dl class="last"> <!-- 最后一个dl 加类last -->
            <!--   <dt>2.许坤 四川省 成都市 高新区 仙人跳大街 17002810530 </dt>
               <dd>
                   <a href="">修改</a>
                   <a href="">删除</a>
                   <a href="">设为默认地址</a>
               </dd>
           </dl>-->

        </div>

        <div class="address_bd mt10">
            <h4>新增收货地址</h4>
            <?php
                $form = \yii\widgets\ActiveForm::begin([
                    'fieldConfig'=>[
                        'options'=>[
                            'tag'=>'li',
                        ],
                        'labelOptions'=>[
                            'class'=>''
                        ]
                    ]
                ]);
                echo '<ul>';
                echo $form->field($model,'name')->textInput(['class'=>'txt'])->label('<span>*</span>收 货 人 ：');
                echo '<li><label for=""><span>*</span>所在地区：</label>';
               echo $form->field($model,'province',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList([''=>'=选择省='],['id'=>'province']);
            echo $form->field($model,'city',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList([''=>'=选择市='],['id'=>'city']);
            echo $form->field($model,'area',['template' => "{input}",'options'=>['tag'=>false]])->dropDownList([''=>'=选择县='],['id'=>'area']);
            echo '</li>';
            echo $form->field($model,'detail_address')->textInput(['class'=>'txt address'])->label('<span>*</span>详细地址：');
            echo $form->field($model,'phone')->textInput(['class'=>'txt'])->label('<span>*</span>手机号码：');
            echo $form->field($model,'status')->checkbox()->label('&nbsp;');
            echo '<li>
                        <label for="">&nbsp;</label>
                        <input type="submit" name="" class="btn" value="保存" />
                    </li>';
                echo '</ul>';
                \yii\widgets\ActiveForm::end();
            ?>
           <!-- <form action="" name="address_form">
                <ul>
                    <li>
                        <label for=""><span>*</span>收 货 人：</label>
                        <input type="text" name="" class="txt" />
                    </li>
                    <li>
                        <label for=""><span>*</span>所在地区：</label>
                        <select name="" id="">
                            <option value="">请选择</option>
                            <option value="">北京</option>
                            <option value="">上海</option>
                            <option value="">天津</option>
                            <option value="">重庆</option>
                            <option value="">武汉</option>
                        </select>

                        <select name="" id="">
                            <option value="">请选择</option>
                            <option value="">朝阳区</option>
                            <option value="">东城区</option>
                            <option value="">西城区</option>
                            <option value="">海淀区</option>
                            <option value="">昌平区</option>
                        </select>

                        <select name="" id="">
                            <option value="">请选择</option>
                            <option value="">西二旗</option>
                            <option value="">西三旗</option>
                            <option value="">三环以内</option>
                        </select>
                    </li>
                    <li>
                        <label for=""><span>*</span>详细地址：</label>
                        <input type="text" name="" class="txt address"  />
                    </li>
                    <li>
                        <label for=""><span>*</span>手机号码：</label>
                        <input type="text" name="" class="txt" />
                    </li>
                    <li>
                        <label for="">&nbsp;</label>
                        <input type="checkbox" name="" class="check" />设为默认地址
                    </li>
                    <li>
                        <label for="">&nbsp;</label>
                        <input type="submit" name="" class="btn" value="保存" />
                    </li>
                </ul>
            </form>-->
        </div>

    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->
<?php
/**
 * @var $this \yii\web\view
 */
$this->registerJsFile('@web/js/address.js');

$js = <<<JS
$(address).each(function(i,v) {
    var str = '<option value="'+v.name+'">'+v.name+'</option>';
        //因为要追加到市级下拉框，所以使用appendTo
        $(str).appendTo('#province');
});
$('#province').on('change',function() {
    $('#city option:not(:first)').remove();
    $('#area option:not(:first)').remove();
    var province = $(this).val();
    $(address).each(function() {
        if(this.name == province){
            $(this.city).each(function() {
                var str = '<option value="'+this.name+'">'+this.name+'</option>';
                //因为要追加到市级下拉框，所以使用appendTo
                $(str).appendTo('#city');
            })
        }
    })
});

$('#city').on('change',function() {
    $('#area option:not(:first)').remove();
    var province = $('#province').val();
    var city = $(this).val();
    console.debug(province,city);
    $(address).each(function() {
        if(this.name == province){
            $(this.city).each(function() {
                if(this.name == city){
                    $(this.area).each(function(i,v) {
                    var str = '<option value="'+v+'">'+v+'</option>';
                    //因为要追加到市级下拉框，所以使用appendTo
                    $(str).appendTo('#area');
                    })
                }
            })
        }
    });
  
})

JS;
$this->registerJS($js);

$js1 = '';
if($model->province){
    $js1 .= '$("#province").val("'.$model->province.'");';
}
if($model->city){
    $js1 .= '$("#province").change();$("#city").val("'.$model->city.'");';
}
if($model->area){
    $js1 .= '$("#city").change();$("#area").val("'.$model->area.'");';
}
$this->registerJs($js1);

