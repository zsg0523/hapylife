<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*hapylife Food模块权限管理
**/
class FoodController extends AdminBaseController{

	/**
	* 餐厅列表
	**/
	public function food(){
		$assign=D('Food')->getPage(D('Food'),$map=array());
		$this->assign($assign);
		$this->display();
	}


	/**
	* 添加餐厅信息
	**/
	public function addFood(){
		$data = I('post.');
		$upload=several_upload_arr();
		if($upload['name'][0]){
			$data['shopImage1'] = C('WEB_URL').$upload['name'][0];
		}
		if($upload['name'][1]){
			$data['shopImage2'] = C('WEB_URL').$upload['name'][1];
		}
		if($upload['name'][2]){
			$data['shopImage3'] = C('WEB_URL').$upload['name'][2];
		}

		$result=D('Food')->addData($data);
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 添加餐厅时间
	**/
	public function addShopTime(){
		$data   = I('post.');
		$result = M('shoptime')->add($data);
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 编辑餐厅信息
	**/
	public function editFood(){
		$data = I('post.');
		$map=array(
			'fid'=>$data['id']
			);
		$upload=several_upload_arr();
		if($upload['name'][0]){
			$data['shopImage1'] = C('WEB_URL').$upload['name'][0];
		}
		if($upload['name'][1]){
			$data['shopImage2'] = C('WEB_URL').$upload['name'][1];
		}
		if($upload['name'][2]){
			$data['shopImage3'] = C('WEB_URL').$upload['name'][2];
		}

		$result=D('Food')->editData($map,$data);;
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 显示餐厅
	**/
	public function food_show(){
		$data=I('get.');
		$map =array(
			'fid'=>$data['id']
			);
		$result=D('Food')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('编辑失败');
		}
	}
	
	/**
	* 置顶餐厅
	**/
	public function food_top(){
		$data=I('get.');
		$map =array(
			'fid'=>$data['id']
			);
		$result=D('Food')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除餐厅
	**/
	public function delete_food(){
		$id=I('get.id');
		$map=array(
			'fid'=>$id
			);
		$result=D('Food')->deleteData($map);
		if($result){
			$this->redirect('Admin/Food/food');
		}else{
			$this->error('删除失败');
		}
	}

	



























}
