<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class HapylifeApiController extends HomeBaseController{

	public function index(){
		$data = D('Users')->limit('0,400000')->getfield('lastname',true);
		$this->ajaxreturn($data);
		unset($data);
		// foreach ($data as $key => $value) {
		// // die;
		// set_time_limit(10000);
		// $data1 = D('Users')->limit('900000,1000000')->select();
		// // print_r($data);
		// foreach ($data1 as $key => $value) {
		// 	$tmpe['LastName']  = trim($value['lastname']);
		// 	$tmpe['FirstName'] = trim($value['firstname']);
		// 	$tmpe['CustomerID']= trim($value['customerid']);
		// 	$tmpe['Placement']= trim($value['placement']);
		// 	$tmpe['CustomerStatus ']= trim($value['customerstatus']);
		// 	$tmpe['CustomerType']= trim($value['customertype']);
		// 	$tmpe['EnrollerID']= trim($value['enrollerid']);
		// 	$tmpe['SponsorID']= trim($value['sponsorid']);
		// 	$tmpe['City']= trim($value['city']);
		// 	$tmpe['State']= trim($value['state']);
		// 	$tmpe['Country']= trim($value['country']);
		// 	$tmpe['JoinedOn']= trim($value['joinedon']);
		// 	$tmpe['HighestAchievedRank']= trim($value['highestachievedrank']);
		// 	$tmpe['WeeklyVolume']= trim($value['weeklyvolume']);
		// 	$tmpe['OrderDate']= trim($value['orderdate']);
		// 	$add = D('User')->add($tmpe);
		// }
		// if($add){
		// 	echo '添加完毕';
		// }
		// 	$add = D('User1')->add($tmpe);
		// }
		// if($add){
		// 	echo '80-90添加完毕';
		// }
		// unset($data1);
	}
	/**
	* 旧用户注册
	**/
	public function oldregister(){
		$data  = I('post.');
		$where= array(
			'LastName'     =>$data['LastName'],
			'FirstName'    =>$data['FirstName'],
			'CustomerID'   =>$data['CustomerID']
		);
		$find = D('User')->where($where)->find();
		if($find){
			if(!empty($data['JustIdcard'])){
                $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                $JustIdcard = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
            }
            if(!empty($data['BackIdcard'])){
                $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                $BackIdcard = time().'_'.mt_rand().'.jpg';
                $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
            }
			$tmpe = array(
				'Phone'     =>$data['Phone'],
				'PassWord'  =>md5($data['PassWord']),
				'JustIdcard'=>C('WEB_URL').'/Upload/file/'.$JustIdcard,
				'BackIdcard'=>C('WEB_URL').'/Upload/file/'.$BackIdcard
			);
			$data['iuid'] = $find['iuid'];
			$save = D('User')->where($where)->save($tmpe);
			if($save){
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
	}

	/**
	* 新用户注册
	**/
	public function newregister(){

	}

	/**
	* 新用户注册LastName FirstName EnrollerID Email PassWord Phone JustIdcard BackIdcard Sex
	**/
	public function newregister(){
		$data  = I('post.');
		$find  = D('User')->where(array('CustomerID'=>$data['EnrollerID']))->find();
		if(!$find){
			$tmpe['status'] = 2;
			$this->ajaxreturn($tmpe);			
		}else{
			if(!empty($data['JustIdcard'])){
                $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                $JustIdcard = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
            }
            if(!empty($data['BackIdcard'])){
                $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                $BackIdcard = time().'_'.mt_rand().'.jpg';
                $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
            }
			$custid= D('User')->order('iuid desc')->getfield('CustomerID');
			$where = array(
				'CustomerID'         => $custid+1,
				'IsNew'              => 1,
				'Placement'          => 'Right',
				'CustomerStatus'     => 'Active',
				'CustomerType'       => 'Distributor',
				'LastName'           => $data['LastName'],
				'FirstName'          => $data['FirstName'],
				'EnrollerID'         => $data['EnrollerID'],
				'SponsorID'          => $data['EnrollerID'],
				'JoinedOn'           => date("m/d/Y h:i:s A"),
				'HighestAchievedRank'=> 'Director',
				'Email'              => $data['Email'],
				'WeeklyVolume'       => 0,
				'PassWord'           => md5($data['PassWord']),
				'Phone'              => $data['Phone'],
				'JustIdcard'         => C('WEB_URL').'/Upload/file/'.$JustIdcard,
				'BackIdcard'         => C('WEB_URL').'/Upload/file/'.$BackIdcard,
				'Sex'                => $data['Sex'],
				'IsCheck'            => 0
			);
			$add   = D('User')->add($where);
			if($add){
				$tmpe  = D('User')->where(array('CustomerID'=>$custid+1))->find();
				$tmpe['CustomerID'] = $custid+1;
				$tmpe['status']     = 1;
				$this->ajaxreturn($tmpe);				
			}else{
				$tmpe['status'] = 0;
				$this->ajaxreturn($tmpe);
			}
		}
	}
	/**
	* 登录
	**/
	public function login(){
		$tmpe = I('post.');
		$where= array(
			'CustomerID'=>$tmpe['CustomerID'],
			'PassWord'  =>md5($tmpe['PassWord'])
		);
		$data = D('User')->where($where)->find();
		if($data){
			// $time=strtotime($data['orderdate']);
			// if($time>time()){
				$data['status'] = 1;
				$this->ajaxreturn($data);	
			// }else{
				// $data['status'] = 2;
				// $this->ajaxreturn($data);					
			// }
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);			
		}
	}
	/**
	* 获取用户信息
	**/
	public function userinfo(){
		$iuid = I('post.iuid');
		$data = D('User')->where(array('iuid'=>$iuid))->find();
		$right= D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Right'))->select();
		$left = D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Left'))->select();
		if($right){
			$data['right'] = count($right);
		}else{
			$data['right'] = 0;
		}
		if($left){
			$data['left'] = count($left);
		}else{
			$data['left'] = 0;
		}
		if($data){
			$data['status'] = 1;
			$this->ajaxreturn($data);	
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);			
		}
	}

	/**
	* 新闻列表
	**/	
	public function newslist(){
		$map  = array(
				'is_show' =>1
			);
		$data = M('News')
				->where($map)
				->order('addtime desc')
				->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data = array(
					'status'=>0,
					'msg'	=>'无法获取新闻列表'
				);
			$this->ajaxreturn($data);
		}
	}

	/**
	* 新闻详情
	**/
	public function newscontent(){
		$nid  = I('post.nid');
        $data = M('News')->where(array('nid'=>$nid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
					'status'=>0,
					'msg'	=>'获取新闻详情失败'
				);
            $this->ajaxreturn($data);
        }
	}

	/**
	* 商品列表
	**/
	public function productlist(){
		$map = array(
            'ip_type'=>1
        );
        $data= M('Product')->where($map)->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
					'status'=>0,
					'msg'	=>'获取商品列表失败'
				);
            $this->ajaxreturn($data);
        }
	}

	/**
	* 商品详情
	**/
	public function product(){
			$ipid = I('post.ipid');
            $data = M('Product')
            			->where(array('ipid'=>$ipid))
            			->find();
            if($data){
           		$this->ajaxreturn($data);
        	}else{
	            $data = array(
					'status'=>0,
					'msg'	=>'获取商品详情失败'
				);
	            $this->ajaxreturn($data);
        }
	}
	/**
	* 订单
	**/
	public function order(){
		$iuid = trim(I('post.iuid'));
        $ipid = trim(I('post.ipid'));
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        $order = array(
                //订单编号
                'ir_receiptnum' =>$order_num,
                //订单创建日期
                'ir_date'=>time(),
                //订单的状态(0待生成订单，1待支付订单，2已付款订单)
                'ir_status'=>0,
                //下单用户id
                'iuid'=>$iuid,
                //下单用户
                'CustomerID'=>$userinfo['customerid'],
                //收货人
                'ia_name'=>$userinfo['firstname'],
                //收货人电话
                'ia_phone'=>$userinfo['phone'],
                //收货地址
                'ia_address'=>$userinfo['city'],
                //订单总商品数量
                'ir_productnum'=>1,
                //订单总金额
                'ir_price'=>$product['ip_price_rmb'],
                //订单总积分
                'ir_point'=>$product['ip_point'],
                //订单备注
                'ir_desc'=>'首购单',
                //订单类型（10首购 20升级 30重消 40零售）
                'ir_ordertype' => 10
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
        $content = '您的首购订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        $log = array(
                'from_iuid' =>$iuid,
                'content'   =>$content,
                'action'    =>0,
                'type'      =>2,
                'date'      =>date('Y-m-d H:i:s')          
            );
        $addlog = M('Log')->add($log);
        if($addlog){
            $order['status'] = 1;
            $order['msg']    = '订单已生成';
            $this->ajaxreturn($order);
        }else{
            $order['status'] = 0;
            $order['msg']    = '订单生成失败';
            $this->ajaxreturn($order);
        }
	}

	/**
    * 快钱支付
    **/
    public function kqPayment(){
        //$order_num = trim(I('post.ir_receiptnum'))?trim(I('post.ir_receiptnum')):date(YmdHis);
        $order_num = trim(I('post.ir_receiptnum'));
        //订单信息
        $order     = M('Receipt')->where(array('ir_receiptnum'=>$order_num))->find();
        $kq_target          = "https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
        $kq_merchantAcctId  = "1020997278101";      //*  商家用户编号     (30)
        $kq_inputCharset    = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
        $kq_pageUrl         = ""; //   直接跳转页面 (256)
        $kq_bgUrl           = "http://apps.hapy-life.com/hapylife/index.php/Api/HapylifeApi/getKqReturn"; //   后台通知页面 (256)
        $kq_version         = "mobile1.0";  //*  版本  固定值 v2.0   (10)
        $kq_language        = "1";  //*  默认 1 ， 显示 汉语   (2)
        $kq_signType        = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
        $kq_payerName       = $order['customerid']; //   英文或者中文字符   (32)
        $kq_payerContactType= "1";    //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
        $kq_payerContact    = "";     //   支付人联系方式    (50)
        $kq_orderId         = $order_num; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
        $kq_orderAmount     = $order['ir_price']*100; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
        $kq_orderTime       = date(YmdHis);  //*  交易时间  格式: 20110805110533
        $kq_productName     = "nulife";//    商品名称英文或者中文字符串(256)
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
        //header("Location:".$url);
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
            //写入日志记录
            $map = array(
                    'content'=>'<result>1</result><redirecturl>http://success.html</redirecturl>',
                    'date'   =>date('Y-m-d H:i:s'),
                    'billno' =>$_GET['orderId'],
                    'amount' =>$_GET['orderAmount'],
                    'action' =>1,
                    'status' =>1
                ); 
            $add = M('Log')->add($map);
            //修改用户最近订单日期
            $tmpe['OrderDate']= date("m/d/Y h:i:s A");
            $tmpe['iuid']     =D('Receipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->getfield('iuid');
            $update           =D('User')->save($tmpe);
            //做订单的处理
            $receipt = M('Receipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->setField('ir_status',2);
            if($receipt){
                //通知快钱商户收到的结果
                echo '<result>1</result><redirecturl>http://success.html</redirecturl>';
            }
        }else{
            $map = array(
                    'content'=>'<result>1</result><redirecturl>http://false.html</redirecturl>',
                    'date'   =>date('Y-m-d H:i:s'),
                    'action' =>1,
                    'status' =>0
                ); 
            //通知快钱商户收到的结果
            echo '<result>1</result><redirecturl>http://false.html</redirecturl>';
            $this->ajaxreturn($map);
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
        $receipt       = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
            //支付成功
            $data['ir_price'] = $receipt['ir_price'];
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
	* 旅游列表
	**/	
	public function travellist(){
		$map  = array(
				'is_show' =>1
			);
		$data = M('Travel')
				->where($map)
				->order('addtime desc')
				->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data = array(
				'status'=>0,
				'msg'	=>'无法获取旅游列表'
			);
			$this->ajaxreturn($data);
		}
	}

	/**
	* 旅游详情
	**/
	public function travelcontent(){
		$tid  = I('post.tid');
        $data = M('Travel')->where(array('tid'=>$tid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
				'status'=>0,
				'msg'	=>'获取旅游详情失败'
			);
            $this->ajaxreturn($data);
        }
	}


}