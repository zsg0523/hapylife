<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac��Ϣ�������model
**/
class HracInfoclassModel extends BaseModel{
	public function getAllData($model,$map,$order='',$limit=10,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		//��ȡ��ҳ����
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