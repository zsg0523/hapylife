<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* 出纳核对采购 收货 出票控制器
**/
class CheckController extends AdminBaseController{

	public function index(){
		//采购表头数据信息
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('PurchaseOrderHead')->getPage(D('PurchaseOrderHead'),$word,$starttime,$endtime);
		foreach ($assign['data'] as $key => $value) {
			$temp[$key]              = $value;
			//采购员详情
			$temp[$key]['buyer']     = D('staff')->where(array('sid'=>$value['po_order_buyer']))->find();
			//验货员详情
			$temp[$key]['receiver']  = D('staff')->where(array('sid'=>$value['po_order_receiver']))->find();
			//采购单详情
			$temp[$key]['poDetail']  = D('PurchaseOrderLine')
										->alias('pol')
										->join('product p on pol.pid=p.pid')
	        							->join('vendor v on p.vid=v.vid')
										->where(array('po_order_item'=>$value['po_order_item']))
										->select();
			if($temp[$key]['poDetail'] !== null){
				foreach ($temp[$key]['poDetail'] as $k => $v){
					//采购单采购物料总数
					$temp[$key]['poQuantityTotal'] += $v['po_product_quantity'];
					//采购物料总价
					$temp[$key]['poTotalPrice']    += $v['po_product_quantity']*$v['product_price'];
				}
			}

			//验货单详情
			$temp[$key]['rsvDetail'] = D('RsvShipmentHead')->where(array('rs_po_item'=>$value['po_order_item']))->select();
			if($temp[$key]['rsvDetail'] !== null){
				foreach ($temp[$key]['rsvDetail'] as $k => $v){
					$temp[$key]['rsvDetail'][$k]['rsvDetailList'] = D('RsvShipmentLine')
																	->alias('rsvl')
																	->join('product p on rsvl.pid=p.pid')
							            							->join('vendor v on p.vid=v.vid')
																	->where(array('rsl_item'=>$v['rs_item']))
																	->select();
					foreach ($temp[$key]['rsvDetail'][$k]['rsvDetailList'] as $m => $n) {
						//验货单验货明细统计
						$temp[$key]['rsvDetail'][$k]['rsvTotalQuantity']             += $n['rs_quantity'];
						$temp[$key]['rsvDetail'][$k]['rsvTotalEnableQuantity']       += $n['rs_enable_quantity'];
						$temp[$key]['rsvDetail'][$k]['rsvTotalDisableQuantity']      += $n['rs_disable_quantity'];
						$temp[$key]['rsvDetail'][$k]['rsvTotalQuantityPrice']        += $n['rs_quantity']*$n['product_price'];
						$temp[$key]['rsvDetail'][$k]['rsvTotalEnableQuantityPrice']  += $n['rs_enable_quantity']*$n['product_price'];
						$temp[$key]['rsvDetail'][$k]['rsvTotalDisableQuantityPrice'] += $n['rs_disable_quantity']*$n['product_price'];
					}
					//采购单对应验货总计
					$temp[$key]['po_rsvTotalQuantity']          += $temp[$key]['rsvDetail'][$k]['rsvTotalQuantity'];
					$temp[$key]['rsvTotalEnableQuantity']       += $temp[$key]['rsvDetail'][$k]['rsvTotalEnableQuantity'];
					$temp[$key]['rsvTotalDisableQuantity']      += $temp[$key]['rsvDetail'][$k]['rsvTotalDisableQuantity'];
					$temp[$key]['rsvTotalQuantityPrice']        += $temp[$key]['rsvDetail'][$k]['rsvTotalQuantityPrice'];
					$temp[$key]['rsvTotalEnableQuantityPrice']  += $temp[$key]['rsvDetail'][$k]['rsvTotalEnableQuantityPrice'];
					$temp[$key]['rsvTotalDisableQuantityPrice'] += $temp[$key]['rsvDetail'][$k]['rsvTotalDisableQuantityPrice'];
				}
			}
			//总价差额
			$temp[$key]['balancePrice'] = $temp[$key]['poTotalPrice']-$temp[$key]['rsvTotalQuantityPrice'];
			if($temp[$key]['balancePrice'] === 0){
				$temp[$key]['is_cashier'] = 1;
			}else{
				$temp[$key]['is_cashier'] = 0;
			}
		}
		// p($temp);die;
		if($excel == 'excel'){
			$export_excel = D('PurchaseOrderHead')->export_excel($assign['data']);
		}else{
			$assign = array(
				'data'=>$temp,
				'page'=>$poList['page']
			);
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('staffList',$staffList);
			$this->assign($assign);
			$this->display();
		}
	}

