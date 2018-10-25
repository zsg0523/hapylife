<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class EmailController extends HomeBaseController{
    //核对邮箱是否注册
    public function checkMail(){
        $email    = I('post.email');
        $happyLifeID  = I('post.happyLifeID');
        $userinfo   = M('User')->where(array('CustomerID'=>$happyLifeID))->find();
        if(!$userinfo){
            $data = array(
                'status' => 2,
                'msg' => '用户不存在'
            );
            $this->ajaxreturn($data);
        }else{
            //控制URL链接的时效性
            $passwordToken = md5($userinfo['iuid'].$userinfo['customerid'].$userinfo['password']);
            $link = "http://localhost/hapylife/index.php/Home/Email/checkToken/email/{$email}/happyLifeID/{$happyLifeID}/passwordToken/{$passwordToken}";
            $str  = "您好!{$userinfo['lastname']}{$userinfo['firstname']},请点击下面的链接重置您的密码：<p></p>".$link;
            $sendResult = sendMail($email,"Hapylife重置密码",$str);
            if($sendResult) {
                $data = array(
                    'status' => 1,
                    'msg' => '发送成功'
                );
                $this->ajaxreturn($data);
            }else{
                $data = array(
                    'status' => 0,
                    'msg' => '发送失败'
                );
                $this->ajaxreturn($data);
            }
        }
    }


    public function checkToken(){
        //说明:首先接受参数email和token，然后根据email查询数据表user中是否存在该Email，如果存在则获取该用户的信息，并且和数据库中的token组合方式一样构建token值，然后与url传过来的token进行对比，如果当前时间与发送邮件时的时间相差超过24小时的，则提示“该链接已过期！”，反之，则说明链接有效，并且调转到重置密码页面，最后就是用户自己设置新密码了
        $happyLifeID    = I('get.happyLifeID');
        $passwordToken  = I('get.passwordToken');
        $userinfo = M('User')->where(array('CustomerID'=>$happyLifeID))->find();
        if(!$userinfo) exit('error link');
        $checkToken = md5($userinfo['iuid'].$userinfo['customerid'].$userinfo['password']);
        if($checkToken != $passwordToken) exit('this no exit link');
        $link = "http://localhost/hapylife/index.php/Home/Purchase/editNewEmail/happyLifeID/{$happyLifeID}"; 
        // 跳转至客户密码重置页面
        header('location:' . $link);
    }

    public function reSet(){
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
                $this->success('修改成功',U('Home/Index/login'));
            }else{
                $this->error('修改失败',U('Home/Purchase/editNewEmail'));
            }
        }else{
            $this->error('用户不存在',U('Home/Purchase/editNewEmail'));
        }
    }

}

