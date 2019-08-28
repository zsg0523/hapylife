<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
* 官网控制器
**/
class WebController extends HomeBaseController{
	public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            $this->redirect('Home/Index/end');
        }
    }


	public function home(){
		$this->display();
	}



}