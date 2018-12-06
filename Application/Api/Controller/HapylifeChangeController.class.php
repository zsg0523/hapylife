<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 修改密码
**/
class HapylifeChangeController extends HomeBaseController{
    /**
    * 修改用户密码
    **/ 
    public function changePassWord(){
        $iuid = I('post.iuid');
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
        $usa         = new \Common\UsaApi\Usa;

        if($time>60){
            $sample = array(
                'status' => 202,
                'msg' => '验证码已过期'
            );
            $this->ajaxreturn($sample);
        }else{
            if($new_array['PassWord'] != $userinfo['password'] || $new_array['WvPass'] != $userinfo['wvpass']){
                if($data && $data['code']==$code){
                    // 修改用户信息
                    $result = M('User')->where(array('iuid'=>$iuid))->save($new_array);
                    if($result){
                        // 发送给usa,更新usa数据
                        $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                        if($res['code'] == 200){
                            $sample = array(
                                'status' => 1,
                                'msg' => '修改成功'
                            );
                            $this->ajaxreturn($sample);
                        }else{
                            $sample = array(
                                'status' => 0,
                                'msg' => '修改失败'
                            );
                            $this->ajaxreturn($sample);
                        }
                    }else{
                        // 修改失败
                        $sample = array(
                            'status' => 0,
                            'msg' => '修改失败'
                        );
                        $this->ajaxreturn($sample);
                    }
                }else{
                    // 验证码错误
                     $sample = array(
                        'status' => 2,
                        'msg' => '验证码错误'
                    );
                    $this->ajaxreturn($sample);
                }
            }else{
                // 发送给usa,更新usa数据
                $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                if($res['code'] == 200){
                    $sample = array(
                        'status' => 1,
                        'msg' => '修改成功'
                    );
                    $this->ajaxreturn($sample);
                }else{
                    // 修改失败
                    $sample = array(
                        'status' => 0,
                        'msg' => '修改失败'
                    );
                    $this->ajaxreturn($sample);
                }
            }
        }
    }

