<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * PayController
 */
class PayController extends HomeBaseController{
	// 积分支付信息页面
	public function payPoint(){
		$iuid = $_SESSION['user']['id'];
		$ir_receiptnum = I('get.ir_receiptnum');
		// 查询订单信息
		$data = M('IbosReceipt')
						->alias('r')
						->join('nulife_ibos_users AS u ON r.iuid = u.iuid')
						->where(array('ir_receiptnum'=>$ir_receiptnum,'r.iuid'=>$iuid))
						->find();
		$data['re_ep'] = $data['iu_point']-$data['ir_point'];
		$assign = array(
					'data' => $data,
					'ir_receiptnum' => $ir_receiptnum
					);
		$this->assign($assign);
		$this->display();
	}
    // 积分支付信息页面
    public function newpayPoint(){
        $iuid = $_SESSION['user']['id'];
        $ir_receiptnum = I('get.ir_receiptnum');
        // 查询订单信息
        $data = M('IbosReceipt')
                        ->alias('r')
                        ->join('nulife_ibos_users AS u ON r.iuid = u.iuid')
                        ->where(array('ir_receiptnum'=>$ir_receiptnum,'r.iuid'=>$iuid))
                        ->find();
        $data['re_ep'] = $data['iu_point']-$data['ir_point'];
        $assign = array(
                    'data' => $data,
                    'ir_receiptnum' => $ir_receiptnum
                    );
        $this->assign($assign);
        $this->display();
    }

	// 确认支付密码页面
	public function toCheckPoint(){
		$iuid = $_SESSION['user']['id'];
		$ir_receiptnum = I('get.ir_receiptnum');
		$assign = array(
					'ir_receiptnum' => $ir_receiptnum
					);
		$this->assign($assign);
		$this->display();
	}
	// 确认支付密码页面
	public function newCheckPoint(){
		$iuid = $_SESSION['user']['id'];
		$ir_receiptnum = I('get.ir_receiptnum');
		$assign = array(
					'ir_receiptnum' => $ir_receiptnum
					);
		$this->assign($assign);
		$this->display();
	}

