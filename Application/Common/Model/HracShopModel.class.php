<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * Hrac门店model
 */
class HracShopModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=10,$field=''){
		if(empty($keyword)){
			$count=$model
				  ->where($map)
				  ->count();
		}else{
			$count=$model
			      ->alias('hs')
				  ->where($map)
				  ->where(array('hs.s_name'=>array('like','%'.$keyword.'%')))
				  ->count();
		}	
		$page=new_page($count,$limit);
		//获取分页数据
		if(empty($keyword)){
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->alias('hs')
					->join('__HRAC_AREA__ ha ON hs.aid=ha.aid')
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->alias('hs')
					->join('__HRAC_AREA__ ha ON hs.aid=ha.aid')
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}else{
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->alias('hs')
					->join('__HRAC_AREA__ ha ON hs.aid=ha.aid')
				    ->where(array('hs.s_name'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->alias('hs')
					->join('__HRAC_AREA__ ha ON hs.aid=ha.aid')
				    ->where(array('hs.s_name'=>array('like','%'.$keyword.'%')))
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
