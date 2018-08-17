<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* HapylifeCouponController
**/
class HapylifeCouponController extends HomeBaseController{
    // 获取hapylife用户信息
    public function userinfo(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        // $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        $result = M('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
        if(!empty($result['result'])){
            $this->ajaxreturn($result['result']);
        }
    }
}