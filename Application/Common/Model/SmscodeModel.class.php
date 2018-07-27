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
    public function sms($params,$appid='1400096409',$appkey='fc1c7e21ab36fef1865b0a3110709c51',$smsSign='三次猿',$templateId='127203',$phoneNumber='18902448086',$acnumber='86'){
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
