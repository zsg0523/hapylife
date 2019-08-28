<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 产品订单 首单 升级 月费
**/
class HapylifeReceiptController extends HomeBaseController{
	public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            die;
        }
    }
	/**
	*订单列表
	**/
	public function receiptList(){
		$iuid      = I('post.iuid');
		$map  = array(
				'riuid'=>$iuid,
                'ir_status'=>array('in','0,1,2,3,4,5,202'),
                'is_delete'=>0
			);
		$receipt = M('Receipt')
					->alias('r')
					->join('hapylife_receiptlist hr on r.ir_receiptnum = hr.ir_receiptnum')
					->join('hapylife_product hp on hr.ipid=hp.ipid')
					->where($map)
					->order('irid desc')
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
	/**
	*订单详情
	**/
	public function getReceiptSon(){
		$receiptnum = I('post.ir_receiptnum');
		$receiptson = D('receiptson')->where(array('ir_receiptnum'=>$receiptnum,'status'=>2))->select();
		foreach ($receiptson as $key => $value) {
			$receiptson[$key]['paytime'] = date('Y-m-d H:i:s',$value['paytime']);
			$receiptson[$key]['keynum']  = $key+1;
		}
		if($receiptson){
			$this->ajaxreturn($receiptson);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}
}