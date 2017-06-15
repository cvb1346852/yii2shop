<?php
/* @var $this yii\web\View */
?>
<h1>用户列表</h1>

<table class="table ">
    <tr>
        <td>id</td>
        <td>用户名</td>
        <td>邮箱</td>
        <td>状态</td>
        <td>最后登录时间</td>
        <td>最后登录ip</td>
        <td>操作</td>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?=$user->id?></td>
        <td><?=$user->username?></td>
        <td><?=$user->email?></td>
        <td><?=\backend\models\User::$statusOption[$user->status]?></td>
        <td><?=date('Y-m-d H:i:s',$user->last_login_time)?></td>
        <td><?=$user->last_login_ip?></td>
        <td>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-pencil"></span>',['user/edit','id'=>$user->id],['class'=>'btn btn-warning btn-sm'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-trash"></span>',['user/delete','id'=>$user->id],['class'=>'btn btn-danger btn-sm'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>