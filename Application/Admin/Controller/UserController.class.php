<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 后台首页控制器
 */
class UserController extends AdminBaseController{

	/**
	* 用户列表
	**/
	public function index(){
		// //账户昵称搜索
		$word = I('post.word');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'hu_nickname'=>$word
			);
		}
		$assign=D('IbosUsers')->getAllData(D('IbosUsers'),$map,$order="hu_nickname");
		// //hrac门店信息
		$HracShop   =D('HracShop')->select();
		$HracGrage 	=D('HracGrade')->select();
		$this->assign($assign);
		$this->assign('shop',$HracShop);
		$this->assign('grade',$HracGrage);
		$this->display();
	}


	/**
	* 添加用户
	**/
	public function add_users(){
		$data = I('post.');
		$data['iu_registertime'] = date('Y-m-d');
		$data['iu_password']	 = md5('123456');
		//IbosUsers建立账户
		$ibosuser = D('IbosUsers')->add($data);
		$user     = D('IbosUsers')->where(array('hu_nickname'=>$data['hu_nickname']))->find();
		if($user){
			//HracUsers建立账户
			$map = array(
				'iuid'		=>$user['iuid'],
				'hu_type'	=>$data['hu_type']
			);
			$hracuser = D('HracUsers')->add($map);
			if($hracuser && !empty($data['hu_type'])){
				//HracDocter建立职员信息
				$tmp = array(
					'sid'		=>$data['sid'],
					'hd_name'	=>$data['hu_nickname'],
					'hd_phone'	=>$data['hu_phone'],
					'hd_type'	=>$data['hu_type'],
					'hgid'		=>$data['hgid']
				);
				$hracdocter = D('HracDocter')->add($tmp);
				if($hracdocter){
					$this->redirect('Admin/User/index');
				}else{
					$this->error('添加失败');
				}
			}else{
				$this->redirect('Admin/User/index');
			}
		}
	}



	/**
	* 用户编辑
	**/
	public function edit_users(){
		$data=I('post.');
		$map=array(
			'iuid'=>$data['id']
		);
		//修改头像
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hu_photo']=C('WEB_URL').$upload['name'];
		}
		$result = D('IbosUsers')->editData($map,$data);
		if($result){
			$this->redirect('Admin/User/index');
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 用户锁定
	**/
	public function lock_users(){
		$data=I('get.');
		$map =array(
			'iuid'=>$data['id']
			);
		$result=D('IbosUsers')->editData($map,$data);
		if($result){
			$this->redirect('Admin/User/index');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 用户删除
	**/
	public function delete_users(){
		$id=I('get.id');
		$map=array(
			'iuid'=>$id
			);
		$nickname =D('IbosUsers')->where($map)->getfield('hu_nickname');
		$hrac_user=D('HracUsers')->where($map)->find();
		$hd_name  =D('HracDocter')->where(array('hd_name'=>$nickname))->find();
		//删除Hrac职员信息
		if(!empty($hd_name)){
			$hracdocter = D('HracDocter')->where(array('hd_name'=>$nickname))->delete();
		}
		//删除Hrac金卡合伙人信息
		if(!empty($hrac_user)){
			$hracuser  = D('HracUsers')->where($map)->delete();
		}
		//删除IbosUser会员信息
		$result = D('IbosUsers')->where($map)->delete();
		if($result && $hracuser && $hracdocter){
			$this->redirect('Admin/User/index');
		}else{
			$this->error('删除失败');
		}
	}




}
