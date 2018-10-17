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
    * 修改wv用户信息
    **/ 
    public function updateCustomer(){
        $iuid = I('post.iuid');
        $data = I('post.');
        // p($data);die;
        $signPhone = M('User')->getField('phone',true);
        if(in_array($data['Phone'],$signPhone)){
            $msg = array(
                'msg' => '该号码已注册',
                'status' => '202'
            );
            $this->ajaxreturn();
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

}