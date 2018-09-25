<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class ChangeController extends HomeBaseController{
	/**
    * 修改密码
    **/ 
    public function changePsd(){
        $iuid = $_SESSION['user']['id'];
        // 用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 新密码
        $new_psd['PassWord'] = md5(trim(I('post.passwords')));

        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $acid        =I('post.acid');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $result = M('User')->where(array('iuid'=>$iuid))->save($new_psd);
                if($result){
                    // 修改成功
                    // 发送给usa,更新usa数据
                    $usa    = new \Common\UsaApi\Usa;
                    $res = $usa->updateCustomer($userinfo['customerid'],I('post.passwords'));
                    if($res){
                        $sample['status'] = 1;
                        $this->ajaxreturn($sample);
                    }else{
                        $sample['status'] = 0;
                        $this->ajaxreturn($sample);
                    }
                }else{
                    // 修改失败
                    $sample['status'] = 0;
                    $this->ajaxreturn($sample);
                }
            }else{
                // 验证码错误
                $sample['status'] = 2;
                $this->ajaxreturn($sample);
            }
        }
    }

    /**
    *腾讯云发送短信(注册)
    *参数：phoneNumber(手机号),whichapp(指定app),acnumber(区号)
    **/
    public function smsCode(){
        // require __DIR__ . "/vendor/autoload.php";
        if(!IS_POST){
            $data['status'] = 100;
            $data['msg']    = '请填写手机号';
            $this->ajaxreturn($data);
        }else{
            vendor('SmsSing.SmsSingleSender');
            // 短信应用SDK AppID
            $appid = 1400096409; // 1400开头
            // 短信应用SDK AppKey
            $appkey = "fc1c7e21ab36fef1865b0a3110709c51";
            // 需要发送短信的手机号码
            $phoneNumber = I('post.phoneNumber');
            //手机区号
            $acnumber    = I('post.acnumber');
            // 短信模板ID，需要在短信应用中申请$templateId
            // 签名
            if($acnumber==86){
                $templateId = 127203;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
                $smsSign = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
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
    * 修改wv用户信息
    **/ 
    public function updateCustomer(){
        $iuid = $_SESSION['user']['id'];
        $data = I('post.');
        // p($data);die;
        // 修改系统数据
        $saveData = M('User')->where(array('iuid'=>$iuid))->save($data);
        if($saveData){
            //更新usa数据
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->updateCustomer($data['happyLifeID'],$data['password'],$data['Email'],$data['Phone'],$data['PlacementPreference']);
            if($result){
                $this->success('修改成功',U('Home/Purchase/myProfile'));
            }else{
                $this->error('修改失败',U('Home/Purchase/editProfile'));
            }
        }
    }

    /**
    * 重置密码
    **/ 
    public function resetPsd(){
        $iuid = $_SESSION['user']['id'];
        // 用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 新密码
        $new_psd['PassWord'] = md5(trim(I('post.passwords')));

        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $acid        =I('post.acid');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $result = M('User')->where(array('iuid'=>$iuid))->save($new_psd);
                if($result){
                    // 修改成功
                    // 发送给usa,更新usa数据
                    $usa    = new \Common\UsaApi\Usa;
                    $res = $usa->updateCustomer($userinfo['customerid'],I('post.passwords'));
                    if($res){
                        $sample['status'] = 1;
                        $this->ajaxreturn($sample);
                    }else{
                        $sample['status'] = 0;
                        $this->ajaxreturn($sample);
                    }
                }else{
                    // 修改失败
                    $sample['status'] = 0;
                    $this->ajaxreturn($sample);
                }
            }else{
                // 验证码错误
                $sample['status'] = 2;
                $this->ajaxreturn($sample);
            }
        }
    }
}

