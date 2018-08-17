<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hapylife房间管理model
**/
class RoomModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				$count=$model
					  ->where($map)
					  ->where(array('name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}else{
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->alias('hr')
					  ->join('__TRAVEL__ ht ON hr.tid=ht.tid')
					  ->where(array('ht.tid'=>$cid))
					  ->count();
			}else{
				$count=$model
					  ->where($map)
					  ->alias('hr')
					  ->join('__TRAVEL__ ht ON hr.tid=ht.tid')
					  ->where(array('ht.tid'=>$cid))
					  ->where(array('name'=>array('like','%'.$keyword.'%')))
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
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->where(array('name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->where(array('name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}
		}else{
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->where(array('ht.tid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->where(array('ht.tid'=>$cid))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->where(array('ht.tid'=>$cid))
						->where(array('name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->alias('hr')
						->join('__TRAVEL__ ht ON hr.tid=ht.tid')
						->order($order)
						->where(array('ht.tid'=>$cid))
						->where(array('name'=>array('like','%'.$keyword.'%')))
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