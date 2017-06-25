<?php
namespace frontend\components;

use Flc\Alidayu\App;
use Flc\Alidayu\Client;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use yii\base\Component;

class Sms extends Component{
    public $appKey;
    public $appSecret;
    public $FreeSignName;
    public $TemplateCode;
    private $_num;
    private $_code=[];

    //设置手机号码
    public function setNum($num){
        $this->_num = $num;
        return $this;
    }
    //设置短信内容
    public function setCode($code){
        $this->_code = ['code'=>$code];
        return $this;
    }
    //设置短信签名
    public function setSign($sign){
        $this->FreeSignName = $sign;
        return $this;
    }
    //设置短信模板
    public function setTemp($temp){
        $this->TemplateCode = $temp;
        return $this;
    }

//    发送短信
    public function send(){
        $client = new Client(new App([ 'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,]));
        $req = new AlibabaAliqinFcSmsNumSend();
        $req->setRecNum($this->_num)//电话号码
        ->setSmsParam($this->_code)
            ->setSmsFreeSignName($this->FreeSignName)
            ->setSmsTemplateCode($this->TemplateCode);

       return $client->execute($req);
    }

}