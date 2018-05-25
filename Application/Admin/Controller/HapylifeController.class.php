<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*后台权限管理
**/
class HapylifeController extends AdminBaseController{

//***********************新闻*******************
	/**
	* 新闻列表
	**/
	public function news(){
		$assign=D('News')->getPage(D('News'),$map=array());
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
		$result=D('News')->addData($data);
		if($result){
			$this->redirect('Admin/Hapylife/news');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑新闻
	**/
	public function edit_news(){
		$data=I('post.');
		if(empty($data['news_des'])){
			$data['news_des'] = mb_substr(I('post.news_content'),0,20).'.....';
		}
		$map=array(
			'nid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['news_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('News')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/news');
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
		$result=D('News')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hapylife/news');
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
		$result=D('News')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/news');
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
		$result=D('News')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/news');
		}else{
			$this->error('编辑失败');
		}
	}


//***********************旅游*******************
	/**
	* 旅游列表
	**/
	public function travel(){
		$assign=D('Travel')->getPage(D('Travel'),$map=array());
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加旅游(默认不置顶travel_top 0，默认显示is_show 1)
	**/
	public function add_travel(){
		$upload=several_upload_arr();
		$data=array(
			'travel_title'	=>I('post.travel_title'),
			'travel_content'=>I('post.travel_content'),
			'whattime'		=>I('post.whattime'),
			'include'		=>I('post.include'),
			'hotel'		    =>I('post.hotel'),
			'addition'		=>I('post.addition'),
			'destination'	=>I('post.destination'),
			'itinerary'		=>I('post.itinerary'),
			'platinum'		=>I('post.platinum'),
			'policies'		=>I('post.policies'),
			'address'		=>I('post.address'),
			'travel_price'  =>I('post.travel_price'),
			'travel_des'	=>I('post.travel_des')?I('post.travel_des'):mb_substr(I('post.travel_content'),0,50).'.....',
			'keyword'	    =>mb_substr(I('post.travel_content'),0,50)
		);
		$data['starttime']  = I('post.starttime');
		$tmpe1   = explode('/',$data['starttime']);
		$moth1   = substr(date('F',strtotime($data['starttime'])),0,3);
		$endtime = strtotime($data['starttime'])+$data['whattime']*86400;
		$data['endtime']    = date('m/d/Y',$endtime);
		$tmpe2   = explode('/',$data['endtime']);
		$moth2   = substr(date('F',$endtime),0,3);
		if($tmpe1[2]==$tmpe2[2]){
			if($tmpe1[0]==$tmpe2[0]){
				$data['addtime'] = $moth1.' '.$tmpe1[1].' - '.$tmpe2[1].', '.$tmpe1[2];
			}else{
				$data['addtime'] = $moth1.' '.$tmpe1[1].' - '.$moth2.' '.$tmpe2[1].', '.$tmpe1[2];
			}
		}else{
			$data['addtime']     = $moth1.' '.$tmpe1[1].', '.$tmpe1[2].' - '.$moth2.' '.$tmpe2[1].', '.$tmpe2[2];
		}
		// p($data);die;
		if($upload['name'][0]){
			$data['travel_picture'] = C('WEB_URL').$upload['name'][0];
		}
		if($upload['name'][1]){
			$data['travel_picture1'] = C('WEB_URL').$upload['name'][1];
		}
		if($upload['name'][2]){
			$data['travel_picture2'] = C('WEB_URL').$upload['name'][2];
		}
		if($upload['name'][3]){
			$data['travel_picture3'] = C('WEB_URL').$upload['name'][3];
		}
		if($upload['name'][4]){
			$data['travel_picture4'] = C('WEB_URL').$upload['name'][4];
		}
		if($upload['name'][5]){
			$data['travel_picture5'] = C('WEB_URL').$upload['name'][5];
		}
		// p($data);die;
		$result=D('Travel')->addData($data);
		if($result){
			$this->redirect('Admin/Hapylife/travel');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑旅游
	**/
	public function edit_travel(){
		$map=array(
			'tid'=>I('post.id')
			);
		$upload=several_upload_arr();
		$data=array(
			'travel_title'	=>I('post.travel_title'),
			'travel_content'=>I('post.travel_content'),
			'whattime'		=>I('post.whattime'),
			'include'		=>I('post.include'),
			'hotel'		    =>I('post.hotel'),
			'addition'		=>I('post.addition'),
			'destination'	=>I('post.destination'),
			'itinerary'		=>I('post.itinerary'),
			'platinum'		=>I('post.platinum'),
			'policies'		=>I('post.policies'),
			'address'		=>I('post.address'),
			'travel_price'  =>I('post.travel_price'),
			'travel_des'	=>I('post.travel_des')?I('post.travel_des'):mb_substr(I('post.travel_content'),0,50).'.....',
			'keyword'	    =>mb_substr(I('post.travel_content'),0,50)
		);
		$data['starttime']  = I('post.starttime');
		$tmpe1   = explode('/',$data['starttime']);
		$moth1   = substr(date('F',strtotime($data['starttime'])),0,3);
		$endtime = strtotime($data['starttime'])+$data['whattime']*86400;
		$data['endtime']    = date('m/d/Y',$endtime);
		$tmpe2   = explode('/',$data['endtime']);
		$moth2   = substr(date('F',$endtime),0,3);
		if($tmpe1[2]==$tmpe2[2]){
			if($tmpe1[0]==$tmpe2[0]){
				$data['addtime'] = $moth1.' '.$tmpe1[1].' - '.$tmpe2[1].', '.$tmpe1[2];
			}else{
				$data['addtime'] = $moth1.' '.$tmpe1[1].' - '.$moth2.' '.$tmpe2[1].', '.$tmpe1[2];
			}
		}else{
			$data['addtime']     = $moth1.' '.$tmpe1[1].', '.$tmpe1[2].' - '.$moth2.' '.$tmpe2[1].', '.$tmpe2[2];
		}
		if($upload['name'][0]){
			$data['travel_picture'] = C('WEB_URL').$upload['name'][0];
		}
		if($upload['name'][1]){
			$data['travel_picture1'] = C('WEB_URL').$upload['name'][1];
		}
		if($upload['name'][2]){
			$data['travel_picture2'] = C('WEB_URL').$upload['name'][2];
		}
		if($upload['name'][3]){
			$data['travel_picture3'] = C('WEB_URL').$upload['name'][3];
		}
		if($upload['name'][4]){
			$data['travel_picture4'] = C('WEB_URL').$upload['name'][4];
		}
		if($upload['name'][5]){
			$data['travel_picture5'] = C('WEB_URL').$upload['name'][5];
		}
		$result=D('Travel')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/travel');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除旅游
	**/
	public function delete_travel(){
		$id=I('get.id');
		$map=array(
			'tid'=>$id
			);
		$result=D('Travel')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hapylife/travel');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 置顶旅游
	**/
	public function travel_top(){
		$data=I('get.');
		$map =array(
			'tid'=>$data['id']
			);
		$result=D('Travel')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/travel');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 显示旅游
	**/
	public function travel_show(){
		$data=I('get.');
		$map =array(
			'tid'=>$data['id']
			);
		$result=D('Travel')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/travel');
		}else{
			$this->error('编辑失败');
		}
	}

//***********************商品分类***************
	/**
	* 商品分类列表
	**/
	public function category(){
		$data=D('Category')->getTreeData('tree','id','icat_name_zh');
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
		$result=D('Category')->addData($data);
		if($result){
			$this->redirect('Admin/Hapylife/category');
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
		$result=D('Category')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/category');
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
		$result=D('Category')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hapylife/category');
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
		$tmpe['pid'] =array('NEQ','0');
		$catList=D('Category')->where($tmpe)->select();

		//获取商品分类列表
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'ip_name_zh'=>$word
			);
		}
		$assign=D('Product')->getAllData(D('Product'),$map);
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
		$result=D('Product')->addData($data);
		if($result){
			$this->redirect('Admin/Hapylife/product');
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
	public function delete_product(){
		$id=I('get.id');
		$map=array(
			'ipid'=>$id
			);
		$result=D('Product')->deleteData($map);
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
		$result=D('Product')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}


	/**
	* 删除所有商品
	**/
	public function delete_all(){
		$data = I('post.');
		$map  = array(
			'ipid'=>array('in',$data['ck'])
		);
		if($data){
			$delete_all = M('Product')->where($map)->delete();
			if($delete_all){
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->error("删除失败");
			}
		}else{
			$this->error("请选择要删除的选项");
		}
	}

	//**********************订单*********************
	/**
	* 订单列表
	*@param ir_status -1所有订单 0待付款 1待审核 2已支付 3已完成
	*@param excel 导出excel
	*@param word  搜索关键词
	*@param status订单状态筛选
	*@param starttime 起始时间 endtime 结束时间
	**/
	public function receipt(){
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$status    = I('get.status')-1;
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime')):time();
		$assign    = D('Receipt')->getPage(D('Receipt'),$word,$order='ir_date desc',$status,$starttime,$endtime);
		
		//导出excel
		if($excel == 'excel'){
			$export_excel = D('Receipt')->export_excel($assign['data']);
		}else{
			$this->assign($assign);
			$this->assign('status',I('get.status'));
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->display();
		}
		
	}

	/**
	* 订单修改
	**/
	public function edit_receipt(){
		$data= I('post.');
		$map=array(
			'irid'=>$data['id']
		);
		$result=D('Receipt')->editData($map,$data);
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
		$result=D('Receipt')->deleteData($map);
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
	public function user(){
		//有密码账户搜索
		$count = M('user')->where(array('PassWord'=>array('neq','')))->count();
		// //账户昵称搜索
		$word = trim(I('get.word'));
		if(empty($word)){
			$map = array();
		}else{
			$map = array(
				'iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%')
			);
		}
		
		$excel     = I('get.excel');
		$status    = I('get.status')-1;
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('User')->getPage(D('User'),$word,$order='joinedon desc',$status,$starttime,$endtime);
		// p($assign);
		// die;
		//导出excel
		if($excel == 'excel'){
			$export_excel = D('User')->export_excel($assign['data']);
		}else{
			$this->assign($assign);
			$this->assign('count',$count);
			$this->assign('status',I('get.status'));
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->display();
		}
	}

	/**
	* 用户编辑
	**/
	public function edit_user(){
		$data=I('post.');
		$map=array(
			'iuid'=>$data['id']
		);
		//修改头像
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hu_photo']=C('WEB_URL').$upload['name'];
		}
		$result = D('User')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 用户锁定
	**/
	public function lock_user(){
		$data=I('get.');
		$map =array(
			'iuid'=>$data['id']
			);
		$result=D('User')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 用户删除
	**/
	public function delete_user(){
		$id=I('get.id');
		$map=array(
			'iuid'=>$id
			);
		$result=D('User')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 房间列表
	**/
	public function room(){
		$cid       =I('get.cid');
		$keyword   =I('get.keyword');
		$travel=D('Travel')->select();
		$assign=D('Room')->getAllData(D('Room'),$map,$cid,$keyword,$order='rid desc');
		$this->assign($assign);
		$this->assign('travel',$travel);
		$this->assign('cid',$cid);
		$this->assign('keyword',$keyword);
		$this->display();
	}

	/**
	* 添加房间(默认显示is_show 1)
	**/
	public function add_room(){
		$data=I('post.');
		$result=D('Room')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑房间
	**/
	public function edit_room(){
		$data=I('post.');
		$map=array(
			'rid'=>$data['rid']
			);
		$result=D('Room')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除房间
	**/
	public function delete_room(){
		$id=I('get.id');
		$map=array(
			'rid'=>$id
			);
		$result=D('Room')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}


}