	//判断密码是否正确
	public function judgePSD(){
		$iuid = $_SESSION['user']['id'];
		$userinfo = M('IbosUsers')->where(array('iuid'=>$iuid))->find();
		$hu_nickname = strtoupper($userinfo['hu_nickname']);
        $iu_password = trim(I('post.password'));
        //验证ibos360是否有该账号
		$ibos360_result = ibos360_userlogin($hu_nickname,$iu_password);
		$is360User      = $ibos360_result['success']?1:0;
        // p($ibos360_result);die;
		if(!empty($is360User)){
			$ir_receiptnum = I('post.ir_receiptnum');
			//订单信息查询
			$order = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
	        
	        switch ($order['ir_paytype']){
                //微信支付
                case 1:
                    $client        = new \SoapClient("https://pay.hkipsec.com/webservice/GetQRCodeWebService.asmx?wsdl");

                    try{
                        $merAccNo       = "T0000603";
                        $orderId        = $ir_receiptnum;
                        $fee_type       = "CNY";
                        $amount         = "0.02";
                        $goodsInfo      = "Nulife Product";
                        $strMerchantUrl = "http://apps.nulifeshop.com/nulifeshop/index.php/Home/api/getResponse";
                        $cert           = "Yv1IqGRl5rfLOmUILIjdjrh1BlYjpXrFxo1So0BvcQXy5zB1aCiRDndGnZhfHHW7azyALb4ugNastIWlA6iCO0bkkLEos6DuU0yfbaGYiOKQmKw5dW5l9tIAxozZxpb9";
                        $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
                        $signMD5_lower  = strtolower(md5($signMD5));

                        $para = array(
                            'merAccNo'      => $merAccNo,
                            'orderId'       => $orderId,
                            'fee_type'      => $fee_type,
                            'amount'        => $amount,
                            'goodsInfo'     => $goodsInfo,
                            'strMerchantUrl'=> $strMerchantUrl,
                            'signMD5'       => $signMD5_lower
                        );

                        $result           = $client->GetQRCodeXml($para);
                        //对象操作
                        $xmlstr           = $result->GetQRCodeXmlResult;
                        //构造SimpleXMLEliement对象
                        $xml              = new \SimpleXMLElement($xmlstr);
                        //微信支付链接
                        $code_url         = (string)$xml->code_url;
                        //返回数据
                        $para['code_url'] = $code_url;
                        $this->ajaxreturn($para);
                        
                    }catch(SoapFault $f){
                        echo "Error Message:{$f->getMessage()}";
                    }
                    break;
                //积分购买
                case 2:
                    //获取用户订单信息
                    $order      = M('IbosReceipt')
                                    ->where(array('ir_receiptnum'=>$ir_receiptnum))
                                    ->find();
                    $orderdetail= M('IbosReceiptlist')
                    				->alias('l')
                                    ->join('nulife_ibos_product AS p on l.ipid = p.ipid')
                                    ->where(array('ir_receiptnum'=>$ir_receiptnum))
                                    ->find();
                    //获取用户积分
                    $user_point = M('IbosUsers')->where(array('iuid'=>$iuid))->getfield('iu_point');
                    //获取订单积分
                    $ir_point   = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_point');
                    //扣除所需购买积分
                    $last_point = bcsub($user_point,$ir_point,4);
                    // p($last_point);die;
                    if($last_point<0){
                        //积分不足，请充值
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }else{
                        //修改用户积分
                        $data = array(
                            'iuid'    =>$iuid,
                            'iu_point'=>$last_point
                        );
                        $insertpoint = M('IbosUsers')->save($data);
                        // p($userinfo);
                        if($insertpoint){
                            //修改订单状态
                            $map = array(
                                'ir_paytype'=>2,
                                'ir_status' =>2,
                                'update_time'=>time(),
                            );
                            $change_orderstatus = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                            
                            //日志记录
                            $content = '订单:'.$ir_receiptnum.'支付成功,扣除EP:'.$ir_point.',剩余EP:'.$last_point;
                            $addlog  = addLog($iuid,$content,$action=3,$type=1);
                            // p($userinfo);
                            if($change_orderstatus){
                                //如果是支付升级单,则升级 PC首购发单默认先报单升级
                                if($order['ir_ordertype'] == 20 || $order['ir_ordertype'] == 10){
                                    //升级商品对应可以升的等级
                                    $ip_upgrade = M('IbosReceiptlist')
                                    			->alias('l')
                                                ->join('nulife_ibos_product AS p on l.ipid = p.ipid')
                                                ->where(array('ir_receiptnum'=>$ir_receiptnum))
                                                ->getfield('ip_upgrade');
                                    //会员升级
                                    $upGrade = M('IbosUsers')->where(array('iuid'=>$iuid))->setField('iu_grade',$ip_upgrade);
                                    //日志记录
                                    $content = '成功购买套装,会员等级成为'.$ip_upgrade;
                                    $addlog  = addLog($iuid,$content,$action=4,$type=4);
                                }
                                // p($userinfo);
                                //ibos360入单
                                $memberId       = $userinfo['iu_memberid']; 
                                $memberNo       = $userinfo['hu_nickname'];
                                $orderStatus    = 20;
                                $orderType      = $order['ir_ordertype'];
                                $sessionId      = $userinfo['iu_sessionid'];
                                $address        = $order['ia_address'];
                                $productNumber  = $order['ir_productnum'];
                                $productId      = $orderdetail['productid'];
                                $bonusDate      = date('Y-m-d H:i:s');
                                $hu_username    = $userinfo['hu_username'];
                                $hu_phone       = $userinfo['hu_phone'];

                                $sendOrder = sendOrder($memberId,$memberNo,$orderStatus,$orderType,$sessionId,$address,$productNumber,$productId,$bonusDate,$hu_username,$hu_phone);
                                // p($memberId);
                                // p($sendOrder);die;
                                if($sendOrder['orderNo'] !== ""){
                                    //将ibos360订单号,订单id入库,更改ir_change的值为1,使该订单无法再报单
                                    $temp = array(
                                        'ir_360_orderId'=> $sendOrder['orderId'],
                                        'ir_360_orderNo'=> $sendOrder['orderNo'],
                                        'to360_time'    =>time(),
                                        'ir_change'     => 1
                                    );
                                    $saveOrder = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($temp); 
                                }
                                //积分消费记录
                                $tmp     = array(
                                    'iuid'           =>$userinfo['iuid'],
                                    'pointNo'        =>$ir_receiptnum,
                                    'hu_username'    =>$userinfo['hu_username'],
                                    'hu_nickname'    =>$userinfo['hu_nickname'],
                                    'getpoint'       =>$ir_point,
                                    'feepoint'       =>0,
                                    'realpoint'      =>$ir_point,
                                    'unpoint'        =>$userinfo['iu_unpoint'],
                                    'leftpoint'      =>$last_point,
                                    'date'           =>date('Y-m-d H:i:s'),
                                    'handletime'     =>date('Y-m-d H:i:s'),
                                    'status'         =>2,
                                    'pointtype'      =>7,
                                    'whichApp'       =>2,
                                    'content'        =>$userinfo['hu_nickname'].'在'.date('Y-m-d H:i:s').'时,消费出'.$ir_point.'EP到'.'系统'.',剩EP余额'.$last_point,
                                    'send'           =>$userinfo['hu_nickname'],
                                    'received'       =>'系统'
                                );
                                $addtmp = M('IbosGetpoint')->add($tmp);


                                if($sendOrder['orderNo'] !== ""){
                                    $data['status'] = 1;
                                    $data['msg']    = "积分支付成功,360入单成功";
                                    $data['success']  = $sendOrder;
                                    $this->ajaxreturn($data);
                                }else{
                                    $data['status'] = 1;
                                    $data['msg']    = "积分支付成功，360入单失败";
                                    $data['error']  = $sendOrder;
                                    $this->ajaxreturn($data);
                                }
                            }else{
                                $data['status'] = 0;
                                $data['msg']    = "积分支付失败";
                                $this->ajaxreturn($data);
                            }
                        }
                    break; 
                }
            }
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	
	}

	/**
    * 快钱支付
    **/
    public function kqPayment(){
        // $order_num = trim(I('post.ir_receiptnum'))?trim(I('post.ir_receiptnum')):date(YmdHis);
        $order_num = trim(I('post.ir_receiptnum'));
        //订单信息
        $order     = M('IbosReceipt')->where(array('ir_receiptnum'=>$order_num))->find();
        $kq_target          = "https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
        // $kq_target          = "https://sandbox.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
        $kq_merchantAcctId  = "1020997278101";      //*  商家用户编号     (30)
        // $kq_merchantAcctId  = "1001213884201";
        $kq_inputCharset    = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
        $kq_pageUrl         = ""; //   直接跳转页面 (256)
        $kq_bgUrl           = "http://apps.nulifeshop.com/nulife/index.php/Home/Pay/getKqReturn"; //   后台通知页面 (256)
        $kq_version         = "mobile1.0";  //*  版本  固定值 v2.0   (10)
        $kq_language        = "1";  //*  默认 1 ， 显示 汉语   (2)
        $kq_signType        = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
        $kq_payerName       = $order['hu_nickname']; //   英文或者中文字符   (32)
        $kq_payerContactType= "1";    //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
        $kq_payerContact    = "";     //   支付人联系方式    (50)
        $kq_orderId         = $order_num; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
        $kq_orderAmount     = $order['ir_price']*100; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
        // $kq_orderAmount     = 10;
        $kq_orderTime       = date(YmdHis);  //*  交易时间  格式: 20110805110533
        $kq_productName     = "Nulife";//    商品名称英文或者中文字符串(256)
        $kq_productNum      = $order['ir_productnum'];   //    商品数量  (8)
        $kq_productId       = "";   //    商品代码，可以是 字母,数字,-,_   (20) 
        $kq_productDesc     = ""; //    商品描述， 英文或者中文字符串  (400)
        $kq_ext1            = "";   //    扩展字段， 英文或者中文字符串，支付完成后，按照原样返回给商户。 (128)
        $kq_ext2            = "";
        $kq_payType         = "21"; //*  固定选择值：00、15、21、21-1、21-2
        //00代表显示快钱各支付方式列表；
        //15信用卡无卡支付
        //21 快捷支付
        //21-1 代表储蓄卡快捷；21-2 代表信用卡快捷
        //*其中”-”只允许在半角状态下输入。
        $kq_bankId          = "";   //银行代码 银行代码 要在开通银行时 使用， 默认不开通 (8)
        $kq_redoFlag        = "0";  //同一订单禁止重复提交标志  固定值 1 、 0      
                                    //1 表示同一订单只允许提交一次 ； 0 表示在订单没有支付成功状态下 可以重复提交； 默认 0 
        $kq_pid             = "";       //合作伙伴在快钱的用户编号 (30)
        $kq_payerIdType     ="3";        //指定付款人
        $kq_payerId         =date('YmdHis').rand(10000, 99999);       //付款人标识

        $map = array(
                'inputCharset'      =>$kq_inputCharset,
                'pageUrl'           =>$kq_pageUrl,
                'bgUrl'             =>$kq_bgUrl,
                'version'           =>$kq_version,
                'language'          =>$kq_language,
                'signType'          =>$kq_signType,
                'merchantAcctId'    =>$kq_merchantAcctId,
                'payerName'         =>$kq_payerName,
                'payerContactType'  =>$kq_payerContactType,
                'payerContact'      =>$kq_payerContact,
                'payerIdType'       =>$kq_payerIdType,
                'payerId'           =>$kq_payerId,
                'orderId'           =>$kq_orderId,
                'orderAmount'       =>$kq_orderAmount,
                'orderTime'         =>$kq_orderTime,
                'productName'       =>$kq_productName,
                'productNum'        =>$kq_productNum,
                'productId'         =>$kq_productId,
                'productDesc'       =>$kq_productDesc,
                'ext1'              =>$kq_ext1,
                'ext2'              =>$kq_ext2,
                'payType'           =>$kq_payType,
                'bankId'            =>$kq_bankId,
                'redoFlag'          =>$kq_redoFlag,
                'pid'               =>$kq_pid
            );

        foreach ($map as $k => $v) {
            if(!empty($v)){
                $k.='='.$v.'&';
                $kq_all_para .= $k;
            }
        }
        $kq_all_para = rtrim($kq_all_para,'&');
        //生成证书
        $priv_key = file_get_contents("./99bill-rsa.pem");
        $pkeyid   = openssl_get_privatekey($priv_key);
        // compute signature
        openssl_sign($kq_all_para, $signMsg, $pkeyid);
        // free the key from memory
        openssl_free_key($pkeyid);
        $kq_sign_msg = urlencode(base64_encode($signMsg));
        $url = $kq_target.'?'.$kq_all_para.'&signMsg='.$kq_sign_msg;
        // $data= header("Location:".$url);

        if($url){
            $data = array(
                'ir_receiptnum'=>$order_num,
                'url'          =>$url,
                'status'       =>1,
                'msg'          =>'跳转至该url，使用快钱支付'
            );
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'ir_receiptnum'=>$order_num,
                'url'          =>'请求失败',
                'status'       =>0,
                'msg'          =>'支付请求失败'
            );
            $this->ajaxreturn($data);
        }
    }

