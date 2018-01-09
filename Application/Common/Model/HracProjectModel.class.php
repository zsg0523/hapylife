<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac服务项目管理model
**/
class HracProjectModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=10,$field=''){
		if(empty($keyword)){
			$count=$model
			      ->alias('hp')
				  ->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
				  ->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
				  ->where($map)
				  ->where(array('hp.hpcid'=>$cid))
				  ->count();
		}else{
			$count=$model
			      ->alias('hp')
				  ->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
				  ->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
				  ->where($map)
				  ->where(array('hp_name'=>array('like','%'.$keyword.'%')))
				  ->count();
		}
		$page=new_page($count,$limit);
		//获取分页数据
		if(empty($keyword)){
			if(empty($field)){
				$list=$model
				 	->alias('hp')
	  			    ->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
	  			    ->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->alias('hp')
				    ->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
				    ->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
					->field($field)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}else{
			if(empty($field)){
				$list=$model
				 	->alias('hp')
	  			  	->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
	  			  	->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
					->where($map)
					->order($order)
					->where(array('hp_name'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->alias('hp')
				    ->join('left join __HRAC_PROJECTCLASS__ hpc ON hp.hpcid=hpc.hpcid')
				    ->join('left join __HRAC_CATEGORY__ hc ON hp.id=hc.id')
					->field($field)
					->order($order)
					->where(array('hp_name'=>array('like','%'.$keyword.'%')))
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