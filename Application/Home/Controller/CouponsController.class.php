<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * LoginController
 */
class CouponsController extends HomeBaseController{    
    public function coupons(){
    	// 通过时间修改优惠券信息
		$list = M('CouponUser')
							->alias('u')
							->join('nulife_coupon_groups AS g ON u.cg_id = g.gid')
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
	}
        
}
