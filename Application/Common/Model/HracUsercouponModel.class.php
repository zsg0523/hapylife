<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac用户优惠券model
**/
class HracUsercouponModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				if($keyword=='nowflasekailenkoala'){
					$count = 0;
				}else{
					$count=$model
						  ->where($map)
						  ->alias('huc')
						  ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						  ->where(array('huc.huid'=>$keyword))
						  ->count();
					
				}
			}
		}else{
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->where(array('hcid'=>$cid))
					  ->count();
			}else{
				if($keyword=='nowflasekailenkoala'){
					$count = 0;
				}else{
					$count=$model
						  ->where($map)
						  ->alias('huc')
						  ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						  ->where(array('hcid'=>$cid))
						  ->where(array('huc.huid'=>$keyword))
						  ->count();
					
				}
			}
		}
		$page=new_page($count,$limit);
		//获取分页数据
		if($cid==0){
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('huc')
					    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
					    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('huc')
					    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
					    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					if($keyword=='nowflasekailenkoala'){
						$list = array();
					}else{
						$list=$model
							->where($map)
							->alias('huc')
						    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						    ->where(array('huc.huid'=>$keyword))
							->order($order)
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}else{
					if($keyword=='nowflasekailenkoala'){
						$list= array();
					}else{
						$list=$model
							->field($field)
							->alias('huc')
						    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						    ->where(array('huc.huid'=>$keyword))
							->order($order)
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}
			}
		}else{
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('huc')
					    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
					    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
					    ->where(array('huc.hcid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('huc')
					    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
					    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
					    ->where(array('huc.hcid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					if($keyword=='nowflasekailenkoala'){
						$list = array();
					}else{
						$list=$model
							->where($map)
							->alias('huc')
						    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						    ->where(array('huc.hcid'=>$cid))
						    ->where(array('huc.huid'=>$keyword))
							->order($order)
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}else{
					if($keyword=='nowflasekailenkoala'){
						$list = array();
					}else{
						$list=$model
							->field($field)
							->alias('huc')
						    ->join('__HRAC_USERS__ hu ON hu.huid=huc.huid')
						    ->join('__HRAC_COUPON__ hc ON hc.hcid=huc.hcid')
						    ->where(array('huc.hcid'=>$cid))
						    ->where(array('huc.huid'=>$keyword))
							->order($order)
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}
			}
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
}