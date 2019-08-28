<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 畅捷支付
**/
class HapylifePayController extends HomeBaseController{
	public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            die;
        }
    }
	/**
	*生成子订单
	**/
	public function receiptSon(){
		$ir_receiptnum   = I('post.ir_receiptnum');
        $ip_paytype      = I('post.ip_paytype');
        $ir_price        = I('post.ir_unpaid');
        $pay_receiptnum  = date('YmdHis').rand(100000, 999999);
        $iuid            = D('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('riuid');
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
        // p($mape);die;
        if($ir_price>0){
            $add = D('receiptson')->add($mape);
            if($add){
	           $mape['status'] = 1;
	           $mape['msg']    = '提交成功,跳转中';
	           $this->ajaxreturn($mape);
            }else{
	           $mape['status'] = 0;
	           $mape['msg']    = '系统超时,请重新提交';
	           $this->ajaxreturn($mape);
            }
        }else{
           $mape['status'] = 0;
		   $mape['msg']    = '请输入正确金额或积分';
           $this->ajaxreturn($mape);
        }
	}
    /**
    *显示子订单详情
    **/
    public function toCheckPoint(){
        $pay_receiptnum = I('post.pay_receiptnum');
        $data = M('receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->find();
        if($data){
        	$data['status'] = 1;
        	$this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    *调取支付 ip_paytype 1IPS 2积分 3转账 4畅捷
    **/
    public function productPayment(){
    	$iuid          = I('post.iuid');
        $pay_receiptnum= I('post.pay_receiptnum');
        $ip_paytype    = I('post.ir_paytype');
        $bankId 	   = I('post.bankId');
        // 获取子订单信息
        $order         = M('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->find();
        // 获取父订单信息
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$order['ir_receiptnum']))->find();
        // 订单金额
        $orderAmount = bcmul($receiptson['ir_price'],100,0);
        // p($order);
        // p($receipt);die;
        switch($ip_paytype){
        	case '1':
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
		            // $merAccNo       = "E0004004";
		            $merAccNo       = "E0004014";//E0004004的升级
		            $orderId        = $pay_receiptnum;
		            $fee_type       = "CNY";
		            $amount         = $order['ir_price'];
		            // $amount         = '0.1';
		            $goodsInfo      = "Product";
		            $strMerchantUrl = "http://apps.hapy-life.com/hapylife/index.php/Api/HapylifePay/getResponse";
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
		            $para['code_url']    = $code_url;
		            $para['return_code'] = $return_code;
		            $para['return_msg']  = $return_msg;
		            //生成二维码
		            $url            = createCode(urldecode($code_url),'Upload/avatar/'.$pay_receiptnum.'.png');
		            $para['qrcode'] = C('WEB_URL').'/Upload/avatar/'.$pay_receiptnum.'.png';
		            $this->ajaxreturn($para);
		            
		        }catch(SoapFault $f){
		            echo "Error Message:{$f->getMessage()}";
		        }
        		break;
        	case '2':
        		if($receipt['ir_paytime'] != 0){
        			// 积分不足
		            $data['status'] = 0;
		            $this->ajaxreturn($data);
        		}else{
	        		$grade   = D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade');
	        		$product   = D('Product')->where(array('ipid'=>$receipt['ipid']))->find();
	        		// p($grade);die;
	        		// 获取用户信息
	        		$userinfo= M('User')->where(array('iuid'=>$iuid))->find();
			        // 用户剩余积分
			        $residue = bcsub($userinfo['iu_point'],$order['ir_point'],2);
			        if($residue>=0){
			            //修改用户积分
			            $message = array(
			                'iu_point'  =>$residue
			            );
			            $insertpoint = M('User')->where(array('iuid'=>$iuid))->save($message);
			            if($insertpoint){
			                //修改子订单状态
			                $map = array(
			                    'ir_paytype' => 2,
			                    'status' => 2,
			                    'paytime' => time()
			                );
			                $change_orderstatus = M('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->save($map);
			                if($change_orderstatus){
			                    //生成子订单日志记录
			                    $content = '订单:'.$pay_receiptnum.'支付成功,扣除Ep:'.$order['ir_point'].',剩余Ep:'.$residue;
			                    $log     = array(
			                                'iuid' => $iuid,
			                                'name' => $userinfo['customerid'],
			                                'content' => $content,
			                                'create_time'   =>time(),
			                                'create_month'   =>date('Y-m'),
			                                'type' => 1,
			                                'action' => 3,
			                            );
			                    $addlog  = M('Log')->add($log);
			                    if($addlog){
			                        // 记录会员使用EP日志
			                        $content = $userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时，消费出'.$order['ir_point'].'EP到系统，剩EP余额'.$residue;
			                        $logs = array(
			                                    'pointNo' => $pay_receiptnum,
			                                    'iuid' => $iuid,
			                                    'hu_username' => $userinfo['lastname'].$userinfo['firstname'],
			                                    'hu_nickname' => $userinfo['customerid'],
			                                    'send' => $userinfo['customerid'],
			                                    'received' => '系统',
			                                    'getpoint' => $order['ir_point'],
			                                    'pointtype' => 7,
			                                    'realpoint' => $order['ir_point'],
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
			                            $ir_unpoint = bcsub($receipt['ir_unpoint'],$order['ir_point'],2);
			                            // 父订单待支付金额
			                            $ir_unpaid = bcsub($receipt['ir_unpaid'],$order['ir_price'],2);
			                            // 修改父订单状态
			                            if($ir_unpoint != 0 && $ir_unpaid != 0){
			                                $maps = array(
			                                    'ir_unpoint' => $ir_unpoint,
			                                    'ir_unpaid' => $ir_unpaid,
			                                    'ir_status' => 202,
			                                );
			                                $change_receipts = M('Receipt')->where(array('ir_receiptnum'=>$order['ir_receiptnum']))->save($maps);
			                                if($change_receipts){
			                                   	$data['status'] = 3;
			            						$this->ajaxreturn($data);
			                                }
			                            }else{
			                                $maps = array(
			                                    'ir_unpoint' => $ir_unpoint,
			                                    'ir_unpaid' => $ir_unpaid,
			                                    'ir_status' => 2,
			                                    'ir_paytime' => time(),
			                                );
			                                $change_receipt = M('Receipt')->where(array('ir_receiptnum'=>$order['ir_receiptnum']))->save($maps);
			                                if($change_receipt){
			                                    if($receipt['ir_ordertype']==4){
			                                    	// 添加通用券
			                                        $product= M('Receipt')
	                                                        ->alias('r')
	                                                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
	                                                        ->where(array('ir_receiptnum'=>$order['ir_receiptnum']))
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
	                                                	//支付成功
											            $data['status'] = 1;
											            $this->ajaxreturn($data);
			                                        }else{
											            $data['status'] = 0;
											            $this->ajaxreturn($data);
			                                        }
			                                    }else if($receipt['ir_ordertype']==5){
			                                    	// 支付完成
										            $data['status'] = 1;
										            $this->ajaxreturn($data);
			                                    }else{
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
				                                            'DistributorType'    =>$grade,
				                                            'JoinedOn'    => time(),
				                                        );
				                                        $update     = M('User')->add($tmpe);       
				                                        $riuid      = $update;
				                                        $OrderDate  = date("Y-m-d",strtotime("-1 month",time()));
				                                        $userinfos= M('User')->where(array('CustomerID'=>$CustomerID))->find();
				                                        $status  = array(
				                                            'ir_status'  =>$maps['ir_status'],
				                                            'rCustomerID'=>$CustomerID,
				                                            'riuid'      =>$userinfos['iuid'],
				                                            'ia_name'    =>$userinfos['lastname'].$userinfos['firstname'],
				                                            'ia_name_en' =>$userinfos['enlastname'].$userinfos['enfirstname'],
				                                            'ia_phone'   =>$userinfos['phone'],
				                                            'ia_province'=>$userinfos['shopprovince'],
						                                    'ia_city'    =>$userinfos['shopcity'],
						                                    'ia_area'    =>$userinfos['shoparea'],
						                                    'ia_address' =>$userinfos['shopaddress1'],
				                                            'ir_unpaid'  =>$maps['ir_unpaid'],
				                                            'ir_unpoint' =>$maps['ir_unpoint']
				                                        );
				                                        //更新订单信息
				                                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
				                                        // 添加激活记录
				                                        if($upreceipt){    
				                                            $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$receipt['ir_receiptnum']);
				                                        }
				                                        $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
				                                        if($ir_status == 2){
					                                        // 发送数据到usa
					                                        $usa    = new \Common\UsaApi\Usa;
					                                        // switch($receipt['ipid']){
					                                        // 	case '31':
				                                         //            $products = '1';
				                                         //            break;
				                                         //        case '62':
				                                         //            $products = '5';
				                                         //            break;
				                                         //        case '64':
				                                         //            $products = '4';
				                                         //            break;
					                                        // }
					                                        $result = $usa->createCustomer($userinfos['customerid'],$tmpeArr['password'],$userinfos['enrollerid'],$userinfos['enfirstname'],$userinfos['enlastname'],$userinfos['email'],$userinfos['phone'],$product['productid'],$tmpeArr['birthday']);
					                                        if(!empty($result['result'])){
					                                            $log = addUsaLog($result['result']);
					                                            $maps = json_decode($result['result'],true);
					                                            $wv  = array(
					                                                        'wvCustomerID' => $maps['wvCustomerID'],
					                                                        'wvOrderID'    => $maps['wvOrderID'],
					                                                        'Products'     => $product['productid']
					                                                    );
					                                            $res = M('User')->where(array('iuid'=>$userinfos['iuid']))->save($wv);
					                                            if($res){
					                                            	$addressee = $status['ia_name'];
					                                                // 发送短信提示
					                                                $templateId ='244312';
					                                                $params     = array($addressee,$maps['wvCustomerID'],$product['ip_name_zh']);
					                                                $sms        = D('Smscode')->sms($userinfos['acnumber'],$userinfos['phone'],$params,$templateId);
					                                                if($sms['errmsg'] == 'OK'){
					                                                    $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
					                                                    $contents = array(
					                                                                'acnumber' => $userinfos['acnumber'],
					                                                                'phone' => $userinfos['phone'],
					                                                                'operator' => '系统',
					                                                                'addressee' => $addressee,
					                                                                'product_name' => $receiptlist['product_name'],
					                                                                'date' => time(),
					                                                                'content' => '欢迎来到DT!，亲爱的DT会员您好，欢迎您加入DT成为DT大家庭的一员！在开始使用您的新会员资格前，请确认下列账户信息是否正确:姓名：'.$addressee.'会员号码：'.$maps['wvCustomerID'].'产品：'.$product['ip_name_zh'].'使用上面的会员ID号码以及您在HapyLife帐号注册的时候所创建的密码登录DT官网，开始享受您的会籍。我们很开心您的加入。我们迫不及待地与您分享无数令人兴奋和难忘的体验！',
	                                        										'customerid' => $userinfos['customerid']
					                                                    );
					                                                    $logs = M('SmsLog')->add($contents);
					                                                }

					                                                // 给上线发短信
											                        $enrollerinfo = M('User')->where(array('CustomerID'=>$userinfos['enrollerid']))->find(); 
											                        $templateId ='244300';
											                        $params     = array($enrollerinfo['customerid'],$userinfos['customerid']);
											                        $sms        = D('Smscode')->sms($enrollerinfo['acnumber'],$enrollerinfo['phone'],$params,$templateId);
											                        if($sms['errmsg'] == 'OK'){
											                            $addressee = $enrollerinfo['lastname'].$enrollerinfo['firstname'];
											                            $contents = '尊敬的'.$enrollerinfo['customerid'].'会员，您增加一名成员：'.$userinfos['customerid'];
											                            $addlog = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$addressee,'上线接收短信',$contents,$enrollerinfo['customerid']);
											                        }
	                        
					                                            	$createPayment = $usa->createPayment($userinfos['customerid'],$maps['wvOrderID'],date('Y-m-d H:i',time()));
					                                            	$log = addUsaLog($createPayment['result']);

				                                                	//支付成功
														            $data['status'] = 1;
														            $this->ajaxreturn($data);
					                                            }
					                                        }
				                                        }
				                                    }else{
				                                        $userinfo   = D('User')->where(array('iuid'=>$receipt['riuid']))->find();
				                                        //修改用户最近订单日期/是否通过/等级/数量
				                                        $tmpe['iuid'] = $receipt['riuid'];
				                                        //产品等级
				                                        $tmpe['DistributorType'] = $grade;
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
				                                        // if($receipt['ir_ordertype']==1){
				                                        // 	// 发送数据到usa
					                                       //  $usa    = new \Common\UsaApi\Usa;
					                                       //  $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone']);
					                                       //  if(!empty($result['result'])){
					                                       //      $log = addUsaLog($result['result']);
					                                       //      $maps = json_decode($result['result'],true);
					                                       //      $wv  = array(
					                                       //                  'wvCustomerID' => $maps['wvCustomerID'],
					                                       //                  'wvOrderID'    => $maps['wvOrderID']
					                                       //              );
					                                       //      $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
					                                       //      if($res){
					                                       //          // 发送短信提示
					                                       //          $templateId ='164137';
					                                       //          $params     = array();
					                                       //          $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
					                                       //          if($sms['errmsg'] == 'OK'){
					                                       //              $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
					                                       //              $contents = array(
					                                       //                          'acnumber' => $userinfo['acnumber'],
					                                       //                          'phone' => $userinfo['phone'],
					                                       //                          'operator' => '系统',
					                                       //                          'addressee' => $status['ia_name'],
					                                       //                          'product_name' => $receiptlist['product_name'],
					                                       //                          'date' => time(),
					                                       //                          'content' => '恭喜您注册成功，请注意查收邮件',
					                                       //                          'customerid' => $CustomerID
					                                       //              );
					                                       //              $logs = M('SmsLog')->add($contents);
					                                       //          }
					                                       //      }
					                                       //  }
				                                        // }
				                                        $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
				                                        if($ir_status == 2){
				                                        	$usa = new \Common\UsaApi\Usa;
				                                            $createPayment = $usa->createPayment($userinfo['customerid'],$receipt['ir_receiptnum'],date('Y-m-d H:i',time()));
				                                            $log = addUsaLog($createPayment['result']);
				                                            $jsonStr = json_decode($createPayment['result'],true);
				                                            // p($jsonStr);die;
				                                            if($jsonStr['paymentId']){
				                                            	// 检测所有月费单是否存在未支付
							                                    $allIrstatus = M('Receipt')->where(array('ir_ordertype'=>3,'rCustomerID'=>$userinfo['customerid']))->getField('ir_status',true);
							                                    if(!in_array(0,$allIrstatus) && !in_array(7,$allIrstatus)){
							                                        $statusResult = M('User')->where(array('customerid'=>$userinfo['customerid']))->setfield('showProduct','0');
							                                    }
				                                            }
					                                        // 支付完成
												            $data['status'] = 1;
												            $this->ajaxreturn($data);
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
			            $data['status'] = 0;
			            $this->ajaxreturn($data);
			        }
        		}
        		break;
        	case '3':
        		# code...
        		break;
        	case '4':
		        $postData                      = array();   
		        // 基本参数
		        $postData['Service']           = 'nmg_quick_onekeypay';
		        $postData['Version']           = '1.0';
		        // $postData['PartnerId']         = '200001280051';//商户号
		        $postData['PartnerId']         = '200001380239';//商户号
		        $postData['InputCharset']      = 'UTF-8';
		        $postData['TradeDate']         = date('Ymd').'';
		        $postData['TradeTime']         = date('His').'';
		        $postData['ReturnUrl']         = '';// 前台跳转url
		        $postData['Memo']              = '备注';
		        // 4.4.2.8. 直接支付请求接口（畅捷前台） 业务参数++++++++++++++++++
		        $postData['TrxId']             = $pay_receiptnum; //外部流水号
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
		        $postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Api/HapylifePay/notifyVerify';//异步通知地址
		        $postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
		        $postData['Extension']         = '';//扩展字段
		        $postData['Sign']              = rsaSign($postData);
		        $postData['SignType']          = 'RSA'; //签名类型      
		        $query                         = http_build_query($postData);
		        $url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
		        $data['url']                   = $url;
		        $this->ajaxreturn($data);
        		break;
        	case '8' || '9' || '10':
        		switch ($ip_paytype) {
        			case '8':
        				$payType = 1;
        				break;
        			case '9':
        				$payType = 2;
        				break;
        			case '10':
        				$payType = 4;
        				break;
        			
        		}
        		$ext = json_encode(array('deviceInfo'=>4,'payType'=>$payType,"deviceType"=>2));
		        //商户会员号，订单金额*100
		        $post_data = array (
		            "inputCharset" => "1",//编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
		            "pageUrl" => "http://apps.hapy-life.com/hapylife/index.php/Home/Pay/waitings",//接收支付结果的页面地址，该参数一般置为空即可。
		            "bgUrl" => "http://apps.hapy-life.com/hapylife/index.php/Api/HapylifePay/notifyReceiver",//服务器接收支付结果的后台地址，该参数务必填写，不能为空。
		            // "bgUrl" => "http://localhost/hapylife/index.php/Home/Purchase/notifyReceiver",//服务器接收支付结果的后台地址，该参数务必填写，不能为空。
		            "version" => "3.0",//网关版本，固定值：3.0,该参数必填。
		            "language" => "1",//语言种类，1代表中文显示，2代表英文显示。默认为1,该参数必填。
		            "signType" => "4",//签名类型,固定值：4。RSA加签
		            "merchantAcctId" => "1021903029201",//商户会员号，该账号为11位商户号+01,该参数必填。
		            "terminalId" => "AYGJ001",//终端号，由我司提供。
		            "payerName" => "",//支付人姓名,根据产品开通时的配置确定是否必填。
		            "payerContactType" => "2",//支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空
		            "payerContact" => "",//支付人联系方式，与payerContactType设置对应，payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码。可以为空。
		            "payerIdentityCard" => "",//支付人身份证号码，根据产品开通时的配置确定是否必填。
		            "mobileNumber" => "",//支付人手机号，可为空。
		            "cardNumber" => "",//支持人所持卡号，可为空
		            "customerId" => "",//支付人在商户系统的客户编号，可为空。
		            "orderId" =>$pay_receiptnum,//商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
		            "settlementCurrency" => "CNY",//结算币种，3位币别码，详情请参照币别代码附录。该字段值不能为CNY。
		            "inquireTrxNo" => "",//询盘流水号
		            "orderCurrency" => "CNY",
		            "orderAmount" => $orderAmount,//订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。
		            "orderTime" => date("YmdHis"),//订单提交时间，格式：yyyyMMddHHmmss，如：20071117020101，不能为空。
		            "productName" => "test",//商品名称，可以为空。
		            "productNum" => "1",//商品数量，可以为空
		            "productId" => "100003",//商品代码，可以为空。
		            "productDesc" => "",//商品描述，可以为空。
		            "ext1" => '',//扩展字段1，商户可以传递自己需要的参数，支付完后会原值返回，可以为空。
		            "ext2" => '',//扩展自段2，商户可以传递自己需要的参数，支付完后会原值返回，可以为空。
		            "deviceType" => "1",//1：pc端支付2：移动端支付
		            "payType" => $payType,//1：微信扫码（返回二维码）2：支付宝扫码
		            "bankId" => $bankId,//银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
		            "customerIp" => "",//支付人Ip，可为空。
		            "redoFlag" => "0",//同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
		        );
		        $post_data['signMsg'] = htxRsaSign($post_data);
		        // p($post_data);die;

		        /////////////  后台置单 ///////// 开始 //
		        $url = 'https://global.chinapnr.com/pay/unifiedorder.htm';
		        $curl = curl_init();
		        curl_setopt($curl, CURLOPT_URL, $url);
		        curl_setopt($curl, CURLOPT_HEADER, 0);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);// 设置 POST 参数
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//不验证证书
		        $respData = curl_exec($curl);
		        $respUrl = "";
		        curl_close($curl);
		        /////////////  后台置单 ///////// 结束//
		        if (strpos($respData,"errCode")) {
		            echo $respData;
		            // $para = array(
		            // 	'status' => 202,
		            // 	'msg' => $respData
		            // );
		            // $this->ajaxreturn($para);
		        }else{
		            $respUrl = $respData;
	            	$qrUrl=urldecode($respUrl);
		            if(!strpos($respUrl,'&respMsg')){
		                //获取二维码地址
		                $ary=explode("&url=",$qrUrl);
		                if ($ip_paytype == 8 || $ip_paytype == 9){
		                    //生成二维码，并存储
		                    $url             = createCode(urldecode(explode('&',$ary[1])[0]),'Upload/avatar/'.$pay_receiptnum.'.png');
		                    $para['qrcode']  = C('WEB_URL').'/Upload/avatar/'.$pay_receiptnum.'.png';
		                    $para['amount'] = $order['ir_price'];
		                    $para['orderId'] = $pay_receiptnum;
		                    $this->ajaxReturn($para);
		                }elseif ($ip_paytype == 10){
		                    $para['payUrl'] = $ary[1];
		                    $this->ajaxReturn($para);
		                }
		            }else{
		                $para = array(
		                    'status' => 202,
		                    'msg' => $respData
		                );
		                $this->ajaxreturn($para);
		            }
		        }
        		break;
        }
    }
    /**
    *IPS返回值
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
                    $userinfo   = D('User')->where(array('iuid'=>$receipt['riuid']))->find();  
                    $product_name   = D('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('product_name');  
                    $OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                    $subnum= bcsub($order['ir_unpaid'],$receipt['ir_price'],2);
                    if($subnum==0){
                        $sub      = 0;
                        $unp      = 0;
                        $ir_status= 2;
                    }else{
                        $sub      = $subnum;
                        $unp      = bcdiv($sub,100,2);
                        $ir_status= 202;
                    }
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp
                    );
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    if($upreceipt){
                        if($sub == 0){
                            $updateReceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->setfield('ir_paytime',time());
                            switch ($order['ir_ordertype']) {
								case '3':
									$addactivation     = D('Activation')->addAtivation($OrderDate,$receipt['riuid'],$receipt['ir_receiptnum']); 
                            		break;
								case '4':
									// 添加通用券
	                                $product = M('Receipt')
	                                                ->alias('r')
	                                                ->join('hapylife_product AS p ON r.ipid = p.ipid')
	                                                ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
	                                                ->find();
	                                $data = array(
	                                        'product' => $product,
	                                        'userinfo' => $userinfo,
	                                        'ir_receiptnum' => $order['ir_receiptnum']
	                                    );
	                                $data    = json_encode($data);
	                                $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
	                                $result  = post_json_data($sendUrl,$data);
                            		break;
                            }
                            $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
                            if($ir_status == 2){
	                            // 发送短信提示
	                            $templateId ='178959';
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
	                            $usa = new \Common\UsaApi\Usa;
	                            $createPayment = $usa->createPayment($userinfo['customerid'],$receipt['ir_receiptnum'],date('Y-m-d H:i',time()));
	                            $log = addUsaLog($createPayment['result']);
	                            $jsonStr = json_decode($createPayment['result'],true);
	                            // p($jsonStr);die;
	                            if($jsonStr['paymentId']){
	                            	// 检测所有月费单是否存在未支付
                                    $allIrstatus = M('Receipt')->where(array('ir_ordertype'=>3,'rCustomerID'=>$userinfo['customerid']))->getField('ir_status',true);
                                    if(!in_array(0,$allIrstatus) && !in_array(7,$allIrstatus)){
                                        $statusResult = M('User')->where(array('customerid'=>$userinfo['customerid']))->setfield('showProduct','0');
                                    }
	                            }
                            }
                        }else{
                            // 共总支付
                            $total = bcsub($order['ir_unpaid'],$sub,2);
                            // 发送短信提示
                            $templateId ='178957';
                            $params     = array($receipt['ir_receiptnum'],$receipt['ir_price'],$total,$sub);
                            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                            if($sms['errmsg'] == 'OK'){
                                $contents = array(
                                    'acnumber' => $userinfo['acnumber'],
                                    'phone' => $userinfo['phone'],
                                    'operator' => '系统',
                                    'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                    'product_name' => '',
                                    'date' => time(),
                                    'content' => '订单编号：'.$receipt['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$sub,
                                    'customerid' => $userinfo['customerid']
                                );
                                $logs = M('SmsLog')->add($contents);
                            }
                        }
                    }
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
    *畅捷返回值
    **/
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
        $usa    = new \Common\UsaApi\Usa;
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
                    switch ($order['ir_ordertype']) {
                        case '1':
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
                                    'ia_province'=>$userinfo['shopprovince'],
                                    'ia_city'    =>$userinfo['shopcity'],
                                    'ia_area'    =>$userinfo['shoparea'],
                                    'ia_address' =>$userinfo['shopaddress1'],
                                    'ir_unpaid'  =>$sub,
                                    'ir_unpoint' =>$unp,
                                    'ir_paytime' =>$ir_paytime,
                                );
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
                                $tmpeArr['password'] = $userinfo['wvpass'];
                                $status['ia_name']   = $userinfo['lastname'].$userinfo['firstname'];
                            }
                            // 获取产品名称
                            $product = D('Product')->where(array('ipid'=>$order['ipid']))->find();
                            //更新订单信息
                            $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                            if($upreceipt){ 
                                $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$order['ir_receiptnum']);
                                $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('ir_status');
                                if($ir_status == 2){
                                	// switch($order['ipid']){
                                 //        case '31':
                                 //            $products = '1';
                                 //            break;
                                 //        case '62':
                                 //            $products = '5';
                                 //            break;
                                 //        case '64':
                                 //            $products = '4';
                                 //            break;
                                 //        case '66':
                                 //            $products = '6';
                                 //            break;
                                 //        case '67':
                                 //            $products = '7';
                                 //            break;
                                 //    }
                                    $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$product['productid'],$tmpeArr['birthday']);
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
                            break;
                        case '4':
                            $userinfo= D('User')->where(array('iuid'=>$order['riuid']))->find();
                            $status  = array(
                                'ir_status'  =>$ir_status,
                                'riuid'      =>$userinfo['iuid'],
                                'ir_unpaid'  =>$sub,
                                'ir_unpoint' =>$unp,
                                'ir_paytime' =>$ir_paytime,
                            );
                            //更新订单信息
                            $updateReceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                            // 添加通用券
                            $product = M('Receipt')
                                            ->alias('r')
                                            ->join('hapylife_product AS p ON r.ipid = p.ipid')
                                            ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
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
                                $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                            }
                            break;
                        case '5':
                            $status  = array(
                                'ir_status'  =>$ir_status,
                                'ir_unpaid'  =>$sub,
                                'ir_unpoint' =>$unp,
                                'ir_paytime' =>$ir_paytime,
                            );
                            //更新订单信息
                            $updateReceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                            if($updateReceipt){
                                $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                            }
                            break;
                    }
                }else{
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp
                    );                   
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    if($upreceipt){
                        // 总共已经支付金额
                        $total = bcsub($receipt['ir_price'],$sub,2);
                        // 发送短信提示
                        $templateId ='178957';
                        $params     = array($receipt['ir_receiptnum'],$receiptson['ir_price'],$total,$sub);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                        'acnumber' => $userinfo['acnumber'],
                                        'phone' => $userinfo['phone'],
                                        'operator' => '系统',
                                        'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                        'product_name' => $receiptlist['product_name'],
                                        'date' => time(),
                                        'content' => '订单编号：'.$receipt['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$sub,
                                        'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                    }
                }
            }
        }
    }

    /**
    * 汇付天下异步回调
    **/
    public function notifyReceiver(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        // $jsonStr ='payResult=10&merchantAcctId=1021903029201&orderId=20190412155443665881&orderCurrency=CNY&dealId=2002302361&terminalId=AYGJ001&version=3.0&bankDealId=2019041215515800000000PNRI20000001680468&bankId=ccb&payType=4&orderTime=20190412155451&orderAmount=1&dealTime=20190412155157&errCode=000000&signMsg=lsuAWmSUS0QozwxEa5DW2SrXF8Ddp%252Bu9ILvAbyVnbBQswAR8BlyqgeytLvmkPvNKN%252Bm3wyM7tM8dziiq%252BxFFcpLuX9iVLWl%252B8lNpC8XRgJr3BW9xQYRUHh3aHijTCbq9QTVGeVND2Pn45JxyDKXM8etQNv4RdBNAffLV5qI2tOc%253D';
        // $log     = logTest($jsonStr);
        $data    = explode('&', $jsonStr);
        //签名数据会被转码，需解码urldecode
        foreach ($data as $key => $value) {
            $temp = explode('=', $value);
            $map[$temp[0]]=urldecode(trim($temp[1]));
        }
        // p($map);
        //验签
        $return = htxRsaVerify($map);
        if($return=="true" && $map['payResult']==10){
        	switch ($map['payType']) {
                case '1':
                    $payType = 8;
                    break;
                case '2':
                    $payType = 9;
                    break;
                case '4':
                    $payType = 10;
                    break;
            }
            //订单处理
            $save = array(
                'ir_paytype'       => $payType,
                'status'           => 2,
                'htx_trade_no'     => $map['dealId'],
                'htx_trade_status' => $map['payResult'],
            );
            //限制多次回调，$map里的参数值必须是只成功修改一次
            $result = M('Receiptson')->where(array('pay_receiptnum'=>$map['orderId']))->save($save);
            // 获取子订单信息
            $receiptson = M('Receiptson')->where(array('pay_receiptnum'=>$map['orderId']))->find();
            // 用户信息
            $userinfo = M('User')->where(array('iuid'=>$receiptson['riuid']))->find();
            // 获取产品名称
            $product_name = M('Receiptlist')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->getfield('product_name');
            // 获取父订单信息
            $order = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->find();
            if($result){
                // 修改子订单支付时间
                M('Receiptson')->where(array('pay_receiptnum'=>$map['orderId']))->setfield('paytime',time());
                $subnum= bcsub($order['ir_unpaid'],$receiptson['ir_price'],2);
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
                if($subnum==0){
                    // 更新订单信息
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp,
                        'ir_paytime' =>$ir_paytime,
                    );
                    $updateReceipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($status);
                    // 修改父订单状态
                    $ir_status = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->getfield('ir_status');
                    if($ir_status == 2){
                        // 发送短信提示
                        $templateId ='178959';
                        $params     = array($receiptson['ir_receiptnum'],$product_name);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                'acnumber' => $userinfo['acnumber'],
                                'phone' => $userinfo['phone'],
                                'operator' => '系统',
                                'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                'product_name' => $product_name,
                                'date' => time(),
                                'content' => '订单编号：'.$receiptson['ir_receiptnum'].'，产品：'.$product_name.'，支付成功。',
                                'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                        $usa = new \Common\UsaApi\Usa;
                        // $createPayment = $usa->createPayment($userinfo['customerid'],$receiptson['ir_receiptnum'],date('Y-m-d H:i',time()));
                        $log = addUsaLog($createPayment['result']);
                        $jsonStr = json_decode($createPayment['result'],true);
                        // p($jsonStr);die;
                        if($jsonStr['paymentId']){
                            // 检测所有月费单是否存在未支付
                            $allIrstatus = M('Receipt')->where(array('ir_ordertype'=>3,'rCustomerID'=>$userinfo['customerid']))->getField('ir_status',true);
                            if(!in_array(0,$allIrstatus) && !in_array(7,$allIrstatus)){
                                $statusResult = M('User')->where(array('customerid'=>$userinfo['customerid']))->setfield('showProduct','0');
                            }
                        }
                    }
                }else{
                    // 未全额支付
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp,
                        'ir_paytime' =>$ir_paytime,
                    );
                    //更新订单信息
                    $updateReceipt = M('Receipt')->where(array('ir_receiptnum'=>$receiptson['ir_receiptnum']))->save($status);
                    if($updateReceipt){
                        // 总共已经支付金额
                        $total = bcsub($receiptson['ir_price'],$sub,2);
                        // 发送短信提示
                        $templateId ='178957';
                        $params     = array($receiptson['ir_receiptnum'],$receiptson['ir_price'],$total,$sub);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                'acnumber' => $userinfo['acnumber'],
                                'phone' => $userinfo['phone'],
                                'operator' => '系统',
                                'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                'product_name' => $receiptlist['product_name'],
                                'date' => time(),
                                'content' => '订单编号：'.$receiptson['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$sub,
                                'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                    }
                }
            }else{
                $data['message']='订单状态修改失败';
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 订单状态查询
    * @param 子订单pay_receiptnum 订单编号
    **/
    public function checkreceipt(){
        $pay_receiptnum = I('post.pay_receiptnum');
        //订单状态查询
        $receipt_status = M('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->find();
        if($receipt_status['status'] == 2){
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
    *支付成功后获取父订单信息
    **/
    public function receiptInfo(){
        $pay_receiptnum = I('post.pay_receiptnum');
        $isSon          = I('post.isSon');//是否子订单
        if($isSon){
	        $ir_receiptnum  = M('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->getfield('ir_receiptnum');
	        $data = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        }else{
        	$data = M('Receipt')->where(array('ir_receiptnum'=>$pay_receiptnum))->find();
        }
        $data['ispullamount'] = D('Product')->where(array('ipid'=>$data['ipid']))->getfield('ispullamount');
        switch ($data['ir_status']){
        	case '2':
        		$data['status'] = 1;
        		break;
        	case '202':
        		$data['status'] = 3;
        		break;
        	default:
        		$data['status'] = 2;
        		break;
        }
        $this->ajaxreturn($data);
    }
    /**
    * 新代理注册成功显示页面
    **/ 
    public function registerSuccess(){
        $pay_receiptnum = I('post.pay_receiptnum');
        $ir_receiptnum  = M('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->getfield('ir_receiptnum');
        $receipt        = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
        	$data['status']        = 1;
            $data                  = M('User')->where(array('CustomerID'=>$receipt['rcustomerid']))->find();
            $data['ir_receiptnum'] = $ir_receiptnum; 
            $this->ajaxreturn($data);
        }else{
            $data['status']        = 0;
        	$this->ajaxreturn($data);
        }
    }

    /**
    * 购买成功显示页面
    **/ 
    public function paySuccess(){
        $pay_receiptnum = I('post.pay_receiptnum');
        $receiptlist  = M('Receiptson')
        						->alias('rs')
        						->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
        						->join('hapylife_receiptlist AS rl ON rs.ir_receiptnum = rl.ir_receiptnum')
        						->where(array('pay_receiptnum'=>$pay_receiptnum,'r.ir_status'=>2))
        						->find();
        if($receiptlist){
        	$data['status']        = 1;
            $this->ajaxreturn($receiptlist);
        }else{
            $data['status']        = 0;
        	$this->ajaxreturn($data);
        }
    }

    /**
     * 获取银行代码
     */
    public function getBankId(){
        $Bankid = array(
            array('name' => '招商银行','id' => 'CMB'),
            array('name' => '中国工商银行','id' => 'ICBC'),
            array('name' => '中国农业银行','id' => 'ABC'),
            array('name' => '中国建设银行','id' => 'CCB'),
            array('name' => '中国银行','id' => 'BOC'),
            array('name' => '浦发银行','id' => 'SPDB'),
            array('name' => '中国交通银行','id' => 'BCOM'),
            array('name' => '中国民生银行','id' => 'CMBC'),
            array('name' => '广东发展银行','id' => 'GDB'),
            array('name' => '中信银行','id' => 'CITIC'),
            array('name' => '华夏银行','id' => 'HXB'),
            array('name' => '上海农村商业银行','id' => 'SRCB'),
            array('name' => '中国邮政储蓄银行','id' => 'PSBC'),
            array('name' => '北京银行','id' => 'BOB'),
            array('name' => '渤海银行','id' => 'CBHB'),
            array('name' => '北京农商银行','id' => 'BJRCB'),
            array('name' => '南京银行','id' => 'NJCB'),
            array('name' => '中国光大银行','id' => 'CEB'),
            array('name' => '浙商银行','id' => 'CZB'),
            array('name' => '兴业银行','id' => 'CIB'),
            array('name' => '杭州银行','id' => 'HZB'),
            array('name' => '平安银行','id' => 'PAB'),
            array('name' => '上海银行','id' => 'SHB'),
        );
        $this->ajaxReturn($Bankid);
    }









































}