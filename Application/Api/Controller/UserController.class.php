<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
 * 关于用户
 */
class UserController extends HomeBaseController{
    /**
    *注册用户 account password repassword(确认密码) name sex phone email
    *用户存在 202 注册成功201 注册失败200
    **/
    public function register(){
        $data = I('post.');
        // $account = I('post.account');
        $data['password']   = md5($data['password']);
        $data['repassword'] = md5($data['repassword']);
        $data['registertime'] = time();
        // p($data);die;
        $account = D('User')->where(array('account'=>$data['account']))->find();
        $phone    = D('User')->where(array('phone'=>$data['phone']))->find();
        if($data['account']){
            if($account||$phone){
                $data['status'] = 202;
                $data['msg']    ="该用户名或手机号已经存在";
                $this->ajaxreturn($data);
            }else{
                if ($data['repassword'] == $data['password']) {
                    $add = D('User')->field('account,name,password,sex,phone,registertime')->add($data);
                    if($add){
                        $data['status'] = 201;
                        $data['msg']    ="注册成功,请登录";
                        $this->ajaxreturn($data);
                    }else{
                        $data['status'] = 200;
                        $data['msg']    ="注册失败,请检查网络是否打开";
                        $this->ajaxreturn($data);
                    }
                }else{
                    $data['status'] = 200;
                    $data['msg']    ="密码输入不一致，请重新输入";
                    $this->ajaxreturn($data);
                }
            }
        }else{
            $data['status'] = 204;
            $data['msg']    ="不能为空";
            $this->ajaxreturn($data);
        }
    }
    /**
     * 登录 account(手机号或用户名) password
     * 202用户名或手机号错误 201成功 200错误
     */
    public function login(){
        $account              = I('post.account');
        $password             = I('post.password');
        $map['account|phone'] = $account;
        $data                 = M('User')->where($map)->find();
        if($data){
            if($data['password']!=md5($password)){
                $data['status'] =200;
                $data['msg']    ="密码错误";
                $this->ajaxReturn($data);
            }else{
                $data['status'] =201;
                $data['msg']    ="密码正确";
                $this->ajaxreturn($data);
            }
        }else{
            $data['status'] =202;
            $data['msg']    ="用户名或手机号错误";
            $this->ajaxreturn($data); 
        }
    }
    /**
     * 获取用户信息 uid
     * 200没有数据
     */
    public function userInfo(){
        $uid  = I('post.uid');
        $data = D('User')->where(array('uid'=>$uid))->find();
        if($data){
            $data['status']=201;
            $this->ajaxreturn($data);
        }else{
            $data['status']=200;
            $data['msg']="没有数据";
            $this->ajaxreturn($data); 
        }
    }
    /**
     * 修改用户信息 uid para paravalue
     */
    public function editUserInfo(){
        $data['uid']= I('post.uid');
        $para       = I('post.para');
        $paravalue  = I('post.paravalue');
        switch ($para) {
            case 'phone':
                $user = D('User')->where(array('uid'=>$data['uid']))->find();
                $info = D('User')->where(array('phone'=>$paravalue))->find();
                if($info){
                    $data['status']=202;
                    $data['msg']="该手机号已被使用";
                    $this->ajaxreturn($data);      
                }else{
                    $data['phone']   = $paravalue;
                }
                break;
            case 'sex':
                $data['sex']        = $paravalue;
                break;
            case 'email':
                $data['email']      = $paravalue;
                break;
            case 'name':
                $data['name']       = $paravalue;
                break;
        }
        $save = D('User')->save($data);
        if($save){
            $data['status']=201;
            $data['msg']="修改成功";
            $this->ajaxreturn($data);    
        }else{
            $data['status']=200;
            $data['msg']="修改失败";
            $this->ajaxreturn($data);     
        }
    }

    /**
    *修改密码
    *输入原密码password 新密码newPassword 对应后才可修改
    * 201成功 200修改失败
    **/
    public function editPassword(){
        $data['uid'] = I('post.uid');
        $oldPassword = I('post.password');
        $password    = I('post.newpassword');
        $data['password'] = md5($password);
        $user = D('User')->where(array('uid'=>$data['uid']))->find();
        if (md5($oldPassword) == $user['password']) {
            if ($oldPassword == $password) {
                $data['status']=200;
                $data['msg']="修改失败，新旧密码一致";
                $this->ajaxreturn($data); 
            }else{
                $save = D('User')->save($data);
                if ($save) {
                    $data['status']   = 201;
                    $data['edittime'] = time();
                    $data['msg']      ="修改成功";
                    $this->ajaxreturn($data);    
                }else{
                    $data['status']=200;
                    $data['msg']="修改失败，请检查网络状态";
                    $this->ajaxreturn($data);  
                }
            }
        }else{
            $data['status']=200;
            $data['msg']="修改失败,原密码输入错误";
            $this->ajaxreturn($data); 
        }
    }
    
}
