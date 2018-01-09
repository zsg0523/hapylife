<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac职员管理model
**/
class HracDocterModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				$count=$model
				      ->alias('hd')
					  ->where($map)
					  ->where(array('hd.hd_name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}else{
			if(empty($keyword)){
				$count=$model
				      ->alias('hd')
					  ->where($map)
					  ->where(array('hd.sid'=>$cid))
					  ->count();
			}else{
				$count=$model
				      ->alias('hd')
					  ->where($map)
					  ->where(array('hd.sid'=>$cid))
					  ->where(array('hd.hd_name'=>array('like','%'.$keyword.'%')))
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
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}
		}else{
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd.sid'=>$cid))
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hd')
						->join('left join __HRAC_SHOP__ hs ON hd.sid=hs.sid')
						->where(array('hd.sid'=>$cid))
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
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