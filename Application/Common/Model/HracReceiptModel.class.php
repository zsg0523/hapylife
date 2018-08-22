<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac预约单model
**/
class HracReceiptModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=25,$field=''){
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
						  ->alias('hr')
						  ->where($map)
						  ->where(array('hr.huid'=>$keyword))
						  ->count();
					
				}
			}
		}else{
			if(empty($keyword)){
				$count=$model
				      ->alias('hr')
					  ->where($map)
					  ->where(array('hr.sid'=>$cid))
					  ->count();
			}else{
				if($keyword=='nowflasekailenkoala'){
					$count = 0;
				}else{
					$count=$model
					      ->alias('hr')
						  ->where($map)
						  ->where(array('hr.sid'=>$cid))
						  ->where(array('hr.huid'=>$keyword))
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
						->order($order)
						->alias('hr')
						->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
						->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
						->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hr')
						->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
						->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
						->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
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
							->order($order)
							->alias('hr')
							->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
							->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
							->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
							->where(array('hr.huid'=>$keyword))
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}else{
					if($keyword=='nowflasekailenkoala'){
						$list = array();
					}else{
						$list=$model
							->field($field)
							->order($order)
							->alias('hr')
							->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
							->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
							->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
							->where(array('hr.huid'=>$keyword))
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
						->order($order)
						->alias('hr')
						->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
						->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
						->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
						->where(array('hr.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hr')
						->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
						->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
						->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
						->where(array('hr.sid'=>$cid))
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
							->order($order)
							->alias('hr')
							->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
							->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
							->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
							->where(array('hr.sid'=>$cid))
							->where(array('hr.huid'=>$keyword))
							->limit($page->firstRow.','.$page->listRows)
							->select();
					}
				}else{
					if($keyword=='nowflasekailenkoala'){
						$list = array();
					}else{
						$list=$model
							->field($field)
							->order($order)
							->alias('hr')
							->join('__HRAC_SHOP__ hs ON hr.sid=hs.sid')
							->join('__HRAC_PROJECT__ hp ON hr.hpid=hp.hpid')
							->join('left join __HRAC_HOUSE__ hh ON hr.hhid=hh.hhid')
							->where(array('hr.sid'=>$cid))
							->where(array('hr.huid'=>$keyword))
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