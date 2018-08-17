<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* Receipt Controller
**/
class ReceiptController extends AdminBaseController{

	/**
	* 订单列表
	**/
		public function index(){
		$keyword = I('get.keyword');
		$user    = D('User')->getAllData(D('User'),$keyword,$order='irid',$limit=50,$field='');
		$assign  = D('Receipt')->getAllData(D('Receipt'),$keyword,$order='irid',$limit=50,$field='');
		$this->assign($user);
		$this->assign($assign);
		$this->assign('keyword',$keyword);
		$this->display();
	}
	
	/**
	* 添加订单
	**/
	public function addReceipt(){
		$data = I('post.');
		$data['ir_createTime'] = strtotime($data['ir_createTime']);
		$data['ir_payTime'] = strtotime($data['ir_payTime']);
		$result = D('Receipt')->addData($data);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Add Failed');
		}
	}

	/**
	* 编辑订单
	**/
	public function editReceipt(){
		$data = I('post.');
		$data['ir_createTime'] = strtotime($data['ir_createTime']);
		$data['ir_payTime'] = strtotime($data['ir_payTime']);
		$map  = array(
				'irid'=>$data['id']
			);
		$result = D('Receipt')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Edit Failed');
		}
	}

	/**
	* 删除订单
	**/
	public function deleteReceipt(){
		$id = I('get.id');
		$map= array(
			'irid'=>$id
		);
		$result=D('Receipt')->where($map)->setfield('is_delete',1);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Delete Failed');
		}
	}


	/**
	* 订单详情列表
	**/
	public function receiptlist(){
		$id  = I('get.id');
		$Receiptnum = D('Receipt')->where(array('irid'=>$id))->getfield('ir_receiptnum'); 
		$map = array(
			'ir_receiptnum'=>$id
		);
		$assign=D('Receiptlist')->getPage(D('Receiptlist'),$map);
		$this->assign('Receiptnum',$Receiptnum);
		$this->assign('ir_receiptnum',$id);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加订单详情
	**/
	public function addReceiptlist(){
		$data=I('post.');
		$result=D('Receiptlist')->addData($data);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Add Failed');
		}
	}


	/**
	* 编辑订单详情
	**/
	public function editReceiptlist(){
		$data=I('post.');
		$map=array(
			'irlid'=>$data['id']
			);
		$result=D('Receiptlist')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Edit Failed');
		}
	}

	/**
	* 删除订单详情
	**/
	public function deleteReceiptlist(){
		$id=I('get.id');
		$map=array(
			'irlid'=>$id
			);
		$result=D('Receiptlist')->deleteData($map);
		if($result){
			$this->redirect('Admin/Receipt/index');
		}else{
			$this->error('Delete Failed');
		}
	}




}