<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
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
            $User = D("User1"); // 实例化User对象
            if(!$User->create($data)){
                 // 如果创建失败 表示验证没有通过 输出错误提示信息
                $error = $User->getError();
                $assign = array(
                            'error' => $error,
                            'data' => $data
                            );
                $this->assign($assign);
                $this->display('Register/new_register');
            }else{
                $data['iuid']= $_SESSION['user']['id'];
    			if(isset($upload['name'])){
    				$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
    				$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
    			}
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
            'from_iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>0,
            'type'      =>2,
            'date'      =>date('Y-m-d H:i:s')          
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            $this->success('生成订单成功',U('Home/Register/new_cjPayment',array('ir_receiptnum'=>$order_num)));
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
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
        $cjPayStatus = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($map);
        //验签
        $return = rsaVerify($map);
        //更改订单状态
        if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
            if($receipt['ir_status']==7){
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
                    $where =array('iuid'=>$userinfo['iuid'],'ir_receiptnum'=>$map['outer_trade_no'],'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
                    $save  = M('Activation')->add($where);
                    $status  = array(
                        'ir_status'  =>2,
                        'rCustomerID'=>$CustomerID,
                        'riuid'      =>$userinfo['iuid'],
                        'ir_paytype' =>1,
                        'ir_paytime' =>time(),
                        'ia_name'    =>$userinfo['lastname'].$userinfo['firstname'],
                        'ia_name_en' =>$userinfo['enlastname'].$userinfo['enfirstname'],
                        'ia_phone'   =>$userinfo['phone'],
                        'ia_address' =>$userinfo['shopaddress1'],
                    );
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($status);
                }
            }
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
    /**
    * 购买产品订单状态查询
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单编号
    **/
    public function checkreceipt(){
        $ir_receiptnum = I('get.ir_receiptnum');
        //订单状态查询
        $receipt       = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
            //支付成功
            $receipt['status'] = 1;
            $receipt['msg'] = '支付成功，请跳转...';
            $this->ajaxreturn($receipt);
        }else{
            $data['status'] = 0;
            $data['msg'] = '正在支付，请等待...';
            $this->ajaxreturn($data);
        }
    }
    /*********************************************************************普通注册********************************************************************************************/  
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
		$data = I('post.');

		$upload = several_upload();
		if(IS_POST){
			if($data['Sex']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if($User->create($data)){
						if(isset($upload['name'])){
							$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
							$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						}
						$data['PassWord'] = md5($data['PassWord']);
						$data['JoinedOn'] = time();
						$data['CustomerID'] = strtoupper($data['CustomerID']);
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
						$result	= D('User')->add($data);
						if($result){
							$this->redirect('Home/Register/regsuccess');
						}
					}
				}
			}else{
				$error['Sex'] = '请选择性别';
			}

			if($data['AccountType']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if($User->create($data)){
						if(isset($upload['name'])){
							$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
							$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						}
						$data['PassWord'] = md5($data['PassWord']);
						$data['JoinedOn'] = time();
						$data['CustomerID'] = strtoupper($data['CustomerID']);
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
						$result	= D('User')->add($data);
						if($result){
							$this->redirect('Home/Register/regsuccess');
						}
					}
				}
			}else{
				$error['AccountType'] = '请选择销售类型';
			}
		}

		$assign = array(
            		'error' => $error,
            		'data' => $data
            		);
        $this->assign($assign);
		$this->display();
	}

	// 注册成功显示页面
	public function regsuccess(){
		$data = max(D('User')->select());
		$data = D('User')->where(array('iuid'=>$data['iuid']))->find();
		
		$assign = array(
						'data' => $data,
						);
        $this->assign($assign);
		$this->display();
	}





}



 ?>