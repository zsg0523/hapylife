<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* Product Controller
**/
class ProductController extends AdminBaseController{

	/**
	* 商品列表
	**/
	public function index(){
		//获取商品供应商列表
		$vendorList = D('Vendor')->select();
		//分类列表
		$categoryList = D('Category')->select();

		$word=I('get.word','');
		if(empty($word)){
			$map=array(
			);
		}else{
			$map=array(
				'product_name'=>$word
			);
		}
		$assign=D('Product')->getPage(D('Product'),$map,$order="pid");
		// p($assign);die;
		$this->assign('vendorList',$vendorList);
		$this->assign('categoryList',$categoryList);
		$this->assign($assign);
		$this->display();
	}
	
	/**
	* 添加商品
	**/
	public function addProduct(){
		$data=I('post.');
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['product_picture']=C('WEB_URL').$upload['name'];		
		}
		$result=D('Product')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑商品
	**/
	public function editProduct(){
		$data=I('post.');
		$map=array(
			'pid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['product_picture']=C('WEB_URL').$upload['name'];		
		}
		$result=D('Product')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除商品
	**/
	public function deleteProduct(){
		$id=I('get.id');
		$map=array(
			'pid'=>$id
			);
		$result=D('Product')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 商品详细参数
	**/
	public function specifications(){
		$product_number = I('get.product_number');
		// $product_number = '102-113-681475-00';
		$data = M('specifications')->where(array('sku'=>$product_number))->select();
		// p($data);die;
		$this->assign('product_number',$product_number);
		$this->assign('data',$data);
		$this->display();
	}

	/**
	* 编辑参数
	**/
	public function editSpecifications(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
			);
		
		$result=D('specifications')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 添加参数
	**/
	public function addSpecifications(){
		$data=I('post.');
		$result=D('specifications')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 删除参数
	**/
	public function deleteSpecifications(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('specifications')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}




}