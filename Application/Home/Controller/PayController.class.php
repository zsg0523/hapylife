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

    // 积分支付
    public function payInt(){
        $iuid = $_SESSION['user']['id'];
        $ir_receiptnum = I('post.ir_receiptnum');
        // 获取用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 获取订单信息
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        // 用户剩余积分
        $residue = $userinfo['iu_point']-$receipt['ir_point'];
        if($residue>0){
            //修改用户积分
            $message = array(
                'iuid'      =>$iuid,
                'iu_point'  =>$residue
            );
            $insertpoint = M('User')->save($message);
            if($insertpoint){
                //修改订单状态
                $map = array(
                    'ir_paytype' => 2,
                    'ir_status' => 2,
                    'ir_paytime' => time()
                );
                $change_orderstatus = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                if($change_orderstatus){
                    //生成日志记录
                    $content = '订单:'.$ir_receiptnum.'支付成功,扣除Ep:'.$receipt['ir_point'].',剩余Ep:'.$residue;
                    $log     = array(
                                'name' => $userinfo['CustomerID'],
                                'content' => $content,
                                'create_time'   =>date('Y-m-d H:i:s'),
                                'type' => 2,
                            );
                    $addlog  = M('Log')->add($log);
                    if($addlog){
                        $tmpeArr = M('Tempuser')->where(array('htid'=>$receipt['htid']))->find();
                        //添加新用户
                        $keyword= 'HPL';
                        $custid = M('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
                        if(empty($custid)){
                            $CustomerID = 'HPL00000001';
                        }else{
                            $num   = substr($custid,3);
                            $nums  = $num+1;
                            $count = strlen($nums);
                            switch ($count) {
                                case '1':
                                    $CustomerID = 'HPL0000000'.$nums;
                                    break;
                                case '2':
                                    $CustomerID = 'HPL000000'.$nums;
                                    break;
                                case '3':
                                    $CustomerID = 'HPL00000'.$nums;
                                    break;
                                case '4':
                                    $CustomerID = 'HPL0000'.$nums;
                                    break;
                                case '5':
                                    $CustomerID = 'HPL000'.$nums;
                                    break;
                                case '6':
                                    $CustomerID = 'HPL00'.$nums;
                                    break;
                                case '7':
                                    $CustomerID = 'HPL0'.$nums;
                                    break;
                                default:
                                    $CustomerID = 'HPL'.$nums;
                                    break;
                             } 
                        }
                        //用户资料
                        $tmpe = array(
                            'EnrollerID'  =>$tmpeArr['enrollerid'],
                            'Sex'         =>$tmpeArr['sex'],
                            'LastName'    =>$tmpeArr['lastname'],
                            'FirstName'   =>$tmpeArr['firstname'],
                            'Email'       =>$tmpeArr['email'],
                            'PassWord'    =>md5($tmpeArr['password']),
                            'acid'        =>$tmpeArr['acid'],
                            'acnumber'    =>$tmpeArr['acnumber'],
                            'Phone'       =>$tmpeArr['phone'],
                            'ShopAddress1'=>$tmpeArr['shopaddress1'],
                            'ShopArea'    =>$tmpeArr['shoparea'],
                            'ShopCity'    =>$tmpeArr['shopcity'],
                            'ShopProvince'=>$tmpeArr['shopprovince'],
                            'ShopCountry' =>$tmpeArr['shopcountry'],
                            'Idcard'      =>$tmpeArr['idcard'],
                            'JustIdcard'  =>$tmpeArr['justidcard'],
                            'BackIdcard'  =>$tmpeArr['backidcard'],
                            'Language'    =>$tmpeArr['language'],
                            'EnLastName'  =>$tmpeArr['enlastname'],
                            'EnFirstName' =>$tmpeArr['enfirstname'],
                            'EnMiddleName'=>$tmpeArr['enmiddlename'],
                            'DeviceType'  =>$tmpeArr['devicetype'],
                            'Browser'     =>$tmpeArr['browser'],
                            'PaymentType' =>$tmpeArr['paymenttype'],
                            'BankName'    =>$tmpeArr['bankname'],
                            'BankAccount' =>$tmpeArr['bankaccount'],
                            'BankProvince'=>$tmpeArr['bankprovince'],
                            'BankCity'    =>$tmpeArr['bankcity'],
                            'BankArea'    =>$tmpeArr['bankarea'],
                            'SubName'     =>$tmpeArr['subname'],
                            'AccountType' =>$tmpeArr['accounttype'],
                            'CustomerID'  =>$CustomerID,
                            'OrderDate'   =>date("m/d/Y h:i:s A"),
                            'Number'      =>1,
                            'MailingProvince'    =>$tmpeArr['mailingprovince'],
                            'TermsAndConditions' =>1,
                            'DeviceGeolocation'  =>$tmpeArr['devicegeolocation'],
                            'BrowserVersion'     =>$tmpeArr['browserversion'],
                            'DistributorType'    =>D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade'),
                            'JoinedOn'    => time(),
                        );
                        $OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                        $activa = $OrderDate;
                        $day    = date('d',strtotime($OrderDate));
                        if($day>=28){
                            $allday = 28;
                        }else{
                            $allday = $day;
                        }
                        $ddd = $allday-1;
                        if($ddd>=10){
                            $oneday = $ddd;
                        }else{
                            $oneday = '0'.$ddd;
                        }
                        $update     = M('User')->add($tmpe);
                        if($update){
                            $userinfo= M('User')->where(array('CustomerID'=>$CustomerID))->find();
                            //添加激活
                            $time  = date("Y-m",strtotime("+1 month",strtotime($activa)));
                            $year  = date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日';
                            $endday= date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日';
                            $where =array('iuid'=>$iuid,'ir_receiptnum'=>$ir_receiptnum,'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
                            $save  = M('Activation')->add($where);
                            $status  = array(
                                'ir_status'  =>2,
                                'rCustomerID'=>$CustomerID,
                                'riuid'      =>$iuid,
                                'ir_paytype' =>2,
                                'ir_paytime' =>time(),
                                'ia_name'    =>$userinfo['lastname'].$userinfo['firstname'],
                                'ia_name_en' =>$userinfo['enlastname'].$userinfo['enfirstname'],
                                'ia_phone'   =>$userinfo['phone'],
                                'ia_address' =>$userinfo['shopaddress1'],
                            );
                            //更新订单信息
                            $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($status);
                            //发送数据至usa
                            if($upreceipt){
                                $usa    = new \Common\UsaApi\Usa;
                                $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone']);
                                if(!empty($result['result'])){
                                    $log = addUsaLog($result['result']);
                                    $maps = json_decode($result['result'],true);
                                    $wv  = array(
                                                'wvCustomerID' => $maps['wvCustomerID'],
                                                'wvOrderID'    => $maps['wvOrderID']
                                            );
                                    $res = M('User')->where(array('iuid'=>$iuid))->save($wv);
                                    if($res){
                                        $templateId ='164137';
                                        $params     = array();
                                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                        if($sms['errmsg'] == 'OK'){
                                            $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
                                            $contents = array(
                                                        'acnumber' => $userinfo['acnumber'],
                                                        'phone' => $userinfo['phone'],
                                                        'operator' => '系统',
                                                        'addressee' => $status['ia_name'],
                                                        'product_name' => $receiptlist['product_name'],
                                                        'date' => time(),
                                                        'content' => '恭喜您注册成功，请注意查收邮件'
                                            );
                                            $logs = M('SmsLog')->add($contents);
                                        }
                                    }
                                }
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
