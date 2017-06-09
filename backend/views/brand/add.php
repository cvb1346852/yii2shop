<?php
use yii\web\JsExpression;

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'logo')->hiddenInput();
//echo $form->field($model,'imgFile')->fileInput(['id'=>'test']);
echo \yii\bootstrap\Html::fileInput('test',null,['id'=>'test']);
echo \xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
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
        $('#brand-logo').val(data.fileUrl);
    }
}
EOF
        ),
    ]
]);
if ($model->logo){
    echo \yii\bootstrap\Html::img($model->logo,['id'=>'img_logo','width'=>100]);
}else{
    echo \yii\bootstrap\Html::img($model->logo,['style'=>'display:none','id'=>'img_logo','width'=>100]);
}
echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',0=>'隐藏']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();