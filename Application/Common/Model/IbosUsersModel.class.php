<?php
namespace Common\Model;
use Think\Model;
/**
* nulife用户model
**/
class IbosUsersModel extends BaseModel{
	/**
	* 获取所有用户列表
	**/
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
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->field($field)
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
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