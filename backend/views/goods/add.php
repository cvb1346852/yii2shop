<?php

use yii\web\JsExpression;

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
//echo $form->field($model,'imgFile')->fileInput();
echo $form->field($model,'logo')->hiddenInput(['id'=>'goods-logo']);
echo \yii\bootstrap\Html::fileInput('test',null,['id'=>'test']);
echo \xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['upload/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'width' => 120,
        'height' => 40,
        'onUploadError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadSuccess' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //图片上传成功后将地址写入img标签
        $('#img_logo').attr('src',data.fileUrl).show();
        //图片上传成功后将地址保存到logo
        $('#goods-logo').val(data.fileUrl);
    }
}
EOF
        ),
    ]
]);
if ($model->logo){
    echo \yii\bootstrap\Html::img($model->logo,['id'=>'img_logo','width'=>100]);
}else{
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','id'=>'img_logo','width'=>100]);
}
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<ul id="treeDemo" class="ztree"></ul>';
echo $form->field($model,'brand_id')->dropDownList(\backend\models\Goods::getBrand(),['prompt'=>'请选择品牌']);
echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
echo $form->field($model,'is_on_sale')->radioList(\backend\models\Goods::$saleOptions);
echo $form->field($model,'status')->radioList(\backend\models\Goods::$statusOptions);
echo $form->field($model,'sort');
//echo $form->field($content,'content')->textarea();
echo $form->field($content,'content')->widget(\kucha\ueditor\UEditor::className(),[]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success']);
\yii\bootstrap\ActiveForm::end();

$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$zNode = \yii\helpers\Json::encode($goods_category);
$js = new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback:{
                onClick:function(event, treeId, treeNode){
                    $('#goods-goods_category_id').val(treeNode.id);
                }
            }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$zNode}
         /*{
         name: "父节点1", children: [
		{name: "子节点1"},
		{name: "子节点2"}
	    ]}*/
        
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            zTreeObj.expandAll(true);
            var node =  zTreeObj.getNodeByParam("id",$('#goods-goods_category_id').val(), null);
            //console.debug(node[0]);
            zTreeObj.selectNode(node);

JS
);
$this->registerJs($js);