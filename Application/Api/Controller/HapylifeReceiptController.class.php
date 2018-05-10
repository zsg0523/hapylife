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
		$ir_status = I('post.ir_status')-1;
		if($ir_status==-1){
			$where = array('iuid'=>$iuid,'is_delete'=>0);
		}else{
			$where = array('iuid'=>$iuid,'ir_status'=>$ir_status,'is_delete'=>0);
		}
		$receipt = D('Receipt')->where($where)->order('ir_date desc')->select();
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