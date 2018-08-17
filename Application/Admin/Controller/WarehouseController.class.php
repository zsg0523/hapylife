<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* 货仓控制器
**/
class WarehouseController extends AdminBaseController{

	/**
	* 货仓列表
	**/
	public function warehouse(){
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'wh_number'=>$word
			);
		}
		$assign=D('Warehouse')->getPage(D('Warehouse'),$map);
		// p($assign);die;
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