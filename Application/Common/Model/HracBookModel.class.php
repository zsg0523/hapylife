<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* hrac排班model
**/
class HracBookModel extends BaseModel{
	public function getAllData($model,$map,$cid,$keyword='',$order='',$limit=10,$field=''){
		if($cid==0){
			if(empty($keyword)){
				$count=$model
					  ->where($map)
					  ->count();
			}else{
				$count=$model
					  ->alias('hc')
					  ->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
					  ->where($map)
					  ->where(array('hd.hd_name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}else{
			if(empty($keyword)){
				$count=$model
				      ->alias('hc')
					  ->where($map)
					  ->where(array('hc.sid'=>$cid))
					  ->count();
			}else{
				$count=$model
				      ->alias('hc')
					  ->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
					  ->where($map)
					  ->where(array('hc.sid'=>$cid))
					  ->where(array('hd.hd_name'=>array('like','%'.$keyword.'%')))
					  ->count();
			}
		}
		$page=new_page($count,$limit);
		//获取分页数据
		if($cid==0){
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}
		}else{
			if(empty($keyword)){
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hc.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hc.sid'=>$cid))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}else{
				if(empty($field)){
					$list=$model
						->where($map)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hc.sid'=>$cid))
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
						->field($field)
						->order($order)
						->alias('hc')
						->join('__HRAC_SHOP__ hs ON hc.sid=hs.sid')
						->join('__HRAC_DOCTER__ hd ON hc.hdid=hd.hdid')
						->where(array('hc.sid'=>$cid))
						->where(array('hd_name'=>array('like','%'.$keyword.'%')))
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}
			}
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
}