<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 修改用户积分
**/
class HapylifeCouponController extends HomeBaseController{
    public function edit_point(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        // $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        $result = M('User')->where(array('CustomerID'=>$data['customerid']))->save($data);
    }
}