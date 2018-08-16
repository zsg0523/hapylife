<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* 检查控制器
**/
class InspectionController extends AdminBaseController{

	/**
	* 验货单头列表
	**/
	public function rsvHead(){
		//需要验货的采购订单列表
		$itemList = D('PurchaseOrderHead')->select();
		//验货单列表
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign = D('RsvShipmentHead')->getPage(D('RsvShipmentHead'),$word,$starttime,$endtime);
		foreach ($assign['data'] as $key => $value) {
			$temp[$key]              = $value;
			//采购员详情
			$temp[$key]['buyer']     = M('staff')->where(array('sid'=>$value['po_order_buyer']))->find();
			//验货员详情
			$temp[$key]['receiver']  = M('staff')->where(array('sid'=>$value['po_order_receiver']))->find();
			//采购单详情
			$temp[$key]['poDetail']  = M('PurchaseOrderLine')->where(array('po_order_item'=>$value['po_order_item']))->select();
			//验货单详情
			$temp[$key]['rsvDetail'] = M('RsvShipmentLine')->where(array('rsl_item'=>$value['rs_item']))->select();
			if($temp[$key]['poDetail'] !== null){
				foreach ($temp[$key]['poDetail'] as $k => $v){
					//采购单采购物料总数
					$temp[$key]['po_quantity_total'] += $v['po_product_quantity'];
				}
			}

			if($temp[$key]['rsvDetail'] !== null){
				foreach ($temp[$key]['rsvDetail'] as $k => $v){
					//验货单验货物料总数
					$temp[$key]['rsv_quantity_total'] += $v['rs_quantity'];
					//
				}
			}
		}
		// p($temp);die;
		if($excel == 'excel'){
			$export_excel = D('RsvShipmentHead')->export_excel($temp);
		}else{
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('itemList',$itemList);
			$this->assign('data',$temp);
			$this->assign('page',$assign['page']);
			$this->display();
		}		
	}

	/**
	* 添加验货单头
	**/
	public function addRsvHead(){
		//验货单号
		$rs_item = 'rsv'.date('YmdHis').rand(1000, 9999);

		$data = array(
			//验货单号
			'rs_item'        =>$rs_item,
			//唯一采购单号
			'rs_po_item'     =>trim(I('post.item')),
			//创建时间
			'rs_create_time' =>time()
		);
		$result = D('RsvShipmentHead')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 删除验货单表头
	**/
	public function deleteRsvHead(){
		$id = I('get.id');
		$map= array(
			'rs_hid'=>$id
		);
		$item  =D('RsvShipmentHead')->where($map)->getfield('rs_item');
		$temp  = array(
			'rsl_item'=>$item
		);
		//检查是否有表行
		$isLine = D('RsvShipmentLine')->where($temp)->select();
		if($isLine){
			//先删除表行内容
			$deleteLine=D('RsvShipmentLine')->deleteData($temp);
			if($deleteLine){
				//删除表头内容
				$result = D('RsvShipmentHead')->deleteData($map);
				if($result){
					redirect($_SERVER['HTTP_REFERER']);
				}else{
					$this->error('删除失败');
				}
			}
		}else{
			//直接删除表头
			$result = D('RsvShipmentHead')->deleteData($map);
			if($result){
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->error('删除失败');
			}
		}
	}

	/**
	* 编辑验货单头
	**/
	public function editRsvHead(){
		
	}

	/**
	* 验货单行列表
	**/
	public function rsvLine(){
		//采购单号
		$rs_po_item = I('get.rs_po_item');
		//验货单号
		$rs_item    = I('get.rs_item');
		//该采购单的详情
		$itemDetail = D('PurchaseOrderLine')
					->alias('pol')
					->join('product p on p.pid=pol.pid')
					->join('vendor v on v.vid=pol.vid')
					->where(array('po_order_item'=>$rs_po_item))
					->select();

		if(empty($word)){
			$map=array(
				'rsl_item'=>$rs_item
			);
		}else{
			$map=array(
				'rsl_item'=>$rs_item
			);
		}
		$assign = D('RsvShipmentLine')->getPage(D('RsvShipmentLine'),$map);
		// p($assign);die;
		$this->assign($assign);
		$this->assign('itemDetail',$itemDetail);
		//采购单号
		$this->assign('item',$item);
		//验货单号
		$this->assign('rs_item',$rs_item);
		$this->display();
	}

	/**
	* 添加验货单行
	**/
	public function addRsvLine(){
		$data   = I('post.');
		$data['rsl_create_time'] = time();
		$result = D('RsvShipmentLine')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}	

	/**
	* 编辑验货单行
	**/
	public function editRsvLine(){
		$data = I('post.');
		// p($data);die;
		$map  = array(
				'rs_lid'=>$data['id']
			);
		$result = D('RsvShipmentLine')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}


	/**
	* 删除验货单表行
	**/
	public function deleteRsvLine(){
		$id = I('get.id');
		$map= array(
			'rs_lid'=>$id
		);
		$result = D('RsvShipmentLine')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}


	/**
	* 确认入库,增加库存
	**/
	public function addInventory(){
		$id = I('get.id');
		$map= array(
			'rs_lid'=>$id
		);
		//验货行信息
		$rsInfo      = D('RsvShipmentLine')->where($map)->find();
		//产品信息
		$product     = D('product')->where(array('pid'=>$rsInfo['pid']))->find();

		// $endQuantity = bcadd($rsInfo['rs_quantity'], $product['product_inventory']);
		$endQuantity = $rsInfo['rs_enable_quantity']+$product['product_inventory'];
		//产品入库
		$product     = D('product')->where(array('pid'=>$rsInfo['pid']))->setfield('product_inventory',$endQuantity);
		//验货行更改信息
		$isInventory = D('RsvShipmentLine')->where($map)->setfield('is_inventory',1);

		if($endQuantity){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('入库失败');
		}
	}













































}