<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 奖金核算
**/
class HapylifeBonusController extends HomeBaseController{
	public function index(){
		$map = array(
			'iuid'         =>3,
			'sponsorid'    =>35862601,
			'placement'    =>'right',
			'create_time'  =>time(),
			'create_month' =>date('Y-m',time()),
		);
		$add = M('weekbonus')->add($map);
		if($add){
			$this->ajaxreturn($map);
		}else{
			$data['status'] = 0;
			$data['message']= '写入失败';
			$this->ajaxreturn($data);
		}
	}

	public function weekbonus(){
		
	}


}