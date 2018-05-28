<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class PurchaseController extends HomeBaseController{
    /**
    * 
    **/
    public function test(){
        $order = M('Receipt')
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->select();
        // p($order);die;
        foreach ($order as $key => $value) {
            if($value['ir_status'] == 2){
                //添加支付时间
                $ir_paytime = $value['ir_date']+600;
                $map = array(
                    'ir_paytime'=>$ir_paytime
                );
                $save = M('Receipt')->where(array('ir_receiptnum'=>$value['ir_receiptnum']))->save($map);
            }
        }
    }
	
	/**
	* 主界面
	**/
	public function main(){
		$iuid  = $_SESSION['user']['id'];
        $find  = M('User')->where(array('iuid'=>$iuid))->find();
        $type  = trim($find['distributortype']);
        $mtype = trim($find['customertype']);
        //判断用户等级 show--1、可点击 2、不可点击
        // p($type);
        $tmpe    = array(
            'ip_grade' =>$type,
            'is_pull'  =>1
        ); 
        $product = D('Product')->where($tmpe)->order('is_sort desc')->select();

        foreach ($product as $key => $value) {
            $data[$key]         = $value; 
            $data[$key]['show'] = 1; 
        }
        // p($data);die;
        $this->assign('product',$data);
		$this->display();
	}


	/**
	* 购买礼包
	**/
	public function purchase(){
		$iuid  = $_SESSION['user']['id'];
        $find  = M('User')->where(array('iuid'=>$iuid))->find();
        $type  = trim($find['distributortype']);
        $mtype = trim($find['customertype']);
        //判断用户等级 show--1、可点击 2、不可点击
        // p($type);die;
        $tmpe    = array(
            'ip_grade' =>$type,
            'is_pull'  =>1
        );
        $product = D('Product')->where($tmpe)->order('is_sort desc')->select();

        foreach ($product as $key => $value) {
            $data[$key]         = $value; 
            $data[$key]['show'] = 1; 
        }
        // p($tmpe);die;
        $this->assign('product',$data);
		$this->display();
	}


	/**
	* 会籍激活记录
	**/
	public function activaction(){
		$iuid     = $_SESSION['user']['id'];
        $orderTime= D('User')->where(array('iuid'=>$iuid))->getfield('OrderDate');
        if($orderTime){
            $date     = date('Y-m',time());
            $time     = date('Y-m',strtotime($orderTime));
            $day      = date('d',strtotime($orderTime));
            if($day>=28){
                $allday = 28;
            }else{
                $allday = $day;
            }
            $ddd    = $allday-1;
            if($ddd>=10){
                $oneday = $ddd;
            }else{
                $oneday = '0'.$ddd;
            }
            $date1    = explode('-',$date);
            $date2    = explode('-',$time);
            $number   = ($date1[0]-$date2[0])*12+($date1[1]-$date2[1]);
            for($i=0;$i<$number;$i++){
                $arr.=date("Y-m",strtotime("+1 month",strtotime($time))).',';
                $time= date("Y-m",strtotime("+1 month",strtotime($time)));
            }
            $str    = chop($arr,',');
            $strarr = explode(',', $str);
            $mape   = D('Activation')->where(array('iuid'=>$iuid))->order('datetime asc')->getfield('datetime',true);
            $end    = D('Activation')->where(array('iuid'=>$iuid))->order('datetime desc')->getfield('datetime');
            $endtime= strtotime($end.'-'.$oneday);
            if(empty($mape)){  
                $diffarr[0]= $time;
                foreach ($diffarr as $key => $value) {
                    if(!empty($value)){
                       $diff[] = $value;
                    }
                }
                if($diff){
                    foreach ($diff as $key => $value) {
                        $year  = date("Y年m月",strtotime($value)).$allday.'日';
                        $endday= date("Y年m月",strtotime("+1 month",strtotime($value))).$oneday.'日';
                        $where = array('iuid'=>$iuid,'datetime'=>$value,'is_tick'=>1,'hatime'=>$year,'endtime'=>$endday);
                        D('Activation')->add($where);
                    }   
                }
            }else{ 
                $diffarr   = array_diff($strarr,$mape);
                foreach ($diffarr as $key => $value) {
                    if(!empty($value)){
                       $diff[] = $value;
                    }
                }
                if($diff){
                    foreach ($diff as $key => $value) {
                        $year  = date("Y年m月",strtotime($value)).$allday.'日';
                        $endday= date("Y年m月",strtotime("+1 month",strtotime($value))).$oneday.'日';
                        $where = array('iuid'=>$iuid,'datetime'=>$value,'is_tick'=>0,'hatime'=>$year,'endtime'=>$endday);
                        if(time()>$endtime){
                            D('Activation')->add($where);
                        }
                    }   
                }
            }
        }
        $data = D('Activation')->where(array('iuid'=>$iuid))->select();
        $this->assign('data',$data);
		$this->display();
	}


	/**
	* 我的订单列表
	**/
	public function myOrder(){
		$iuid = $_SESSION['user']['id'];
        $data['status'] = $_SESSION['user']['status'];
		$map  = array(
				'riuid'=>$iuid
			);
		$data = M('Receipt')
				->alias('r')
				->join('hapylife_receiptlist hr on r.ir_receiptnum = hr.ir_receiptnum')
				->join('hapylife_product hp on hr.ipid=hp.ipid')
				->where($map)
				->select();
		// p($data);
		$this->assign('data',$data);
		$this->display();
	}


	/**
	* 删除订单
	**/
	public function delete_order(){
		//订单号
		$order_num  = I('get.ir_receiptnum');
	    $result = M('receipt')->where(array('ir_receiptnum'=>$order_num))->delete();
	    if($result){
	    	redirect($_SERVER['HTTP_REFERER']);
	    }else{
	    	$this->error('删除失败');
	    }
	}


	/**
	* 个人资料
	**/
	public function myProfile(){
		$iuid = $_SESSION['user']['id'];
		$data = D('User')->where(array('iuid'=>$iuid))->find();
		$right= D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Right'))->select();
		$left = D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Left'))->select();
        //right右脚、left左脚
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
		// p($data);die;
		$this->assign('userinfo',$data);
		$this->display();
	}

	/**
	* 购买详情
	**/
	public function purchaseInfo(){
		$ipid = I('get.ipid');
		// p($ipid);die;
        $data = M('Product')
			  ->where(array('ipid'=>$ipid))
			  ->find();
		$this->assign('data',$data);
		$this->display();
	}


	/**
	* 生成订单
	**/
	public function order(){
		$iuid = $_SESSION['user']['id'];
        $ipid = trim(I('get.ipid'));
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        switch ($product['ip_type']) {
            case '1':
                $list= D('Receipt')->where(array('ir_ordertype'=>$product['ip_type'],'riuid'=>$iuid,'is_delete'=>0))->select();
                if($list){
                    $this->error('请付款或删除重新下单');
                }else{
                    $con = '首购单';
                }
                break;
            case '2':
                $con = '升级单';
                break;
            case '3':
                $con = '月费单';
                break;
        }
        if(empty($userinfo['shopaddress1'])||empty($userinfo['shopaddress1'])){
            $this->error('请先填写个人信息的地区和详细地址');
        }
        $ia_name  = $userinfo['lastname'].$userinfo['firstname'];
        $order = array(
            //订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'       =>time(),
            //订单的状态(0待生成订单，1待支付订单，2已付款订单)
            'ir_status'     =>0,
            //下单用户id
            'riuid'          =>$iuid,
            //下单用户
            'rCustomerID'    =>$userinfo['customerid'],
            //收货人
            'ia_name'       =>$ia_name,
            //收货人电话
            'ia_phone'      =>$userinfo['phone'],
            //收货地址
            'ia_address'    =>$userinfo['shopaddress1'],
            //订单总商品数量
            'ir_productnum' =>1,
            //订单总金额
            'ir_price'      =>$product['ip_price_rmb'],
            //订单总积分
            'ir_point'      =>$product['ip_point'],
            //订单备注
            'ir_desc'       =>$con,
            //订单类型
            'ir_ordertype'  => $product['ip_type'],
            //产品id
            'ipid'          => $product['ipid']
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
        $content = '您的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        $log = array(
            'from_iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>0,
            'type'      =>2,
            'date'      =>date('Y-m-d H:i:s')          
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            $this->success('下单成功,前往支付页面',U('Home/Purchase/cjPayment',array('ir_receiptnum'=>$order_num)));
        }else{
            $this->error('订单生成失败');
        }
	}

	/**
    * 购买产品畅捷支付
    **/
    public function cjPayment(){
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
		$postData['ReturnUrl']         = 'http://apps.hapy-life.com/hapylife';// 前台跳转url
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
		$postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/Purchase/notifyVerify';//异步通知地址
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
		$receipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
		$cjPayStatus = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($map);
		//验签
		$return = rsaVerify($map);
		//更改订单状态
		if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
			//修改用户最近订单日期/是否通过/等级/数量
            $tmpe['iuid'] = $receipt['riuid'];
            $userinfo     = D('User')->where(array('iuid'=>$receipt['riuid']))->find();
            //number 购买产品的次数
            if($userinfo['number']==0){
                //支付日期
            	$tmpe['OrderDate'] = date("m/d/Y h:i:s A");
            	$OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                //如果是旧用户
                if($userinfo['isnew'] == 0){
                    if($userinfo['number']==0){
                        //isCheck 是否审核
                        $tmpe['IsCheck'] = 1;   
                    }
                }else{
                    $tmpe['IsCheck'] = 2;
                }
            }else{
                //
            	$OrderDate       = $userinfo['orderdate'];
                $tmpe['IsCheck'] = 2;
            }
            //产品等级
            $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade');
            //购买产品次数+1
            $tmpe['Number']          = $find['number']+1;
            //用户的激活记录
            $activaDate = D('Activation')->where(array('iuid'=>$receipt['riuid'],'is_tick'=>1))->order('datetime desc')->getfield('datetime');

            if(empty($activaDate)){
                $activa = $OrderDate;
            }else{
                $activa = $activaDate;
            }
            $day = date('d',strtotime($OrderDate));
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
            // for($i=0;$i<$receipt['ir_productnum'];$i++) {
                //删除原先未激活，添加激活
                $time  = date("Y-m",strtotime("+1 month",strtotime($activa)));
                $year  = date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日';
                $endday= date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日';
                $delete= D('Activation')->where(array('iuid'=>$receipt['riuid'],'datetime'=>$time))->delete();
                $where =array('iuid'=>$receipt['riuid'],'ir_receiptnum'=>$map['outer_trade_no'],'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
                $save  = D('Activation')->add($where);
            // }

			$status  = array(
				'ir_status'  =>2,
				'ir_paytype' =>1,
                'ir_paytime' =>time()
			);
            //更新订单信息
        	$upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($status);
            //修改用户信息
            $update    = D('User')->save($tmpe);
        	if($upreceipt){
        		//通知畅捷完成支付
				echo "success";
				$this->seccess('支付成功，跳转',U('Home/Purchase/myOrder'));
        	}
		}
    }


    /**
    * 购买产品订单状态查询
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

// *****************收货地址********************
    /**
    * 收货地址列表
    **/ 
    public function addressList(){
        $iuid = $_SESSION['user']['id'];
        // 查询注册信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 
        // 查询地址表信息
        $ia_road = M('Address')->where(array('iuid'=>$iuid))->getField('ia_road',true); 
        
        if(!in_array($userinfo['shopaddress1'], $ia_road)){
           $message = array(
                    'iuid' => $userinfo['iuid'],
                    'ia_name' => $userinfo['lastname'].$userinfo['firstname'],
                    'ia_phone' => $userinfo['phone'],
                    'ia_address' => $userinfo['shopprovince'].$userinfo['shopcity'].$userinfo['shoparea'],
                    'ia_road' => $userinfo['shopaddress1']
                );
            $result = M('Address')->add($message);
        }
        
        $data = M('Address')->where(array('iuid'=>$iuid))->select();
        $assign = array(
                    'data' => $data
                );
        $this->assign($assign);
        $this->display();
    } 

    /**
    * 添加收货地址
    **/ 
    public function addressAdd(){
        $data = I('post.');
        $data = array(
                'iuid' => I('post.iuid'),
                'ia_name' => I('post.ia_name'),
                'ia_phone' => I('post.ia_phone'),
                'ia_address' => I('post.ia_pro').I('post.ia_town').I('post.ia_dis'),
                'ia_road' => I('post.ia_road'),
                );
      
        $result = M('Address')->add($data);
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('添加失败');
        }
    }

    /**
    * 编辑收货地址
    **/ 
    public function addressEdit(){
        $iaid = I('post.iaid');
        $data = array(

                    );
        $result = M('Address')->where(array('iaid'=>$iaid))->edit($data);
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('修改失败');
        }
    }

    /**
    * 删除收货地址
    **/ 
    public function addressDelect(){
        $iaid = I('post.iaid');
        $result = M('Address')->where(array('iaid'=>$iaid))->delete();
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('删除失败');
        }
    }

