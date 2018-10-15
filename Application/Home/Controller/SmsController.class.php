<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class SmsController extends HomeBaseController{

    /**
    *通过用户获取手机号和地区
    **/
    // public function areaPhone(){
    //     $hu_nickname = I('post.hu_nickname');
    //     $tmpe        = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
    //     if($tmpe){
    //         if($tmpe['hu_phone']){
    //             if($tmpe['acnumber']){
    //                 $mape = D('Areacode')->where(array('acnumber'=>$tmpe['acnumber']))->select();
    //             }else{
    //                 $mape = D('Areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
    //             }
    //             $data['areaPhone'] = $mape;
    //             $data['phone']     = $tmpe['hu_phone'];
    //             if($data){
    //                 $data['status'] = 201;
    //                 $data['msg']    = '数据获取成功';
    //                 $this->ajaxreturn($data); 
    //             }else{
    //                 $data['status'] = 202;
    //                 $data['msg']    = '数据获取失败，请确认后重新提交';
    //                 $this->ajaxreturn($data); 
    //             }
    //         }else{
    //             $data['status'] = 203;
    //             $data['msg']    = '暂无绑定手机号';
    //             $this->ajaxreturn($data);  
    //         }
    //     }else{
    //         $data['status'] = 204;
    //         $data['msg']    = '用户不存在，请确认后重新提交';
    //         $this->ajaxreturn($data);
    //     }
    // }
    /****************************************************************首次注册验证码********************************************************************************/
    /**
    *注册手机区号 is_show值为1
    **/
    public function areacode(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
        // $this->assign('data',$data);
        // $this->display();
    }
    /**
    *腾讯云发送短信(注册)
    *参数：phoneNumber(手机号),whichapp(指定app),acnumber(区号)
    **/
    public function registerSmsCode(){
        // require __DIR__ . "/vendor/autoload.php";
        if(!IS_POST){
            $data['status'] = 100;
            $data['msg']    = '请填写手机号';
            $this->ajaxreturn($data);
        }else{
            vendor('SmsSing.SmsSingleSender');
            // 短信应用SDK AppID
            $appid = 1400149268; // 1400开头
            // 短信应用SDK AppKey
            $appkey = "010151f33eaec872109b1b507c820bce";
            // 需要发送短信的手机号码
            $phoneNumber = I('post.phoneNumber');
            //手机区号
            $acnumber    = I('post.acnumber');
            // 短信模板ID，需要在短信应用中申请$templateId
            // 签名
            if($acnumber==86){
                $templateId = 173737  ;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
                $smsSign = "安永中国"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
            }else if($acnumber==886 || $acnumber==852 || $acnumber==853){
                $templateId = 127206;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }else{
                $templateId = 127204;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }
            $code   =rand(100000,999999);
            $minute ='1';
            // 指定模板ID单发短信
            try {
                $ssender = new \SmsSingleSender($appid, $appkey);
                $params = array($code,$minute);
                $result = $ssender->sendWithParam($acnumber,$phoneNumber,$templateId,$params,$smsSign,"","");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                $rsp = json_decode($result,true);
                if($rsp['errmsg']=='OK'){
                    $mape  = array(
                        'phone'   =>$phoneNumber,
                        'code'    =>$code,
                        'acnumber'=>$acnumber,
                        'date'    =>date('Y-m-d H:i:s')
                    );
                    $add = D('Smscode')->add($mape);
                    $data['status'] = 101;
                    $data['msg']    = '验证码发送成功,请留意短信';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 103;
                    $data['msg']    = '发送失败,请确认手机号码正确并有效';
                    $this->ajaxreturn($data);
                }
            }catch(\Exception $e) {
                $data['status'] = 102;
                $data['msg']    = '发送失败,请确认手机号码正确并有效';
                $this->ajaxreturn($data);
            }

        }
    }
    /**
    *检查注册验证码是否正确
    *参数：phoneNumber(手机号),whichapp(指定app),acnumber(区号),code(验证码)
    **/
    public function registerOInCode(){
        if(!IS_POST){
            $data['status'] = 200;
            $data['msg']    = '请填写验证码';
            $this->ajaxreturn($data);
        }else{
            $phoneNumber =I('post.phoneNumber');
            $code        =I('post.code');
            $acnumber    =I('post.acnumber');
            $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
            $time        =time()-strtotime($data['date']);
            if($time>60){
                $data['status'] = 203;
                $data['msg']    = '验证码失效,请重新发送';
                $this->ajaxreturn($data);
            }else{
                if($data && $data['code']==$code){
                    $data['status'] = 201;
                    $data['msg']    = '验证码正确';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 202;
                    $data['msg']    = '验证码错误';
                    $this->ajaxreturn($data);
                }
            }
        }
    }  
}


?>
