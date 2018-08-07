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
		$order_status    = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$status = '0,1,2,3,4,5,7,8';
		}else{
			$status = (string)$order_status;
		}
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('Receipt')->getPage(D('Receipt'),$word,$starttime,$endtime,$status,$order='ir_date desc',$timeType);
		//导出excel
		if($excel == 'excel'){
			$export_excel = D('Receipt')->export_excel($assign['data']);
		}else{
			$this->assign($assign);
			$this->assign('status',I('get.status'));
			$this->assign('word',$word);
			$this->assign('timeType',$timeType);
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
		$count = M('user')->count();
		//账户昵称搜索
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

		// $assign = unique_arr($data['data']);
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

	public function users(){
		//有密码账户搜索
		$count = M('user')->count();
		//账户昵称搜索
		$word = trim(I('get.word'));
		if(empty($word)){
			$map = array();
		}else{
			$map = array(
				'iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%')
			);
		}
		
		$excel     = I('get.excel');
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('User')->getPageS(D('User'),$word,$order='joinedon desc',$starttime,$endtime);

		//导出excel
		if($excel == 'excel'){
			$export_excel = D('User')->export_excel($assign['data']);
		}else{
			$this->assign($assign);
			$this->assign('count',$count);
			$this->assign('word',$word);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->display();
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
		
		$result=D('User')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}


	/*****************************************************************送货单管理******************************************************************/
	//送货单列表
	public function sendReceipt(){
		$mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $code[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
            	$code[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
            	$code[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
		//0、7未支付 1待审核 2已支付 3已发货 4已到达 5申请退货 8确定退货
		$order_status = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$status = '2,3,4,5,6,8';
		}else{
			$status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();

		$assign    = D('Receipt')->getSendPage(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$order='ir_paytime asc');
		// p($assign);
		// die;
		// 导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getAllSendData(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$order='ir_paytime desc');
			// p($data);die;
			$export_send_excel = D('Receipt')->export_send_excel($data);
		}else{
			$this->assign($assign);
			$this->assign('status',I('get.status'));
			$this->assign('word',$word);
			$this->assign('timeType',$timeType);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('code',$code);
			$this->display();
		}
	}


	/**
	* 送货单核对
	**/
	public function send(){
		$irid      = trim(I('get.id'));
		$ir_status = trim(I('get.ir_status'));
		$ir_statuss = trim(I('get.ir_statuss'));
		$data      = M('Receipt')->where(array('irid'=>$irid))->find();
		$userinfo  = M('User')->where(array('iuid'=>$data['riuid']))->find();
        switch ($ir_status) {
            case '2':
                $ir_status = 3;
                $map  = array(
					'irid'       =>$irid,
					'ir_status'  =>$ir_status,
					'send_time'  =>time()
                );
                $save = M('Receipt')->save($map);
                //日志记录
                $content = '单号:'.$data['ir_receiptnum'].',确认发货';
                $add = addLog($data['riuid'],$content,$action=3,$type=2);
                break;
            case '3':
                $ir_status = 4;
                $map  = array(
                    'irid'  =>$irid,
                    'ir_status'=>$ir_status,
                    'receive_time'  =>time()
                );
                $save = M('Receipt')->save($map);
                
                //日志记录
                $content = '单号:'.$data['ir_receiptnum'].',确认送达';
                $add     = addLog($data['riuid'],$content,$action=3,$type=2);
                break;
        }

        switch($ir_statuss){
        	case '1':
                $ir_status = 5;
                $map  = array(
                    'irid'  =>$irid,
                    'ir_status'=>$ir_status,
                    'returns_time'  =>time()
                );
                $save = M('Receipt')->save($map);
                
                //日志记录
                $content = '单号:'.$data['ir_receiptnum'].',退货申请';
                $add     = addLog($data['riuid'],$content,$action=3,$type=2);
                break;
            case '2':
                $ir_status = 8;
                $map  = array(
                    'irid'  =>$irid,
                    'ir_status'=>$ir_status,
                    'returns_times'  =>time()
                );
                $save = M('Receipt')->save($map);
                
                //日志记录
                $content = '单号:'.$data['ir_receiptnum'].',确认退货';
                $add     = addLog($data['riuid'],$content,$action=3,$type=2);
                break;
        }
        
        if($save){
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->error("确认失败");
        }

	}


	// 发送物流短信
	public function send_sms(){
		$data = I('post.');
		$issend = I('post.is_send');
		
		if(!empty($issend)){
			// 发送续费短信
			$spotemplate = 146228;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($data['username'],$data['productnams']);
		}

        $sponsorSms    = D('Smscode')->sms($appid='1400096409',$appkey='fc1c7e21ab36fef1865b0a3110709c51',$data['phone'],$data['acnumber'],$spotemplate,$sposmsSign,$spoparams);
        
        if($sponsorSms['errmsg']=='OK'){
        	if(!empty($issend)){
        		$is_send['is_send'] = 2;
				$result = M('Receipt')->where(array('irid'=>$data['irid']))->save($is_send);
				if($result){
	        		$mape  = array(
	                    'phone'   =>$data['phone'],
	                    'content'    =>'亲爱的会员'.$data['username'].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。',
	                    'acnumber'=>$data['acnumber'],
	                    'date'    =>date('Y-m-d H:i:s'),
	                    'operator' => $_SESSION['user']['username'],
	                    'product_name' => $data['productnams'],
	                    'addressee' => $data['username'],
	                );
	                $add = D('SmsLog')->add($mape);
	                if($add){
						$this->success('发送成功',U('Admin/Hapylife/sendReceipt'));
	                }else{
	                	$this->error('发送失败',U('Admin/Hapylife/sendReceipt'));
	                }
	        	}
        	}
        }else{
            $this->error('发送失败',U('Admin/Hapylife/sendReceipt'));
        }
	}

	// 短信列表
	public function sends(){
		$mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $code[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
            	$code[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
            	$code[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
       
		$word      = trim(I('get.word',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();

		$assign    = D('SmsLog')->getSendPage(D('SmsLog'),$word,$starttime,$endtime,$order='date desc');
		// p($assign);
		// die;		
		$this->assign($assign);
		$this->assign('word',$word);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->assign('code',$code);
		$this->display();
	}

	// 发送短信
	public function add_sends(){
		$data = I('post.');
		if($data['psd'] == 146228){
			// 物流信息通知
			$spotemplate = 146228;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($data['username'],$data['productnams']);
		}

		if($data['psd'] == 146227){
			// 续费信息通知
			$spotemplate = 146227;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($data['username'],$data['endtime']);
		}

        $sponsorSms    = D('Smscode')->sms($appid='1400096409',$appkey='fc1c7e21ab36fef1865b0a3110709c51',$data['phone'],$data['acnumber'],$spotemplate,$sposmsSign,$spoparams);
        
        if($sponsorSms['errmsg']=='OK'){
        	if($data['psd'] == 146227){
				$mape  = array(
                    'phone'   =>$data['phone'],
                    'content'    =>'亲爱的会员'.$data['username'].'，这是系统提醒消息，请在'.$data['endtime'].'之前购买月费包。',
                    'acnumber'=>$data['acnumber'],
                    'date'    =>time(),
                    'operator' => $_SESSION['user']['username'],
                    'addressee' => $data['username'],
                );
                $add = D('SmsLog')->add($mape);
                if($add){
					$this->success('发送成功',U('Admin/Hapylife/sends'));
                }else{
                	$this->error('发送失败',U('Admin/Hapylife/sends'));
                }
        	}
    		if($data['psd'] == 146228){
        		$mape  = array(
                    'phone'   =>$data['phone'],
                    'content'    =>'亲爱的会员'.$data['username'].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。',
                    'acnumber'=>$data['acnumber'],
                    'date'    =>time(),
                    'operator' => $_SESSION['user']['username'],
                    'product_name' => $data['productnams'],
                    'addressee' => $data['username'],
                );
                $add = D('SmsLog')->add($mape);
                if($add){
					$this->success('发送成功',U('Admin/Hapylife/sends'));
                }else{
                	$this->error('发送失败',U('Admin/Hapylife/sends'));
                }
        	}
        }else{
            $this->error('发送失败',U('Admin/Hapylife/sends'));
        }
	}
}