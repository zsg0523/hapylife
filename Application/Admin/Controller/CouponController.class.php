<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台首页控制器
 */
class CouponController extends AdminBaseController{

	/**
	* 用户列表
	**/
	public function index(){
		//账户昵称搜索
		$word = I('get.word');
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		
		$assign=D('CouponUser')->getSendPage(D('CouponUser'),$word,$starttime,$endtime);
		
		$coupon = M('CouponGroups')
						->alias('g')
						->join('hapylife_coupon AS c ON g.cid = c.id')
						->select();
		$couponlist = M('Coupon')->select();
		
		
		$this->assign($assign);
		$this->assign('status',I('get.status'));
		$this->assign('word',$word);
		$this->assign('coupon',$coupon);
		$this->assign('couponlist',$couponlist);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}

	/**
	* 给用户添加优惠券
	**/
	public function add_coupons(){
		$data = I('post.');
		$remove = explode('+',$data['coupon']);
		$arr = array(
					'cg_id' => $remove[0],
					'coupon_id' => $remove[1],
					'coupon_code' => $remove[2],
					'coupon_name' => $remove[3],
					'user_id' => $data['iuid'],
					'add_time' => time(),
					'condition' => $remove[4],
					'user_name' => $data['hu_username'],
				);
		$content = array(
				'user_id' => $data['iuid'],
				'is_used' =>0,
				'conpon_id' => $remove[1],
				'status' =>	1,
			);
		$arr['qrcode'] = qrcode_arr($content);
		$result = M('CouponUser')->add($arr);
		if($result){
			$array = array(
					'iuid' => $data['iuid'],
					'hu_nickname' => $data['customerid'],
					'hu_username' => $data['hu_username'],
					'cu_id' => $remove[0],
					'operator' => $_SESSION['user']['username'],
					'date' => time(),
					'content' => $_SESSION['user']['username'].'在'.date('Y-m-d H:i:s',time()).'，给用户：'.$data['customerid'].'添加了1张'.$remove[3].'，优惠券编码：'.$remove[2],
					'type' => 2,
				);
			$res = M('CouponLog')->add($array);
			
		}
		
		if($res){
			$this->redirect('Admin/Coupon/index');
		}else{
			$this->error('添加失败',U('Admin/Coupon/index'));
		}
	}

	// 获取用户信息
	public function userinfo(){
		$customerid = strtoupper(trim(I('post.customerid')));
		$iuid = trim(I('post.iuid'));
		if(!empty($iuid)){
			$data = M('User')->where(array('iuid'=>$iuid))->find();
		}
		
		if(!empty($customerid)){
			$data = M('User')->where(array('customerid'=>$customerid))->find();
		}

        if($data){
        	$data['status'] = 1;
        	$this->ajaxreturn($data);
        }else{
        	$sample['status'] = 0;
        	$this->ajaxreturn($sample);
        }
	}

	/**
	* 给用户修改优惠券
	**/
	public function edit_coupons(){
		$data = I('post.');
		$remove = explode('+',$data['coupon']);
		$arr = array(
					'cg_id' => $remove[0],
					'coupon_id' => $remove[1],
					'coupon_code' => $remove[2],
					'coupon_name' => $remove[3],
					'user_id' => $data['iuid'],
					'add_time' => time(),
				);
		$result = M('CouponUser')->where(array('cu_id'=>$data['cu_id']))->save($arr);
		if($result){
			$content = array(
					'cu_id' => $data['cu_id'],
					'user_id' => $data['iuid'],
					'is_used' =>0,
					'conpon_id' => $arr['coupon_id'],
					'status' =>	1,
				);
			$arr['qrcode'] = qrcode_arr($content);
			$res = M('CouponUser')->where(array('cu_id'=>$data['cu_id']))->save($arr);
			if($res){
				$array = array(
						'iuid' => $data['iuid'],
						'hu_nickname' => $data['hu_nickname'],
						'hu_username' => $data['hu_username']?$data['hu_username']:'',
						'number' => 1,
						'operator' => $_SESSION['user']['username'],
						'date' => time(),
						'content' => $_SESSION['user']['username'].'在'.date('Y-m-d H:i:s',time()).'，给用户：'.$data['hu_nickname'].'添加了1张'.$remove[3].'，优惠券编码：'.$remove[2],
					);
				$re = M('CouponLog')->where(array('date'=>time()))->save($array);
			}
		}
		if($re){
			$this->redirect('Admin/Coupon/index');
		}else{
			$this->error('修改失败',U('Admin/Coupon/index'));
		}
	}

	/**
	* 给用户优惠券
	**/
	public function delect_coupons(){
		$cu_id = I('get.cu_id');
		$time = M('CouponUser')->where(array('cu_id'=>$cu_id))->getfield('add_time');
		$result = M('CouponUser')->where(array('cu_id'=>$cu_id))->delete();
		$res = M('CouponLog')->where(array('date'=>$time))->delete();
		if($result && $res){
			$this->redirect('Admin/Coupon/index');
		}else{
			$this->error('删除失败',U('Admin/Coupon/index'));
		}
	}


