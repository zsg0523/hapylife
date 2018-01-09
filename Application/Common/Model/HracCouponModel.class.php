<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac优惠券model
**/
class HracCouponModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				$count=$model
					  ->where($map)
					  ->where(array('hc_name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}else{
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->alias('hc')
					  ->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
					  ->where(array('hp.hpid'=>$cid))
					  ->count();
			}else{
				$count=$model
					  ->where($map)
					  ->alias('hc')
					  ->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
					  ->where(array('hp.hpid'=>$cid))
					  ->where(array('hc_name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}
		$page=new_page($count,$limit);
		//获取分页数据
		if($cid==0){
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->where(array('hc_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->where(array('hc_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}
		}else{
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->where(array('hp.hpid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->where(array('hp.hpid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->where(array('hp.hpid'=>$cid))
						->where(array('hc_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hc')
						->join('__HRAC_PROJECT__ hp ON hc.hpid=hp.hpid')
						->join('__HRAC_CATEGORY__ hcat ON hcat.id=hc.id')
						->order($order)
						->where(array('hp.hpid'=>$cid))
						->where(array('hc_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
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