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
               $this->error('系统超时,请重新提交'); 
            }
        }else{
            $this->error('支付金额不能少于或等于0');
        }
    }
















}