	// ****************优惠券管理***********************
	/**
	* 优惠券类型列表
	**/
	public function coupon(){
		$data = D('Coupon')->order('order_number ASC')->select();
		$assign = array(
					'data' =>$data
					);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加优惠券类型
	**/
	public function add_coupon(){
		$data = I('post.');
		$upload = post_upload();
		if(isset($upload['name'])){
			$data['img'] = C('WEB_URL').$upload['name'];
		}
		$result = D('Coupon')->addData($data);
		if($result){
			$this->redirect('Admin/Coupon/coupon');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 修改优惠券类型
	**/
	public function edit_coupon(){
		$data = I('post.');
		$map = array(
				'id' => $data['id']
				);
		$upload = post_upload();
		if(isset($upload['name'])){
			$data['img'] = C('WEB_URL').$upload['name'];
		}

		$result = D('Coupon')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Coupon/coupon');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除优惠券类型
	**/
	public function delect_coupon(){
		$id = I('get.id');
		$data = M('CouponGroups')->where(array('cid'=>$id))->select();
		$map = array(
			'id' => $id
		);
		$result = D('Coupon')->deleteData($map);
		if($result){
			$res = M('CouponGroups')->where(array('cid'=>$id))->delete();
		}
		if($result){
			$this->redirect('Admin/Coupon/coupon');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 优惠券类型排序
	**/
	public function order_coupon(){
		$data = I('post.');
		$result = D('Coupon')->orderData($data,$id='id');
		if($result){
			$this->redirect('Admin/Coupon/coupon');
		}else{
			$this->error('排序失败');
		}
	}

// ************优惠券类型内容管理********************
	/**
	* 优惠券类型内容列表
	**/
	public function details(){
		$id = I('get.id');
		$name = D('Coupon')->where(array('id'=>$id))->field('name')->find();
		$c_type = M('CouponType')->where(array('cid'=>$id))->select();
		$data = M('CouponGroups')->where(array('cid'=>$id))->select();
		$coupon = M('Product')->where(array('ipid'=>31))->select();

		$assign = array(
					'id' => $id,
					'name' => $name,
					'data' => $data,
					'c_type' => $c_type,
					'coupon' => $coupon
				);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加优惠券类型内容
	**/ 
	public function add_details(){
		$data = I('post.');
		$data['start_time'] = strtotime($data['start_time'])+23*3600+59*60;
		$data['end_time'] = strtotime($data['end_time'])+23*3600+59*60;
		// p($data);
		// die;
		// 8位随机字符串
		$str_time = dec62(msectime());
		// 构造优惠券编码
		if($data['cid'] == 1){
			$data['c_number'] = 'POC'.rand_char().$str_time;
		}elseif($data['cid'] == 2) {
			$data['c_number'] = 'GFC'.rand_char().$str_time;
		}elseif($data['cid'] == 3) {
			$data['c_number'] = 'TKC'.rand_char().$str_time;
		}elseif($data['cid'] == 4) {
			$data['c_number'] = 'CDC'.rand_char().$str_time;
		}elseif($data['cid'] == 5) {
			$data['discount'] = rtrim($data['discount'],"%").'%';
			$data['type'] = 'EP';
			$data['which_app'] = $data['app'];
			if($data['app'] == 1){
				// 分解参数
				$resolve = explode('+',$data['product_id1']);
				$data['product_id'] = $resolve[0];
				$data['pro_name'] = $resolve[1];
			}else if($data['app'] == 2){
				// 分解参数
				$resolve = explode('+',$data['product_id2']);
				$data['product_id'] = $resolve[0];
				$data['pro_name'] = $resolve[1];
			}else if($data['app'] == 3){
				// 分解参数
				$resolve = explode('+',$data['product_id3']);
				$data['product_id'] = $resolve[0];
				$data['pro_name'] = $resolve[1];
			}else if($data['app'] == 4){
				// 分解参数
				$resolve = explode('+',$data['product_id4']);
				$data['product_id'] = $resolve[0];
				$data['pro_name'] = $resolve[1];
			}
			$data['product_id'] = $data['product_id'];
			$data['c_number'] = 'DCC'.rand_char().$str_time;
		}elseif($data['cid'] == 6) {

			$data['c_number'] = 'LTC'.rand_char().$str_time;
			
		}elseif($data['cid'] == 7) {
			$data['which_app'] = 3;
			$data['pro_name'] = $data['pro_name5'];
			$data['type'] = 'RMB';
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['product_id'] = $resolve[0];
			$data['discount_money'] = $resolve[1];
			$data['c_number'] = 'LGC'.rand_char().$str_time;
		}elseif($data['cid'] == 8) {
			$data['which_app'] = 2;
			$data['pro_name'] = $data['pro_name6'];
			$data['type'] = 'RMB';
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['product_id'] = $resolve[0];
			$data['discount_money'] = $resolve[1];
			$data['c_number'] = 'SVC'.rand_char().$str_time;
		}elseif($data['cid'] == 12){
			$data['which_app'] = 5;
			$data['pro_name'] = $data['pro_name7'];
			$data['type'] = 'RMB';
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['product_id'] = $resolve[0];
			$data['discount_money'] = $resolve[1];
			$data['c_number'] = 'FPC'.rand_char().$str_time;
		}
		
		$upload = several_upload();
		if(isset($upload['name'][0])){
			$data['img'] = C('WEB_URL').$upload['name'][0];
		}
		if(isset($upload['name'][1])){
			$data['pro_img'] = C('WEB_URL').$upload['name'][1];
		}
		
		$result	= M('CouponGroups')->add($data);

		if($result){
			$this->redirect('Admin/Coupon/details',array('id'=>$data['cid']));
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 修改优惠券类型内容
	**/ 
	public function edit_details(){
		$data = I('post.');
		$data['start_time'] = strtotime($data['start_time'])+23*3600+59*60;
		$data['end_time'] = strtotime($data['end_time'])+23*3600+59*60;
		// p($data);
		// die;		
		if($data['cid'] == 5){
			$data['discount'] = rtrim($data['discount'],"%").'%';
			$data['type'] = 'EP';
			if(!empty($data['app'])){
				$data['which_app'] = $data['app'];
			}
			if($data['app'] == 1){
				if(!empty($data['product_id11'])){
					// 分解参数
					$resolve = explode('+',$data['product_id11']);
					$data['product_id'] = $resolve[0];
					$data['pro_name'] = $resolve[1];
				}
			}else if($data['app'] == 2){
				if(!empty($data['product_id22'])){
					// 分解参数
					$resolve = explode('+',$data['product_id22']);
					$data['product_id'] = $resolve[0];
					$data['pro_name'] = $resolve[1];
				}	
			}else if($data['app'] == 3){
				if(!empty($data['product_id33'])){
					// 分解参数
					$resolve = explode('+',$data['product_id33']);
					$data['product_id'] = $resolve[0];
					$data['pro_name'] = $resolve[1];
				}
			}else if($data['app'] == 4){
				if(!empty($data['product_id44'])){
					// 分解参数
					$resolve = explode('+',$data['product_id44']);
					$data['product_id'] = $resolve[0];
					$data['pro_name'] = $resolve[1];
				}
			}
		}elseif($data['cid'] == 6){

			// $data['c_number'] = 'LTC'.rand_char().$str_time;

		}elseif($data['cid'] == 7){
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['discount_money'] = $resolve[1];
			$data['which_app'] = 3;
			if(!empty($data['product_id'])){
				$data['pro_name'] = $data['pro_name55'];
				$data['product_id'] = $resolve[0];
			}
			
		}elseif($data['cid'] == 8){
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['discount_money'] = $resolve[1];
			$data['which_app'] = 2;
			if(!empty($data['product_id'])){
				$data['pro_name'] = $data['pro_name66'];
				$data['product_id'] = $resolve[0];
			}
		}elseif($data['cid'] == 12){
			// 分解参数
			$resolve = explode('+',$data['product_id']);
			$data['discount_money'] = $resolve[1];
			$data['which_app'] = 5;
			if(!empty($data['product_id'])){
				$data['pro_name'] = $data['pro_name77'];
				$data['product_id'] = $resolve[0];
			}
		}
		
		$upload = several_upload();
		if(isset($upload['name'][0])){
			$data['img'] = C('WEB_URL').$upload['name'][0];
		}
		if(isset($upload['name'][1])){
			$data['pro_img'] = C('WEB_URL').$upload['name'][1];
		}
		
		$result = M('CouponGroups')->where(array('gid'=>$data['gid']))->save($data);

		if($result){
			$this->redirect('Admin/Coupon/details',array('id'=>$data['cid']));
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 删除优惠券类型内容
	**/ 
	public function delect_details(){
		$id = I('get.id');
		$gid = I('get.gid');
		
		$result = M('CouponGroups')->where(array('gid'=>$gid))->delete();

		if($result){
			$this->redirect('Admin/Coupon/details',array('id'=>$id));
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 优惠券列表
	**/
	public function couponlist(){
		$coupon = D('Coupon')->select();
		//1积分券,2礼品券,3入场券,4现金券,5折扣券,6抽奖券,7注册券,8服务券,12首购券
		$id = I('get.id');
		if($id == 0){
			//所有订单
			$status = '1,2,3,4,5,6,7,8,12';
		}else{
			$status = (string)$id;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		
		$assign = D('Coupon')->getPage(D('Coupon'),$word,$starttime,$endtime,$status);
		$data = M('Coupon')->alias('c')->join('hapylife_coupon_groups AS g ON c.id = g.cid')->select();

		$this->assign($assign);
		$this->assign('status',I('get.status'));
		$this->assign('word',$word);
		$this->assign('coupon',$coupon);
		$this->assign('id',$id);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}

	/**
	* 会员优惠券使用情况
	**/ 
	public function usage(){
		//0未使用 1已使用 2转赠
		$order_status = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$status = '0,1,2';
		}else{
			$status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		
		$assign = D('CouponUser')->getPage(D('CouponUser'),$word,$starttime,$endtime,$status);
		// p($assign);
		// die;
		$this->assign($assign);
		$this->assign('status',I('get.status'));
		$this->assign('word',$word);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}
}
