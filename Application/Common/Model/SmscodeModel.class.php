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
    // public function sms($acnumber,$phoneNumber,$params,$templateId,$appid='1400149268',$appkey='010151f33eaec872109b1b507c820bce',$smsSign='安永中国'){
    //     vendor('SmsSing.SmsSingleSender');
    //     // 指定模板ID单发短信
    //     try {
    //         $ssender = new \SmsSingleSender($appid, $appkey);
    //         $result = $ssender->sendWithParam($acnumber,$phoneNumber,$templateId,$params,$smsSign,"","");  // 签名参数未提供或者为空时，会使用默认签名发送短信
    //         $rsp = json_decode($result,true);
    //     }catch(\Exception $e) {
    //         $rsp['errmsg']=='NO';
    //     }
    //     return $rsp;
    // }
    public function sms($acnumber,$phoneNumber,$params,$templateId,$appid='1400096409',$appkey='fc1c7e21ab36fef1865b0a3110709c51',$smsSign='三次猿'){
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

    /**
    * 添加短信发送日志
    **/ 
    public function addLog($acnumber,$phone,$operator,$addressee,$product_name,$content,$customerid){
        $array = array(
            'acnumber' => $acnumber,
            'phone' => $phone,
            'operator' => $operator,
            'addressee' => $addressee,
            'product_name' => $product_name,
            'date' => time(),
            'content' => $content,
            'customerid' => $customerid
        );
        $addLog = M('SmsLog')->add($array);
        return $addLog;
    }
}
