<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 短信model
 */
class SmscodeModel extends BaseModel{
	/**
    *腾讯云发送短信(纯发送短信)
    *参数：phoneNumber(手机号),acnumber(区号)
    **/
    public function sms($appid='1400094198',$appkey='81ab38811e166f44ec8b77e09ee7c032',$phoneNumber,$acnumber,$templateId,$smsSign,$params){
        // require __DIR__ . "/vendor/autoload.php";
        vendor('SmsSing.SmsSingleSender');
        // 指定模板ID单发短信
        try {
            $ssender = new \SmsSingleSender($appid, $appkey);
            $result = $ssender->sendWithParam($acnumber,$phoneNumber,$templateId,$params,$smsSign,"","");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $rsp = json_decode($result,true);
        }catch(\Exception $e) {
            $rsp['errmsg']=='NO';
        }
        return $rsp;
    }
}