    //快钱返回结果
    public function getKqReturn(){
        $kq_check_all_para=kq_ck_null($_GET['merchantAcctId'],'merchantAcctId').kq_ck_null($_GET['version'],'version').kq_ck_null($_GET['language'],'language').kq_ck_null($_GET['signType'],'signType').kq_ck_null($_GET['payType'],'payType').kq_ck_null($_GET['bankId'],'bankId').kq_ck_null($_GET['orderId'],'orderId').kq_ck_null($_GET['orderTime'],'orderTime').kq_ck_null($_GET['orderAmount'],'orderAmount').kq_ck_null($_GET['bindCard'],'bindCard').kq_ck_null($_GET['bindMobile'],'bindMobile').kq_ck_null($_GET['dealId'],'dealId').kq_ck_null($_GET['bankDealId'],'bankDealId').kq_ck_null($_GET['dealTime'],'dealTime').kq_ck_null($_GET['payAmount'],'payAmount').kq_ck_null($_GET['fee'],'fee').kq_ck_null($_GET['ext1'],'ext1').kq_ck_null($_GET['ext2'],'ext2').kq_ck_null($_GET['payResult'],'payResult').kq_ck_null($_GET['errCode'],'errCode');

        $trans_body= substr($kq_check_all_para,0,strlen($kq_check_all_para)-1);
        $MAC       = base64_decode($_GET['signMsg']);
        $cert      = file_get_contents("./99bill.cert.rsa.20340630.cer");
        $pubkeyid  = openssl_get_publickey($cert); 
        $ok        = openssl_verify($trans_body, $MAC, $pubkeyid); 
        if ($ok == 1) {
            //订单处理
            $map = array(
                    'ir_paytype'=>4,
                    'ir_status'=>2,
                    'update_time'=>time()
                );
            $receipt = M('IbosReceipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->save($map);
            //订单信息
            $order = M('IbosReceipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->find();
            //写入日志记录
            $content = "订单号：".$_GET['orderId']."支付成功,金额:".$_GET['orderAmount'];
            $add     = addLog($order['iuid'],$content,$action=2,$type=2);
            //订单详情
            $orderdetail= M('IbosReceiptlist')
                        ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
                        ->where(array('ir_receiptnum'=>$_GET['orderId']))
                        ->find();
            //用户信息
            $userinfo = M('IbosUsers')->where(array('iuid'=>$order['iuid']))->find();
            //如果是支付升级单,则升级
            if($order['ir_ordertype'] == 20){
                //升级商品对应可以升的等级
                $ip_upgrade = M('IbosReceiptlist')
                        ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
                        ->where(array('ir_receiptnum'=>$ir_receiptnum))
                        ->getfield('ip_upgrade');
                //会员升级
                $upGrade = M('IbosUsers')->where(array('iuid'=>$iuid))->setField('iu_grade',$ip_upgrade);
            }
            //ibos360入单
            //sendOrder($memberId,$memberNo,$orderStatus,$orderType,$sessionId,$address,$productNumber,$productNo)
            $memberId      = $userinfo['iu_memberid']; 
            $memberNo      = $userinfo['hu_nickname'];
            $orderStatus   = 20;
            $orderType     = $order['ir_ordertype'];
            $sessionId     = $userinfo['iu_sessionid'];
            $address       = $order['ia_address'];
            $productNumber = $order['ir_productnum'];
            $productId     = $orderdetail['productid']?$orderdetail['productid']:'PBF120';
            $bonusDate     = date('Y-m-d H:i:s');
            $hu_username   = $userinfo['hu_username'];
            $hu_phone      = $userinfo['hu_phone'];

            $sendOrder = sendOrder($memberId,$memberNo,$orderStatus,$orderType,$sessionId,$address,$productNumber,$productId,$bonusDate,$hu_username,$hu_phone);
            if($sendOrder['orderNo'] !== ""){
                //将ibos360订单号,订单id入库,标记订单已经报单
                $temp = array(
                    'ir_360_orderId'=> $sendOrder['orderId'],
                    'ir_360_orderNo'=> $sendOrder['orderNo'],
                    'to360_time'    => time(),
                    'ir_change'     => 1
                );
                $saveOrder = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($temp); 
            }else{
                $data['status'] = 0;
                $data['msg']    = $sendOrder;
                $this->ajaxreturn($data);
            }
            if($receipt){
                //通知快钱商户收到的结果
                // echo '<result>1</result><redirecturl>http://success.html</redirecturl>';
                $this->redirect('Home/Pay/paySuccess',array('ir_receiptnum'=>$ir_receiptnum));
            }
        }else{
            $map = array(
                    'content'=>'<result>1</result><redirecturl>http://false.html</redirecturl>',
                    'date'   =>date('Y-m-d H:i:s'),
                    'action' =>1,
                    'status' =>0
                ); 
            //通知快钱商户收到的结果
            // echo '<result>1</result><redirecturl>http://false.html</redirecturl>';
            // $this->ajaxreturn($map);
            $this->error('支付失败',U('Home/PersonalCenter/myOrder'));
        }
    }


	// 支付成功页面
	public function paySuccess(){
		//获取订单号
		$ir_receiptnum = I('get.ir_receiptnum');
		$assign = array(
					'ir_receiptnum' => $ir_receiptnum,
					);
		$this->assign($assign);
		$this->display();
	}

}
