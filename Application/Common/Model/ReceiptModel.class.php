<?php
namespace Common\Model;
use Think\Model;
/**
* 订单model
**/
class ReceiptModel extends BaseModel{
	public function getAllData($model,$keyword,$order='',$limit=50,$field=''){
		$count=$model
			  ->where(array('irid|ir_uid|ir_receiptnum|ir_desc|ir_status|ir_productnum|ir_price|ir_name|ir_phone|ir_address|ir|paytype|is_delete|ir_createTime|ir_payTime'=>array('like','%'.$keyword.'%')))
			  ->count();
		$page=new_page($count,$limit);
		//获取分页数据
		if(empty($field)){
			$list=$model
				->join('user on receipt.ir_uid=user.uid')
				->where(array('irid|ir_uid|ir_receiptnum|ir_desc|ir_status|ir_productnum|ir_price|ir_name|ir_phone|ir_address|ir_paytype|ir_createTime|ir_payTime'=>array('like','%'.$keyword.'%')))
				->order($order)
				->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->field($field)
				->join('user on receipt.ir_uid=user.uid')
				->where(array('irid|ir_uid|ir_receiptnum|ir_desc|ir_status|ir_productnum|ir_price|ir_name|ir_phone|ir_address|ir|paytype|is_delete|ir_createTime|ir_payTime'=>array('like','%'.$keyword.'%')))
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