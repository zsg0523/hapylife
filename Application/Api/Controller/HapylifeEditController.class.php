<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 修改用户状态
**/
class HapylifeEditController extends HomeBaseController{
    /**
    * 修改用户状态 -> 购买200月费包
    **/ 
    public function editStatus(){
        $CustomerID = json_decode(htmlspecialchars_decode(trim(I('post.customerid'))));
        foreach($CustomerID as $key=>$value){
            $id = strtoupper($value);
            $result = M('User')->where(array('CustomerID'=>$id))->setField('status',1);
        }
        if($result){
            $sample['status'] = 1;
            $sample['msg'] = '修改成功';
            $this->ajaxreturn($sample);
        }else{
            $sample['status'] = 0;
            $sample['msg'] = '修改失败';
            $this->ajaxreturn($sample);
        }
    }
}