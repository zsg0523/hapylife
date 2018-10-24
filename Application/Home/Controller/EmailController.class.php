<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class EmailController extends HomeBaseController{
    //核对邮箱是否注册
    public function checkMail(){
        $account  = I('post.account');
        $email    = I('post.mail');
        $result   = M('User')->where(array('Email'=>$email))->order('iuid DESC')->select();
        if(!$result){
            $status = 3;
            $this->ajaxreturn($status);
        }else{
            //控制URL链接的时效性
            $passwordToken = md5($result[0]['iuid'].$result[0]['customerid'].$result[0]['password']);
            $link = "http://localhost/hapylife/index.php/Home/Email/checkToken/email/{$email}/passwordToken/{$passwordToken}";
            $str  = "您好!{$result[0]['lastname']}{$result[0]['firstname']},请点击下面的链接重置您的密码：<p></p>".$link;
            $sendResult = sendMail($email,"Hapylife重置密码",$str);
            if($sendResult) {
                $status = 1;
                $this->ajaxreturn($status);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
        
        //修改密码发送时间
        // $passwordTime = time();
        // $where['uid'] = $result['uid'];
        // $updateResult = M('user')
        //              ->where($where)
        //              ->setField('passwordTime',$passwordTime);
        // if(!$updateResult) exit('修改数据库密码发送时间失败');
        // exit("系统已向您的邮箱发送了一封邮件<br/>请登录到您的邮箱及时重置您的密码！");

    }


    public function checkToken(){
        //说明:首先接受参数email和token，然后根据email查询数据表user中是否存在该Email，如果存在则获取该用户的信息，并且和数据库中的token组合方式一样构建token值，然后与url传过来的token进行对比，如果当前时间与发送邮件时的时间相差超过24小时的，则提示“该链接已过期！”，反之，则说明链接有效，并且调转到重置密码页面，最后就是用户自己设置新密码了
        $email          = I('get.email');
        $passwordToken  = I('get.passwordToken');
        $result         = M('User')->where(array('Email'=>$email))->order('iuid DESC')->select();
        if(!$result) exit('error link');
        $checkToken = md5($result[0]['iuid'].$result[0]['customerid'].$result[0]['password']);
        if($checkToken != $passwordToken) exit('this no exit link');
        $link = "http://localhost/hapylife/index.php/Home/Email/reSet?iuid={$result[0]['iuid']}"; 
        // 跳转至客户密码重置页面
        header('location:' . $link);
    }

}

