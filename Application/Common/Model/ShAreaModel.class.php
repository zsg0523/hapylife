<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 省市区model
 */
class ShAreaModel extends BaseModel{
    public function ShArea($pid){
    	if($pid){
            $data = M('ShArea')->where(array('pid'=>$pid))->select();
        }else{
            $data = M('ShArea')->where(array('pid'=>0))->select();
        }
        return $data;
    }
}
