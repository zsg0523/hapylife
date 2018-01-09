<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac������Ŀ����model
**/
class HracUsersModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=15,$field=''){
		if(empty($keyword)){
			$count=$model
				  ->alias('hu')
			      ->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
				  ->where($map)
				  ->count();
		}else{
			$count=$model
				  ->alias('hu')
			      ->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
				  ->where($map)
				  ->where(array('hu_nickname'=>array('like','%'.$keyword.'%')))
				  ->count();
		}
		$page=new_page($count,$limit);
		//��ȡ��ҳ����
		if(empty($keyword)){
			if(empty($field)){
				$list=$model
				    ->alias('hu')
			        ->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->alias('hu')
			        ->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
					->field($field)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}else{
			if(empty($field)){
				$list=$model
					->alias('hu')
					->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
					->where($map)
					->order($order)
					->where(array('hu_nickname'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->alias('hu')
					->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')
					->field($field)
					->order($order)
					->where(array('hu_nickname'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
}