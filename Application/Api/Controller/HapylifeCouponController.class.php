<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 修改用户积分
**/
class HapylifeCouponController extends HomeBaseController{
	/**
	* 接受nulife数据，修改hapylife用户积分
	**/ 
    public function edit_point(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        $result = M('User')->where(array('CustomerID'=>$data['customerid']))->save($data);
    }

    /**
	* 后台添加优惠券获取用户信息
	**/ 
	public function userinfo(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
		$log      = addUsaLog($jsonStr);
		$data     = json_decode($jsonStr,true);
		$userinfo = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
	    if($userinfo){
	    	$userinfo['status'] = 1;
	    	$this->ajaxreturn($userinfo);
	    }
	}

	/**
	* 接收前台数据，返回用户信息
	**/ 
	public function callBack(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
	    $userinfo = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
	    if($userinfo){
	    	$userinfo['status'] = 1;
	    	$this->ajaxreturn($userinfo);
	    }
	}

	/**
	* 接收前台数据，修改用户积分
	**/
	public function editPoint(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
	    $result = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->save($data);
	    if($result){
	    	$result['status'] = 1;
	    	$this->ajaxreturn($result);
	    }
	}

	/**
	* 前台接收数据，生成子订单日志记录
	**/ 
	public function addLog(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
        $addlog  = M('Log')->add($data['log']);

	    if($addlog){
	    	$result['status'] = 1;
	    	$this->ajaxreturn($result);
	    }
	}

	/**
	* 前台接收数据，记录会员使用EP日志
	**/ 
	public function addLogs(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
        $addlogs = M('Getpoint')->add($data['logs']);
        if($addlogs){
	    	$result['status'] = 1;
	    	$this->ajaxreturn($result);
	    }
	}

	/**
	* 给nulife传送产品信息
	**/ 
	public function sendPro(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
		$log    = addUsaLog($jsonStr);
		$data   = json_decode($jsonStr,true);
		if($data['product_id']){
			$result = M('Product')->where(array('ipid'=>array('in',$data['product_id'])))->select();	
		}else{
			$result = M('Product')->where(array('is_pull'=>1))->select();
		}
		if($result){
	    	$this->ajaxreturn($result);
		}
	}
	/**
	 * [registByCoupon description]
	 * @return [type] [description]
	 */
	public function registByCoupon(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
		$log    = addUsaLog($jsonStr);
		$data   = json_decode($jsonStr,true);
		$result = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->setField('registByCoupon',1);
	    if($result){
	    	$result['status'] = 1;
	    	$this->ajaxreturn($result);
	    }
	}
	/**
	* 通过用户账号查询是否有注册券
	**/ 
	public function checkCoupon(){
		$hu_nickname = I('post.hu_nickname');
		$userinfo = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
		if(substr($userinfo['customerid'],0,3) == 'HPL' && $userinfo['distributortype'] == 'Pc' && empty($userinfo['wvCustomerID'])){
			$data = array(
					'hu_nickname' => $hu_nickname,
				);
			$data    = json_encode($data);
			$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/checkCoupon";
			$result  = post_json_data($sendUrl,$data);
			$back_result = json_decode($result['result'],true);
			if($back_result['couponStatus'] && $back_result['coupon']){
				foreach ($back_result['coupon'] as $key => $value) {
					if($value['is_dt']==1){
						$DT1[] = $value;
					}else{
						$DT0[] = $value;
					}
				}
				if($DT1){
					$map['cu_id'] = $DT1[0]['cu_id'];
				}else{
					$map['cu_id'] = $DT0[0]['cu_id'];
				}
				$map['status'] = 1;
				$this->ajaxreturn($map);
			}else{
				$map['status'] = 0;
				$this->ajaxreturn($map);
			}
		}else{
			$map['status'] = 0;
			$this->ajaxreturn($map);
		}
	}

	/**
	* 使用该通用券创建送货单
	**/
	public function createReceipt(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
		$log    = addUsaLog($jsonStr);
		$data   = json_decode($jsonStr,true);
		$coupon = $data['coupon'];
		// 如果用户信息不存在,则通过订单号查询地址信息
		$receipt_msg = M('Receipt')->where(array('ir_receiptnum'=>$data['operator']))->find();
		$riuid = $receipt_msg['riuid'];
		$rCustomerID = $receipt_msg['rcustomerid'];
		$ia_name = $receipt_msg['ia_name'];
		$ia_phone = $receipt_msg['ia_phone'];
		$ia_province = $receipt_msg['ia_province'];
		$ia_city = $receipt_msg['ia_city'];
		$ia_area = $receipt_msg['ia_area'];
		$ia_address = $receipt_msg['ia_address'];
		$order_num = 'CP'.date('YmdHis').rand(10000, 99999);
		$con = $coupon['c_name'].$coupon['coupon_code'];
		$receipt = array(
			//订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'=>time(),
            //订单的状态(0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过  7待注册)
            'ir_status'=>2,
            //下单用户id
            'riuid'=>$riuid,
            //下单用户
            'rCustomerID'=>$rCustomerID,
            //收货人
            'ia_name'=>$ia_name,
            //收货人电话
            'ia_phone'=>$ia_phone,
            // 省
            'ia_province' => $ia_province,
            // 市
            'ia_city' => $ia_city,
            // 区
            'ia_area' => $ia_area,
            //收货地址
            'ia_address'=>$ia_address,
            //订单总商品数量
            'ir_productnum'=>1,
            //订单总金额
            'ir_price'=>'',
            //订单总积分
            'ir_point'=>'',
            //订单待付款总金额
            'ir_unpaid'=>'',
            //订单待付款总积分
            'ir_unpoint'=>'',
            //订单备注
            'ir_desc'=>$con,
            //订单类型
            'ir_ordertype' => 4,
            // 支付时间
            'ir_paytime' => time(),
            // 通用券编码
            'coucode' => $coupon['coupon_code'],
            // 产品id
            'ipid'	 => $coupon['product_id'],
		);
		$receiptAdd = M('Receipt')->add($receipt);
		$receiptlist = array(
			'ir_receiptnum' => $order_num,
			'ipid' => $coupon['product_id'],
			'ilid' => '',
			'product_num' => 1,
			'product_price' => '',
			'product_point' => '',
			'product_name' => $coupon['g_name'],
			'product_picture' => $coupon['img'],
		);
		$receiptlistAdd = M('Receiptlist')->add($receiptlist);
        if($receiptAdd && $receiptlistAdd){
        	$map['status'] = 1;
        	$map['ia_name'] = $ia_name;
        	$map['iuid'] = $riuid;
        	$this->ajaxreturn($map);
        }else{
        	$map['status'] = 0;
        	$this->ajaxreturn($map);
        }
	}
}