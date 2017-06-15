<?php

use yii\web\JsExpression;

/* @var $this yii\web\View */
?>
<h2>商品相册</h2>
<h3><?=$goods->name?></h3>
<p>

    <?php
    echo \yii\bootstrap\Html::fileInput('test',null,['id'=>'test']);
    echo \xj\uploadify\Uploadify::widget([
        'url' => yii\helpers\Url::to(['s-upload']),
        'id' => 'test',
        'csrf' => true,
        'renderTag' => false,
        'jsOptions' => [
            'formData' =>['goods_id'=>$goods->id],
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
        console.log(data);
        //图片上传成功后将地址写入img标签
        //$('#img_logo').attr('src',data.fileUrl).show();
        //图片上传成功后将地址保存到logo
        //$('#goods-logo').val(data.fileUrl);
        
        var html = '<tr data-id="'+data.id+'" id="photo_'+data.id+'">';
        html += '<td><img src="'+data.fileUrl+'" width="400"/></td>';
        html += '<td><button type="button" class="btn btn-danger btn-xs del_btn">删除</button></td></tr>';
        $('table').append(html);
    }
}
EOF
            ),
        ]
    ]);
    ?>
</p>
<table class="table table-striped">
    <tr>
        <td>图片</td>
        <td>操作</td>
    </tr>
    <?php foreach ($models as $model):?>
    <tr data-id="<?=$model->id?>" id="photo_<?=$model->id?>">
        <td><?=\yii\bootstrap\Html::img($model->photo,['width'=>400])?></td>
        <td>
            <?=\yii\bootstrap\Html::button('<span class="glyphicon glyphicon-trash"></span>',['class'=>'btn btn-danger btn-xs del_btn']) ?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<?php
$url = \yii\helpers\Url::to(['goods-photo/delete']);
$this->registerJs(new JsExpression(
    <<<EOT
    $('table').on('click','.del_btn',function(){
        var id = $(this).closest('tr').attr('data-id');
        console.debug(id);
        $.post('{$url}',{id:id},function(data){
    console.debug(data);
            if(data === 'success'){
            alert('删除成功');
            $('#photo_'+id).remove();
            }
        })
    })
EOT

));
