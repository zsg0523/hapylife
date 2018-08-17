<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 激活记录model
**/
class ActivationModel extends BaseModel{
	/**
	* 添加激活记录
	**/
	public function addAtivation($OrderDate,$iuid,$receiptnum){
		//查看是否有用户的激活记录
        $activaDate = D('Activation')->where(array('iuid'=>$iuid,'is_tick'=>1))->order('datetime desc')->getfield('datetime');
        if(empty($activaDate)){
            $activa = $OrderDate;
        }else{
            $activa = $activaDate;
        }
        $day = date('d',strtotime($OrderDate));
        if($day>=28){
            $allday = 28;
        }else{
            $allday = $day;
        }
        $ddd = $allday-1;
        if($ddd>=10){
            $oneday = $ddd;
        }else{
            if($ddd==0){
                $oneday = date('d', mktime(0,0,0,date('n'),date('t'),date('Y')));
            }else{
                $oneday = '0'.$ddd;     
            }
        }
        $where      = array('iuid'=>$iuid,'datetime'=>date("Y-m",strtotime("+1 month",strtotime($activa))));
        $activation =D('Activation')->where($where)->find();
        if($activation){
            $action= array(
                'hatime'       =>date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日',
                'endtime'      =>date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日',
                'ir_receiptnum'=>$receiptnum,
                'is_tick'      =>1
            );
            $save  = D('Activation')->where($where)->save($action);
        }else{
            $action= array(
                'hatime'       =>date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日',
                'endtime'      =>date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日',
                'ir_receiptnum'=>$receiptnum,
                'is_tick'      =>1,
                'iuid'         =>$iuid,
                'datetime'     =>date("Y-m",strtotime("+1 month",strtotime($activa)))
            );
            $save  = D('Activation')->add($action);
        }
        return $save; 		
	}

	
}