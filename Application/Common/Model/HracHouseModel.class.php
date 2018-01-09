<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac房间列表model
**/
class HracHouseModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				$count=$model
				      ->alias('hh')
					  ->where($map)
					  ->where(array('hh.hh_numbeer'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}else{
			if(empty($keyword)){
				$count=$model
				      ->alias('hh')
					  ->where($map)
					  ->where(array('hh.sid'=>$cid))
					  ->count();
			}else{
				$count=$model
				      ->alias('hh')
					  ->where($map)
					  ->where(array('hh.sid'=>$cid))
					  ->where(array('hh.hh_numbeer'=>array('like','%'.$keyword.'%')))
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
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh_numbeer'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh_numbeer'=>array('like','%'.$keyword.'%')))
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
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh.sid'=>$cid))
						->where(array('hh_numbeer'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hh')
						->join('__HRAC_SHOP__ hs ON hh.sid=hs.sid')
						->where(array('hh.sid'=>$cid))
						->where(array('hh_numbeer'=>array('like','%'.$keyword.'%')))
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