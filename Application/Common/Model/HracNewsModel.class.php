<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * Hrac新闻model
 */
class HracNewsModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=10,$field=''){
		if(empty($keyword)){
			$count=$model
				  ->where($map)
				  ->count();
		}else{
			$count=$model
			      ->alias('hn')
				  ->where($map)
				  ->where(array('hn.hn_title'=>array('like','%'.$keyword.'%')))
				  ->count();
		}	
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
					->alias('hn')
				    ->where(array('hn.hn_title'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->alias('hn')
				    ->where(array('hn.hn_title'=>array('like','%'.$keyword.'%')))
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
