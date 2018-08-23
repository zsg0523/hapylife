<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Api\Controller\HapylifeUsaController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{

	/**
    *注册手机区号 is_show值为1
    **/
    public function registerCode(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
            	$data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
            	$data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('data',$data);
        $this->display();
    }
    public function new_register(){
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
        $this->display();
    }
    /**
    *腾讯云发送短信(注册)
    *参数：phoneNumber(手机号),whichapp(指定app),acnumber(区号)
    **/
    public function smsCode(){
        // require __DIR__ . "/vendor/autoload.php";
        if(!IS_POST){
            $data['status'] = 100;
            $data['msg']    = '请填写手机号';
            $this->ajaxreturn($data);
        }else{
            vendor('SmsSing.SmsSingleSender');
            // 短信应用SDK AppID
            $appid = 1400096409; // 1400开头
            // 短信应用SDK AppKey
            $appkey = "fc1c7e21ab36fef1865b0a3110709c51";
            // 需要发送短信的手机号码
            $phoneNumber = I('post.phoneNumber');
            //手机区号
            $acnumber    = I('post.acnumber');
            // 短信模板ID，需要在短信应用中申请$templateId
            // 签名
            if($acnumber==86){
                $templateId = 127203;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
                $smsSign = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
            }else if($acnumber==886 || $acnumber==852 || $acnumber==853){
                $templateId = 127206;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }else{
                $templateId = 127204;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }
            $code   =rand(100000,999999);
            $minute ='1';
            // 指定模板ID单发短信
            try {
                $ssender = new \SmsSingleSender($appid, $appkey);
                $params = array($code,$minute);
                $result = $ssender->sendWithParam($acnumber,$phoneNumber,$templateId,$params,$smsSign,"","");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                $rsp = json_decode($result,true);
                if($rsp['errmsg']=='OK'){
                    $mape  = array(
                        'phone'   =>$phoneNumber,
                        'code'    =>$code,
                        'acnumber'=>$acnumber,
                        'date'    =>date('Y-m-d H:i:s')
                    );
                    $add = D('Smscode')->add($mape);
                    $data['status'] = 101;
                    $data['msg']    = '验证码发送成功,请留意短信';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 103;
                    $data['msg']    = '发送失败,请确认手机号码正确并有效';
                    $this->ajaxreturn($data);
                }
            }catch(\Exception $e) {
                $data['status'] = 102;
                $data['msg']    = '发送失败,请确认手机号码正确并有效';
                $this->ajaxreturn($data);
            }

        }
    }
    /********************************************************************新代理注册--需要购买产品********************************************************************************/
    /**
    *检查注册验证码是否正确
    *参数：phoneNumber(手机号),acnumber(区号),code(验证码)
    **/
    public function inCode(){
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $acid        =I('post.acid');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $this->success('验证码正确',U('Home/Register/new_register',array('phoneNumber'=>$phoneNumber,'acnumber'=>$acnumber,'acid'=>$acid)));
            }else{
                $this->error('验证码错误');
            }
        }
    }

    /**
    * 返回推荐人姓名
    **/ 
    public function checkName(){
        $customerid = strtoupper(trim(I('post.EnrollerID')));
        if($customerid){
            if(substr($customerid,0,3) == 'HPL'){
                $data = M('User')->where(array('CustomerID'=>$customerid))->find();
                if(!empty($data)){
                    $this->ajaxreturn($data);     
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);           
                }
            }else{
                $key      = "KDHE5011CVFO1KJEP1A0S";
                $url      = "https://signupapi.wvhservices.com/api/Hpl/Validate?customerId=".$customerid."&"."key=".$key;
                $wv       = file_get_contents($url);
                $data = json_decode($wv,true);
                if(empty($data['error'])){
                    $data['lastname'] = $data['lastName'];
                    $data['firstname'] = $data['firstName'];
                    $this->ajaxreturn($data);     
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);           
                }
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);           
        }
    }
    /**
    * 保存用户资料
    **/ 
    public function new_registerInfo(){
        if(!IS_POST){
            $msg['status'] = 200;
            $msg['message']= '未提交任何数据';
            $this->ajaxreturn($msg);
        }else{
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
                $this->display('Register/new_register');
            }else{
    			if(isset($upload['name'])){
    				$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
    				$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
    			}
                $data['EnrollerID'] = strtoupper(I('post.EnrollerID'));
                $data['LastName'] = trimall(I('post.LastName'));
                $data['FirstName'] = trimall(I('post.FirstName'));
                $data['EnLastName'] = trimall(I('post.EnLastName'));
                $data['EnFirstName'] = trimall(I('post.EnFirstName'));
                $data['WvPass'] = $data['PassWord'];
                $add = D('Tempuser')->add($data);
                if($add){
    		        $this->assign('userinfo',$data);
    		        $this->display();
                }
            }
        }
    }
    /**
    * 获取首购产品
    **/ 
    public function new_purchase(){
    	$data = D('product')->where(array('ip_type'=>1,'is_pull'=>1))->select();
    	$this->assign('data',$data);
        $this->display();

    }
    /**
    * 获取产品详情
    **/ 
    public function new_purchaseInfo(){
    	$ipid =I('get.ipid');
    	$data = D('product')->where(array('ipid'=>$ipid))->find();
    	$this->assign('data',$data);
        $this->display();
    }
   	/**
	* 首购订单
	**/
	public function registerOrder(){
        $iuid = $_SESSION['user']['id'];
        $ipid = I('get.ipid');
        $htid = D('Tempuser')->order('htid desc')->getfield('htid');
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
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
            //下单用户id
            'riuid'=>$iuid,
            //下单用户
            'rCustomerID'=>$userinfo['customerid'],
            //收货人
            'ia_name'=>$userinfo['firstname'],
            //收货人电话
            'ia_phone'=>$userinfo['phone'],
            //收货地址
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
            'htid'        => $htid
        );
        $receipt = M('Receipt')->add($order);
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
        $content = '您帮代理进行的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        $log = array(
            'iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>0,
            'type'      =>2,
            'create_time'   =>time(),
            'create_month'   =>date('Y-m'),
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            if($product['ip_type'] == 1){
                $this->redirect('Home/Pay/choosePay1',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }else{
                $this->redirect('Home/Pay/choosePay',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }
        }else{
            $this->error('生成订单失败');
        }  
	}
    
    /**
    * 首购产品畅捷支付
    **/
    public function new_cjPayment(){
        //订单号
        $order_num                     = I('get.ir_receiptnum');
        $order = M('Receipt')->where(array('ir_receiptnum'=>$order_num))->find();
        $postData                      = array();   
        // 基本参数
        $postData['Service']           = 'nmg_quick_onekeypay';
        $postData['Version']           = '1.0';
        // $postData['PartnerId']         = '200001280051';//商户号
        $postData['PartnerId']         = '200001380239';//商户号
        $postData['InputCharset']      = 'UTF-8';
        $postData['TradeDate']         = date('Ymd').'';
        $postData['TradeTime']         = date('His').'';
        $postData['ReturnUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/Register/new_regsuccess?ir_receiptnum='.$order_num;// 前台跳转url
        $postData['Memo']              = '备注';
        // 4.4.2.8. 直接支付请求接口（畅捷前台） 业务参数++++++++++++++++++
        $postData['TrxId']             = $order_num; //外部流水号
        $postData['SellerId']          = '200001380239'; //商户编号，调用畅捷子账户开通接口获取的子账户编号;该字段可以传入平台id或者平台id下的子账户号;作为收款方使用；与鉴权请求接口中MerchantNo保持一致
        $postData['SubMerchantNo']     = '200001380239'; //子商户，在畅捷商户自助平台申请开通的子商户，用于自动结算
        $postData['ExpiredTime']       = '48h'; //订单有效期，取值范围：1m～48h。单位为分，如1.5h，可转换为90m。用来标识本次鉴权订单有效时间，超过该期限则该笔订单作废
        $postData['MerUserId']         = $order['riuid']; //用户标识
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
        $postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/Register/new_notifyVerify';//异步通知地址
        $postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
        $postData['Extension']         = '';//扩展字段
        $postData['Sign']              = rsaSign($postData);
        $postData['SignType']          = 'RSA'; //签名类型      
        $query                         = http_build_query($postData);
        $url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
        $data['url']                   = $url;
        header("Location:".$url);
    }
    //购买产品畅捷返回结果
    public function new_notifyVerify(){
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
                $order = D('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
                $subnum= bcsub($order['ir_unpaid'],$receipt['ir_price'],2);
                if($subnum=='0.00'){
                    $sub      = 0;
                    $unp      = 0;
                    $ir_status= 2;
                    $ir_paytime = time();
                }else{  
                    $sub      = $subnum;
                    $unp      = bcdiv($sub,100,4);
                    $ir_status= 202;
                    $ir_paytime = 0;
                }
                if($sub==0){
                    //判断是否新代理注册
                    if($order['htid']){
                        $tmpeArr = M('Tempuser')->where(array('htid'=>$order['htid']))->find();
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
                            'DistributorType'    =>D('Product')->where(array('ipid'=>$order['ipid']))->getfield('ip_after_grade'),
                            'JoinedOn'    => time(),
                            'WvPass' => $tmpeArr['password'],
                        );
                        $update     = M('User')->add($tmpe);       
                        $riuid      = $update;
                        $OrderDate  = date("Y-m-d",strtotime("-1 month",time()));
                        $userinfo= M('User')->where(array('CustomerID'=>$CustomerID))->find();
                        $status  = array(
                            'ir_status'  =>$ir_status,
                            'rCustomerID'=>$CustomerID,
                            'riuid'      =>$userinfo['iuid'],
                            'ia_name'    =>$userinfo['lastname'].$userinfo['firstname'],
                            'ia_name_en' =>$userinfo['enlastname'].$userinfo['enfirstname'],
                            'ia_phone'   =>$userinfo['phone'],
                            'ia_address' =>$userinfo['shopaddress1'],
                            'ir_unpaid'  =>$sub,
                            'ir_unpoint' =>$unp,
                            'ir_paytime' =>$ir_paytime,
                        );
                        //更新订单信息
                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    }else{
                        $userinfo   = D('User')->where(array('iuid'=>$order['riuid']))->find();
                        //修改用户最近订单日期/是否通过/等级/数量
                        $tmpe['iuid'] = $order['riuid'];
                        //产品等级
                        $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$order['ipid']))->getfield('ip_after_grade');
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
                        $riuid     = $order['riuid'];
                        $status  = array(
                            'ir_status'  =>$ir_status,
                            'ir_unpaid'  =>$sub,
                            'ir_unpoint' =>$unp,
                            'ir_paytime' =>$ir_paytime,
                        );                   
                        //更新订单信息
                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                        $tmpeArr['password'] = $userinfo['wvpass'];
                        $status['ia_name']   = $userinfo['shopaddress1'];
                    }
                    if($upreceipt){    
                        $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$order['ir_receiptnum']);
                        $usa    = new \Common\UsaApi\Usa;
                        $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone']);
                        if(!empty($result['result'])){
                            $log = addUsaLog($result['result']);
                            $maps = json_decode($result['result'],true);
                            $wv  = array(
                                        'wvCustomerID' => $maps['wvCustomerID'],
                                        'wvOrderID'    => $maps['wvOrderID']
                                    );
                            $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
                            if($res){
                                $templateId ='164137';
                                $params     = array();
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
                                                'content' => '恭喜您注册成功，请注意查收邮件'
                                    );
                                    $logs = M('SmsLog')->add($contents);
                                }
                            }
                        }
                    }
                }else{
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp
                    );                   
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                }
            }
        }
    }

    /**
    * 二维码获取
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    **/
    public function getQrcode(){
        //订单号
        $ir_receiptnum  = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(10000, 99999);
        //用户iuid
        $iuid           = I('post.iuid');
        //订单信息查询
        $order          = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();

        // wsdl模式访问wsdl程序
        $client = new \SoapClient("https://pay.hkipsec.com/webservice/GetQRCodeWebService.asmx?wsdl",
            array(
                'trace' => true,
                'exceptions' => true,
                'stream_context'=>stream_context_create(array('ssl' => array('verify_peer'=>false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        )
                    )
                )
            ));

        //E0001904
        // $merchantcert = "GB30j0XP0jGZPVrJc6G69PCLsmPKNmDiISNvrXc0DB2c7uLLFX9ah1zRYHiXAnbn68rWiW2f4pSXxAoX0eePDCaq3Wx9OeP0Ao6YdPDJ546R813x2k76ilAU8a3m8Sq0";
        //E000404
        $merchantcert = "1mtfZAJ3sGPc22Vq20LUaJ9Z8w0S8BBP3Jc5uJkwM7v7099nbmwwvVfICu7CkQVGea9JzzVIpzh3xb9YNmRvpp47DtTam7lWCF20aPOBrDgVOCvAL9PXZ91P6bff6U6H";

        try{
             // $merAccNo       = 'E0001904';
            $merAccNo       = "E0004004";
            $orderId        = $ir_receiptnum;
            $fee_type       = "CNY";
            $amount         = $order['ir_price'];
            $goodsInfo      = "Product";
            $strMerchantUrl = "http://apps.hapy-life.com/hapylife/index.php/Home/Purchase/getResponse";
            $cert           = $merchantcert;
            $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
            $signMD5_lower = strtolower(md5($signMD5));

            $para = array(
                'merAccNo'      => $merAccNo,
                'orderId'       => $orderId,
                'fee_type'      => $fee_type,
                'amount'        => $amount,
                'goodsInfo'     => $goodsInfo,
                'strMerchantUrl'=> $strMerchantUrl,
                'signMD5'       => $signMD5_lower
            );

            $result      = $client->GetQRCodeXml($para);
            //对象操作
            $xmlstr      = $result->GetQRCodeXmlResult;
            //构造SimpleXMLEliement对象
            $xml         = new \SimpleXMLElement($xmlstr);
            //微信支付链接
            $code_url    = (string)$xml->code_url;
            $return_code = (string)$xml->return_code;
            $return_msg  = (string)$xml->return_msg;

            //返回数据
            $para['code_url'] = $code_url;
            $para['return_code'] = $return_code;
            $para['return_msg'] = $return_msg;

            $this->ajaxreturn($para);
            
        }catch(SoapFault $f){
            echo "Error Message:{$f->getMessage()}";
        }
    }
    

    /**
    * 支付成功订单状态修改
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    **/
    public function getResponse(){
        //获取ips回调数据
        $data = I('post.');

        //写入日志记录
        $jsonStr = json_encode($data);
        $log     = logTest($jsonStr);
        
        //查询订单信息
        $receipt = M('Receiptson')->where(array('pay_receiptnum'=>$data['billno']))->find();

        //支付返回数据验证,是否支付成功验证
        if($data['succ'] == 'Y'){
            //签名验证
            //订单数量&订单金额
            if($data['amount'] == $receipt['ir_price']){                
                //修改订单状态
                $map = array(
                    'ir_paytype' =>1,
                    'status'  =>2,
                    'paytime'=>time(),
                    'ips_trade_no' => $data['ipsbillno'],
                    'ips_trade_status' => $data['msg']
                );
                $change_orderstatus = M('Receiptson')->where(array('pay_receiptnum'=>$data['billno']))->save($map);
                if($change_orderstatus){
                    $order   = D('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();  
                    $OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                    $subnum= bcsub($order['ir_unpaid'],$receipt['ir_price'],2);
                    if($subnum=='0.00'){
                        $sub      = 0;
                        $unp      = 0;
                        $ir_status= 2;
                        $ir_paytime = time();
                    }else{
                        $sub      = $subnum;
                        $unp      = bcdiv($sub,100,4);
                        $ir_status= 202;
                        $ir_paytime = 0;
                    }
                    if($sub==0){
                        $addactivation     = D('Activation')->addAtivation($OrderDate,$receipt['riuid'],$receipt['ir_receiptnum']);
                    }
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp,
                        'ir_paytime' =>$ir_paytime,
                    );
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);  
                }
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);         
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 订单状态查询
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单编号
    **/
    public function checkreceipt(){
        $ir_receiptnum = I('post.ir_receiptnum');
        //订单状态查询
        $receipt_status = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_status');
        if($receipt_status == 2){
            //支付成功
            $data['status'] = 1;
            $data['msg'] = '支付成功，请跳转...';
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg'] = '正在支付，请等待...';
            $this->ajaxreturn($data);
        }
    }

    /**
    *代理成功注册页面
    **/
    public function new_regsuccess(){
        $ir_receiptnum = I('get.ir_receiptnum');
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
            $data                  = M('User')->where(array('CustomerID'=>$receipt['rcustomerid']))->find();
            $data['ir_receiptnum'] = $ir_receiptnum;
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('失败');
        }
    }

    /*********************************************************************普通注册********************************************************************************************/  
    /**
    *注册手机区号 is_show值为1
    **/
    public function oldRegisterCode(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
            	$data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
            	$data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('data',$data);
        $this->display();
    }
    
    /**
    *检查旧注册验证码是否正确
    *参数：phoneNumber(手机号),acnumber(区号),code(验证码)
    **/
    public function oldInCode(){
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $this->success('验证码正确',U('Home/Register/register'));
            }else{
                $this->error('验证码错误');
            }
        }
    }
	// 普通用户注册
	public function register(){
		$mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $datas[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
            	$datas[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
            	$datas[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
		$data = I('post.');
		$upload = several_upload();
		if(IS_POST){
			$User = D("User"); // 实例化User对象
            if(!$User->create($data)){
                 // 如果创建失败 表示验证没有通过 输出错误提示信息
                $error = $User->getError();
            }else{
                 // 验证通过 可以进行其他数据操作
                if($User->create($data)){
                    if(isset($upload['name'])){
                        $data['JustIdcard']=C('WEB_URL').$upload['name'][0];
                        $data['BackIdcard']=C('WEB_URL').$upload['name'][1];
                    }
                    $data['WvPass'] = $data['PassWord'];
                    $data['PassWord'] = md5($data['PassWord']);
                    $data['JoinedOn'] = time();
                    $data['CustomerID'] = strtoupper($data['CustomerID']);
                    $data['LastName'] = trimall(I('post.LastName'));
                    $data['FirstName'] = trimall(I('post.FirstName'));
                    $data['EnLastName'] = trimall(I('post.EnLastName'));
                    $data['EnFirstName'] = trimall(I('post.EnFirstName'));
                    $keyword= 'HPL';
                    $custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
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
                    $data['CustomerID'] = $CustomerID;
                    $result = D('User')->add($data);
                    if($result){
                        $this->redirect('Home/Register/registerInfo',array('iuid'=>$result));
                    }
                }
            }
		}

		$assign = array(
            		'error' => $error,
            		'data' => $data,
            		'datas' => $datas
            		);
        $this->assign($assign);
		$this->display();
	}

    // 确认信息页面
    public function registerInfo(){
        $iuid = I('get.iuid');
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $assign = array(
                        'userinfo' => $userinfo,
                        'iuid' => $iuid,
                        );
        $this->assign($assign);
        $this->display();
    }

	// 注册成功显示页面
	public function regsuccess(){
		$iuid = I('get.iuid');
		$data = D('User')->where(array('iuid'=>$iuid))->find();
        if($data){
            // 发送短信提示
            $templateId ='178952';
            $params     = array($data['customerid']);
            $sms        = D('Smscode')->sms($data['acnumber'],$data['phone'],$params,$templateId);
            if($sms['errmsg'] == 'OK'){
                $contents = array(
                            'acnumber' => $data['acnumber'],
                            'phone' => $data['phone'],
                            'operator' => '系统',
                            'addressee' => $data['lastname'].$data['firstname'],
                            'product_name' => '',
                            'date' => time(),
                            'content' => '恭喜您创建成功，您的会员号码是'.$data['customerid'].'，同时注意查收Rovia邮件。',
                            'customerid' => $data['customerid']
                );
                $logs = M('SmsLog')->add($contents);
            }
        }
		$assign = array(
						'data' => $data,
                        'iuid' => $iuid,
						);
        $this->assign($assign);
		$this->display();
	}





}



 ?>