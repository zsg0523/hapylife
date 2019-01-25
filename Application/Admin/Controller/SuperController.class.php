<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* super控制器
**/
class SuperController extends AdminBaseController{
	/**
	* 超级查询
	**/ 
	public function index(){
		$customerid = strtoupper(I('get.customerid'));
		$assign = D('User')->getIndexPage(D('User'),$customerid);
		// p($assign);
		$this->assign($assign);
		$this->assign('customerid',$customerid);
		$this->display();
	}
}