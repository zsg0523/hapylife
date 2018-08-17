<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* Purchase requirement controller
**/
class PrController extends AdminBaseController{

	/**
	* 请购单表头列表
	**/
	public function index(){
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'pr_order_item'=>$word
			);
		}
		$assign = D('PurchaseRequirementHead')->getPage(D('PurchaseRequirementHead'),$map);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 请购单表行列表
	**/
	public function prline(){
		$item = I('get.item');
		$map  = array(
			'pr_order_item'=>$item
		);
		$assign = D('PurchaseRequirementLine')->getPage(D('PurchaseRequirementLine'),$map);
		$this->assign('item',$item);
		$this->assign($assign);
		$this->display();
	}
	
	/**
	* 添加请购单表头
	**/
	public function addPrHead(){
		//唯一请购单号
		$pr_order_item = date('YmdHis').rand(1000, 9999);
		$data = array(
			'pr_staff_id'=>$_SESSION['user']['id'],
			'pr_order_item'=>$pr_order_item,
			'pr_order_type'=>10,
			'pr_description'=>trim(I('post.pr_description')),
			'pr_create_time'=>time()
		);
		$result = D('PurchaseRequirementHead')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 添加请购单表行
	**/
	public function addPrLine(){
		$data   = I('post.');
		$result = D('PurchaseRequirementLine')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑请购单表头
	**/
	public function editPrHead(){
		$data = I('post.');
		$map  = array(
				'pr_hid'=>$data['id']
			);
		$result = D('PurchaseRequirementHead')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 编辑请购单表行
	**/
	public function editPrLine(){
		$data = I('post.');
		$map  = array(
				'pr_lid'=>$data['id']
			);
		$result = D('PurchaseRequirementLine')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}



	/**
	* 删除请购单表头
	**/
	public function deletePrHead(){
		$id = I('get.id');
		$map= array(
			'pr_hid'=>$id
		);
		$item  =D('PurchaseRequirementHead')->where($map)->getfield('pr_order_item');
		//删除表行内容
		$temp  = array(
			'pr_order_item'=>$item
		);
		$deleteLine=D('PurchaseRequirementLine')->deleteData($temp);
		if($deleteLine){
			//删除表头内容
			$result = D('PurchaseRequirementHead')->deleteData($map);
			if($result){
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->error('删除失败');
			}
		}
	}


	/**
	* 删除请购单表行
	**/
	public function deletePrLine(){
		$id = I('get.id');
		$map= array(
			'pr_lid'=>$id
		);
		$result = D('PurchaseRequirementLine')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 请购单审核
	**/
	public function checkPr(){
		
	}









}
