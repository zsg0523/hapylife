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
//      p($ip_paytype);die;
        $ir_price        = I('post.ir_unpaid');
        $pay_receiptnum  = date('YmdHis').rand(100000, 999999);
        $iuid            = D('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('riuid');
        if($ip_paytype == 2){
            $ir_prices = bcmul($ir_price,100,2);
            $mape            = array(
                'ir_receiptnum'   =>$ir_receiptnum,
                'ip_paytype'      =>$ip_paytype,
                'ir_price'        =>$ir_prices,
                'pay_receiptnum'  =>$pay_receiptnum,
                'riuid'           =>$iuid,
                'cretime'         =>time(),
                'ir_point'        =>$ir_price
            );
        }else{
            $ir_prices = bcdiv($ir_price,100,2);
            $mape            = array(
                'ir_receiptnum'   =>$ir_receiptnum,
                'ip_paytype'      =>$ip_paytype,
                'ir_price'        =>$ir_price,
                'pay_receiptnum'  =>$pay_receiptnum,
                'riuid'           =>$iuid,
                'cretime'         =>time(),
                'ir_point'        =>$ir_prices
            );
        }
        if($ir_price>0){
            $add = D('receiptson')->add($mape);
            if($add){
             	// if($ip_paytype==1){
             	// 	$this->redirect('Home/Purchase/Qrcode',array('ir_receiptnum'=>$pay_receiptnum));
             	// }else if($ip_paytype==4){
             	// 	$this->redirect('Home/Purchase/cjPayment',array('ir_receiptnum'=>$pay_receiptnum));
             	// }
                switch ($ip_paytype) {
                    case '1':
                        $this->redirect('Home/Purchase/Qrcode',array('ir_receiptnum'=>$pay_receiptnum));
                        break;
                    case '2':
                        $this->redirect('Home/Pay/toCheckPoint',array('ir_receiptnum'=>$pay_receiptnum)); 
                        break;
                    case '4':
                        $this->redirect('Home/Purchase/cjPayment',array('ir_receiptnum'=>$pay_receiptnum));
                        break;
                }
            }else{
               $this->error('系统超时,请重新提交'); 
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

    public function toCheckPoint(){
        $ir_receiptnum = I('get.ir_receiptnum');
        $receipt = M('receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        $this->assign('data',$receipt);
        $this->display();
    }

    // 积分支付
    public function payInt(){
        $iuid = $_SESSION['user']['id'];
        $ir_receiptnum = I('get.ir_receiptnum');
        // 获取用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 获取子订单信息
        $receiptson = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        // 获取父订单信息
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->find();
        // 用户剩余积分
        $residue = bcsub($userinfo['iu_point'],$receiptson['ir_point'],2);
        if($residue>0){
            //修改用户积分
            $message = array(
                'iuid'      =>$iuid,
                'iu_point'  =>$residue
            );
            $insertpoint = M('User')->save($message);
            if($insertpoint){
                //修改子订单状态
                $map = array(
                    'ir_paytype' => 2,
                    'status' => 2,
                    'paytime' => time()
                );
                $change_orderstatus = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->save($map);
                if($change_orderstatus){
                    //生成子订单日志记录
                    $content = '订单:'.$ir_receiptnum.'支付成功,扣除Ep:'.$receiptson['ir_point'].',剩余Ep:'.$residue;
                    $log     = array(
                                'iuid' => $iuid,
                                'name' => $userinfo['customerid'],
                                'content' => $content,
                                'create_time'   =>time(),
                                'create_month'   =>date('Y-m'),
                                'type' => 2,
                                'action' => 3,
                            );
                    $addlog  = M('Log')->add($log);
                    if($addlog){
                        // 父订单待支付积分
                        $ir_unpoint = bcsub($receipt['ir_point'],$receiptson['ir_point'],2);
                        // 父订单待支付金额
                        $ir_unpaid = bcsub($receipt['ir_price'],$receiptson['ir_price'],2);
                        // 修改父订单状态
                        if($ir_unpoint == 0 && $ir_unpaid == 0){
                            $maps = array(
                                'ir_unpoint' => $ir_unpoint,
                                'ir_unpaid' => $ir_unpaid,
                                'ir_status' => 2,
                            );
                            $change_receipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($maps);
                            if($change_receipt){
                                // 支付完成
                                // $data['status'] = 1;
                                // $this->ajaxreturn($data);
                                $this->redirect('Home/Purchase/center');
                            }
                        }else{
                            $maps = array(
                                'ir_unpoint' => $ir_unpoint,
                                'ir_unpaid' => $ir_unpaid,
                                'ir_status' => 202,
                            );
                            $change_receipts = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($maps);
                            if($change_receipts){
                                // 支付完成一部分
                                // $data['status'] = 3;
                                // $this->ajaxreturn($data);
                                $this->redirect('Home/Pay/choosePay',array('ir_unpoint'=>$ir_unpoint,'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$ir_unpaid,'ir_receiptnum'=>$receipt['ir_receiptnum']));
                            }
                        }
                    }
                }
            }
        }else{
            // 积分不足
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
















}
