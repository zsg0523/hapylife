<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* �û�model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$keyword,$order='',$limit=50,$field=''){
		$count=$model
			  ->where(array('account|phone|email|sex|name'=>array('like','%'.$keyword.'%')))
			  ->count();
		$page=new_page($count,$limit);
		//��ȡ��ҳ����
		if(empty($field)){
			$list=$model
				->where(array('account|phone|email|sex|name'=>array('like','%'.$keyword.'%')))
				->order($order)
				->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->field($field)
				->where(array('account|phone|email|sex|name'=>array('like','%'.$keyword.'%')))
				->order($order)
				->limit($page->firstRow.','.$page->listRows)
				->select();
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
}