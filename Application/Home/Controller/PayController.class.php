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
        $customerid      = D('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('rcustomerid');
        $iuid            = D('User')->where(array('CustomerID'=>$customerid))->getfield('iuid');
        $saveReceipt     = D('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save(array('riuid'=>$iuid));
        $saveReceiptSon  = D('Receiptson')->where(array('ir_receiptnum'=>$ir_receiptnum))->save(array('riuid'=>$iuid));
        if($ip_paytype == 2){
            $ir_prices = bcmul($ir_price,100,2);
            $mape            = array(
                'ir_receiptnum'   =>$ir_receiptnum,
                'ir_paytype'      =>$ip_paytype,
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
                'ir_paytype'      =>$ip_paytype,
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
        // $iuid = I('get.iuid');
        $ir_receiptnum = I('get.ir_receiptnum');
        // 获取用户信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        // 获取子订单信息
        $receiptson = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        // 获取父订单信息
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->find();
        // 用户剩余积分
        $residue = bcsub($userinfo['iu_point'],$receiptson['ir_point'],2);
        // 获取订单状态
        $ir_ordertype = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->getfield('ir_ordertype');
        // 获取产品名称
        $product_name = M('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('product_name');
        if($residue>=0){
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
                        // 记录会员使用EP日志
                        $content = $userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时，消费出'.$receiptson['ir_point'].'EP到系统，剩EP余额'.$residue;
                        $logs = array(
                            'pointNo' => $ir_receiptnum,
                            'iuid' => $iuid,
                            'hu_username' => $userinfo['lastname'].$userinfo['firstname'],
                            'hu_nickname' => $userinfo['customerid'],
                            'send' => $userinfo['customerid'],
                            'received' => '系统',
                            'getpoint' => $receiptson['ir_point'],
                            'pointtype' => 7,
                            'realpoint' => $receiptson['ir_point'],
                            'leftpoint' => $residue,
                            'date' => date('Y-m-d H:i:s'),
                            'handletime' => date('Y-m-d H:i:s'),
                            'content' => $content,
                            'status' => 2,
                            'whichApp' => 5,
                        );
                        $addlogs = M('Getpoint')->add($logs);
                        if($addlogs){
                            // 父订单待支付积分
                            $ir_unpoint = bcsub($receipt['ir_unpoint'],$receiptson['ir_point'],2);
                            // 父订单待支付金额
                            $ir_unpaid = bcsub($receipt['ir_unpaid'],$receiptson['ir_price'],2);
                            // 总共已经支付金额
                            $total = bcsub($receipt['ir_price'],$ir_unpaid,2);
                            // 修改父订单状态
                            if($ir_unpoint != 0 && $ir_unpaid != 0){
                                $maps = array(
                                    'ir_unpoint' => $ir_unpoint,
                                    'ir_unpaid' => $ir_unpaid,
                                    'ir_status' => 202,
                                );
                                $change_receipts = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($maps);
                                if($change_receipts){
                                    // 发送短信提示
                                    $templateId ='209014';
                                    $params     = array($receipt['ir_receiptnum'],$receiptson['ir_price'],$total,$ir_unpaid);
                                    $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                    if($sms['errmsg'] == 'OK'){
                                        $contents = array(
                                            'acnumber' => $userinfo['acnumber'],
                                            'phone' => $userinfo['phone'],
                                            'operator' => '系统',
                                            'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                            'product_name' => '',
                                            'date' => time(),
                                            'content' => '订单编号：'.$receipt['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$ir_unpaid,
                                            'customerid' => $userinfo['customerid']
                                        );
                                        $logs = M('SmsLog')->add($contents);
                                    }
                                    // 支付完成一部分，获取产品类型
                                    // switch ($ir_ordertype) {
                                    //     case '1':
                                    //         $this->success('支付成功',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum']));
                                    //         break;
                                    //     case '3':
                                    //         $this->success('支付成功',U('Home/Pay/choosePay',array('ir_unpoint'=>$ir_unpoint,'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$ir_unpaid,'ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                    //         break;
                                    //     case '4':
                                    //         $this->success('支付成功',U('Home/Pay/choosePay',array('ir_unpoint'=>$ir_unpoint,'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$ir_unpaid,'ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                    //         break;
                                    // }
                                    $this->success('支付成功',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                }
                            }else{
                                $maps = array(
                                    'ir_unpoint' => $ir_unpoint,
                                    'ir_unpaid' => $ir_unpaid,
                                    'ir_status' => 2,
                                    'ir_paytime' => time(),
                                );
                                $change_receipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($maps);
                                if($change_receipt){
                                    // 发送短信提示
                                    $templateId ='209011';
                                    $params     = array($receipt['ir_receiptnum'],$product_name);
                                    $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                    if($sms['errmsg'] == 'OK'){
                                        $contents = array(
                                            'acnumber' => $userinfo['acnumber'],
                                            'phone' => $userinfo['phone'],
                                            'operator' => '系统',
                                            'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                            'product_name' => $product_name,
                                            'date' => time(),
                                            'content' => '订单编号：'.$receipt['ir_receiptnum'].'，产品：'.$product_name.'，支付成功。',
                                            'customerid' => $userinfo['customerid']
                                        );
                                        $logs = M('SmsLog')->add($contents);
                                    }
                                    // 存在htid，生成新账号
                                    if($receipt['htid']){
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
                                        $update     = M('User')->add($tmpe);       
                                        $riuid      = $update;
                                        $OrderDate  = date("Y-m-d",strtotime("-1 month",time()));
                                        $userinfo= M('User')->where(array('CustomerID'=>$CustomerID))->find();
                                        $status  = array(
                                            'ir_status'  =>$maps['ir_status'],
                                            'rCustomerID'=>$CustomerID,
                                            'riuid'      =>$userinfo['iuid'],
                                            'ia_name'    =>$userinfo['lastname'].$userinfo['firstname'],
                                            'ia_name_en' =>$userinfo['enlastname'].$userinfo['enfirstname'],
                                            'ia_phone'   =>$userinfo['phone'],
                                            'ia_address' =>$userinfo['shopaddress1'],
                                            'ir_unpaid'  =>$maps['ir_unpaid'],
                                            'ir_unpoint' =>$maps['ir_unpoint']
                                        );
                                        //更新订单信息
                                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                                        $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
                                        if($ir_status == 2){
                                            $usa    = new \Common\UsaApi\Usa;
                                            $products = 'RBS,DTP';
                                            $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$products);
                                            if(!empty($result['result'])){
                                                $log = addUsaLog($result['result']);
                                                $maps = json_decode($result['result'],true);
                                                $wv  = array(
                                                    'wvCustomerID' => $maps['wvCustomerID'],
                                                    'wvOrderID'    => $maps['wvOrderID'],
                                                    'Products'     => $products
                                                );
                                                $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
                                                if($res){
                                                    $createPayment = $usa->createPayment($userinfo['customerid'],$maps['wvOrderID'],date('Y-m-d H:i',time()));
                                                    $log = addUsaLog($createPayment['result']);
                                                    $jsonStr = json_decode($createPayment['result'],true);
                                                    if($jsonStr['paymentId']){
                                                        $templateId ='208995';
                                                        $params     = array($userinfo['customerid'],$maps['wvCustomerID']);
                                                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                                        if($sms['errmsg'] == 'OK'){
                                                            $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
                                                            $contents = array(
                                                                'acnumber' => $userinfo['acnumber'],
                                                                'phone' => $userinfo['phone'],
                                                                'operator' => '系统',
                                                                'addressee' => $status['ia_name'],
                                                                'product_name' => $receiptlist['product_name'],
                                                                'date' => time(),
                                                                'content' => '恭喜您创建成功，您的 HapyLife 会员号码是'.$userinfo['customerid'].'以及 DreamTrips 会员号码是'.$maps['wvCustomerID'].'，同时注意查收Rovia邮件。',
                                                                'customerid' => $userinfo['customerid']
                                                            );
                                                            $logs = M('SmsLog')->add($contents);
                                                        }
                                                    }
                                                    // 支付完成
                                                    $this->success('注册成功',U('Home/Register/new_regsuccess',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                                }
                                            }
                                        }
                                    }else if($ir_ordertype == 4){
                                        // 添加通用券
                                        $product = M('Receipt')
                                                        ->alias('r')
                                                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                                                        ->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))
                                                        ->find();
                                        $data = array(
                                                'product' => $product,
                                                'userinfo' => $userinfo,
                                                'ir_receiptnum' => $receipt['ir_receiptnum'],
                                            );
                                        $data    = json_encode($data);
                                        $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
                                        // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/addCoupon";
                                        $result  = post_json_data($sendUrl,$data);
                                        $back_msg = json_decode($result['result'],true);
                                        if($back_msg['status']){
                                            $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receiptson['ir_receiptnum'])));
                                        }
                                    }else if($ir_ordertype == 5){
                                        $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                    }else{
                                        $userinfo   = D('User')->where(array('iuid'=>$receipt['riuid']))->find();
                                        //修改用户最近订单日期/是否通过/等级/数量
                                        $tmpe['iuid'] = $receipt['riuid'];
                                        //产品等级
                                        $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade');
                                        //购买产品次数+1
                                        $tmpe['Number']          = $userinfo['number']+1;
                                        //number 购买产品的次数
                                        if($userinfo['number']==0){
                                            //支付日期
                                            $tmpe['OrderDate']= date("m/d/Y h:i:s A");
                                            $OrderDate        = date("Y-m-d",strtotime("-1 month",time()));
                                        }else{
                                            $OrderDate        = $userinfo['orderdate'];
                                        }
                                        //修改用户信息
                                        $update    = D('User')->save($tmpe);
                                        $riuid     = $receipt['riuid'];
                                        $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$receipt['ir_receiptnum']);
                                        $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
                                        if($ir_status == 2){
                                            $usa = new \Common\UsaApi\Usa;
                                            $createPayment = $usa->createPayment($userinfo['customerid'],$receipt['ir_receiptnum'],date('Y-m-d H:i',time()));
                                            $log = addUsaLog($createPayment['result']);
                                            $jsonStr = json_decode($createPayment['result'],true);
                                            // p($jsonStr);die;
                                            if($jsonStr['paymentId']){
                                                $showProduct = M('User')->where(array('iuid'=>$receipt['riuid']))->setfield('showProduct',0);
                                                // 支付完成
                                                // $this->success('完成支付',U('Home/Purchase/center'));
                                                $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                                            }
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
            switch ($ir_ordertype) {
                case '1':
                    $this->error('积分不足',U('Home/Pay/choosePay1',array('ir_unpoint'=>$receipt['ir_unpoint'],'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$receipt['ir_unpaid'],'ir_receiptnum'=>$receipt['ir_receiptnum'])));
                    break;
                case '3':
                    $this->error('积分不足',U('Home/Pay/choosePay',array('ir_unpoint'=>$receipt['ir_unpoint'],'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$receipt['ir_unpaid'],'ir_receiptnum'=>$receipt['ir_receiptnum'])));
                    break;
                case '4':
                    $this->error('积分不足',U('Home/Pay/choosePay',array('ir_unpoint'=>$receipt['ir_unpoint'],'ir_price'=>$receipt['ir_price'],'ir_point'=>$receipt['ir_point'],'ir_unpaid'=>$receipt['ir_unpaid'],'ir_receiptnum'=>$receipt['ir_receiptnum'])));
                    break;
            }
        }
    }

    /**
    * 获取订单信息
    **/ 
    public function getReceipt(){
        $ir_receiptnum = I('post.ir_receiptnum');
        $receipt = M('Receipt')
                        ->alias('r')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->where(array('ir_receiptnum'=>$ir_receiptnum))
                        ->find();
        $userinfo = M('User')->where(array('iuid'=>$receipt['riuid']))->find();
        if($receipt){
            $receipt['status'] = 1;
            $receipt['userinfo'] = $userinfo;
            $this->ajaxreturn($receipt);
        }else{
            $receipt['status'] = 0;
            $this->ajaxreturn($receipt);
        }
    }
    /**
    *检查用户DT是否足够支付
    **/
//     public function getUserDt(){
//         $iuid    = $_SESSION['user']['id'];
// //         $iuid    = I('post.iuid');
//         $ip_dt   = I('post.ip_dt');
//         $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
//         $bcsub   = bcsub($userinfo['iu_dt'],$ip_dt,2);
//         $data['iu_dt'] =$userinfo['iu_dt'];
//         $data['bc_dt'] =$bcsub;
//         if($bcsub>=0){
//             $data['status']=1;
//             $data['msg']   ='DT充足';
//             $this->ajaxreturn($data);
//         }else{
//             $data['status']=0;
//             $data['msg']   ='DT不足';
//             $this->ajaxreturn($data);
//         }
//     }
    public function getUserDt(){
        $iuid    = $_SESSION['user']['id'];
//         $iuid    = I('post.iuid');
        $ip_dt   = I('post.ip_dt');
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
        // 获取用户在美国的dtp
        $usa = new \Common\UsaApi\Usa;
        $result = $usa->dtPoint($userinfo['customerid']);
        foreach($result['softCashCategories'] as $key=>$value){
            if($value['categoryType'] == 'DreamTripPoints'){
                $userinfo['iu_dt'] = $value['balance'];
            }
        }
        $bcsub   = bcsub($userinfo['iu_dt'],$ip_dt,2);
        $data['iu_dt'] =$userinfo['iu_dt'];
        $data['bc_dt'] =$bcsub;
        if($bcsub>=0){
            $data['status']=1;
            $data['msg']   ='DT充足';
            $this->ajaxreturn($data);
        }else{
            $data['status']=0;
            $data['msg']   ='DT不足';
            $this->ajaxreturn($data);
        }
    }













}
