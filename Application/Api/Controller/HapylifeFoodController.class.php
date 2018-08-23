<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife food控制器
**/
class HapylifeFoodController extends HomeBaseController{
	/**
	* index操作
	**/
	public function index(){

	}

	/**
	* 餐厅列表
	**/
	public function shopList(){
		$map  = array(
				'isShow'=>1
			);
		$data = M('food')
				->where($map)
				->select();
		//计算每个餐厅平均星数
		foreach ($data as $key => $value) {
			$temp[$key] = $value;

			$temp[$key]['shopstars'] = M('shopcomment')
								->where(array('fid'=>$value['fid']))
								->field('ceil(avg(stars)) as avgStars,count(*) as reviewer')
								->find();
			$temp[$key]['avgStars'] = $temp[$key]['shopstars']['avgstars'];
			$temp[$key]['reviewer'] = $temp[$key]['shopstars']['reviewer'];
		}
		
		if($temp){
			$this->ajaxreturn($temp);
		}else{
			$data['status']     = 0;
			$data['message']	= '获取列表失败';
			$this->ajaxreturn($data);
		}
	}
	/**
	* 餐厅详情
	**/
	public function shopDetail(){
		$fid      = trim(I('post.fid'));
		$data     = M('food')->where(array('fid'=>$fid))->find();
		//餐厅平均星数
		$data['shopstars'] = M('shopcomment')
							->where(array('fid'=>$fid))
							->field('ceil(avg(stars)) as avgStars,count(*) as reviewer')
							->find();
		$data['avgStars'] = $data['shopstars']['avgstars'];
		$data['reviewer'] = $data['shopstars']['reviewer'];
		//餐厅营业时间
		$shoptime = M('shoptime')->where(array('fid'=>$fid))->field('spid,fid',true)->find();
		foreach ($shoptime as $key => $value) {
			$time[$key]['week'] = ucwords($key);
			$time[$key]['time'] = $value?$value:'Rest Time'; 
		}
		foreach ($time as $key => $value) {
			$data['shoptime'][] = $value;
		}
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']     = 0;
			$data['message']	= '获取详情失败';
			$this->ajaxreturn($data);
		}
	}

	

	/**
	* 折扣计算
	**/
	public function calculate(){
		$fid          = trim(I('post.fid'));
		$shop         = M('food')->where(array('fid'=>$fid))->find();

		$shopDiscount = (float)(($shop['shopdiscount'])/100);
		$totalMoney   = trim(I('totalMoney'));
		//支付的积分
		$lifePoint    = $totalMoney*$shopDiscount;
		//支付的金额
		$shopMoney    = $totalMoney-$lifePoint;
		
		$data = array(
				'shopMoney' => $shopMoney,
				'lifePoint' => $lifePoint
			);
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']     = 0;
			$data['message']	= '核算奖金失败';
			$this->ajaxreturn($data);
		}
	}

	/**
	* 订单生成
	**/
	public function order(){
		$iuid       = trim(I('post.iuid'));
		$fid        = trim(I('post.fid'));
		$totalMoney = trim(I('post.totalMoney'));
		//图片处理
		$image      = trim(I('post.image'));
		$img_body   = substr(strstr($image,','),1);
		if($img_body == ""){
			$data['image'] = "";
		}else{
			$imageName     = time().'_'.mt_rand().'.jpg';
			$img           = file_put_contents('./Upload/file/'.$imageName, base64_decode($img_body));
			$data['image'] = C('WEB_URL').'/Upload/file/'.$imageName;	
		}

		
		//生成唯一订单号
		$orderNum     = date('YmdHis').rand(10000, 99999);
		//餐厅信息
		$shop         = M('food')->where(array('fid'=>$fid))->find();
		//支付的积分
		$shopDiscount = (float)($shop['shopdiscount']/100);
		$lifePoint    = $totalMoney*$shopDiscount;
		//支付的金额
		$shopMoney    = $totalMoney-$lifePoint;
		//65% 30%对碰 5%代理
		$pengPoint    = $lifePoint* 0.3;
		$agentPoint   = $lifePoint*0.05;

		$map = array(
				'iuid'         =>$iuid,
				'fid'          =>$fid,
				'orderNum'     =>$orderNum,
				'totalMoney'   =>$totalMoney,
				'shopMoney'    =>$shopMoney,
				'lifePoint'    =>$lifePoint,
				'pengPoint'    =>$pengPoint,
				'agentPoint'   =>$agentPoint,
				'image'		   =>$data['image'],
				'payStatus'    =>0,
				'isDelete'     =>0,
				'create_month' =>date('Y-m',time()),
				'create_time'  =>time(),
				'update_time'  =>time(),
				'pay_time'     =>0
			);
		$add = M('foodorder')->add($map);
		//写入日志记录
        $content = "订单号：".$orderNum."生成成功,需支付商店金额:".$shopMoney."支付积分：".$lifePoint;
        $add     = addLog($iuid,$content,$action=1,$type=2);
		if($add){
			$map['shopinfo'] = $shop;
			$this->ajaxreturn($map);
		}else{
			$data['status']     = 0;
			$data['message']	= '生成订单失败';
			$this->ajaxreturn($data);
		}
	}

	/**
    * 购买产品快钱支付
    **/
    public function kqPayment(){
        //订单信息
		$orderNum            = trim(I('post.orderNum'));
		$order               = M('foodorder')
								->alias('fo')
								->join('hapylife_user u on u.iuid = fo.iuid')
							    ->where(array('orderNum'=>$orderNum))
							    ->find();
		$kq_target           = "https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
		$kq_merchantAcctId   = "1020997278101";      //*  商家用户编号     (30)
		$kq_inputCharset     = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
		$kq_pageUrl          = ""; //   直接跳转页面 (256)
		$kq_bgUrl            = "http://apps.hapy-life.com/hapylife/index.php/Api/HapylifeApi/getKqReturn"; //   后台通知页面 (256)
		$kq_version          = "mobile1.0";  //*  版本  固定值 v2.0   (10)
		$kq_language         = "1";  //*  默认 1 ， 显示 汉语   (2)
		$kq_signType         = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
		$kq_payerName        = $order['firstname']; //   英文或者中文字符   (32)
		$kq_payerContactType = "1";    //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
		$kq_payerContact     = "";     //   支付人联系方式    (50)
		$kq_orderId          = $orderNum; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
		$kq_orderAmount      = $order['shopmoney']*100; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
		$kq_orderTime        = date(YmdHis);  //*  交易时间  格式: 20110805110533
		$kq_productName      = "nulife";//    商品名称英文或者中文字符串(256)
		$kq_productNum       = "1";   //    商品数量  (8)
		$kq_productId        = "";   //    商品代码，可以是 字母,数字,-,_   (20) 
		$kq_productDesc      = ""; //    商品描述， 英文或者中文字符串  (400)
		$kq_ext1             = "";   //    扩展字段， 英文或者中文字符串，支付完成后，按照原样返回给商户。 (128)
		$kq_ext2             = "";
		$kq_payType          = "21"; //*  固定选择值：00、15、21、21-1、21-2
        //00代表显示快钱各支付方式列表；
        //15信用卡无卡支付
        //21 快捷支付
        //21-1 代表储蓄卡快捷；21-2 代表信用卡快捷
        //*其中”-”只允许在半角状态下输入。
        $kq_bankId          = "";   //银行代码 银行代码 要在开通银行时 使用， 默认不开通 (8)
        $kq_redoFlag        = "0";  //同一订单禁止重复提交标志  固定值 1 、 0      
                                    //1 表示同一订单只允许提交一次 ； 0 表示在订单没有支付成功状态下 可以重复提交； 默认 0 
        $kq_pid             = "";       //合作伙伴在快钱的用户编号 (30)
        $kq_payerIdType     = "3";        //指定付款人
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
                'orderNum'     =>$orderNum,
                'url'          =>$url,
                'status'       =>1,
                'message'      =>'跳转至该url，使用快钱支付'
            );
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'orderNum'	   =>$orderNum,
                'url'          =>'请求失败',
                'status'       =>0,
                'message'      =>'支付请求失败'
            );
            $this->ajaxreturn($data);
        }
    }

    //购买产品快钱返回结果
    public function getKqReturn(){
        $kq_check_all_para=kq_ck_null($_GET['merchantAcctId'],'merchantAcctId').kq_ck_null($_GET['version'],'version').kq_ck_null($_GET['language'],'language').kq_ck_null($_GET['signType'],'signType').kq_ck_null($_GET['payType'],'payType').kq_ck_null($_GET['bankId'],'bankId').kq_ck_null($_GET['orderId'],'orderId').kq_ck_null($_GET['orderTime'],'orderTime').kq_ck_null($_GET['orderAmount'],'orderAmount').kq_ck_null($_GET['bindCard'],'bindCard').kq_ck_null($_GET['bindMobile'],'bindMobile').kq_ck_null($_GET['dealId'],'dealId').kq_ck_null($_GET['bankDealId'],'bankDealId').kq_ck_null($_GET['dealTime'],'dealTime').kq_ck_null($_GET['payAmount'],'payAmount').kq_ck_null($_GET['fee'],'fee').kq_ck_null($_GET['ext1'],'ext1').kq_ck_null($_GET['ext2'],'ext2').kq_ck_null($_GET['payResult'],'payResult').kq_ck_null($_GET['errCode'],'errCode');

        $trans_body= substr($kq_check_all_para,0,strlen($kq_check_all_para)-1);
        $MAC       = base64_decode($_GET['signMsg']);
        $cert      = file_get_contents("./99bill.cert.rsa.20340630.cer");
        $pubkeyid  = openssl_get_publickey($cert); 
        $ok        = openssl_verify($trans_body, $MAC, $pubkeyid); 
        if ($ok == 1) {
            //写入日志记录
            $content = "订单号：".$_GET['orderId']."支付成功,金额:".$_GET['orderAmount'];
            $add     = addLog($order['iuid'],$content,$action=2,$type=2);
            //订单查询
            $order = M('foodorder')->where(array('orderNum'=>$_GET['orderId']))->find();
            //用户信息
            $user  = M('user')->where(array('iuid'=>$order['iuid']))->find();
            //做订单的处理
     		$temp    = array(
						'payStatus'   =>2,
						'update_time' =>time()
     				);
            $updateOrder = M('foodorder')->where(array('orderNum'=>$_GET['orderId']))->save($temp);
            if($updateOrder){
            	//积分支付
            	$lastpoint = $user['point']-$order['lifepoint'];
            	$updatePoint = M('user')->where(array('iuid'=>$order['iuid']))->setField('point',$lastpoint);
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
    * 购买产品订单状态查询
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param orderNum 订单编号
    **/
    public function checkOrder(){
		$orderNum = I('post.orderNum');
		//订单状态查询
		$order    = M('foodorder')->where(array('orderNum'=>$orderNum))->find();
        if($order['paystatus'] == 2){
            //支付成功
			$data['shopmoney'] = $order['shopmoney'];
			$data['status']    = 1;
			$data['message']   = '支付成功，请跳转...';
			$this->ajaxreturn($data);
		}else{
			$data['status']    = 0;
			$data['message']   = '正在支付，请等待...';
            $this->ajaxreturn($data);
        }
    }

    /***********************************************************评论类********************************************************
    /**
	* 五星好评
	**/
	public function starComment(){
		//最多三张图	
		$image= array(
					'image1' =>trim(I('post.image1')),
					'image2' =>trim(I('post.image2')),
					'image3' =>trim(I('post.image3'))
				);
		$data = array(
					'iuid'         => trim(I('post.iuid')),
					'fid'          => trim(I('post.fid')),
					'content'      => trim(I('post.content')),
					'stars'        => trim(I('post.stars')),
					'create_month' => date('Y-m',time()),
					'create_time'  => time()
				);
		for ($i=0; $i<count($image); $i++) { 
			$img_body                 = substr(strstr($image['image'.($i+1)],','),1);
			if($img_body == ""){
				$data['image'.($i+1)] = "无图片";
			}else{
				$imageName            = time().'_'.mt_rand().'.jpg';
				$img                  = file_put_contents('./Upload/file/'.$imageName, base64_decode($img_body));
				$data['image'.($i+1)] = C('WEB_URL').'/Upload/file/'.$imageName;	
			}
		}
		$addComment = M('shopcomment')->add($data);

		if($addComment){
			$this->ajaxreturn($data);
		}else{
			$data['status']     = 0;
			$data['message']	= '添加评论失败';
			$this->ajaxreturn($data);
		}
	}


	/**
	* 五星好评的列表
	**/
	public function commentList(){
		$readMore = trim(I('post.readMore'));
		$fid      = trim(I('post.fid'));

		if(empty($readMore)){
			$data = M('shopcomment')
					->alias('sc')
					->join('hapylife_user u on sc.iuid = u.iuid')
					->where(array('fid'=>$fid))
					->field('scid,content,customerid,firstname,create_time,stars,photo')
					->limit(0,2)
					->select();
		}else{
			$data = M('shopcomment')
					->alias('sc')
					->join('hapylife_user u on sc.iuid = u.iuid')
					->where(array('fid'=>$fid))
					->field('scid,content,customerid,firstname,create_time,stars,photo')
					->select();
		}
		
		foreach ($data as $key => $value) {
			$data[$key]                = $value;
			$data[$key]['create_time'] = formattime(intval($value['create_time']));
		}
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}


	/**
	* 评论详情
	**/
	public function commentContent(){
		$scid  = trim(I('post.scid'));
		$data  = M('shopcomment')
					->alias('sc')
					->join('hapylife_user u on sc.iuid = u.iuid')
					->where(array('scid'=>$scid))
					->field('scid,content,customerid,firstname,create_time,stars,photo')
					->limit(2)
					->find();
		$data['create_time'] = formattime(intval($data['create_time']));
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}




    /************************************************************工具类********************************************************
    /**
	* 用户反馈
	* 1ibos 2nlc  3hrac 4elpa 5hapylife
	**/
	public function feedback(){
		//最多三张图	
		$image= array(
					'image1' =>trim(I('post.image1')),
					'image2' =>trim(I('post.image2')),
					'image3' =>trim(I('post.image3'))
				);
		$data = array(
					'iuid'         => trim(I('post.iuid')),
					'whichApp'     => trim(I('post.whichApp')),
					'content'      => trim(I('post.content')),
					'create_month' => date('Y-m',time()),
					'create_time'  => time(),
					'type'		   => trim(I('post.type')),
				);
		for ($i=0; $i<count($image); $i++) { 
			$img_body                 = substr(strstr($image['image'.($i+1)],','),1);
			if($img_body == ""){
				$data['image'.($i+1)] = "无图片";
			}else{
				$imageName            = time().'_'.mt_rand().'.jpg';
				$img                  = file_put_contents('./Upload/file/'.$imageName, base64_decode($img_body));
				$data['image'.($i+1)] = C('WEB_URL').'/Upload/file/'.$imageName;	
			}
		}
		$addComment = M('feedback')->add($data);

		if($addComment){
			$this->ajaxreturn($data);
		}else{
			$data['status']     = 0;
			$data['message']	= '提交反馈失败';
			$this->ajaxreturn($data);
		}
	}

	/**
    * 用户反馈列表
    * 1ibos 2nlc  3hrac 4elpa 
    **/
    public function feedbackList(){
        $iuid     = I('post.iuid');
        // $whichApp = I('post.whichApp');
        $data     = D('feedback')->where(array('iuid'=>$iuid,'whichApp'=>5))->order('create_time desc')->select();
        foreach ($data as $key => $value) {
            $feedback[$key]   = $value;
            switch ($value['type']) {
                case '0':
                    $feedback[$key]['title'] = '软件';
                    break;
                case '1':
                    $feedback[$key]['title'] = '账户';
                        break;
                case '2':
                    $feedback[$key]['title'] = '购物';
                        break;
                case '3':
                    $feedback[$key]['title'] = '银行';
                        break;
                case '4':
                    $feedback[$key]['title'] = '服务';
                        break;
                case '5':
                    $feedback[$key]['title'] = '其他';
                        break;
            } 
            $feedback[$key]['create_time'] = word_time($value['create_time']); 
            
        }
        if($feedback){
            $this->ajaxreturn($feedback);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    
	/**
    * 用户反馈详情及回复
    **/
    public function feedbackInfo(){
        $fbid    = I('post.fbid');
        $content = D('feedback')->join('hapylife_user on hapylife_feedback.iuid = hapylife_user.iuid')->where(array('fbid'=>$fbid))->select();
        foreach ($content as $key => $value) {
            $data['content'][$key]                = $value;
            $data['content'][$key]['create_time'] = word_time($value['create_time']); 
        }
        $reply   = D('feedback')->where(array('id'=>$fbid))->select();
        foreach ($reply as $key => $value) {
            $data['reply'][$key]                  = $value;
            $data['reply'][$key]['create_time']   = word_time($value['create_time']);
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

	/**
	* 申请成为美食代理
	**/
	public function makeFoodAgent(){
		$iuid      = trim(I('post.iuid'));
		$foodAgent = trim(I('post.foodAgent'));
		$map  = array(
			'iuid'      =>$iuid,
			'FoodAgent' =>$foodAgent
		);
		$data = M('user')->save($map);
		if($data){
			$data['status'] = 1;
			$data['message']= '申请成功';
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$data['message']= '申请失败';
			$this->ajaxreturn($data);
		}
	}

	/**
	* 美食代理审核
	**/
	public function checkAgent(){
		$iuid = trim(I('iuid'));
		$map  = array(
			'iuid'      =>$iuid,
			'FoodAgent' =>2
		);
		$data = M('user')->save($map);
		if($data){
			$data['status'] = 1;
			$data['message']= '委任成功';
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$data['message']= '委任失败';
			$this->ajaxreturn($data);
		}
	}

	





	
































































}