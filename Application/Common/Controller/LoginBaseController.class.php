<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * Login基类控制器
 */
class LoginBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		$check_login = check_login();
		if(!$check_login){
			$this->error('请先登录',U('Home/Index/login'));
		}
	}




}