	/**
	* 核对订单
	**/
	public function checkItem(){
		$po_order_item = trim(I('get.po_order_item'));
		$po = D('PurchaseOrderHead')->where(array('po_order_item'=>$po_order_item))->find();
		//采购员详情
		$temp['buyer']     = D('staff')->where(array('sid'=>$po['po_order_buyer']))->find();
		//验货员详情
		$temp['receiver']  = D('staff')->where(array('sid'=>$po['po_order_receiver']))->find();
		//采购单详情
		$temp['poDetail']  = D('PurchaseOrderLine')
									->alias('pol')
									->join('product p on pol.pid=p.pid')
        							->join('vendor v on p.vid=v.vid')
									->where(array('po_order_item'=>$po['po_order_item']))
									->select();
		if($temp['poDetail'] !== null){
			foreach ($temp['poDetail'] as $k => $v){
				//采购单采购物料总数
				$temp['poQuantityTotal'] += $v['po_product_quantity'];
				//采购物料总价
				$temp['poTotalPrice']    += $v['po_product_quantity']*$v['product_price'];
			}
		}

		//验货单详情
		$temp['rsvDetail'] = D('RsvShipmentHead')->where(array('rs_po_item'=>$po['po_order_item']))->select();
		if($temp['rsvDetail'] !== null){
			foreach ($temp['rsvDetail'] as $k => $v){
				$temp['rsvDetail'][$k]['rsvDetailList'] = D('RsvShipmentLine')
																->alias('rsvl')
																->join('product p on rsvl.pid=p.pid')
						            							->join('vendor v on p.vid=v.vid')
																->where(array('rsl_item'=>$v['rs_item']))
																->select();
				foreach ($temp['rsvDetail'][$k]['rsvDetailList'] as $m => $n) {
					//验货单验货明细统计
					$temp['rsvDetail'][$k]['rsvTotalQuantity']             += $n['rs_quantity'];
					$temp['rsvDetail'][$k]['rsvTotalEnableQuantity']       += $n['rs_enable_quantity'];
					$temp['rsvDetail'][$k]['rsvTotalDisableQuantity']      += $n['rs_disable_quantity'];
					$temp['rsvDetail'][$k]['rsvTotalQuantityPrice']        += $n['rs_quantity']*$n['product_price'];
					$temp['rsvDetail'][$k]['rsvTotalEnableQuantityPrice']  += $n['rs_enable_quantity']*$n['product_price'];
					$temp['rsvDetail'][$k]['rsvTotalDisableQuantityPrice'] += $n['rs_disable_quantity']*$n['product_price'];
				}
				//采购单对应验货总计
				$temp['po_rsvTotalQuantity']          += $temp['rsvDetail'][$k]['rsvTotalQuantity'];
				$temp['rsvTotalEnableQuantity']       += $temp['rsvDetail'][$k]['rsvTotalEnableQuantity'];
				$temp['rsvTotalDisableQuantity']      += $temp['rsvDetail'][$k]['rsvTotalDisableQuantity'];
				$temp['rsvTotalQuantityPrice']        += $temp['rsvDetail'][$k]['rsvTotalQuantityPrice'];
				$temp['rsvTotalEnableQuantityPrice']  += $temp['rsvDetail'][$k]['rsvTotalEnableQuantityPrice'];
				$temp['rsvTotalDisableQuantityPrice'] += $temp['rsvDetail'][$k]['rsvTotalDisableQuantityPrice'];
			}
		}
		//总数差额
		$temp['balanceQuantity'] = $temp['poQuantityTotal']-$temp['po_rsvTotalQuantity'];
		//总价差额
		$temp['balancePrice']    = $temp['poTotalPrice']-$temp['rsvTotalQuantityPrice'];
		if($temp['balancePrice'] === 0){
			$temp['check_status'] = 1;
		}else{
			$temp['check_status'] = 0;
		}
	
		p($temp);die;
		//采购单状态变为已出纳
		
		
		// $this->assign('item',$item);
		// $this->assign('temp',$temp);
		// $this->display();
	}
}