<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * wv账号重置HPL密码
 **/
class ResetController extends AdminBaseController{
	/**
	 * wv账号重置HPL密码
	 * 列表
	 **/
	public function reset(){
		//账户昵称搜索
		$p = I('get.p',1);
		$word = trim(I('get.word'));
		if($word){
			$list = M('User')->where(array('customerid'=>$word))->select();
		}else{
			$list = M('User')->select();
		}
		foreach($list as $key=>$value){
			if(strlen($value['customerid']) == 8){
				$data[] = $value;
			}
		}
		krsort($data);
		$assign = pages($data,$p,20);
		$this->assign($assign);
		$this->display();
	}

	/**
	* wv账号重置HPL密码
	**/ 
	public function resetPsd(){
		$customerid = I('get.customerid');
		$userinfo = M('User')->where(array('customerid'=>$customerid))->find();
		$resetPsd = array(
			'PassWord' => md5(12345678),
			'WvPass' => '12345678',
		);
		if($userinfo['password'] == $resetPsd['PassWord'] || $userinfo['wvpass'] == $resetPsd['WvPass']){
			$this->error('重置密码已为：12345678',U('Admin/Reset/reset'));
		}else{
			$result = M('User')->where(array('customerid'=>$customerid))->save($resetPsd);
			if($result){
				$this->success('重置成功',U('Admin/Reset/reset'));
			}else{
				$this->error('重置失败',U('Admin/Reset/reset'));
			}
		}
	}

	/**
	* 删除wv账号
	**/ 
	public function delectAccount(){
		$customerid = I('get.customerid');
		$result = M('User')->where(array('CustomerID'=>$customerid))->delete();
		if($result){
			$this->success('删除成功',U('Admin/Reset/reset'));
		}else{
			$this->error('删除失败',U('Admin/Reset/reset'));
		}
	}

	/**
	 * hpl账号重置密码
	 * 列表
	 **/
	public function resetHpl(){
		//账户昵称搜索
		$p = I('get.p',1);
		$word = trim(I('get.word'));
		if($word){
			$list = M('User')->where(array('customerid'=>$word))->select();
		}else{
			$list = M('User')->select();
		}
		foreach($list as $key=>$value){
			if(strlen($value['customerid']) != 8){
				$data[] = $value;
			}
		}
		krsort($data);
		$assign = pages($data,$p,20);
		$this->assign($assign);
		$this->display();
	}

}
