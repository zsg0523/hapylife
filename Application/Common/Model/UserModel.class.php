<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* Hapylife用户model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=100,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		//获取分页数据
		if(empty($keyword)){
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
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
					->where(array('CustomerID'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->where(array('CustomerID'=>array('like','%'.$keyword.'%')))
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