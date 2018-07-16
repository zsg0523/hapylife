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
        // 账号
        $CustomerID = strtoupper(trim(I('post.CustomerID')));
        // 原密码
        $psd = md5(trim(I('post.password')));
        // 用户信息
        $userinfo = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        if($userinfo['password'] != $psd){
            // 密码不正确
            $sample['status'] = 2;
            $this->ajaxreturn($sample);
        }else{
            // 新密码
            $new_psd['PassWord'] = md5(trim(I('post.passowrds')));   
            $result = M('User')->where(array('CustomerID'=>$CustomerID))->save($new_psd);
            if($result){
                // 修改成功
                $sample['status'] = 1;
                $this->ajaxreturn($sample);
            }else{
                // 修改失败
                $sample['status'] = 0;
                $this->ajaxreturn($sample);
            }
        }
    }

}

