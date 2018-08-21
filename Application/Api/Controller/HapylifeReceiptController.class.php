<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 产品订单 首单 升级 月费
**/
class HapylifeReceiptController extends HomeBaseController{

	/**
	*订单列表
	**/
	public function receiptList(){
		$iuid      = I('post.iuid');
		$map  = array(
				'riuid'=>$iuid,
                'ir_status'=>array('in','2,3,4,5,202')
			);
		$receipt = M('Receipt')
					->alias('r')
					->join('hapylife_receiptlist hr on r.ir_receiptnum = hr.ir_receiptnum')
					->join('hapylife_product hp on hr.ipid=hp.ipid')
					->where($map)
					->select();
		if($receipt){
			$this->ajaxreturn($receipt);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}
	/**
	*订单详情
	**/
	public function receiptDetails(){
		$receiptnum = I('post.ir_receiptnum');
		$receipt    = D('Receipt')
		            ->join('hapylife_product on hapylife_product.ipid = hapylife_receipt.ipid')
		            ->where(array('ir_receiptnum'=>$receiptnum))
		            ->find();
		$receipt['ir_date'] = date('Y-m-d H:i:s',$receipt['ir_date']);
		if($receipt){
			$this->ajaxreturn($receipt);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}
	/**
	*删除订单
	**/
	public function deleteReceipt(){
		$receiptnum = I('post.ir_receiptnum');
		$receipt    = D('Receipt')->where(array('ir_receiptnum'=>$receiptnum))->setField('is_delete',1);
		if($receipt){
			$data['status'] = 1;
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}
}