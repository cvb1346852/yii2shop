<?php

use yii\web\JsExpression;

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'goods_id')->hiddenInput(['value'=>$_GET['goods_id']])->label(false);
echo $form->field($model,'photo')->hiddenInput(['id'=>'goods-photo']);
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
        $('#goods-photo').val(data.fileUrl);
    }
}
EOF
        ),
    ]
]);
if ($model->photo){
    echo \yii\bootstrap\Html::img($model->photo,['id'=>'img_logo','width'=>100]);
}else{
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','id'=>'img_logo','width'=>100]);
}
echo '<div>'.\yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success']).'</div>';
\yii\bootstrap\ActiveForm::end();