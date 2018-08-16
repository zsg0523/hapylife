<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*用户管理
**/
class UserController extends AdminBaseController{

	public function index(){
		$keyword = I('get.keyword');
		$assign  = D('User')->getAllData(D('User'),$keyword,$order='uid',$limit=50,$field='');
		$this->assign($assign);
		$this->assign('keyword',$keyword);
		$this->display();
	}

	/**
	* 删除用户
	**/
	public function deleteUser(){
		$id=I('get.id');
		$map=array(
			'uid'=>$id
			);
		$result=D('User')->deleteData($map);
		if($result){
			$this->redirect('Admin/User/index');
		}else{
			$this->error('Failed');
		}
	}

	/**
	* 添加用户
	**/
	public function addUser(){
		$data=I('post.');
		if ($data['account']=='') {
			$this->error('account can`t be null ');
		}elseif($data['password']==''){
			$this->error('password can`t be null ');
		}elseif($data['phone']==''){
			$this->error('phone can`t be null ');
		}else{
			$data['registertime'] = time();
			$data['password'] = md5($data['password']);
			$result=D('User')->addData($data);
			if($result){
				$this->redirect('Admin/User/index');
			}else{
				$this->error('Failed');
			}
		}
	}

	/**
	* 编辑用户
	**/
	public function editUser(){
		$data=I('post.');
		$map=array(
			'uid'=>$data['id']
			);
		$data['password'] = md5($data['password']);
		$result=D('User')->editData($map,$data);
		if($result){
			$this->redirect('Admin/User/index');
		}else{
			$this->error('Failed');
		}
	}

}