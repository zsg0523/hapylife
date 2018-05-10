<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class PurchaseController extends HomeBaseController{
	
	/**
	* 主界面
	**/
	public function main(){
		//礼包详情
		$map = array(
            'ip_type'    =>1,
            'ip_name_zh '=>array('NEQ','Rbs'),
        );
        $data= M('Product')->where($map)->order('is_sort desc')->select();
        // P($_SESSION);
        $this->assign('product',$data);
		$this->display();
	}


	/**
	* 购买礼包
	**/
	public function purchase(){
		$this->display();
	}


	/**
	* 会籍激活
	**/
	public function activaction(){
		$this->display();
	}


	/**
	* 我的订单
	**/
	public function myOrder(){
		$this->display();
	}

	/**
	* 个人资料
	**/
	public function myProfile(){
		$iuid = I('post.iuid')?1:978185;
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
		p($data);die;
		$this->assign('userinfo',$data);
		$this->display();
	}

	/**
	* 银行资料
	**/
	public function bankInfo(){
		$this->display();
	}


	/**
	* 购买详情
	**/
	public function purchaseInfo(){
		$this->display();
	}





















}