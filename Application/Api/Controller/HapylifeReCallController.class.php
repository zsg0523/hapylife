<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 二次发送付费通知短信
**/
class HapylifeReCallController extends HomeBaseController{
    public function ReCall(){
        $data = M('wvNotification')->where(array('secStatus'=>0))->select();
        foreach($data as $key=>$value){
            $message[$value['id']] = json_decode($value['messages'],true);
            $message[$value['id']]['addtime'] = $value['addtime'];
        }
        foreach($message as $key=>$value){
            if($value['OrderTypeId'] == 4){
                $ReceiptPayTime = M('Receipt')->where(array('ir_receiptnum'=>$value['OrderId']))->getfield('ir_paytime');
                if(!empty($ReceiptPayTime)){
                    $status = 1;
                }else{
                    // 用户信息
                    $userinfo = M('User')->where(array('CustomerID'=>$value['HplId']))->find();
                    // 收信人名称
                    $addressee = $userinfo['lastname'].$userinfo['firstname'];
                    $time = date('Y-m-d');
                    // 判断发送时间是否超过4天
                    if(bcdiv(bcsub(time(),strtotime($value['addtime'])),86400,0)>=4){
                        // 发送短信提示
                        $templateId ='236758';
                        $params     = array($addressee,$time);
                        $sms        = D('Smscode')->sms('86',$userinfo['phone'],$params,$templateId);
                        $content = '亲爱的会员'.$addressee.'，这是系统提醒消息，您有未支付的订单，请在'.$time.'之前完成支付。';
                        if($sms['result'] == 0){
                            $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,'二次缴费通知',$content,$userinfo['customerid']);
                        }else{
                            $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,$sms['errmsg'],$content,$userinfo['customerid']);
                        }

                        if($result){
                            $status = M('wvNotification')->where(array('id'=>$key))->setfield('secStatus','1');
                        }
                    }
                }       
            }
        }
        if($status){
            $map = array(
                'status' => 200,
                'msg' => '调用完成'
            );
            $this->ajaxreturn($map);
        }
    }
}