// *****************收货地址********************
    /**
    * 收货地址列表
    **/ 
    public function bankList(){
        $iuid = $_SESSION['user']['id'];
        // 查询注册信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 
        // p($userinfo);
        // 查询银行表信息
        $bankaccount = M('Bank')->where(array('iuid'=>$iuid))->getField('bankaccount',true); 
        
        if(!in_array($userinfo['bankaccount'], $bankaccount)){
           $message = array(
                    'iuid' => $userinfo['iuid'],
                    'iu_name' => $userinfo['lastname'].$userinfo['firstname'],
                    'bankname' => $userinfo['bankname'],
                    'bankaccount' => $userinfo['bankaccount'],
                    'bankbranch' => $userinfo['bankprovince'].$userinfo['bankcity'].$userinfo['bankarea'].$userinfo['subname'],
                    'createtime' => time(),
                );
            $result = M('Bank')->add($message);
        }
        
        $data = M('Bank')->where(array('iuid'=>$iuid))->select();

        $assign = array(
                    'data' => $data
                );
        $this->assign($assign);
        $this->display();
    } 

    /**
    * 添加收货地址
    **/ 
    public function bankAdd(){
        $data = I('post.');
        $data = array(
                'iuid' => I('post.iuid'),
                'ia_name' => I('post.ia_name'),
                'ia_phone' => I('post.ia_phone'),
                'ia_address' => I('post.ia_pro').I('post.ia_town').I('post.ia_dis'),
                'ia_road' => I('post.ia_road'),
                );
      
        $result = M('Address')->add($data);
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('添加失败');
        }
    }

    /**
    * 编辑收货地址
    **/ 
    public function bankEdit(){
        $iaid = I('post.iaid');
        $data = array(

                    );
        $result = M('Address')->where(array('iaid'=>$iaid))->edit($data);
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('修改失败');
        }
    }

    /**
    * 删除收货地址
    **/ 
    public function bankDelect(){
        $iaid = I('post.iaid');
        $result = M('Address')->where(array('iaid'=>$iaid))->delete();
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('删除失败');
        }
    }





















}