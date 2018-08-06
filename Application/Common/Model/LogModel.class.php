<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* HapylifeÓÃ»§model
**/
class LogModel extends BaseModel{
	/**
	 * 获取积分日志
	 * @param  subject  $model  model对象
	 * @param  array    $word   搜索关键词
	 * @param  string   $order  排序规则
	 * @param  integer  $limit  每页数量
	 * @param  integer  $field  $field
	 * @return array            分页数据
	 */
	public function getAllLog($model,$word,$starttime,$endtime,$action,$type,$iuid,$order='',$limit=50,$field=''){
		$count = $model
				->where(array('iuid'=>$iuid,'content'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'action'=>array('in',$action),'type'=>$type))
				->count();
		$page = new_page($count,$limit);
		//获取分页数据
		$list=$model
			->where(array('iuid'=>$iuid,'content'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'action'=>array('in',$action),'type'=>$type))
			->order($order)
			->limit($page->firstRow.','.$page->listRows)
			->select();
		foreach ($list as $key => $value) {
			$arr[$key]                = $value;
			$arr[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
		}
		$data=array(
			'data'=>$arr,
			'page'=>$page->show()
			);
		return $data;
	}

}