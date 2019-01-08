<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 通告model
**/
class NoticeModel extends BaseModel{
	/**
	 * 获取通告数据
	 * @param  subject  $model  model对象
	 * @param  string   $order  排序规则
	 * @param  integer  $limit  每页数量
	 * @param  integer  $field  $field
	 * @return array            分页数据
	 */
	public function getAll($model,$order='',$limit=50,$field=''){
		$count = $model
				->count();
		$page = new_page($count,$limit);
		//获取分页数据
		$list=$model
			->order($order)
			->limit($page->firstRow.','.$page->listRows)
			->select();
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
		);
		return $data;
	}
}