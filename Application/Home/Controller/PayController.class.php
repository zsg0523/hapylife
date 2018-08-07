<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * PayController
 */
class PayController extends HomeBaseController{
    /**
    * 
    **/
    public function receiptSon(){
        $ir_receiptnum   = I('post.ir_receiptnum');
        $ip_paytype      = I('post.ip_paytype');
        $ir_price        = I('post.ir_unpaid');
        $ir_payreceiptnum= date('YmdHis').rand(100000, 999999);
        $mape            = array(
            'ir_receiptnum'   =>$ir_receiptnum,
            'ip_paytype'      =>$ip_paytype,
            'ir_price'        =>$ir_price,
            'ir_payreceiptnum'=>$ir_payreceiptnum
        );
        if($ir_price>0){
            $add = D('receiptSon')->add($mape);
            if($add){
                switch ($ip_paytype) {
                    case '1':
                        $this->redirect('Home/Purchase/Qrcode',array('ir_receiptnum'=>$ir_payreceiptnum));
                        
                        break;
                    case '2':
                        # code...
                        break;
                    case '4':
                        $this->redirect('Home/Purchase/cjPayment',array('ir_receiptnum'=>$ir_payreceiptnum));
                        break;
                }
            }else{
               $this->error('系统缓慢,请重新提交'); 
            }
        }else{
            $this->error('支付金额不能少于或等于0');
        }
    }

    // 确认密码
    public function checkPassWord(){
        $iuid = $_SESSION['user']['id'];
        $password = md5(trim(I('post.password')));
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        if($userinfo['password'] != $password){
            // 密码错误
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 密码正确
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }
    }
















}
