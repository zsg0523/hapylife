<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* Vendor Controller
**/
class VendorController extends AdminBaseController{

	/**
	* 供应商列表
	**/
	public function index(){
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'Vendor_name'=>$word
			);
		}
		$assign = D('Vendor')->getPage(D('Vendor'),$map);
		// p($assign);die;
		$this->assign($assign);
		$this->display();
	}
	
	/**
	* 添加供应商
	**/
	public function addVendor(){
		$data = I('post.');
		$data['create_date'] = time();
		$data['create_by']   = $_SESSION['username'];
		$result = D('Vendor')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑供应商
	**/
	public function editVendor(){
		$data = I('post.');
		$data['create_date'] = time();
		$data['create_by']   = $_SESSION['user']['username'];
		$map  = array(
				'vid'=>$data['id']
			);
		$result = D('Vendor')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除供应商
	**/
	public function deleteVendor(){
		$id = I('get.id');
		$map= array(
			'vid'=>$id
		);
		$result=D('Vendor')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}


	/**
	* 供应商仓库列表
	**/
	public function warehouse(){
		$id  = I('get.id');
		$VendorName = D('Vendor')->where(array('vid'=>$id))->getfield('vendor_name'); 
		$map = array(
			'wh_vid'=>$id
		);
		$assign=D('Warehouse')->getPage(D('Warehouse'),$map);
		$this->assign('VendorName',$VendorName);
		$this->assign('wh_vid',$id);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加货仓
	**/
	public function addWarehouse(){
		$data=I('post.');
		$result=D('Warehouse')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 编辑货仓
	**/
	public function editWarehouse(){
		$data=I('post.');
		$map=array(
			'wid'=>$data['id']
			);
		$result=D('Warehouse')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除货仓
	**/
	public function deleteWarehouse(){
		$id=I('get.id');
		$map=array(
			'wid'=>$id
			);
		$result=D('Warehouse')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}




}