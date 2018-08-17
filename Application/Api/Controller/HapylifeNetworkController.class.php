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
        $data    = D('User')->where(array('EnrollerID'=>$account,'CustomerID'=>array('like','%'.'HPL'.'%')))->select();
        // p($account);
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }
    }

    /**
    * 推荐--下级信息
    **/
    public function getUserInfo(){
        $account = I('post.CustomerID');
        $data    = D('User')->where(array('CustomerID'=>$account))->find();
        // p($account);
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }
    }
    /**
    * 修改左右脚及双轨id
    **/
    public function editUserInfo(){
        $CustomerID = I('post.CustomerID');
        $para       = I('post.para');
        $paravalue  = I('post.paravalue');
        switch ($para) {
            case 'Placement':
                $data['Placement'] = $paravalue;
                break;
            case 'SponsorID':
                $data['SponsorID'] = $paravalue;
                break;
        }
        $mape['CustomerID'] = $CustomerID;
        $save = D('User')->where($mape)->save($data);  
        if($save){
            $data['status']=1;
            $this->ajaxreturn($data); 
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }  
    }
	
}