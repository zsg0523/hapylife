<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Api\Controller\HapylifeUsaController;
/**
 * 用户注册Controller
 **/
class SelfRegisterController extends HomeBaseController{
    public function new_register(){
        $iuid = I('get.iuid');
        $customerid = strtoupper(I('get.hu_nickname'));
        if($customerid){
            $data = array(
                    'customerid' => $customerid,
                );
            $data    = json_encode($data);
            $sendUrl = "http://10.16.0.153/hapylife/index.php/Api/HapylifeApi/userList";
            // $sendUrl = "http://localhost/hapylife/index.php/Api/HapylifeApi/userList";
            $result  = post_json_data($sendUrl,$data);
            $back_msg = json_decode($result['result'],true);
            $hu_nickname = $back_msg['data']['lastname'].$back_msg['data']['firstname'];
        }
        header("Content-type: text/html; charset=gb2312"); 
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $dat[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $dat[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $dat[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('dat',$dat);
        $this->assign('customerid',$customerid);
        $this->assign('hu_nickname',$hu_nickname);
        $this->assign('iuid',$iuid);
        $this->display();
    }
    /********************************************************************新代理注册--需要购买产品********************************************************************************/
    /**
    * 保存用户资料
    **/ 
    public function new_registerInfo(){
        if(!IS_POST){
            $msg['status'] = 200;
            $msg['message']= '未提交任何数据';
            $this->ajaxreturn($msg);
        }else{
            $iuid = I('get.iuid');
            $customerid = strtoupper(I('get.hu_nickname'));
            if($customerid){
                $data = array(
                        'customerid' => $customerid,
                    );
                $data    = json_encode($data);
                $sendUrl = "http://10.16.0.153/hapylife/index.php/Api/HapylifeApi/userList";
                // $sendUrl = "http://localhost/hapylife/index.php/Api/HapylifeApi/userList";
                $result  = post_json_data($sendUrl,$data);
                $back_msg = json_decode($result['result'],true);
                $hu_nickname = $back_msg['data']['lastname'].$back_msg['data']['firstname'];
            }
            // p($_SESSION);
            $data = I('post.');
            $upload = several_upload();
            $User = D("User"); // 实例化User对象
            if(!$User->create($data)){
                $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
                foreach ($mape as $key => $value) {
                    $dat[$key]         = $value;
                    if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                        $dat[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
                    }else{
                        $dat[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
                    }
                }
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $error = $User->getError();
                $assign = array(
                    'error' => $error,
                    'data' => $data,
                    'dat' => $dat
                );
                $this->assign($assign);
                $this->assign('customerid',$customerid);
                $this->assign('hu_nickname',$hu_nickname);
                $this->assign('iuid',$iuid);
                $this->display('SelfRegister/new_register');
            }else{
                if(isset($upload['name'])){
                    $data['JustIdcard']=C('WEB_URL').$upload['name'][0];
                    $data['BackIdcard']=C('WEB_URL').$upload['name'][1];
                }
                $data['EnrollerID'] = strtoupper(I('post.EnrollerID'));
                $data['SponsorID'] = strtoupper($_SESSION['user']['username']);
                $data['LastName'] = trimall(I('post.LastName'));
                $data['FirstName'] = trimall(I('post.FirstName'));
                $data['EnLastName'] = trimall(I('post.EnLastName'));
                $data['EnFirstName'] = trimall(I('post.EnFirstName'));
                $data['WvPass'] = $data['PassWord'];
                $add = D('Tempuser')->add($data);
                if($add){
                    $this->assign('userinfo',$data);
                    $this->assign('htid',$add);
                    $this->display();
                }
            }
        }
    }
    /**
    * 获取首购产品
    **/ 
    public function new_purchase(){
        $iuid = I('get.iuid');
        $data = D('product')->where(array('ip_type'=>1,'is_pull'=>1))->order('is_sort DESC')->select();
        $this->assign('data',$data);
        $this->assign('iuid',$iuid);
        $this->display();

    }
    /**
    * 获取产品详情
    **/ 
    public function new_purchaseInfo(){
        $iuid = I('get.iuid');
        $ipid =I('get.ipid');
        $data = D('product')->where(array('ipid'=>$ipid))->find();
        $this->assign('data',$data);
        $this->assign('iuid',$iuid);
        $this->display();
    }
    /**
    * 首购订单
    **/
    public function registerOrder(){
        $ipid = I('get.ipid');
        $htid = I('get.htid');
        // 临时表用户信息
        $userinfo = M('Tempuser')->where(array('htid'=>$htid))->find();
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        $con = '首购单';
        $order = array(
            //订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'=>time(),
            //订单的状态(0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过  7待注册)
            'ir_status'=>7,
            //收货人
            'ia_name'=>$userinfo['firstname'].$userinfo['lastname'],
            //收货人电话
            'ia_phone'=>$userinfo['phone'],
            // 省，州
            'ia_province' => $userinfo['shopprovince'],
            // 市
            'ia_city' => $userinfo['shopcity'],
            // 区
            'ia_area' => $userinfo['shoparea'],
            //详细收货地址
            'ia_address'=>$userinfo['shopaddress1'],
            //订单总商品数量
            'ir_productnum'=>1,
            //订单总金额
            'ir_price'=>$product['ip_price_rmb'],
            //订单总积分
            'ir_point'=>$product['ip_point'],
            //订单待付款总金额
            'ir_unpaid'=>$product['ip_price_rmb'],
            //订单待付款总积分
            'ir_unpoint'=>$product['ip_point'],
            //订单备注
            'ir_desc'=>$con,
            //订单类型
            'ir_ordertype' => $product['ip_type'],
            //产品id
            'ipid'         => $product['ipid'],
            //待注册用户id
            'htid'        => $htid,
            // 是否外部链接接入
            'isOutside' => 0,
        );
        $receipt = M('ReceiptTempuser')->add($order);
        if($receipt){
            $map = array(
                'ir_receiptnum'     =>  $order_num,
                'ipid'              =>  $product['ipid'],
                'product_num'       =>  1,
                'product_point'     =>  $product['ip_point'],
                'product_price'     =>  $product['ip_price_rmb'],
                'product_name'      =>  $product['ip_name_zh'],
                'product_picture'   =>  $product['ip_picture_zh']
            );
            $addReceiptlist = M('Receiptlist')->add($map);
        }
        //生成日志记录
        // $content = '您的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        // $log = array(
        //     'iuid'      =>$iuid,
        //     'content'   =>$content,
        //     'action'    =>0,
        //     'type'      =>2,
        //     'create_time'   =>time(),
        //     'create_month'   =>date('Y-m'),
        // );
        // $addlog = M('Log')->add($log);
        // if($addlog){
            if($product['ip_type'] == 1){
                $this->redirect('Home/Pay/choosePay3',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }else{
                $this->redirect('Home/Pay/choosePay',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }
        // }else{
        //     $this->error('生成订单失败');
        // }  
    }

    /**
    * 拆单系统
    **/
    public function receiptSon(){
        $ir_receiptnum   = I('post.ir_receiptnum');
        $ip_paytype      = I('post.ip_paytype');
        $ir_price        = I('post.ir_unpaid');
        $pay_receiptnum  = date('YmdHis').rand(100000, 999999);
        if($ip_paytype == 2){
            $ir_prices = bcmul($ir_price,100,2);
            $mape            = array(
                'ir_receiptnum'   =>$ir_receiptnum,
                'ir_paytype'      =>$ip_paytype,
                'ir_price'        =>$ir_prices,
                'pay_receiptnum'  =>$pay_receiptnum,
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
                'cretime'         =>time(),
                'ir_point'        =>$ir_prices
            );
        }
        if($ir_price>0){
            $add = D('receiptson')->add($mape);
            if($add){
                switch ($ip_paytype) {
                    case '1':
                        $this->redirect('Home/Purchase/Qrcode',array('ir_receiptnum'=>$pay_receiptnum));
                        break;
                    case '2':
                        $this->redirect('Home/Pay/payPoint',array('ir_receiptnum'=>$pay_receiptnum)); 
                        break;
                    case '4':
                        $this->redirect('Home/SelfRegister/cjPayment',array('ir_receiptnum'=>$pay_receiptnum));
                        break;
                }
            }else{
               $this->error('系统超时,请重新提交'); 
            }
        }else{
            $this->error('支付金额不能少于或等于0');
        }
    }

    /**
    * 购买产品畅捷支付
    **/
    public function cjPayment(){
        //订单号
        $order_num  = I('get.ir_receiptnum');
        // p($order_num);die;
        $order      = M('Receiptson')->where(array('pay_receiptnum'=>$order_num))->find();
        // p($order);die;
        // p($order);die;
        $postData                      = array();   
        // 基本参数
        $postData['Service']           = 'nmg_quick_onekeypay';
        $postData['Version']           = '1.0';
        // $postData['PartnerId']         = '200001280051';//商户号
        $postData['PartnerId']         = '200001380239';//商户号
        $postData['InputCharset']      = 'UTF-8';
        $postData['TradeDate']         = date('Ymd').'';
        $postData['TradeTime']         = date('His').'';
        $postData['ReturnUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/SelfRegister/new_regsuccess?ir_receiptnum='.$order_num;// 前台跳转url
        $postData['Memo']              = '备注';
        // 4.4.2.8. 直接支付请求接口（畅捷前台） 业务参数++++++++++++++++++
        $postData['TrxId']             = $order_num; //外部流水号
        $postData['SellerId']          = '200001380239'; //商户编号，调用畅捷子账户开通接口获取的子账户编号;该字段可以传入平台id或者平台id下的子账户号;作为收款方使用；与鉴权请求接口中MerchantNo保持一致
        $postData['SubMerchantNo']     = '200001380239'; //子商户，在畅捷商户自助平台申请开通的子商户，用于自动结算
        $postData['ExpiredTime']       = '48h'; //订单有效期，取值范围：1m～48h。单位为分，如1.5h，可转换为90m。用来标识本次鉴权订单有效时间，超过该期限则该笔订单作废
        $postData['MerUserId']         = $order_num; //用户标识
        $postData['BkAcctTp']          = ''; //卡类型（00 –银行贷记账户;01 –银行借记账户;）
        // $postData['BkAcctNo']       =   rsaEncrypt('XXXXX'); //卡号
        $postData['BkAcctNo']          = ''; //卡号
        $postData['IDTp']              = ''; //证件类型，01：身份证
        //$postData['IDNo']            =   rsaEncrypt('XXXX'); //证件号
        $postData['IDNo']              = ''; //证件号
        // $postData['CstmrNm']        =   rsaEncrypt('XX'); //持卡人姓名
        $postData['CstmrNm']           = ''; //持卡人姓名
        // $postData['MobNo']          =   rsaEncrypt('XXXXX'); //银行预留手机号
        $postData['MobNo']             = ''; //银行预留手机号      
        $postData['CardCvn2']          = rsaEncrypt(''); //CVV2码，当卡类型为信用卡时必填
        $postData['CardExprDt']        = rsaEncrypt(''); //有效期，当卡类型为信用卡时必填
        $postData['TradeType']         = '11'; //交易类型（即时 11 担保 12）
        $postData['TrxAmt']            = $order['ir_price']; //交易金额
        $postData['EnsureAmount']      = ''; //担保金额
        $postData['OrdrName']          = '商品名称'; //商品名称
        $postData['OrdrDesc']          = ''; //商品详情
        $postData['RoyaltyParameters'] = '';      //"[{'userId':'13890009900','PID':'2','account_type':'101','amount':'100.00'},{'userId':'13890009900','PID':'2','account_type':'101','amount':'100.00'}]"; //退款分润账号集
        $postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/SelfRegister/notifyVerify';//异步通知地址
        $postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
        $postData['Extension']         = '';//扩展字段s
        $postData['Sign']              = rsaSign($postData);
        $postData['SignType']          = 'RSA'; //签名类型      
        $query                         = http_build_query($postData);
        $url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
        $data['url']                   = $url;
        header("Location:".$url);
    }

    //购买产品畅捷返回结果
    public function notifyVerify(){
        //I('post')，$_POST 无法获取API post过来的字符串数据
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = logTest($jsonStr);
        $data    = explode('&', $jsonStr);
        //签名数据会被转码，需解码urldecode
        foreach ($data as $key => $value) {
            $temp = explode('=', $value);
            $map[$temp[0]]=urldecode(trim($temp[1]));
        }
        // $map['outer_trade_no'] = '20180808104800320253';
        $receipt = M('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->find();
        $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
        $cjPayStatus = M('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->save($map);
        //验签
        $return = rsaVerify($map);
        //更改订单状态
        if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
            $whereSon= array(
                'status'    =>2,
                'ir_paytype'=>4
            );
            $saveSon = D('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->save($whereSon);
            if($saveSon){
                D('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->setfield('paytime',time());
                // $order = D('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
                $ReceiptTempuser = M('ReceiptTempuser')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
                $subnum= bcsub($ReceiptTempuser['ir_unpaid'],$receipt['ir_price'],2);
                if($subnum==0){
                    $sub      = 0;
                    $unp      = 0;
                    $ir_status= 2;
                    $ir_paytime = time();
                }else{  
                    $sub      = $subnum;
                    $unp      = bcdiv($sub,100,2);
                    $ir_status= 202;
                    $ir_paytime = 0;
                }
                if($sub==0){
                    if($ReceiptTempuser['htid']){
                        $tmpeArr = M('Tempuser')->where(array('htid'=>$ReceiptTempuser['htid']))->find();
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
                            'DistributorType'    =>D('Product')->where(array('ipid'=>$ReceiptTempuser['ipid']))->getfield('ip_after_grade'),
                            'JoinedOn'    => time(),
                            'WvPass' => $tmpeArr['password'],
                        );
                        $update     = M('User')->add($tmpe);       
                        $riuid      = $update;
                        $OrderDate  = date("Y-m-d",strtotime("-1 month",time()));
                        $userinfo= M('User')->where(array('CustomerID'=>$CustomerID))->find();
                    }
                    //创建订单信息
                    $addReceiptArr = array(
                        //订单编号
                        'ir_receiptnum' =>$receipt['ir_receiptnum'],
                        //订单创建日期
                        'ir_date'       =>time(),
                        //订单的状态(0待生成订单，1待支付订单，202未全额,2已付款订单)
                        'ir_status'     =>2,
                        //下单用户id
                        'riuid'          =>$riuid,
                        //下单用户
                        'rCustomerID'    =>$CustomerID,
                        //收货人
                        'ia_name'       =>$userinfo['lastname'].$userinfo['firstname'],
                        //收货人电话
                        'ia_phone'      =>$userinfo['phone'],
                        // 省，州
                        'ia_province' => $userinfo['shopprovince'],
                        // 市
                        'ia_city' => $userinfo['shopcity'],
                        // 区
                        'ia_area' => $userinfo['shoparea'],
                        //收货地址
                        'ia_address'    =>$userinfo['shopaddress1'],
                        //订单总商品数量
                        'ir_productnum' =>1,
                        //订单总金额
                        'ir_price'      =>$receipt['ir_price'],
                        'ir_unpaid'     =>0,
                        //订单总积分
                        'ir_point'      =>$receipt['ir_point'],
                        'ir_unpoint'    =>0,
                        'ir_dt'         =>0,
                        //订单备注
                        'ir_desc'       =>'首购单',
                        //订单类型
                        'ir_ordertype'  => $ReceiptTempuser['ir_ordertype'],
                        //产品id
                        'ipid'          => $ReceiptTempuser['ipid'],
                        // 订单支付时间
                        'ir_paytime'    => time(),
                    );
                    $addReceipt = M('Receipt')->add($addReceiptArr);
                    // 获取产品
                    $product = D('Product')->where(array('ipid'=>$ReceiptTempuser['ipid']))->find();
                    if($addReceipt){ 
                        $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$receipt['ir_receiptnum']);
                        // 检测订单状态
                        $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
                        if($ir_status == 2){
                            // 更新子订单
                            M('Receiptson')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->setfield('riuid',$riuid);
                            $usa    = new \Common\UsaApi\Usa;
                            $result = $usa->createCustomer($CustomerID,$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$product['productid'],$tmpeArr['birthday']);
                            if(!empty($result['result'])){
                                $log = addUsaLog($result['result']);
                                $maps = json_decode($result['result'],true);
                                $wv  = array(
                                            'wvCustomerID' => $maps['wvCustomerID'],
                                            'wvOrderID'    => $maps['wvOrderID'],
                                            'Products'     => $product['productid']
                                        );
                                $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
                                if($res){
                                    $addressee = $userinfo['lastname'].$userinfo['firstname'];
                                    $templateId ='244312';
                                    $params     = array($addressee,$maps['wvCustomerID'],$product['ip_name_zh']);
                                    $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                    if($sms['errmsg'] == 'OK'){
                                        $contents = array(
                                                    'acnumber' => $userinfo['acnumber'],
                                                    'phone' => $userinfo['phone'],
                                                    'operator' => '系统',
                                                    'addressee' => $addressee,
                                                    'product_name' => $receiptlist['product_name'],
                                                    'date' => time(),
                                                    'content' => '欢迎来到DT!，亲爱的DT会员您好，欢迎您加入DT成为DT大家庭的一员！在开始使用您的新会员资格前，请确认下列账户信息是否正确:姓名：'.$addressee.'会员号码：'.$maps['wvCustomerID'].'产品：'.$product['ip_name_zh'].'使用上面的会员ID号码以及您在HapyLife帐号注册的时候所创建的密码登录DT官网，开始享受您的会籍。我们很开心您的加入。我们迫不及待地与您分享无数令人兴奋和难忘的体验！',
                                                    'customerid' => $userinfo['customerid']
                                        );
                                        $logs = M('SmsLog')->add($contents);
                                    }

                                    // 给上线发短信
                                    $enrollerinfo = M('User')->where(array('CustomerID'=>$userinfo['enrollerid']))->find(); 
                                    $templateId ='244300';
                                    $params     = array($enrollerinfo['customerid'],$userinfo['customerid']);
                                    $sms        = D('Smscode')->sms($enrollerinfo['acnumber'],$enrollerinfo['phone'],$params,$templateId);
                                    if($sms['errmsg'] == 'OK'){
                                        $addressee = $enrollerinfo['lastname'].$enrollerinfo['firstname'];
                                        $contents = '尊敬的'.$enrollerinfo['customerid'].'会员，您增加一名成员：'.$userinfo['customerid'];
                                        $addlog = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$addressee,'上线接收短信',$contents,$enrollerinfo['customerid']);
                                    }

                                    $createPayment = $usa->createPayment($userinfo['customerid'],$maps['wvOrderID'],date('Y-m-d H:i',time()));
                                    $log = addUsaLog($createPayment['result']);

                                }
                            }    
                        }
                    }
                }
            }
        }
    }

    /**
    *代理成功注册页面
    **/
    public function new_regsuccess(){
        $ir_receiptnum = I('get.ir_receiptnum');
        $receiptSon = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptSon['ir_receiptnum']))->find();
        if($receipt['ir_status'] == 2){
            $data                  = M('User')->where(array('CustomerID'=>$receipt['rcustomerid']))->find();
            $data['ir_receiptnum'] = $receipt['ir_receiptnum'];
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('失败');
        }
    }

    /**
    * 所在省市区
    **/ 
    public function ShArea(){
        $data = M('ShArea')->where(array('pid'=>0))->select();
        // p($data);
        foreach($data as $key=>$value){
           $province['86'][$value['id']] = $value['name'];
        }
        foreach($province['86'] as $key=>$value){
            $city[$key] = M('ShArea')->where(array('pid'=>$key))->select();    
        }
        foreach($city as $key => $value) {
            foreach ($value as $k => $v) {
                $province[$key][$v['id']] = $v['name'];
            }
        }
        foreach($city as $key=>$value){
            foreach ($value as $k => $v) {
                $area[$v['id']] = M('ShArea')->where(array('pid'=>$v['id']))->select(); 
            }   
        }
        foreach($area as $key => $value) {
            foreach ($value as $k => $v) {
                $province[$key][$v['id']] = $v['name'];
            }
        }
        $this->ajaxreturn($province);
    }
}



 ?>