    /**
    * 修改wv用户信息
    **/ 
    public function updateCustomer(){
        $data = I('post.');
        // p($data);die;
        $signPhone = M('User')->getField('phone',true);
        if(in_array($data['Phone'],$signPhone)){
            $msg = array(
                'msg' => '该号码已注册',
                'status' => '202'
            );
            $this->ajaxreturn($msg);
        }else{
            // 修改系统数据
            $saveData = M('User')->where(array('customerid'=>$data['happyLifeID']))->save($data);
        }
        if($saveData){
            //更新usa数据
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->updateCustomer($data['happyLifeID'],$data['Email'],$data['Phone'],$data['Placement']);
            if($result['code'] == 200){
                $msg = array(
                    'msg' => '修改成功',
                    'status' => '1'
                );
                $this->ajaxreturn($msg);
            }else{
                $msg = array(
                    'msg' => '修改失败',
                    'status' => '0'
                );
                $this->ajaxreturn($msg);
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
        $usa         = new \Common\UsaApi\Usa;
        if($time>60){
            // 验证码失效
            $sample = array(
                'status' => 3,
                'msg' => '验证码失效'
            );
            $this->ajaxreturn($sample);
        }else{
            if($new_array['PassWord'] != $userinfo['password'] || $new_array['WvPass'] != $userinfo['wvpass']){
                if($data && $data['code']==$code){
                    // 修改用户信息
                    $result = M('User')->where(array('customerid'=>$customerid))->save($new_array);
                    if($result){
                        // 发送给usa,更新usa数据
                        $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                        if($res['code'] == 200){
                            $sample = array(
                                'status' => 1,
                                'msg' => '修改成功'
                            );
                            $this->ajaxreturn($sample);
                        }else{
                            $sample = array(
                                'status' => 0,
                                'msg' => '修改失败'
                            );
                            $this->ajaxreturn($sample);
                        }
                    }else{
                        // 修改失败
                        $sample = array(
                            'status' => 0,
                            'msg' => '修改失败'
                        );
                        $this->ajaxreturn($sample);
                    }
                }else{
                    // 验证码错误
                    $sample = array(
                        'status' => 2,
                        'msg' => '验证码错误'
                    );
                    $this->ajaxreturn($sample);
                }
            }else{
                // 发送给usa,更新usa数据
                $res = $usa->changePassWord($userinfo['customerid'],I('post.passwords'));
                if($res['code'] == 200){
                    $sample = array(
                        'status' => 1,
                        'msg' => '修改成功'
                    );
                    $this->ajaxreturn($sample);
                }else{
                    $sample = array(
                        'status' => 0,
                        'msg' => '修改失败'
                    );
                    $this->ajaxreturn($sample);
                }
            }
        }
    }

    /**
    * 通过手机号码查询用户信息
    **/ 
    public function checkByPhone(){
        $phoneNumber = I('post.number');
        $data = M('User')->where(array('Phone'=>$phoneNumber))->select();
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'msg' => '号码不存在账号',
                'status' => 0
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 检验验证码是否正确
    * 同时发送短信
    **/ 
    public function smscount(){
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $userinfo    =M('User')->where(array('Phone'=>$phoneNumber))->select();
        foreach($userinfo as $key=>$value){
            $id1 .= $value['customerid'].'、';
            if($value['wvcustomerid']){
                $id2 .= $value['wvcustomerid'].'、';
            }else{
                $id2 .= '暂无、';
            }
        }
        $wvcustomerid = substr($id1,0,-3);
        $customerid = substr($id2,0,-3);

        $time        =time()-strtotime($data['date']);
        if($time>60){
            $sample = array(
                'status' => 404,
                'msg' => '验证码失效'
            );
            $this->ajaxreturn($sample);
        }else{
            if($data && $data['code']==$code){
                $templateId ='223694';
                $params     = array($wvcustomerid,$customerid);
                $sms        = D('Smscode')->sms($acnumber,$phoneNumber,$params,$templateId);
                if($sms['errmsg'] == 'OK'){
                    $addressee = $userinfo['lastname'].$userinfo['firstname'];
                    $contents = '您的Hapylife账号为：'.$wvcustomerid.'，DT的ID：'.$customerid.'。';
                    $addlog = D('Smscode')->addLog($acnumber,$phoneNumber,'系统',$addressee,$contents,$customerid);
                }
                if($addlog){
                    $sample = array(
                        'status' => 1,
                        'msg' => '发送成功'
                    );
                    $this->ajaxreturn($sample);

                }else{
                    $sample = array(
                        'status' => 0,
                        'msg' => '发送失败'
                    );
                    $this->ajaxreturn($sample);
                }
            }else{
                $sample = array(
                    'status' => 202,
                    'msg' => '验证码错误'
                );
                $this->ajaxreturn($sample);
            }
        }
    }

    /**
    * 修改wv用户信息--手机
    **/ 
    public function updatePhone(){
        $data = I('post.');
        // p($data);die;
        $signPhone = M('User')->getField('phone',true);
        if(in_array($data['Phone'],$signPhone)){
            $sample = array(
                'status' => 202,
                'msg' => '该手机号码已被注册，请重新填写！'
            );
            $this->ajaxreturn($sample);
        }else{
            // 修改系统数据
            $saveData = M('User')->where(array('CustomerID'=>$data['happyLifeID']))->save($data);
        }
        if($saveData){
            //更新usa数据
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->changePhone($data['happyLifeID'],$data['Phone']);
            if($result['code'] == 200){
                $sample = array(
                    'status' => 1,
                    'msg' => '修改成功'
                );
                $this->ajaxreturn($sample);
            }else{
                $sample = array(
                    'status' => 0,
                    'msg' => '修改失败'
                );
                $this->ajaxreturn($sample);
            }
        }
    }

    /**
    * 修改wv用户信息--左右脚
    **/ 
    public function updatePlacement(){
        $data = I('post.');
        $userinfo = M('User')->where(array('CustomerID'=>$data['happyLifeID']))->find();
        if($userinfo['placement'] != $data['Placement']){
            // 修改系统数据
            $saveData = M('User')->where(array('CustomerID'=>$data['happyLifeID']))->save($data);
        }
        switch($data['Placement']){
            case 'BuildLeft':
                $note = '区域一';
                break;
            case 'BuildRight':
                $note = '区域二';
                break;
            case 'StrongLegOutside':
                $note = '大区域';
                break;
            case 'WeakLeg':
                $note = '小区域';
                break;
        }
        //更新usa数据
        $usa    = new \Common\UsaApi\Usa;
        $result = $usa->ChangePlacement($data['happyLifeID'],$note);
        if($result['code'] == 200){
            $sample = array(
                'status' => 1,
                'msg' => '修改成功'
            );
            $this->ajaxreturn($sample);
        }else{
            $sample = array(
                'status' => 0,
                'msg' => '修改失败'
            );
            $this->ajaxreturn($sample);
        }   
    }

    /**
    * 修改邮箱
    **/ 
    public function updateEmail(){
        $email  = I('post.Email');
        $happyLifeID    = I('post.happyLifeID');
        $userinfo = M('User')->where(array('CustomerID'=>$happyLifeID))->find();
        if($userinfo){
            if($userinfo['email'] != $email){
                $save = M('User')->where(array('CustomerID'=>$happyLifeID))->setfield('Email',$email);
            }
            //更新usa数据
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->ChangeEmail($happyLifeID,$email);
            if($result['code'] == 200){
                $data = array(
                    'status' => 1,
                    'msg' => '修改成功'
                );
                $this->ajaxreturn($data);
            }else{
                $data = array(
                    'status' => 0,
                    'msg' => '修改失败'
                );
                $this->ajaxreturn($data);
            }
        }else{
            $data = array(
                'status' => 202,
                'msg' => '用户不存在'
            );
            $this->ajaxreturn($data);
        }
    }

}