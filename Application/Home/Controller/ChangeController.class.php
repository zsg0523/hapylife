<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class ChangeController extends HomeBaseController{
    /**
    *   获取验证码
    **/ 
    public function changePassword(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('data',$data);
        $this->display();
    }

    /**
    * 修改密码
    **/ 
    public function changePsd(){
        $iuid = $_SESSION['user']['id'];
        // 用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 新密码
        $new_array = array(
            'PassWord' => md5(trim(I('post.passwords'))),
            'WvPass' => trim(I('post.passwords')),
        );
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($new_array['PassWord'] != $userinfo['password'] || $new_array['WvPass'] != $userinfo['wvpass']){
                if($data && $data['code']==$code){
                    // 修改用户信息
                    $result = M('User')->where(array('iuid'=>$iuid))->save($new_array);
                    if($result){
                        // 发送给usa,更新usa数据
                        $usa    = new \Common\UsaApi\Usa;
                        $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                        if($res['code'] == 200){
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
            }else{
                // 发送给usa,更新usa数据
                $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                if($res['code'] == 200){
                    $sample['status'] = 1;
                    $this->ajaxreturn($sample);
                }else{
                    $sample['status'] = 0;
                    $this->ajaxreturn($sample);
                }
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
                $templateId = 209020;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
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
    * 修改wv用户信息
    **/ 
    public function updateCustomer(){
        $iuid = $_SESSION['user']['id'];
        $data = I('post.');
        // p($data);die;
        $signPhone = M('User')->getField('phone',true);
        if(in_array($data['Phone'],$signPhone)){
            $this->error('该手机号码已被注册，请重新填写！',U('Home/Purchase/editProfile'));
        }else{
            // 修改系统数据
            $saveData = M('User')->where(array('iuid'=>$iuid))->save($data);
        }
        if($saveData){
            //更新usa数据
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->updateCustomer($data['happyLifeID'],$data['Email'],$data['Phone'],$data['PlacementPreference']);
            if($result['code'] == 200){
                $this->success('修改成功',U('Home/Purchase/myProfile'));
            }else{
                $this->error('修改失败',U('Home/Purchase/editProfile'));
            }
        }
    }

    public function forgot(){
        $this->display();
    }

    /**
    * 判断账号是否HPL开头
    **/ 
    public function checkAccount(){
        $CustomerID = strtoupper(I('post.CustomerID'));
        if(substr($CustomerID,0,3) == 'HPL'){
            $data = M('User')->where(array('customerid'=>$CustomerID))->find();
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }else{
            $data['status'] = 2;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 忘记密码
    **/ 
    public function forgotPsd(){
        $customerid = I('post.customerid');
        // 用户信息
        $userinfo = M('User')->where(array('customerid'=>$customerid))->find();

        $new_array = array(
            'PassWord' => md5(trim(I('post.passwords'))),
            'WvPass' => trim(I('post.passwords'))
        );

        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($new_array['PassWord'] != $userinfo['password'] || $new_array['WvPass'] != $userinfo['wvpass']){
                if($data && $data['code']==$code){
                    // 修改用户信息
                    $result = M('User')->where(array('customerid'=>$customerid))->save($new_array);
                    if($result){
                        // 发送给usa,更新usa数据
                        $usa    = new \Common\UsaApi\Usa;
                        $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                        if($res['code'] == 200){
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
            }else{
                // 发送给usa,更新usa数据
                $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                if($res['code'] == 200){
                    $sample['status'] = 1;
                    $this->ajaxreturn($sample);
                }else{
                    $sample['status'] = 0;
                    $this->ajaxreturn($sample);
                }
            }
        }
    }
}

