<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 修改用户积分
**/
class HapylifeCouponController extends HomeBaseController{
	/**
	* 接受nulife数据，修改hapylife用户积分
	**/ 
    public function edit_point(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        // $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        $result = M('User')->where(array('CustomerID'=>$data['customerid']))->save($data);
    }

    /**
	* 后台添加优惠券获取用户信息
	**/ 
	public function userinfo(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    // $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
	    $userinfo = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
	    if($userinfo){
	    	$userinfo['status'] = 1;
	    	$this->ajaxreturn($userinfo);
	    }
	}

	/**
	* 接收前台数据，返回用户信息
	**/ 
	public function callBack(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    // $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
	    $userinfo = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
	    if($userinfo){
	    	$userinfo['status'] = 1;
	    	$this->ajaxreturn($userinfo);
	    }
	}

	/**
	* 接收前台数据，修改用户积分
	**/
	public function editPoint(){
		$jsonStr = file_get_contents("php://input");
	    //写入服务器日志文件
	    // $log     = addUsaLog($jsonStr);
	    $data    = json_decode($jsonStr,true);
	    $result = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->save($data);
	    if($result){
	    	$result['status'] = 1;
	    	$this->ajaxreturn($result);
	    }
	}
 
}