<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac消息分类管理model
**/
class HracInfoclassModel extends BaseModel{
	public function getAllData($model,$map,$order='',$limit=10,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		//获取分页数据
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
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
}