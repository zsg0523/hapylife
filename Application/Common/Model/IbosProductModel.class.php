<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 商品model
**/
class IbosProductModel extends BaseModel{
	/**
	* 获取所有商品列表
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
				->alias('ip')
				->join('__IBOS_CATEGORY__ ic ON ip.icid=ic.id')
				->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->field($field)
				->order($order)
				->alias('ip')
				->join('__IBOS_CATEGORY__ ic ON ip.icid=ic.id')
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