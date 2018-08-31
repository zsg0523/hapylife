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
}