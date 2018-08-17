<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* Purchase Order Controller
**/
class PoController extends AdminBaseController{

	/**
	* 采购单表头列表
	**/
	public function index(){
		//员工信息列表
		$staffList = D('Staff')->select();
		//供应商list
		$vendorList  = D('Vendor')->select();
		//采购表头数据信息
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('PurchaseOrderHead')->getPage(D('PurchaseOrderHead'),$word,$starttime,$endtime);
		// p($assign);die;
		if($excel == 'excel'){
			$export_excel = D('PurchaseOrderHead')->export_excel($assign['data']);
		}else{
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('vendorList',$vendorList);
			$this->assign('staffList',$staffList);
			$this->assign($assign);
			$this->display();
		}
		
	}

	/**
	* 采购单表行列表
	**/
	public function poline(){
		//供应商list
		$vendorList  = D('Vendor')->select();
		//商品list
		$productList = D('Product')->select();
		//供应商货仓list
		$map['wh_vid']  = array('neq',0);
		$whList = D('Warehouse')->where($map)->select();
		// p($whList);die;
		//非供应商货仓
		$whCompanyList = D('Warehouse')->where(array('wh_vid'=>1))->select();

		$item = I('get.item');
		$map  = array(
			'po_order_item'=>$item
		);
		$po     = D('PurchaseOrderHead')->getData($item);
		$assign = D('PurchaseOrderLine')->getPage(D('PurchaseOrderLine'),$map);
		// p($po);
		// p($assign);die;
		$this->assign('po',$po[0]);
		$this->assign('item',$item);
		$this->assign('vendorList',$vendorList);
		$this->assign('productList',$productList);
		$this->assign('whList',$whList);
		$this->assign($assign);
		$this->display();
	}


	/**
	* 打印
	**/
	public function poPrint(){
		//供应商list
		$vendorList  = D('Vendor')->select();
		//商品list
		$productList = D('Product')->select();
		//货仓list
		$whList = D('Warehouse')->select();

		$item = I('get.item');
		$map  = array(
			'po_order_item'=>$item
		);
		$po     = D('PurchaseOrderHead')->getData($item);
		$assign = D('PurchaseOrderLine')->getPage(D('PurchaseOrderLine'),$map);
		// p($po);
		// p($assign);die;
		$this->assign('po',$po[0]);
		$this->assign('item',$item);
		$this->assign('vendorList',$vendorList);
		$this->assign('productList',$productList);
		$this->assign('whList',$whList);
		$this->assign($assign);
		$this->display();
	}
	
	/**
	* 添加采购单表头
	**/
	public function addPoHead(){
		//唯一采购单号
		$po_order_item = date('YmdHis').rand(1000, 9999);
		$data = array(
			'po_order_item'        =>$po_order_item,
			'po_order_type'        =>10,
			'po_order_description' =>trim(I('post.po_order_description')),
			'po_order_buyer'       =>trim(I('post.po_order_buyer')),
			'po_order_receiver'    =>trim(I('post.po_order_receiver')),
			'po_eta_date'          =>strtotime(trim(I('post.po_eta_date'))),
			'po_rec_date'          =>strtotime(trim(I('post.po_rec_date'))),
			'po_quantity_total'    =>trim(I('post.po_quantity_total')),
			'po_wh_number'         =>trim(I('post.po_wh_number')),
			'po_create_date'       =>time()
		);
		$result = D('PurchaseOrderHead')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	

	/**
	* 编辑采购单表头
	**/
	public function editPoHead(){
		$data = array(
			'id'				   =>trim(I('post.id')),
			'po_order_description' =>trim(I('post.po_order_description')),
			'po_order_buyer'       =>trim(I('post.po_order_buyer')),
			'po_order_receiver'    =>trim(I('post.po_order_receiver')),
			'po_eta_date'          =>strtotime(trim(I('post.po_eta_date'))),
			'po_rec_date'          =>strtotime(trim(I('post.po_rec_date'))),
			'po_quantity_total'    =>trim(I('post.po_quantity_total')),
			'po_wh_number'         =>trim(I('post.po_wh_number'))
		);
		$map  = array(
				'po_hid'=>$data['id']
			);
		$result = D('PurchaseOrderHead')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}


	/**
	* 添加采购单表行
	**/
	public function addPoLine(){
		$data   = I('post.');
		$result = D('PurchaseOrderLine')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑采购单表行
	**/
	public function editPoLine(){
		$data = I('post.');
		$map  = array(
				'po_lid'=>$data['id']
			);
		$result = D('PurchaseOrderLine')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}



	/**
	* 删除采购单表头
	**/
	public function deletePoHead(){
		$id = I('get.id');
		$map= array(
			'po_hid'=>$id
		);
		//订单号
		$item  =D('PurchaseOrderHead')->where($map)->getfield('po_order_item');
		//检查是否有表行
		$temp  = array(
			'po_order_item'=>$item
		);
		$isLine=D('PurchaseOrderLine')->where($temp)->select();
		if($isLine){
			//删除表行内容
			$deleteLine=D('PurchaseOrderLine')->deleteData($temp);
		}
		
		//删除表头内容
		$result = D('PurchaseOrderHead')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
		
	}


	/**
	* 删除采购单表行
	**/
	public function deletePoLine(){
		$id = I('get.id');
		$map= array(
			'po_lid'=>$id
		);
		$result = D('PurchaseOrderLine')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 采购单审核
	**/
	public function checkPo(){
		
	}














}