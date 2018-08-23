<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * CouponController
 */

/**
* 数据传输路径
* CouponController->CouponapiController->HapylifeCouponController
**/ 
class CouponController extends HomeBaseController{
	/**
	* 使用优惠券
	**/ 
	public function use_coupon(){
		$hu_nickname = I('post.hu_nickname');
		$cu_id = I('post.cu_id');
		if(substr($hu_nickname,0,3) == 'HPL'){
			$userinfo = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
		}
		$data = array(
					'cu_id' => $cu_id,
					'hu_nickname' => $hu_nickname,
					'userinfo' => $userinfo,
				);
		$data    = json_encode($data);
		$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/use_coupon";
		$result  = post_json_data($sendUrl,$data);
		if($result['result'] == 1){
			$sample['status'] = 1;
			$sample['msg'] = '使用成功';
			$this->ajaxreturn($sample);
		}else{
			$sample['status'] = 0;
			$sample['msg'] = '使用失败';
			$this->ajaxreturn($sample);
		}
	}

	/**
	* 转赠优惠券
	**/ 
	public function pass_coupon(){
		$hu_nickname = I('post.hu_nickname');
		$cu_id = I('post.cu_id');
		$passTo = strtoupper(trim(I('post.customerid')));
		if(substr($passTo,0,3) == 'HPL'){
			$userinfo = M('User')->where(array('CustomerID'=>$passTo))->find();
		}
		if(substr($hu_nickname,0,3) == 'HPL'){
			$userinfos = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
		}
		$data = array(
				'cu_id' => $cu_id,
				'passTo' => $passTo,
				'hu_nickname' => $hu_nickname,
				'userinfo' => $userinfo,
				'userinfos' => $userinfos,
				);
		$data    = json_encode($data);
		$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/pass_coupon";
		$result  = post_json_data($sendUrl,$data);

		if($result['result'] == 1){
			$sample['status'] = 1;
			$sample['msg'] = '转赠成功';
			$this->ajaxreturn($sample);
		}else{
			$sample['status'] = 0;
			$sample['msg'] = '转赠失败';
			$this->ajaxreturn($sample);
		}
	}
     
    /**
	* 转赠优惠券--获取用户账号
	**/ 
	public function getName(){
		// 接受者
		$nickname = strtoupper(I('post.nickname'));
		// 操作者
		$hu_nickname = I('post.hu_nickname');
		if(substr($nickname,0,3) == 'HPL'){
			// 接受人账号
			$userinfo = M('User')->where(array('CustomerID'=>$nickname))->find();
		}
		$data = array(
					'nickname' => $nickname,
					'hu_nickname' => $hu_nickname,
					'userinfo' => $userinfo,
				);
		$data    = json_encode($data);
		$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/getName";
		$result  = post_json_data($sendUrl,$data);
		$back_result = json_decode($result['result'],true);

		if($back_result['status'] == 1){
			$sample['status'] = 1;
			$sample['msg'] = '可以转赠';
			$sample['hu_username'] = $back_result['hu_username'];
			$this->ajaxreturn($sample);
		}else if($back_result['status'] == 2){
			$sample['status'] = 2;
			$sample['msg'] = '不能转给自己';
			$this->ajaxreturn($sample);
		}else{
			$sample['status'] = 0;
			$sample['msg'] = '不存在用户';
			$this->ajaxreturn($sample);
		}
	}

	/**
	* 使用优惠券--获取用户账号
	**/ 
	public function getNames(){
		$hu_nickname = I('post.hu_nickname');
		if(substr($hu_nickname,0,3) == 'HPL'){
			// 用户账号
			$userinfo = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
		}
		$data = array(
					'hu_nickname' => $hu_nickname,
					'userinfo' => $userinfo,
				);
		$data    = json_encode($data);
		$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/getNames";
		$result  = post_json_data($sendUrl,$data);
		$this->ajaxreturn(json_decode($result['result']));
	}
        
}
