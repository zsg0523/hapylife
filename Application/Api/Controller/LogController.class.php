<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 日志记录
**/
class LogController extends HomeBaseController{

	public function index(){
		// \Think\Log::record('测试日志生成信息');
		$time = array('2018-06-06 18:37:39','2018-06-06 19:25:28
','2018-06-07 09:50:14');
		foreach ($time as $key => $value) {
			$data['a'][] = strtotime($value);
			$data['b'][] = strtotime($value)-5400;
		}
		p($data);die;
	}





}