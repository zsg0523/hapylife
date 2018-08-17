<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* staff Controller 员工信息控制器
**/
class StaffController extends AdminBaseController{

	/**
	* 员工列表
	**/
	public function index(){
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'account'=>$word
			);
		}
		$assign=D('Staff')->getPage(D('Staff'),$map,$order="sid");
		$this->assign($assign);
		$this->display();
	}


	/**
	* 添加员工信息
	**/
	public function addStaff(){
		$data   = I('post.');
		//生成自增account
		$Staff = D('Staff')->where(array('account'=>array('like','%'.'IS'.'%')))->order('sid desc')->find();
        if($Staff){
            $name = substr($Staff['account'],2);
            $data['account'] = 'IS'.($name+1);
        }else{
            $data['account'] = 'IS10000001';
        }
		$data['registertime']   = time();
		$result = D('Staff')->addData($data);
		if($result){
			$this->redirect('Admin/Staff/index');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑员工信息
	**/
	public function editStaff(){
		$data = I('post.');
		$map  = array(
				'sid'=>$data['id']
			);
		$result = D('Staff')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除员工信息
	**/
	public function deleteStaff(){
		$id = I('get.id');
		$map= array(
			'sid'=>$id
		);
		$result = D('Staff')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}



















































}