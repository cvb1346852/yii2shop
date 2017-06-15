<div class="site-login">

    <h2>用户登录</h2>
    <div class="row">
        <div class="col-lg-5">
<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className());
echo $form->field($model,'rememberMe')->checkbox();
echo \yii\bootstrap\Html::submitButton('登录',['class'=>'btn btn-info']);
echo \yii\bootstrap\Html::a('注册',['user/add'],['class'=>'btn btn-warning']);
\yii\bootstrap\ActiveForm::end();
?>
        </div>
    </div>
</div>
