<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* 分类控制器
**/
class CategoryController extends AdminBaseController{

	/**
	* 分类列表
	**/
	public function category(){
		$data=D('Category')->getTreeData('tree','id','icat_name');
		// p($data);die;
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}


	/**
	* 添加分类
	**/
	public function addCategory(){
		$data=I('post.');
		unset($data['id']);
		$upload=post_upload();
		$data['icat_picture']=C('WEB_URL').$upload['name'];
		$result=D('Category')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}


	/**
	* 编辑分类
	**/
	public function editCategory(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['icat_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('Category')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除分类
	**/
	public function deleteCategory(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('Category')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}






}