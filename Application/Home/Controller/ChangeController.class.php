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
        // 原密码
        $psd = md5(trim(I('post.password')));
        // 用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 新密码
        $new_psd['PassWord'] = md5(trim(I('post.passwords')));
        if($userinfo['password'] != $psd){
            // 密码不正确
            $sample['status'] = 2;
            $this->ajaxreturn($sample);
        }else{
            $result = M('User')->where(array('iuid'=>$iuid))->save($new_psd);
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

