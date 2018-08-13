<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * LoginController
 */
class CouponController extends HomeBaseController{
	/**
	* 登录
	**/
	public function common(){
		$action_name = ACTION_NAME;
		$this->assign('action_name',$action_name);
		$this->display('Coupon/common/commonHead');
	}	
	public function jfcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/jfcoupon');	       
	}
	public function lpcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/lpcoupon');
	}	
	public function rccoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/rccoupon');
	}
	public function xjcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/xjcoupon');
	}
	public function zkcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/zkcoupon');
	}
	public function cjcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/cjcoupon');
	}
	public function zccoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/zccoupon');
	}
	public function cgcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/cgcoupon');
	}
	public function fwcoupondetail(){
		$action_name = ACTION_NAME;
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'action_name' => $action_name,
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/fwcoupon');
	}   
	
	public function map(){
		$location = I('get.location');
		$this->assign('location',$location);
		$this->display('Coupon/coupondetail/map');
	}   
	
	public function sendcoupondetail(){
		$yname = I('get.yname');
		$cu_id = I('get.cu_id');
		$iuid = I('get.iuid');
		$data = M('CouponUser')
					->alias('u')
					->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
					->where(array('u.cu_id'=>$cu_id))
					->find();
		$data['start_time'] = date('Y-m-d',$data['start_time']);
		$data['end_time'] = date('Y-m-d',$data['end_time']);
		$assign = array(
						'data' => $data,
						'cu_id' => $cu_id,
						'iuid' => $iuid,
						'yname' => $yname
					);
		$this->assign($assign);
		$this->display('Coupon/coupondetail/sendcoupon');
	}
        
    public function coupons(){
    	// 通过时间修改优惠券信息
		$list = M('CouponUser')
							->alias('u')
							->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
							->select();	
		foreach($list as $k=>$v){
			if($v['start_time']>time()){
				$cu_id_start[] = $v['cu_id'];
			}
			if($v['end_time']<time()){
				$cu_id_end[] = $v['cu_id'];
			}
			if($v['start_time']<=time() && $v['end_time']>=time()){
				$cu_id[] = $v['cu_id'];
			}
		}
		if(!empty($cu_id_start)){
			// 提前扫描
			$status['status'] = 3;
			foreach($cu_id_start as $v){
				$result = M('CouponUser')->where(array('cu_id'=>$v))->save($status);
				$arr[] = M('CouponUser')->where(array('cu_id'=>$v))->find();
			}
			foreach($arr as $value){
				$content = array(
							'cu_id' => $value['cu_id'],
							'user_id' => $value['user_id'],
							'is_used' => $value['is_used'],
							'coupon_id' => $value['coupon_id'],
							'status' => $value['status'],
						);
				$qr['qrcode'] = qrcode_arr($content);
				$res = M('CouponUser')->where(array('cu_id'=>$value['cu_id']))->save($qr);
			}
		}
		if(!empty($cu_id_end)){
			// 超时扫描
			$status['status'] = 2;
			foreach($cu_id_end as $v){
				$result = M('CouponUser')->where(array('cu_id'=>$v))->save($status);
				$arr[] = M('CouponUser')->where(array('cu_id'=>$v))->find();
			}
			foreach($arr as $value){
				$content = array(
							'cu_id' => $value['cu_id'],
							'user_id' => $value['user_id'],
							'is_used' => $value['is_used'],
							'coupon_id' => $value['coupon_id'],
							'status' => $value['status'],
						);
				$qr['qrcode'] = qrcode_arr($content);
				$res = M('CouponUser')->where(array('cu_id'=>$value['cu_id']))->save($qr);
				// p($res);
			}
		}
		if(!empty($cu_id)){
			// 正常扫描
			$status['status'] = 1;
			foreach($cu_id as $v){
				$result = M('CouponUser')->where(array('cu_id'=>$v))->save($status);
				$arr[] = M('CouponUser')->where(array('cu_id'=>$v))->find();
			}
			foreach($arr as $value){
				$content = array(
							'cu_id' => $value['cu_id'],
							'user_id' => $value['user_id'],
							'is_used' => $value['is_used'],
							'coupon_id' => $value['coupon_id'],
							'status' => $value['status'],
						);
				$qr['qrcode'] = qrcode_arr($content);
				$res = M('CouponUser')->where(array('cu_id'=>$value['cu_id']))->save($qr);
			}
		}
		// 通过时间修改优惠券信息

    	// 优惠券类型
    	$type = D('Coupon')->select();
		// 用户id
		$_SESSION['user']['id'] = I('get.iuid');
		$iuid = $_SESSION['user']['id'];
		// 优惠券类型id
    	$coupon_id = I('post.coupon_id');
		if(!empty($coupon_id)){
			$data = M('CouponUser')
							->alias('u')
							->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
							->where(array('coupon_id'=>$coupon_id,'user_id'=>$iuid,'is_used'=>array('neq',2)))
							->select();	
		}else{
			$data = M('CouponUser')
							->alias('u')
							->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
							->where(array('user_id'=>$iuid,'is_used'=>array('neq',2)))
							->select();	
		}
		
    	$assign = array(
    				'type' => $type,
    				'data' => $data,
    				'coupon_id' => $coupon_id,
    				'iuid' => $iuid
    				);
    	$this->assign($assign);
		$this->display('Coupon/coupons/coupons');
	}	    

	/**
	* 使用优惠券
	**/ 
	public function use_coupon(){
		$iuid = I('post.iuid');
		$cu_id = I('post.cu_id');
		// 个人信息
		$userinfo = M('User')->where(array('iuid'=>$iuid))->find();
		// 优惠券信息
		$coupon = M('CouponUser')
							->alias('u')
							->join('hapylife_coupon_groups AS g ON u.cg_id = g.gid')
							->where(array('u.cu_id'=>$cu_id,'u.user_id'=>$iuid))
							->find();

		$message = array(
					'is_used' => 1,
					'use_time' => time(),
				);
		$content = array(
					'cu_id' => $cu_id,
					'user_id' => $iuid,
					'is_used' => 1,
					'conpon_id' => $coupon['coupon_id'],
					'status' => 1,
				);
		$message['qrcode'] = qrcode_arr($content);
		$result = M('CouponUser')->where(array('user_id'=>$iuid,'cu_id'=>$cu_id))->save($message);

		if($result){
			$arr = array(
					'iuid' => $iuid,
					'hu_nickname' => $userinfo['custormid'],
					'hu_username' => $userinfo['lastname'].$userinfo['firstname'],
					'number' => 1,
					'operator' => $userinfo['custormid'],
					'date' => time(),
					'type' => 0,
					'content' => '用户'.$userinfo['lastname'].$userinfo['firstname'].'：'.'于'.date('Y-m-d H:i:s').'，使用了1张'.$coupon['coupon_name'].'，优惠券编码：'.$coupon['coupon_code'],
					'cu_id' => $cu_id,
				);
			if($coupon['coupon_id'] == 1){
				$list['iu_point'] = $userinfo['iu_point']+$coupon['discount_money']; 
				$res = M('User')->where(array('iuid'=>$iuid))->save($list);
			}
		}
		// 写入日志记录
		$re = M('CouponLog')->add($arr);

		if($re){
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 转赠优惠券
	**/ 
	public function pass_coupon(){
		$iuid = I('post.iuid');
		$cu_id = I('post.cu_id');
		$passTo = strtoupper(trim(I('post.custormid')));

		// 赠送用户信息
		$userinfo = M('User')->where(array('custormid'=>$passTo))->find();
		// 原用户信息
		$useri = M('User')->where(array('iuid'=>$iuid))->find();
		// 原优惠券信息
		$coupon =  M('CouponUser')->where(array('cu_id'=>$cu_id))->find();
		$message = array(
						'is_used' => 2,
						'passTo' => $passTo,
						'use_time' => time(),
					);
		
		$result = M('CouponUser')->where(array('cu_id'=>$cu_id))->save($message);
		if($result){
			$arr = array(
						'coupon_id' => $coupon['coupon_id'],
						'coupon_name' => $coupon['coupon_name'],
						'cg_id' => $coupon['cg_id'],
						'user_id' => $userinfo['iuid'],
						'add_time' => time(),
						'coupon_code' => $coupon['coupon_code'],
						'user_name' => $userinfo['custormid'],
					);
			if($coupon['condition'] == 2){
				$arr['condition'] = 0;
			}else{
				$arr['condition'] = $coupon['condition'];
			}
			$res = M('CouponUser')->add($arr);
			if($res){
				$content = array(
					'cu_id' => $res,
					'user_id' => $userinfo['iuid'],
					'is_used' =>0,
					'conpon_id' => $coupon['coupon_id'],
					'status' =>	1,
				);
				$qrcode['qrcode'] = qrcode_arr($content);
				$re = M('CouponUser')->where(array('cu_id'=>$res))->save($qrcode);
				if($re){
					$log = array(
								'iuid' => $iuid,
								'hu_nickname' => $useri['custormid'],
								'hu_username' => $useri['lastname'].$useri['firstname'],
								'operator' => $useri['lastname'].$useri['firstname'],
								'date' => time(),
								'content' => '用户：'.$useri['custormid'].'于'.date('Y-m-d H:i:s',time()).'，将1张'.$coupon['coupon_name'].'，编号为：'.$coupon['coupon_code'].'赠送给：'.$userinfo['custormid'],
								'type' => 1,
								'number' => 1,
							);
					$ress = M('CouponLog')->add($log);
				}
			}
		}
		
		if($ress){
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}
     
    /**
	* 获取用户账号
	**/ 
	public function getName(){
		$iuid = I('post.iuid');
		$hu_nickname = I('post.hu_nickname');
		$data = M('User')->where(array('iuid'=>$hu_nickname))->find();
		if(!empty($data) && $data['iuid'] != $iuid){
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else if($data['iuid'] == $iuid){
			$sample['status'] = 2;
			$this->ajaxreturn($sample);
		}else if(empty($data)){
			$sample['status'] = 0;
			$this->ajaxreturn($sample);
		}
	}

	/**
	* 获取用户账号
	**/ 
	public function getNames(){
		$iuid = I('post.iuid');
		$data = M('User')->where(array('iuid'=>$iuid))->find();
		if($data){
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else{
			$sample['status'] = 0;
			$this->ajaxreturn($sample);
		}
	}

	/**
	* 查询二维码状态
	**/ 
	public function check_coupon(){
		$iuid = I('post.iuid');
		$cu_id = I('post.cu_id');
		$data = M('CouponUser')->where(array('user_id'=>$iuid,'cu_id'=>$cu_id))->find();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$sample['is_used'] = 0;
			$this->ajaxreturn($sample);
		}
	}

	/**
	* 注册券返回产品id
	**/ 
	public function back_message(){
		$iuid = I('post.iuid');
		$cu_id = I('post.cu_id');
		$data = M('CouponUser')
					->alias('cu')
					->join('hapylife_coupon_groups AS g ON cu.cg_id = g.gid')
					->where(array('user_id'=>$iuid,'cu_id'=>$cu_id))
					->find();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$sample['status'] = 0;
			$this->ajaxreturn($sample);
		}
	}
    
    
        
}
