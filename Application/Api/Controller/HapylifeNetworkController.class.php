<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class HapylifeNetworkController extends HomeBaseController{

	/**
    * 推荐网查询
    **/
    public function getUserBinary(){
        $account = I('post.CustomerID');
        $mape    = D('User')->where(array('EnrollerID'=>$account))->select();
        // p($account);
        p($mape);die;
    }

    /**
    * 修改推荐人
    **/
    public function againEnrollerID(){
        $CustomerID = I('post.CustomerID');
        $EnrollerID = I('post.EnrollerID');
        $user       = D('User')->where(array('CustomerID'=>$EnrollerID))->find();
        if($user){
            $mape   = D('User')->where(array('CustomerID'=>$CustomerID))->setField('EnrollerID',$EnrollerID);    
        }
        p($mape);die;
    }
    /**
    * 修改左右脚
    **/
    // public function againEnrollerID(){
    //     $CustomerID = I('post.CustomerID');
    //     $EnrollerID = I('post.EnrollerID');
    //     $user       = D('User')->where(array('CustomerID'=>$EnrollerID))->find();
    //     if($user){
    //         $mape   = D('User')->where(array('CustomerID'=>$CustomerID))->setField('EnrollerID',$EnrollerID);    
    //     }
    //     p($mape);die;
    // }
	
}