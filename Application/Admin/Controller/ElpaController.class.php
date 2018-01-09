<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
* elpa控制器
**/
class ElpaController extends AdminBaseController{

	public function index(){
		// $num = 5;
		// $location = 'tree';

		$format = 'The %2$s contains %1$d monkeys';
		echo sprintf($format,$num,$location);
	}

//********************广告************************
	/**
	* 轮播图，推广位列表
	**/
	public function ad(){
		$data=D('ElpaShow')->order('order_number')->select();
		$assign=array(
			'data'=>$data
			);
		//推广位类型
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加广告位
	**/
	public function add_ad(){
		$data=I('post.');
		$upload=post_upload();
		$data['show_picture']= C('WEB_URL').$upload['name'];
		$result=D('ElpaShow')->addData($data);
		if($result){
			$this->redirect('Admin/Elpa/ad');
		}else{
			$this->error('添加失败');
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
		$result=D('ElpaShow')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/ad');
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
		$result=D('ElpaShow')->deleteData($map);
		if($result){
			$this->redirect('Admin/Elpa/ad');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 排序
	**/
	public function order_ad(){
		$data=I('post.');
		$result=D('ElpaShow')->orderData($data,$id='sid');
		if ($result) {
			$this->redirect('Admin/Elpa/ad');
		}else{
			$this->error('排序失败');
		}
	}
//********************新闻**************************
	/**
	* 新闻列表
	**/
	public function news(){
		$assign=D('ElpaNews')->getPage(D('ElpaNews'),$map=array());
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
		$result=D('ElpaNews')->addData($data);
		if($result){
			$this->redirect('Admin/Elpa/news');
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
		$result=D('ElpaNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/news');
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
		$result=D('ElpaNews')->deleteData($map);
		if($result){
			$this->redirect('Admin/Elpa/news');
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
		$result=D('ElpaNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/news');
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
		$result=D('ElpaNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/news');
		}else{
			$this->error('编辑失败');
		}
	}
//********************课程**************************
	/**
	* 课程列表
	**/
	public function lesson(){
		$data=D('ElpaLesson')->getTreeData();
		$assign=array(
			'data'=>$data
		);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加课程
	**/
	public function add_lesson(){
		$data=I('post.');
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('ElpaLesson')->addData($data);
		if($result){
			$this->redirect('Admin/Elpa/lesson');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑课程
	**/
	public function edit_lesson(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
		);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('ElpaLesson')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/lesson');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除课程
	**/
	public function delete_lesson(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
		);
		$result=D('ElpaLesson')->deleteData($map);
		if($result){
			$this->redirect('Admin/Elpa/lesson');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 课程排序
	**/
	public function order_lesson(){
		$data=I('post.');
		$result=D('ElpaLesson')->orderData($data,$id='id');
		if ($result) {
			$this->redirect('Admin/Elpa/lesson');
		}else{
			$this->error('排序失败');
		}
	}

//********************章节**************************
	/**
	* 章节列表
	**/
	public function chapter(){
		$id=I('get.id');
		$map=array(
			'ilid'=>$id
		);
		$assign=D('ElpaChapter')->getPage(D('ElpaChapter'),$map);
		$lesson_name=D('ElpaLesson')
					->where(array('id'=>$assign['data'][0]['ilid']))
					->getfield('name');
		$this->assign('lesson_name',$lesson_name);
		$this->assign('pid',$id);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加章节
	**/
	public function add_chapter(){
		$data=I('post.');
		$upload=several_upload();
		$data['ct_photo']=C('WEB_URL').$upload['name'][0];
		$data['ct_vedio']=C('WEB_URL').$upload['name'][1];
		$result=D('ElpaChapter')->addData($data);
		if($result){
			$this->redirect('Admin/Elpa/chapter');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑章节
	**/
	public function edit_chapter(){
		$data=I('post.');
		$map=array(
			'ct_id'=>$data['id']
		);
		$upload=several_upload();
		$data['ct_photo']=C('WEB_URL').$upload['name'][0];
		$data['ct_vedio']=C('WEB_URL').$upload['name'][1];
		$result=D('ElpaChapter')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/chapter');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除章节
	**/
	public function delete_chapter(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
		);
		$result=D('ElpaChapter')->deleteData($map);
		if($result){
			$this->redirect('Admin/Elpa/chapter');
		}else{
			$this->error('删除失败');
		}
	}


//********************文件**************************
	/**
	* 文件夹，文件列表
	**/
	public function file(){
		$data=D('ElpaFile')->getTreeData('tree','order_number,id');
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}


	/**
	* 添加文件界面
	**/
	public function addfile(){
		$id=I('get.id');
		$this->assign('id',$id);
		$this->display();
	}

	/**
	* 添加文件
	**/
	public function add_file(){
		$data=I('post.');
		$upload=several_upload();
		//上传文件分类
		foreach ($upload as $key => $value) {
			foreach ($value as $k => $v) {
				$type=file_format($v);
				if($type=='video'){
					$new['video']=$v;
				}else if($type=='image'){
					$new['icon']=$v;
				}else if($type=='text'){
					$new['pdf']=$v;
				}
			}
		}
		if(isset($new['icon'])){
			$data['icon']=C('WEB_URL').$new['icon'];	
		}
		if(isset($new['video'])){
			$data['video']=C('WEB_URL').$new['video'];
		}
		if(isset($new['pdf'])){
			$data['pdf']=C('WEB_URL').$new['pdf'];
		}
		$result=D('ElpaFile')->addData($data);
		if ($result) {
			$this->redirect('Admin/Elpa/file');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑文件
	**/
	public function edit_file(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
		);
		$upload=several_upload();
		if(isset($upload['name'])){
			//上传文件分类
		foreach ($upload as $key => $value) {
			foreach ($value as $k => $v) {
					$type=file_format($v);
					if($type=='video'){
						$new['video']=$v;
					}else if($type=='image'){
						$new['icon']=$v;
					}else if($type=='text'){
						$new['pdf']=$v;
					}
				}
			}
			if(isset($new['icon'])){
				$data['icon']=C('WEB_URL').$new['icon'];	
			}
			if(isset($new['vedio'])){
				$data['video']=C('WEB_URL').$new['video'];
			}
			if(isset($new['pdf'])){
				$data['pdf']=C('WEB_URL').$new['pdf'];
			}
		}
		$result=D('ElpaFile')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Elpa/file');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除文件
	**/
	public function delete_file(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('ElpaFile')->deleteData($map);
		if($result){
			$this->redirect('Admin/Elpa/file');
		}else{
			$this->error('请先删除子菜单');
		}
	}

























}