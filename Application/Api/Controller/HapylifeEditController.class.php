<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 添加用户积分
**/
class HapylifeEditController extends HomeBaseController{
    /**
    * 添加用户积分
    **/ 
    public function editStatus(){
        $CustomerID = json_decode(htmlspecialchars_decode(strtoupper(trim(I('post.customerid')))));
        $map = implode(',',$CustomerID);
        p($map);
    }
}