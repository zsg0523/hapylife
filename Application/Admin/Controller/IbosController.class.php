<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*后台权限管理
**/
class IbosController extends AdminBaseController{
//***********************广告*******************
	/**
	* 轮播图，推广位列表
	**/
	public function ad(){
		$category=D('IbosCategory')->where(array('is_show'=>1))->select();
		$product=D('IbosProduct')->where(array('is_pull'=>1))->select();
		$data=D('IbosShow')->order('order_number')->select();
		$assign=array(
			'data'=>$data,
			'category'=>$category,
			'product'=>$product
			);
		//推广位类型
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加
	**/
	public function add_ad(){
		$data=I('post.');
		$upload=post_upload();
		$data['show_picture']= C('WEB_URL').$upload['name'];
		$result=D('IbosShow')->addData($data);
		if($result){
			$this->redirect('Admin/Ibos/ad');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 显示
	**/
	public function show_ad(){
		$data=I('get.');
		$map =array(
			'sid'=>$data['id']
		);
		$result=D('IbosShow')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/ad');
		}else{
			$this->error('编辑失败');
		}
	}



	/**
	* 编辑
	**/
	public function edit_ad(){
		$data=I('post.');
		$map=array(
				'sid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['show_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('IbosShow')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/ad');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除 
	**/
	public function delete_ad(){
		$id=I('get.id');
		$map=array(
			'sid'=>$id
			);
		$result=D('IbosShow')->deleteData($map);
		if($result){
			$this->redirect('Admin/Ibos/ad');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 排序
	**/
	public function order_ad(){
		$data=I('post.');
		$result=D('IbosShow')->orderData($data,$id='sid');
		if ($result) {
			$this->redirect('Admin/Ibos/ad');
		}else{
			$this->error('排序失败');
		}
	}


//***********************新闻*******************
	/**
	* 新闻列表
	**/
	public function news(){
		$assign=D('IbosNews')->getPage(D('IbosNews'),$map=array());
		$this->assign($assign);
		$this->display();
	}
	/**
	* 添加新闻(默认不置顶news_top 0，默认显示is_show 1)
	**/
	public function add_news(){
		$upload=post_upload();
		$data=array(
				'news_title'	=>I('news_title'),
				'news_content'	=>I('news_content'),
				'addtime'		=>date('Y-m-d H:i:s'),
				'news_des'		=>I('post.news_des')?I('post.news_des'):mb_substr(I('post.news_content'),0,20).'.....',
				'news_picture'	=>C('WEB_URL').$upload['name']
			);
		$result=D('IbosNews')->addData($data);
		if($result){
			$this->redirect('Admin/Ibos/news');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑新闻
	**/
	public function edit_news(){
		$data=I('post.');
		$map=array(
			'nid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['news_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('IbosNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/news');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除新闻
	**/
	public function delete_news(){
		$id=I('get.id');
		$map=array(
			'nid'=>$id
			);
		$result=D('IbosNews')->deleteData($map);
		if($result){
			$this->redirect('Admin/Ibos/news');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 置顶新闻
	**/
	public function news_top(){
		$data=I('get.');
		$map =array(
			'nid'=>$data['id']
			);
		$result=D('IbosNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/news');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 显示新闻
	**/
	public function news_show(){
		$data=I('get.');
		$map =array(
			'nid'=>$data['id']
			);
		$result=D('IbosNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/news');
		}else{
			$this->error('编辑失败');
		}
	}

//***********************商品分类***************
	/**
	* 商品分类列表
	**/
	public function category(){
		$data=D('IbosCategory')->getTreeData('tree','id','icat_name_zh');
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加商品分类
	**/
	public function add_category(){
		$data=I('post.');
		unset($data['id']);
		$upload=post_upload();
		$data['icat_picture']=C('WEB_URL').$upload['name'];
		$result=D('IbosCategory')->addData($data);
		if($result){
			$this->redirect('Admin/Ibos/category');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑商品分类
	**/
	public function edit_category(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['icat_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('IbosCategory')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/category');
		}else{
			$this->error('编辑失败');
		}

	}

	/**
	* 删除商品分类
	**/
	public function delete_category(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('IbosCategory')->deleteData($map);
		if($result){
			$this->redirect('Admin/Ibos/category');
		}else{
			$this->error('删除失败');
		}
	}


//**********************商品*********************
	/**
	* 商品列表
	**/
	public function product(){
		//获取分类信息
		$catList=D('IbosCategory')->select();

		//获取商品分类列表
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'ip_name_zh'=>$word
				);
		}
		$assign=D('IbosProduct')->getAllData(D('IbosProduct'),$map);
		$this->assign('catList',$catList);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加商品
	**/
	public function add_product(){
		$data=I('post.');
		$upload=post_upload();
		$data['ip_picture_zh']=C('WEB_URL').$upload['name'];
		$result=D('IbosProduct')->addData($data);
		if($result){
			$this->redirect('Admin/Ibos/product');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑商品
	**/
	public function edit_product(){
		$data=I('post.');
		$map=array(
			'ipid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['ip_picture_zh']=C('WEB_URL').$upload['name'];
		}
		$result=D('IbosProduct')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除商品
	**/
	public function delete_product(){
		$id=I('get.id');
		$map=array(
			'ipid'=>$id
			);
		$result=D('IbosProduct')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 更改商品状态（上架下架，是否推荐，是否在香港或者大陆售卖）
	**/
	public function status_product(){
		$data=I('get.');
		$map =array(
			'ipid'=>$data['id']
			);
		$result=D('IbosProduct')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	//**********************订单*********************
	/**
	* 订单列表
	**/
	public function receipt(){
		//关键字筛选，分类筛选,分页
		//0未支付 1待审核 2已支付 3已完成 
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'hu_nickname'=>$word,
			);
		}
		$assign = D('IbosReceipt')->getPage(D('IbosReceipt'),$map,$order='ir_date desc');
		//p($assign);die;
		$this->assign($assign);
		$this->display();
	}

	public function receipt1(){
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array(
				'ir_status'=>1
			);
		}else{
			$map=array(
				'hu_nickname'=>$word,
				'ir_status'=>1
			);
		}
		$assign = D('IbosReceipt')->getPage(D('IbosReceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	public function receipt2(){
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array(
				'ir_status'=>2
			);
		}else{
			$map=array(
				'hu_nickname'=>$word,
				'ir_status'=>2
			);
		}
		$assign = D('IbosReceipt')->getPage(D('IbosReceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	public function receipt3(){
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array(
				'ir_status'=>3
			);
		}else{
			$map=array(
				'hu_nickname'=>$word,
				'ir_status'=>3
			);
		}
		$assign = D('IbosReceipt')->getPage(D('IbosReceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	public function receipt4(){
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array(
				'ir_status'=>4
			);
		}else{
			$map=array(
				'hu_nickname'=>$word,
				'ir_status'=>4
			);
		}
		$assign = D('IbosReceipt')->getPage(D('IbosReceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	/**
	* 订单修改
	**/
	public function edit_receipt(){
		$data= I('post.');
		$map=array(
			'irid'=>$data['id']
		);
		$result=D('IbosReceipt')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 订单删除
	**/
	public function delete_receipt(){
		$id=I('get.id');
		$map=array(
			'irid'=>$id
			);
		$result=D('IbosReceipt')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	//**********************用户*********************
	/**
	* 用户列表
	**/
	public function users(){
		//账户昵称搜索
		$word = I('post.word');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'hu_nickname'=>$word
			);
		}
		$assign=D('IbosUsers')->getAllData(D('IbosUsers'),$map,$order="iu_logintime desc");
		$this->assign($assign);
		$this->display();
	}

	/**
	* 用户编辑
	**/
	public function edit_users(){
		$data=I('post.');
		$map=array(
			'iuid'=>$data['id']
		);
		//修改头像
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hu_photo']=C('WEB_URL').$upload['name'];
		}
		$result = D('IbosUsers')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/users');
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 用户锁定
	**/
	public function lock_users(){
		$data=I('get.');
		$map =array(
			'iuid'=>$data['id']
			);
		$result=D('IbosUsers')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Ibos/users');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 用户删除
	**/
	public function delete_users(){
		$id=I('get.id');
		$map=array(
			'iuid'=>$id
			);
		$result=D('IbosUsers')->deleteData($map);
		if($result){
			$this->redirect('Admin/Ibos/users');
		}else{
			$this->error('删除失败');
		}
	}





























































































































































































}