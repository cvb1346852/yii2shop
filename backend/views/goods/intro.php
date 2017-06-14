<table class="table table-bordered" >
    <tr>
        <td>商品名称</td>
        <td><?=$model->name?></td>
    </tr>
    <tr><td>商品货号</td><td><?=$model->sn?></td></tr>
    <tr><td>商品添加时间</td><td><?=date('Y-m-d H:i:s',$model->create_time)?></td></tr>
    <tr><td>商品内容</td><td><?=$model->goodsIntro->content?></td></tr>
</table>