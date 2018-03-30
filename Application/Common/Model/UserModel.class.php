<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* HapylifeÓÃ»§model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$map,$word='',$order='',$limit=50,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		
		if(empty($word)){
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->where($map)
					->field($field)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}else{
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->where($map)
					->field($field)
					->order($order)
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