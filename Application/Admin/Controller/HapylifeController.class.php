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
			'news_title'	=>I('post.news_title'),
			'news_content'	=>I('post.news_content'),
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
		$data = array(
			'result' => 'eggcarton',
			'appkey' => 'ALL',
		);
		$data    = json_encode($data);
		$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/getCouponList";
		// $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/getCouponList";
		$result  = post_json_data($sendUrl,$data);
		$back_message = json_decode($result['result'],true);
		$CouponGroups = $back_message;
		//获取商品分类列表
		$word=I('get.word','');
		if(empty($word)){
			$map=array();
		}else{
			$map=array(
				'ip_name_zh'=>$word
			);
		}
		 // p($CouponGroups);die;
		$assign=D('Product')->getAllData(D('Product'),$map,'ipid desc','50');
		$this->assign('catList',$catList);
		$this->assign('CouponGroups',$CouponGroups);
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
		// p($data);die;
		foreach($data['gidnumArr'] as $key => $value){
			if(empty($value)){
				$keys[] = $key;
			}
		}
		if($data['gidnumArr']){
			foreach($data['gidnumArr'] as $key=>$value){
				if($value <= 0){
					unset($data['gidArr'][$key]);
					unset($data['gidnumArr'][$key]);
				}
			}
		}
		foreach($data['gidArr'] as $key=>$value){
			$getCoupn .= $value.',';
		}
		foreach($data['gidnumArr'] as $key=>$value){
			$traversenum .= $value.',';
		}	
		$data['get_coupon'] = substr($getCoupn,0,-1);
		$data['traverse_num'] = substr($traversenum,0,-1);

		if($data['ip_type']==5){
			if(!$data['ip_sprice'] || !$data['ip_dt']){
				$this->error('请填写DT折扣价和可折扣DT数量');
			}
		}
		// p($data);
		// die;
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
		if($data['gidnumArr1']){
			foreach($data['gidnumArr1'] as $key=>$value){
				if($value <= 0){
					unset($data['gidArr1'][$key]);
					unset($data['gidnumArr1'][$key]);
				}
			}
		}
		foreach($data['gidArr1'] as $key=>$value){
			$getCoupn .= $value.',';
		}
		foreach($data['gidnumArr1'] as $key=>$value){
			$traversenum .= $value.',';
		}	
		$data['get_coupon'] = substr($getCoupn,0,-1);
		$data['traverse_num'] = substr($traversenum,0,-1);
		
		if($data['ip_type']==5){
			if(!$data['ip_sprice'] || !$data['ip_dt']){
				$this->error('请填写DT折扣价和可折扣DT数量');
			}
		}
		// p($data);die;
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

	/**
	* 商品排序
	**/ 
	public function order_product(){
		$data=I('post.');
		$result=D('Product')->orderData($data,$id='ipid');
		if ($result) {
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('排序失败');
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
		$ir_receiptnum      = trim(I('get.ir_receiptnum',''));
		$customerid      = trim(I('get.customerid',''));
		$lastname      = trim(I('get.lastname',''));
		$firstname      = trim(I('get.firstname',''));
		$ir_price      = trim(I('get.ir_price',''));
		$ir_unpaid      = trim(I('get.ir_unpaid',''));
		$order_status    = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$status = '0,1,2,3,4,5,7,8,202';
		}else{
			$status = (string)$order_status;
		}
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_paytime';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$assign    = D('Receipt')->getPage(D('Receipt'),$ir_receiptnum,$customerid,$lastname,$firstname,$ir_price,$ir_unpaid,$starttime,$endtime,$status,$order='ir_date desc',$timeType);
		//导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getAllSendData(D('Receipt'),$ir_receiptnum,$customerid,$lastname,$firstname,$ir_price,$ir_unpaid,$starttime,$endtime,$status,$timeType,$order='ir_paytime desc');
			$export_excel = D('Receiptson')->export_excel($data['data']);
		}else{
			$this->assign($assign);
			$this->assign('status',I('get.status'));
			$this->assign('ir_receiptnum',$ir_receiptnum);
			$this->assign('customerid',$customerid);
			$this->assign('lastname',$lastname);
			$this->assign('firstname',$firstname);
			$this->assign('ir_price',$ir_price);
			$this->assign('ir_unpaid',$ir_unpaid);
			$this->assign('timeType',$timeType);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->display();
		}
	}

	/**
	* 修改订单收货地址
	**/ 
	public function editReceiptAddress(){
		$data = I('post.');
		$map  = array('irid'=>$data['id']);
        $save = M('Receipt')->where($map)->save($data);
        if($save){
        	redirect($_SERVER['HTTP_REFERER']);
        }else{
        	$this->error('修改失败');
        }
	}

	/**
	* 订单列表(剔除测试账号和使用通用券的单)
	*@param ir_status -1所有订单 0待付款 1待审核 2已支付 3已完成
	*@param excel 导出excel
	*@param word  搜索关键词
	*@param status订单状态筛选
	*@param starttime 起始时间 endtime 结束时间
	**/
	public function FinanceReceipt(){
		$session   = session();
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$order_status    = I('get.status')-1;
		// p($session);die;
		if($order_status== -1){
			//所有订单
			$status = '0,1,2,3,4,5,7,8,202';
		}else{
			$status = (string)$order_status;
		}
		$test      ='测试,测,试,测试点,test,testtest,测试测试,新建测试,测试地,测试点,测试账号';
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_paytime';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$assign    = D('Receipt')->FinanceGetPage(D('Receipt'),$word,$starttime,$endtime,$status,$test,$timeType,$order='ir_paytime desc');
		//导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->FinanceGetAllSendData(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$test,$order='ir_paytime desc');
			$export_excel = D('Receiptson')->export_excels($data['data']);
		}else{
			$this->assign($assign);
			$this->assign('status',I('get.status'));
			$this->assign('word',$word);
			$this->assign('timeType',$timeType);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('session',$session);
			$this->display();
		}
	}
	//查看订单明细
	public function receiptSon(){
		$ir_receiptnum = I('get.ir_receiptnum');
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point,rs.ir_dt as r_dt';
		$assign = D('Receiptson')->getSendPageSon(D('Receiptson'),$ir_receiptnum,$field);
		$this->assign($assign);
		$this->display();
	}
	//查看订单明细
	public function FinanceReceiptSon(){
		$ir_receiptnum = I('get.ir_receiptnum');
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point';
		$assign = D('Receiptson')->getSendPageSon(D('Receiptson'),$ir_receiptnum,$field);
		$this->assign($assign);
		$this->display();
	}
	/**
	**添加明细
	*/
	public function addReceiptSon(){
		$tmpe      = I('post.');
		$session   = session();
		$password  = md5($tmpe['password']);
		$receiptnum= date('YmdHis').rand(100000, 999999);
        $admin     = D('Admin')->where(array('id'=>$session['user']['id']))->find();
        $receipt   = D('Receipt')->where(array('ir_receiptnum'=>$tmpe['ir_receiptnum']))->find();
        $userinfo  = D('User')->where(array('CustomerID'=>$receipt['rcustomerid']))->find();
        if($admin && $admin['password']==$password){
        	if($tmpe['ir_paytype']==2){
        		$point = $tmpe['ir_price'];
        		$price = bcmul($tmpe['ir_price'],100,2);
        	}else{
        		$price = $tmpe['ir_price'];
        		$point = bcdiv($tmpe['ir_price'],100,2);
        	}
        	$data = array(
        		'operator'      =>$session['user']['username'],
        		'ir_receiptnum' =>$tmpe['ir_receiptnum'],
        		'riuid'         =>$receipt['riuid'],
        		'pay_receiptnum'=>$receiptnum,
        		'ir_paytype'    =>$tmpe['ir_paytype'],
        		'ir_price'      =>$price,
        		'ir_point'      =>$point,
        		'status'        =>2,
        		'cretime'       =>time(),
        		'paytime'       =>time()
        	);
        	$add = D('Receiptson')->addData($data);
        	if($add){
        		$unpaind = bcsub($receipt['ir_unpaid'],$price,2);
        		$unpoint = bcsub($receipt['ir_unpoint'],$point,2);
        		$mape    = array('ir_unpaid'=>$unpaind,'ir_unpoint'=>$unpoint,'ir_paytime'=>time());
        		if($unpoint == 0 && $unpoint==0){
        			$mape['ir_status'] = 2;
        		}else{
        			$mape['ir_status'] = 202;
        		}
    			$save    = D('receipt')->where(array('ir_receiptnum'=>$tmpe['ir_receiptnum']))->save($mape);	
        		if($save){
        			if($unpoint == 0 && $unpoint==0){
                        // 发送短信提示
                        $templateId ='178959';
                        $params     = array($receiptnum,$receipt['ir_desc']);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                'acnumber' => $userinfo['acnumber'],
                                'phone' => $userinfo['phone'],
                                'operator' => '系统',
                                'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                'product_name' => $receipt['ir_desc'],
                                'date' => time(),
                                'content' => '订单编号：'.$receiptnum.'，产品：'.$receipt['ir_desc'].'，支付成功。',
                                'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                        if($receipt['ir_ordertype'] == 4){
                            // 添加通用券
                            $product= M('Receipt')
                                    ->alias('r')
                                    ->join('hapylife_product AS p ON r.ipid = p.ipid')
                                    ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
                                    ->find();
                            $data = array(
                                    'product' => $product,
                                    'userinfo' => $userinfo,
                                    'ir_receiptnum' => $receipt['ir_receiptnum'],
                                );
                            $data    = json_encode($data);
                            $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
                            // $sendUrl = "http://192.168.33.10/testnulife/index.php/Api/Couponapi/addCoupon";
                            $result  = post_json_data($sendUrl,$data);
                            // $back_msg = json_decode($result['result'],true);
                        }
                    }else{
                        // 共总支付
                        $total = bcsub($receipt['ir_unpaid'],$unpaind,2);
                        // 发送短信提示
                        $templateId ='178957';
                        $params     = array($receiptnum,$receipt['ir_price'],$total,$unpaind);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                'acnumber' => $userinfo['acnumber'],
                                'phone' => $userinfo['phone'],
                                'operator' => '系统',
                                'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                'product_name' => '',
                                'date' => time(),
                                'content' => '订单编号：'.$receiptnum.'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$unpaind,
                                'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                    }
        		}
        		$this->success('添加成功');
        	}else{
				$this->error('添加失败');
        	}
        }else{
        	$this->error('管理员密码错误');
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

	// 批量添加订单
	public function add_receipt(){
	 	$upload = post_upload();
	 	// 文件名称
		$file  = '.'.$upload['name'];
		$arr  = import_excel($file);
	 	foreach($arr as $key=>$value){
	 		if($key!=1 && $value[C] !=''){
	 			$data[] = $value;
	 		}
	 	}
	 	foreach ($data as $key => $value) {
	 		$product = M('Product')->where(array('ipid'=>$value[N]))->find();
	 		$userinfo = M('User')->where(array('CustomerID'=>$value[A]))->find();
	 		if($value[S] == 0){
	 			$riuid = $userinfo['iuid'];
	 			$rCustomerID = $value[A];
	 		}else if($value[S] == 1){
	 			//添加新用户
                $keyword= 'HPL';
                $custid = M('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
                if(empty($custid)){
                    $CustomerID = 'HPL00000001';
                }else{
                    $num   = substr($custid,3);
                    $nums  = $num+1;
                    $count = strlen($nums);
                    switch ($count) {
                        case '1':
                            $CustomerID = 'HPL0000000'.$nums;
                            break;
                        case '2':
                            $CustomerID = 'HPL000000'.$nums;
                            break;
                        case '3':
                            $CustomerID = 'HPL00000'.$nums;
                            break;
                        case '4':
                            $CustomerID = 'HPL0000'.$nums;
                            break;
                        case '5':
                            $CustomerID = 'HPL000'.$nums;
                            break;
                        case '6':
                            $CustomerID = 'HPL00'.$nums;
                            break;
                        case '7':
                            $CustomerID = 'HPL0'.$nums;
                            break;
                        default:
                            $CustomerID = 'HPL'.$nums;
                            break;
                    }
                }
                //用户资料
                $tmpe = array(
                    'EnrollerID'  =>$value[B],
                    'Sex'         =>'保密',
                    'LastName'    =>$value[C],
                    'FirstName'   =>$value[D],
                    'Email'       =>$value[M],
                    'PassWord'    =>md5($value[H]),
                    'acid'        =>217,
                    'acnumber'    =>$value[G],
                    'Phone'       =>$value[H],
                    'ShopAddress1'=>$value[L],
                    'ShopArea'    =>$value[K],
                    'ShopCity'    =>$value[J],
                    'ShopProvince'=>$value[I],
                    'ShopCountry' =>'中国',
                    'EnLastName'  =>$value[E],
                    'EnFirstName' =>$value[F],
                    'CustomerID'  =>$CustomerID,
                    'OrderDate'   =>date("m/d/Y h:i:s A"),
                    'Number'      =>1,
                    'TermsAndConditions' =>1,
                    'JoinedOn'    => time(),
                    'WvPass' => $value[H],
                );
                $update     = M('User')->add($tmpe);
                $riuid = $update;
                $rCustomerID = $CustomerID;
            }
			$receipt = array(
	 			'riuid' =>$riuid,
	 			'rCustomerID' => $rCustomerID,
	 			'ir_receiptnum' => date('YmdHis').rand(10000, 99999),
	 			'ir_desc' => $product['ip_name_zh'].'(已在接龙易交付押金'.$value[P].')',
	 			'ir_status' => 202,
	 			'ipid' => $value[N],
	 			'ir_productnum' => 1,
	 			'ir_point' => bcdiv($value[O],100,2),
	 			'ir_unpoint' => bcdiv(bcsub($value[O],$value[P]),100,2),
	 			'ir_price' => $value[O],
	 			'ir_unpaid' => bcsub($value[O],$value[P],2),
	 			'ia_name' => $value[C].$value[D],
	 			'ia_phone' => $value[H],
	 			'ia_province' => $value[I], 
	 			'ia_city' => $value[J],
	 			'ia_area' => $value[K],
	 			'ia_address' => $value[L],
	 			'ir_ordertype' => 4,
	 			'ir_date' => strtotime(gmdate('Y-m-d H:i:s',\PHPExcel_Shared_Date::ExcelToPHP($value[Q]))),
	 		);
	 		$receipt_result = M('Receipt')->add($receipt);

	 		$receiptson = array(
	 			'ir_receiptnum' => $receipt['ir_receiptnum'],
	 			'riuid' => $riuid,
	 			'pay_receiptnum' => date('YmdHis').rand(100000, 999999),
	 			'ir_price' => $value[P],
	 			'ir_point' => bcdiv($value[P],100,2),
	 			'ir_paytype' => 6,
	 			'cretime' => strtotime(gmdate('Y-m-d H:i:s',\PHPExcel_Shared_Date::ExcelToPHP($value[Q]))),
	 			'paytime' => strtotime(gmdate('Y-m-d H:i:s',\PHPExcel_Shared_Date::ExcelToPHP($value[Q]))),
	 			'status' => 2,
	 			'operator' => $_SESSION['user']['username'],
	 		);
	 		$receiptson_result = M('Receiptson')->add($receiptson);

	 		$receiptlist = array(
	 			'ir_receiptnum' => $receipt['ir_receiptnum'],
	 			'ipid' => $value[N],
	 			'ilid' => 0,
	 			'product_num' => 1,
	 			'product_price' => $product['ip_price_rmb'],
	 			'product_point' => $product['ip_point'],
	 			'product_name' => $product['ip_name_zh'],
	 			'product_picture' => $product['ip_picture_zh'],
	 		);
	 		$receiptlist_result = M('receiptlist')->add($receiptlist);
	 		if($receipt_result && $receiptson_result && $receiptlist_result){
	 			// 发送短信提示
                $templateId ='183580';
                $params     = array($product['ip_name_zh']);
                $sms        = D('Smscode')->sms($value[G],$value[H],$params,$templateId);
                if($sms['errmsg'] == 'OK'){
                    $contents = array(
                        'acnumber' => $value[G],
                        'phone' => $value[H],
                        'operator' => '系统',
                        'addressee' => $value[C].$value[D],
                        'product_name' => $product['ip_name_zh'],
                        'date' => time(),
                        'content' => '恭喜您，您的'.$product['ip_name_zh'].'订单已经生成，请登录HAPYLIFE，在订单里查看。',
                        'customerid' => $value[A]
                    );
                    $logs = M('SmsLog')->add($contents);
                }
	 		}
	 	}
	 	if($logs){
	 		$this->success('添加成功',U('Admin/Hapylife/receipt'));
	 	}else{
	 		$this->error('添加失败');
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
		$customerid = trim(I('get.customerid'));
		$wvcustomerid = trim(I('get.wvcustomerid'));
		$lastname = trim(I('get.lastname'));
		$firstname = trim(I('get.firstname'));
		$phone = trim(I('get.phone'));
		$enrollerid = trim(I('get.enrollerid'));
		
		$excel     = I('get.excel');
		$status    = I('get.status')-1;
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$assign    = D('User')->getPage(D('User'),$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='joinedon desc',$status,$starttime,$endtime);
		//导出excel
		if($excel == 'excel'){
			$data = D('User')->getPageAllmemBer(D('User'),$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='joinedon desc',$status,$starttime,$endtime);
			$export_excel = D('User')->export_excel($data['data']);
		}else{
			$this->assign($assign);
			$this->assign('count',$count);
			$this->assign('status',I('get.status'));
			$this->assign('customerid',$customerid);
			$this->assign('wvcustomerid',$wvcustomerid);
			$this->assign('lastname',$lastname);
			$this->assign('firstname',$firstname);
			$this->assign('phone',$phone);
			$this->assign('enrollerid',$enrollerid);
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
	* 用户退会
	**/
	public function isexit(){
		$id    =I('get.id');
		$isexit=I('get.isexit')?0:1;
		$map=array(
			'iuid'=>$id,
			'isexit'=>$isexit
		);
		
		$result=D('User')->save($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('退会失败');
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
		$session = session();
		//有密码账户搜索
		$count = M('user')->count();
		//账户昵称搜索
		$customerid = trim(I('get.customerid'));
		$wvcustomerid = trim(I('get.wvcustomerid'));
		$lastname = trim(I('get.lastname'));
		$firstname = trim(I('get.firstname'));
		$phone = trim(I('get.phone'));
		$enrollerid = trim(I('get.enrollerid'));
		
		$excel     = I('get.excel');
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$assign    = D('User')->getPageS(D('User'),$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='joinedon desc',$starttime,$endtime);

		//导出excel
		if($excel == 'excel'){
			$data = D('User')->getAllmemBer(D('User'),$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='joinedon desc',$starttime,$endtime);
			$export_excel = D('User')->export_excel($data['data']);
		}else{
			$this->assign($assign);
			$this->assign('count',$count);
			$this->assign('customerid',$customerid);
			$this->assign('wvcustomerid',$wvcustomerid);
			$this->assign('lastname',$lastname);
			$this->assign('firstname',$firstname);
			$this->assign('phone',$phone);
			$this->assign('enrollerid',$enrollerid);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('session',$session);
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

	/**
	* 查询用户在美国的资料
	**/ 
	public function searchForUsa(){
		$customerid = I('post.customerid');
		$usa = new \Common\UsaApi\Usa;
		$validateHpl = $usa->validateHpl($customerid);
		// $activities = $usa->activities($customerid);
		$dtPoint = $usa->dtPoint($customerid);

		if(!$validateHpl['errors'] && !$dtPoint['errors']){
			if($dtPoint['softCashCategories'][0]){
				$iu_dt = $dtPoint['softCashCategories'][0]['balance'];
			}else{
				$iu_dt = 0;
			}

			if($dtPoint['softCashCategories'][1]){
				$iu_ac = $dtPoint['softCashCategories'][1]['balance'];
			}else{
				$iu_ac = 0;
			}
			$data = array(
				'status' => 1,
				'validateHpl' => $validateHpl,
				// 'activities' => $activities,
				'iu_dt' => $iu_dt,
				'iu_ac' => $iu_ac,
			);
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}


	/*****************************************************************送货单管理******************************************************************/
	//送货单列表
	public function sendReceipt(){
		$mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $code[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==886){
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
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_paytime';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$assign    = D('Receipt')->getSendPage(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$order='ir_paytime asc');
		// 导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getSendPageSonAll(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$order='ir_paytime asc');
			$export_send_excel = D('Receipt')->export_send_excel($data['data']);
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

	//送货报表
	public function sendReport(){
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
			$status = '2,3,4,5,6,7,8';
		}else{
			$status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_paytime';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$array  = '测,测试,test,试,试点,testtest';
		$ipid = '46,47,48';
		$assign    = D('Receipt')->getSendPages(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$array,$ipid,$order='ir_paytime asc');
		// 导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getSendPageSonAlls(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$array,$ipid,$order='ir_paytime asc');
			$export_send_excel = D('Receipt')->export_send_excel($data['data']);
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

	// 修改收货人信息
	public function editAddress(){
		$data = I('post.');
		$map  = array('irid'=>$data['id']);
        $save = M('Receipt')->where($map)->save($data);
        if($save){
        	redirect($_SERVER['HTTP_REFERER']);
        }else{
        	$this->error('修改失败');
        }
	}

	// 退货申请
	public function saleReturn(){
		$data = I('post.');
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['rimg']=C('WEB_URL').$upload['name'];
		}else{
			$this->error('申请失败，请上传退货凭证');
		}
		if(empty($data['rdesc'])){
			$this->error('备注不能为空');
		}
		if($data){
			// 修改原订单状态
			$editStatus = M('Receipt')->where(array('ir_receiptnum'=>$data['rir_receiptnum']))->setfield('ir_status',5);
			$array = array(
				'rir_receiptnum' => $data['rir_receiptnum'],
				'rnir_receiptnum' => 'SR'.date('YmdHis').rand(10000, 99999),
				'rimg' => $data['rimg'],
				'rnum' => $data['rnum'],
				'rmoney' => $data['rmoney'],
				'rproposer' => $_SESSION['user']['username'],
				'roperator' => '',
				'applyTime' => time(),
				'rdesc' => $data['rdesc']
			);
			$addReturn = M('SaleReturn')->add($array);
		}
		
        if($addReturn){
        	redirect($_SERVER['HTTP_REFERER']);
        }else{
        	$this->error('申请失败');
        }
	}

	// 退货管理
	public function returns(){
		$word = I('get.word');
		$timeType = I('get.timeType')?I('get.timeType'):'applytime';
		$status = I('get.status');
		if($status == 0){
			$ir_status = array(5,8);
		}else{
			$ir_status = $status;
		}
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600*30:0;
		$rid = I('get.rid');
		$rir_receiptnum = I('get.rir_receiptnum');
		if($rid && $rir_receiptnum){
			// 修改订单退货状态
			$receipt = M('Receipt')->where(array('ir_receiptnum'=>$rir_receiptnum))->setfield('ir_status',8);
			// 修改退货记录
			$save = array(
				'status' => 1,
				'roperator' => $_SESSION['user']['username'],
				'confirmTime' => time(),
			);
			$sale = M('SaleReturn')->where(array('rid'=>$rid))->save($save);
			if($receipt && $sale){
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->error('确认失败');
			}
		}
		$assign = D('SaleReturn')->getSendPage(D('SaleReturn'),$word,$starttime,$endtime,$ir_status,$timeType);
		$this->assign($assign);
		$this->assign('status',I('get.status'));
		$this->assign('word',$word);
		$this->assign('timeType',$timeType);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}

	//查看订单明细
	public function sendReceiptSon(){
		$ir_receiptnum = I('get.ir_receiptnum');
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point';
		$assign = D('Receiptson')->getSendPageSon(D('Receiptson'),$ir_receiptnum,$field);
		// p($assign);
		$this->assign($assign);
		$this->display();
	}

	//查看订单明细
	public function sendReportSon(){
		$ir_receiptnum = I('get.ir_receiptnum');
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point';
		$assign = D('Receiptson')->getSendPageSon(D('Receiptson'),$ir_receiptnum,$field);
		// p($assign);
		$this->assign($assign);
		$this->display();
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
		$remove = explode('-',$data['username']);

		switch ($data['spotemplate']) {
			case '146228':
				$spotemplate = 146228;
				$spoparams   = array($remove[0],$data['productnams']);
				$content  	 = '亲爱的会员'.$remove[0].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。';
				$result = M('Receipt')->where(array('irid'=>$data['irid']))->setfield('is_send',2);
				$product_name = $data['productnams'];
				break;
			case '197006':
				$spotemplate = 197006;
				$spoparams   = array($data['ir_receiptnum']);
				$content  	 = '您的订单已发货，编号为'.$data['ir_receiptnum'].'，请保持手机通讯畅通！';
				$result = M('Receipt')->where(array('irid'=>$data['irid']))->setfield('is_send_out',2);
				$product_name = '发货通知';
				break;
		}
		
        $sponsorSms    = D('Smscode')->sms($data['acnumber'],$data['phone'],$spoparams,$spotemplate);
        
        if($sponsorSms['errmsg']=='OK'){
        	$mape  = array(
                'phone'   =>$data['phone'],
                'content'    =>$content,
                'acnumber'=>$data['acnumber'],
                'date'    =>time(),
                'operator' => $_SESSION['user']['username'],
                'product_name' => $product_name,
                'addressee' => $remove[0],
                'customerid' => $remove[1]
            );
            $add = D('SmsLog')->add($mape);
            if($add){
				redirect($_SERVER['HTTP_REFERER']);
            }else{
            	redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            redirect($_SERVER['HTTP_REFERER']);
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
       	
		$customerid = trim(I('get.customerid',''));
		$phone      = trim(I('get.phone',''));
		$addressee  = trim(I('get.addressee',''));
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();

		$assign    = D('SmsLog')->getSendPage(D('SmsLog'),$customerid,$phone,$addressee,$starttime,$endtime,$order='date desc');
		// p($assign);
		// die;		
		$this->assign($assign);
		$this->assign('customerid',$customerid);
		$this->assign('phone',$phone);
		$this->assign('addressee',$addressee);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->assign('code',$code);
		$this->display();
	}

	// 发送短信
	public function add_sends(){
		$data = I('post.');
		$remove = explode('-',$data['username']);
		switch ($data['psd']) {
			// 续费信息通知
			case '146227':
				$spotemplate  = 146227;
				$spoparams    = array($remove[0],$data['endtime']);
				$content      = '亲爱的会员'.$remove[0].'，这是系统提醒消息，请在'.$data['endtime'].'之前购买月费包。';
				$product_name = '月费购买通知消息';
				break;
			// 物流信息通知
			case '146228':
				$spotemplate  = 146228;
				$spoparams    = array($remove[0],$data['productnams']);
				$content      = '亲爱的会员'.$remove[0].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。';
				$product_name = $data['productnams'];
				break;
			// 优惠月费到期通知
			case '196995':
				$spotemplate  = 196995;
				$spoparams    = array($remove[0],$data['endtime']);
				$content      = '尊敬的'.$remove[0].'会员，您的免月费优惠期为'.$data['endtime'].'，请在优惠期结束前购买月费包。';
				$product_name = '优惠月费通知消息';
				break;
			// 套餐收费短信
			case '244290':
				$spotemplate  = 244290;
				$spoparams 	  = array($remove[0],$data['productnams']);
				$content      = '尊敬的'.$remove[0].'会员，今天是优惠'.$data['productnams'].'套餐的最后支付时间，请务必在今天晚上11:59前成功支付尾款，谢谢！';
				$product_name = $data['productnams'];
				break;
		}
		
        $sponsorSms    = D('Smscode')->sms($data['acnumber'],$data['phone'],$spoparams,$spotemplate);
        
        if($sponsorSms['errmsg']=='OK'){
        	$mape  = array(
                'phone'   =>$data['phone'],
                'content' =>$content,
                'acnumber'=>$data['acnumber'],
                'date'    =>time(),
                'operator' => $_SESSION['user']['username'],
                'addressee' => $remove[0],
                'customerid' => $remove[1],
                'product_name' => $product_name
            );
        	$add = D('SmsLog')->add($mape);
            if($add){
				$this->success('发送成功',U('Admin/Hapylife/sends'));
            }else{
            	$this->error('发送失败',U('Admin/Hapylife/sends'));
            }
        }else{
            $this->error('发送失败',U('Admin/Hapylife/sends'));
        }
	}

	/**
	* 查询买四送一订单
	**/ 
	public function search(){
		//202 未全额支付 2已支付,404隐藏
		$order_status = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$ir_status = '0,1,2,3,4,5,6,8,202,404';
		}else{
			$ir_status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_paytime';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$array  = '测试,测,试,测试点,test,testtest,测试测试,新建测试,测试地,测试点,测试账号';
		$assign    = D('Receipt')->getSendPagesearch(D('Receipt'),$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='ir_paytime asc');
		// 导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getSendPageSonAllsearch(D('Receipt'),$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='ir_paytime asc');
			$export_send_excel = D('Receipt')->export_send_excel($data['data']);
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

	//查看订单明细
	public function searchSon(){
		$ir_receiptnum = I('get.ir_receiptnum');
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point';
		$assign = D('Receiptson')->getSendPageSon(D('Receiptson'),$ir_receiptnum,$field);
		// p($assign);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 修改订单显示隐藏状态
	**/ 
	public function editStatus(){
		$setStatus = I('get.status');
		$irid = I('get.irid');
		$ir_status = I('get.ir_status');
		$onekey = I('get.onekey');
		$status = array(0,202);
		if($onekey){
			switch ($onekey) {
				case 'yes':
					$result = M('Receipt')->where(array('ir_status'=>array('in',$status),'ir_ordertype'=>4))->setfield('ir_status',404);
					break;
				case 'no':
					$result = M('Receipt')->where(array('ir_status'=>404,'ir_ordertype'=>4))->setfield('ir_status',202);
					break;
			}
		}else{
			$result = M('Receipt')->where(array('irid'=>$irid))->setfield('ir_status',$ir_status);
		}

		if($result){
			$this->redirect('Admin/Hapylife/search?status='.$setStatus);
		}else{
			$this->error('修改失败');
		}
	}

	/**
	* 奖金报表
	**/ 
	public function wvbonus(){
		$status = I('get.status',-1);
		$customerid = trim(I('get.customerid',''));
		$hplid = I('get.hplid','');
		$excel = I('get.excel');
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		if($status == -1){
			$status = array(0,1);
		}else{
			$status = I('get.status');
		}

		$data = M('WvBonusParities')->select();
		$assign    = D('WvBonus')->getSendPage(D('WvBonus'),$customerid,$hplid,$starttime,$endtime,$status,$order='id desc');

		foreach($assign['data'] as $key=>$value){
			$assign['data'][$key]['bonuses'] = json_decode($value['bonuses'],true);
			$assign['data'][$key]['ep'] = bcdiv(bcmul($assign['data'][$key]['bonuses'][0]['Amount'],$data[0]['parities'],2),100,2);
		}
		if($excel == 'excel'){
			$message = D('WvBonus')->getAll(D('WvBonus'),$customerid,$hplid,$starttime,$endtime,$status,$order='id');
			foreach($message['data'] as $key=>$value){
				$message['data'][$key]['bonuses'] = json_decode($value['bonuses'],true);
				$message['data'][$key]['ep'] = bcdiv(bcmul($message['data'][$key]['bonuses'][0]['Amount'],$data[0]['parities'],2),100,2);
				$message['data'][$key]['parities'] = $data[0]['parities'];
			}
			$export_excel = D('WvBonus')->export_excel($message['data']);
		}else{
			$this->assign($assign);
			$this->assign('status',$status);
			$this->assign('customerid',$customerid);
			$this->assign('hplid',$hplid);
			$this->assign('starttime',I('get.starttime'));
			$this->assign('endtime',I('get.endtime'));
			$this->assign('parities',$data[0]['parities']);
			$this->display();
		}
	}

	/**
	* 发放奖金
	**/ 
	public function addBonus(){
		$data = M('WvBonusParities')->select();
		$id = I('get.id');
		$customerid = I('get.customerid');
		$amount = bcdiv(bcmul(I('get.amount'),$data[0]['parities'],2),100,2);
		$userinfo = M('User')->where(array('customerid'=>$customerid))->find();
		// p($userinfo);die;
		$iu_point = bcadd($userinfo['iu_point'],$amount,2);
		$result = M('User')->where(array('customerid'=>$customerid))->setfield('iu_point',$iu_point);
		if($result){
			$array = array(
				'pointNo' => date('YmdHis').rand(100000, 999999),
				'iuid' => $userinfo['iuid'],
				'hu_username' => $userinfo['lastname'].$userinfo['firstname'],
				'hu_nickname' => $userinfo['customerid'],
				'send' => $_SESSION['user']['username'],
				'received' => $customerid,
				'opename' => $_SESSION['user']['username'],
				'getpoint' => $amount,
				'pointtype' => 3,
				'iu_bank' => $userinfo['bankname'],
				'iu_bankbranch' => $userinfo['subname'],
				'iu_bankaccount' => $userinfo['bankaccount'],
				'iu_bankuser' => $userinfo['lastname'].$userinfo['firstname'],
				'iu_bankprovince' => $userinfo['bankprovince'],
				'iu_bankcity' => $userinfo['bankcity'],
				'date' => date('Y-m-d H:i:s',time()),
				'handletime' => date('Y-m-d H:i:s',time()),
				'status' => 2,
				'whichApp' => 5,
			);
			$array['feepoint'] = 0;
			$array['realpoint'] = bcsub($amount,$array['feepoint'],2);
			$array['leftpoint'] = bcadd($userinfo['iu_point'],$array['realpoint'],2);
			$array['content'] = '系统在'.date('Y-m-d H:i:s',time()).'时，发放奖金到'.$userinfo['customerid'].'，剩EP余额'.$array['leftpoint'];
			$addGetPoint = M('Getpoint')->add($array);
			if($addGetPoint){
				$saveMsg = array(
					'BonusStatus' => 1,
					'BonusPaymentTime' => time(),
					'Operator' => $_SESSION['user']['username']
				);
				$saveStatus = M('WvBonus')->where(array('id'=>$id))->save($saveMsg);
				$content = $_SESSION['user']['username'].'在'.date('Y-m-d H:i:s').',给'.$customerid.'发放了'.$amount.'EP';
				$log = array(
					'customerid' => $customerid,
					'operator' => $_SESSION['user']['username'],
					'addressee' => $userinfo['lastname'].$userinfo['firstname'],
					'date' => time(),
					'content' => $content,
				);
				$log_result = M('WvBonusLog')->add($log);
				if($log_result){
					$templateId ='244298';
		            $params     = array($customerid,$array['realpoint'],$array['leftpoint']);
		            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
		            if($sms['errmsg'] == 'OK'){
		            	$addressee = $userinfo['lastname'].$userinfo['firstname'];
		                $contents = '尊敬的'.$customerid.'会员，您已成功收到EP'.$array['realpoint'].'，当前EP余额是'.$array['leftpoint'].'，请登录Hapylife查询。';
		            	$addlog = D('Smscode')->addLog($userinfo['acnumber'],$userinfo['phone'],'系统',$addressee,'积分通知',$contents,$customerid);
		            }
				}
			}
		}
		if($addlog){
			$this->success('发放成功',U('Admin/Hapylife/wvbonus'));
		}else{
			$this->error('发放失败',U('Admin/Hapylife/wvbonus'));
		}
	}

	/**
	* 修改当前汇率
	**/ 
	public function editParities(){
		$parities = I('post.parities');
		$change = I('post.change');
		if($change){
			$data = M('WvBonusParities')->select();
			if($change != $data[1]['parities']){
				$result = M('WvBonusParities')->where(array('pid'=>2))->setfield('parities',$change);
				if($result){
					$this->redirect('Admin/Point/index');
				}else{
					$this->error('修改失败',U('Admin/Point/index'));
				}
			}else{
				$this->redirect('Admin/Point/index');
			}
		}

		if($parities){
			$data = M('WvBonusParities')->select();
			if($parities != $data[0]['parities']){
				$result = M('WvBonusParities')->where(array('pid'=>1))->setfield('parities',$parities);
				if($result){
					$this->redirect('Admin/Hapylife/wvbonus');
				}else{
					$this->error('修改失败',U('Admin/Hapylife/wvbonus'));
				}
			}else{
				$this->redirect('Admin/Hapylife/wvbonus');
			}
		}
	}

	/**
	* wv国际会员推荐人数统计
	**/ 
	public function wvRecommend(){
		$p = I('get.p',1);
		$wv = I('get.wv');
		$assign = D('User')->getMemberList(D('User'),$wv);
		$this->assign($assign);
		$this->assign('wv',$wv);
		$this->display();
	}

	/**
	* wv推送消息列表
	**/ 
	public function wvNotification(){
		$p = I('get.p',1);
		$CustomerId = I('get.CustomerId');
		$HplId = trim(strtoupper(I('get.HplId')));
		$OrderId = I('get.OrderId');
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$data = M('wvNotification')->where(array('NotificationType'=>1))->order('id DESC')->select();
		foreach($data as $key=>$value){
			$data[$key]['messages'] = json_decode($value['messages'],true);
			if($starttime && empty($endtime)){
				if(strtotime($data[$key]['date']) >= $starttime){
					if($CustomerId && empty($HplId) && empty($OrderId)){
						if($data[$key]['messages']['CustomerId'] == $CustomerId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && empty($HplId) && $OrderId){
						if($data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && $HplId && empty($OrderId)){
						if($data[$key]['messages']['HplId'] == $HplId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && $HplId && empty($OrderId)){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && $HplId && $OrderId){
						if($data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && empty($HplId) && $OrderId){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && $HplId && $OrderId){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else{
						$list[] = $data[$key];
					}
				}
			}else if($starttime && $endtime){
				if(strtotime($data[$key]['date']) >= $starttime && strtotime($data[$key]['date']) <= $endtime){
					if($CustomerId && empty($HplId) && empty($OrderId)){
						if($data[$key]['messages']['CustomerId'] == $CustomerId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && empty($HplId) && $OrderId){
						if($data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && $HplId && empty($OrderId)){
						if($data[$key]['messages']['HplId'] == $HplId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && $HplId && empty($OrderId)){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId){
							$list[] = $data[$key];
						}
					}else if(empty($CustomerId) && $HplId && $OrderId){
						if($data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && empty($HplId) && $OrderId){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else if($CustomerId && $HplId && $OrderId){
						if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
							$list[] = $data[$key];
						}
					}else{
						$list[] = $data[$key];
					}
				}
			}else if(empty($starttime) && empty($endtime)){
				if($CustomerId && empty($HplId) && empty($OrderId)){
					if($data[$key]['messages']['CustomerId'] == $CustomerId){
						$list[] = $data[$key];
					}
				}else if(empty($CustomerId) && empty($HplId) && $OrderId){
					if($data[$key]['messages']['OrderId'] == $OrderId){
						$list[] = $data[$key];
					}
				}else if(empty($CustomerId) && $HplId && empty($OrderId)){
					if($data[$key]['messages']['HplId'] == $HplId){
						$list[] = $data[$key];
					}
				}else if($CustomerId && $HplId && empty($OrderId)){
					if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId){
						$list[] = $data[$key];
					}
				}else if(empty($CustomerId) && $HplId && $OrderId){
					if($data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
						$list[] = $data[$key];
					}
				}else if($CustomerId && empty($HplId) && $OrderId){
					if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['OrderId'] == $OrderId){
						$list[] = $data[$key];
					}
				}else if($CustomerId && $HplId && $OrderId){
					if($data[$key]['messages']['CustomerId'] == $CustomerId && $data[$key]['messages']['HplId'] == $HplId && $data[$key]['messages']['OrderId'] == $OrderId){
						$list[] = $data[$key];
					}
				}else{
					$list[] = $data[$key];
				}
			}else{
				$list[] = $data[$key];
			}
		}
		$assign = pages($list,$p,20);
		$this->assign($assign);
		$this->assign('CustomerId',$CustomerId);
		$this->assign('HplId',$HplId);
		$this->assign('OrderId',$OrderId);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}

	/**
	* 获取成功注册用户的生日
	**/ 
	public function getBirthday(){
		$data = M('User')->order('iuid DESC')->select();
		foreach($data as $key=>$value){
			if(!empty($value['wvcustomerid']) && substr($value['customerid'],0,3) == 'HPL'){
				$userinfo[] = $value;
			}
		}
		$unlist = array('测试','测','试','测试点','test','testtest','测试测试','新建测试','测试地','测试点','测试账号');
		foreach($userinfo as $key=>$value){
			if(!in_array($value['lastname'],$unlist) && !in_array($value['firstname'],$unlist)){
				$list[] = $value;
			}
		}
		$export_excel = D('User')->export_excelBD($list);
	}

//***********************常见问题解答*******************
	/**
	* 常见问题解答列表
	**/
	public function faq(){
		$p = I('get.p',1);
		$data = D('Faq')->getTreeData('tree','order_number','','fid');
		$assign = pages($data,$p,20);
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加常见问题解答
	**/
	public function add_faq(){
		$data = I('post.');
		$result=D('Faq')->addData($data);
		if($result){
			$this->redirect('Admin/Hapylife/faq');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑常见问题解答
	**/
	public function edit_faq(){
		$data=I('post.');
		$map=array(
			'fid'=>$data['id']
		);
		$result=D('Faq')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/faq');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除常见问题解答
	**/
	public function delete_faq(){
		$id=I('get.id');
		$result = D('Faq')->where(array('fid'=>$id))->delete();
		$res = D('Faq')->where(array('pid'=>$id))->delete();
		if($result){
			$this->redirect('Admin/Hapylife/faq');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 显示常见问题解答
	**/
	public function faq_show(){
		$data=I('get.');
		$map =array(
			'fid'=>$data['id']
		);
		$result=D('Faq')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/faq');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	*常见问题解答排序
	*/
	public function order_faq(){
		$data=I('post.');
		$result=D('Faq')->orderData($data,$id='fid','order_number');
		if($result){
			$this->redirect('Admin/Hapylife/faq');
		}else{
			$this->error('排序失败');
		}
	}

//***********************群发通告短信*******************
	// 群发通告短信
	public function massNote(){
		$data = I('post.');
		switch($data['psd']){
			case '146228':
				$spotemplate = 146228;
				$spoparams = array($data['content']);
				$content = '亲爱的会员，很高兴通知你最新的优惠'.$data['content'].'，详情请参阅官方资料。';
				break;
		}

		switch($data['mbType']){
			case '0':
				$this->error('请选择接收人群',U('Admin/Hapylife/sends'));
				break;
			case '1':
				$level = array('NEQ','Pc');
				break;
			case '2':
				$level = array('IN','Platinum');
				break;
			case '3':
				$level = array('IN','Gold');
				break;
		}
        $data = M('User')->where(array('distributortype'=>$level))->select();
        p($data);die;
        foreach($data as $key=>$value){
        	$addressee = $value['lastname'].$value['firstname'];
        	$sponsorSms    = D('Smscode')->sms($value['acnumber'],$value['phone'],$spoparams,$spotemplate);
	        if($sponsorSms['result'] == 0){
	            $result = D('Smscode')->addLog($value['acnumber'],$value['phone'],'系统',$addressee,'群发通过',$content,$value['customerid']);
	        }else{
	            $result = D('Smscode')->addLog($value['acnumber'],$value['phone'],'系统',$addressee,$sponsorSms['errmsg'],$content,$value['customerid']);
	        }
        }

        if($result){
        	$this->success('发送成功',U('Admin/Hapylife/sends'));
        }else{
        	$this->error('发送失败',U('Admin/Hapylife/sends'));
        }
	}

//***********************外部链接***************
	/**
	* 外部链接列表
	**/
	public function outlink(){
		$assign=D('OutsideLink')->getPage(D('OutsideLink'),$map=array());
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加外部链接
	**/
	public function add_outlink(){
		$data=I('post.');
		$result=D('OutsideLink')->add($data);
		if($result){
			$this->redirect('Admin/Hapylife/outlink');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑外部链接
	**/
	public function edit_outlink(){
		$data=I('post.');
		$map=array(
			'lid'=>$data['lid']
		);
		$result=D('OutsideLink')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hapylife/outlink');
		}else{
			$this->error('编辑失败');
		}

	}

	/**
	* 删除外部链接
	**/
	public function delete_outlink(){
		$id=I('get.lid');
		$map=array(
			'lid'=>$id
		);
		$result=D('OutsideLink')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hapylife/outlink');
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 是否显示外部链接
	**/
	public function show_outlink(){
		$data=I('get.');
		$map =array(
			'lid'=>$data['lid']
		);
		$result=D('OutsideLink')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

/***************通告管理***********************/ 
	/**
	* 通告列表
	**/ 
	public function notice(){
		$assign = D('Notice')->getAll(D('Notice'),'nid DESC');
		$this->assign($assign);
		$this->display();
	}

	/**
	* 添加通告
	**/ 
	public function add_notice(){
		$upload=post_upload();
		$data['img']=C('WEB_URL').$upload['name'];
		$result=D('Notice')->addData($data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('添加失败');
		}
	}

	/**
	* 编辑通告
	**/ 
	public function edit_notice(){
		$nid = I('post.id');
		$map=array(
			'nid'=>$nid
		);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['img']=C('WEB_URL').$upload['name'];
		}
		$result=D('Notice')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 删除通告
	**/ 
	public function delect_notice(){
		$nid=I('get.nid');
		$map=array(
			'nid'=>$nid
		);
		$result=D('Notice')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 是否显示
	**/ 
	public function show_notice(){
		$data=I('get.');
		$map =array(
			'nid'=>$data['nid']
		);
		$result=D('Notice')->editData($map,$data);
		if($result){
			$res = D('Notice')->where(array('nid'=>array('NOT IN',$data['nid'])))->setfield('isshow',0);
		}
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	* 推荐人数统计
	**/ 
	public function enroller(){
		$p = I('get.p',1);
		$data = M('User')->where(array('isexit'=>1))->select();
		foreach ($data as $key => $value) {
			$customeridArr[$key]['customerid'] = $value['customerid'];
			$customeridArr[$key]['name'] = $value['lastname'].$value['firstname'];
		}
		foreach ($customeridArr as $key => $value) {
			foreach($data as $k=>$v){
				if($v['enrollerid']==$value['customerid']){
					$newData[$key]['count'][$k]['customerid'] = $v['customerid'];
					$newData[$key]['count'][$k]['name'] = $v['lastname'].$v['firstname'];
					$newData[$key]['customerid'] = $value['customerid'];
					$newData[$key]['name'] = $value['name'];
				}
			}
		}
		foreach($newData as $key=>$value){
			$newData[$key]['num'] = count($value['count']);
		}
		$newData = array_sort($newData,'num','DESC');
		$assign = pages($newData,$p,25);
		$this->assign($assign);
		$this->display();
	}

/***********************wv推送*******************************/ 
	/**
	* 通过wv推送
	* 账号状态变更
	**/ 
	public function changeStatus(){
		$p = I('get.p',1);
		$HapyLifeId = I('get.HapyLifeId');
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:0;
		$data = M('wvNotification')->where(array('NotificationType'=>3))->order('id DESC')->select();
		foreach($data as $key=>$value){
			$data[$key]['messages'] = json_decode($value['messages'],true);
			if($starttime){
				if(strtotime($data[$key]['date']) >= $starttime){
					if($HapyLifeId){
						if($data[$key]['messages']['HapyLifeId'] == $HapyLifeId){
		                    $list[] = $data[$key];
		                }
					}else{
						$list[] = $data[$key];
					}
				}
			}
			if($endtime){
				if(strtotime($data[$key]['date']) >= $starttime && strtotime($data[$key]['date']) <= $endtime){
					if($HapyLifeId){
						if($data[$key]['messages']['HapyLifeId'] == $HapyLifeId){
		                    $list[] = $data[$key];
		                }
					}else{
						$list[] = $data[$key];
					}
				}
			}
		}
		$assign = pages($list,$p,20);
		$this->assign($assign);
		$this->assign('HapyLifeId',$HapyLifeId);
		$this->assign('starttime',I('get.starttime'));
		$this->assign('endtime',I('get.endtime'));
		$this->display();
	}

	/**
	* 会员EP以及DT
	**/ 
	public function both(){
		$json = '[

    {
        "customerid": "HPL00000035",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "云峰",
        "status": false
    },
    {
        "customerid": "HPL00000044",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "冯",
        "firstname": "然",
        "status": false
    },
    {
        "customerid": "HPL00000047",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "瑞芳",
        "status": false
    },
    {
        "customerid": "HPL00000053",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "周",
        "firstname": "焰生",
        "status": false
    },
    {
        "customerid": "HPL00000054",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "窦",
        "firstname": "金喜",
        "status": false
    },
    {
        "customerid": "HPL00000058",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "文超",
        "status": false
    },
    {
        "customerid": "HPL00000061",
        "iu_point": "0",
        "iu_dt": 830,
        "lastname": "王",
        "firstname": "玉兰",
        "status": true
    },
    {
        "customerid": "HPL00000062",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "雪芬",
        "status": false
    },
    {
        "customerid": "HPL00000075",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "彭",
        "firstname": "素华",
        "status": false
    },
    {
        "customerid": "HPL00000081",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孔",
        "firstname": "瑞瑞",
        "status": false
    },
    {
        "customerid": "HPL00000085",
        "iu_point": "0",
        "iu_dt": 925,
        "lastname": "李",
        "firstname": "志强",
        "status": true
    },
    {
        "customerid": "HPL00000086",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "薛",
        "firstname": "广花",
        "status": false
    },
    {
        "customerid": "HPL00000112",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "周",
        "firstname": "本英",
        "status": false
    },
    {
        "customerid": "HPL00000113",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "颖",
        "status": false
    },
    {
        "customerid": "HPL00000114",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杜",
        "firstname": "秀铃",
        "status": false
    },
    {
        "customerid": "HPL00000115",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "国凤",
        "status": false
    },
    {
        "customerid": "HPL00000158",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "凤来",
        "status": false
    },
    {
        "customerid": "HPL00000168",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "马",
        "firstname": "忱",
        "status": false
    },
    {
        "customerid": "HPL00000170",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "侯",
        "firstname": "燕娟",
        "status": false
    },
    {
        "customerid": "HPL00000177",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孔",
        "firstname": "祥鲜",
        "status": false
    },
    {
        "customerid": "HPL00000178",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "春云",
        "status": false
    },
    {
        "customerid": "HPL00000180",
        "iu_point": "5.28",
        "iu_dt": 1180,
        "lastname": "魏",
        "firstname": "亚梅",
        "status": true
    },
    {
        "customerid": "HPL00000182",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曹",
        "firstname": "艳萍",
        "status": false
    },
    {
        "customerid": "HPL00000185",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "林知",
        "status": false
    },
    {
        "customerid": "HPL00000188",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "静",
        "status": false
    },
    {
        "customerid": "HPL00000192",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "柳",
        "firstname": "萌",
        "status": false
    },
    {
        "customerid": "HPL00000193",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "白",
        "firstname": "似冰",
        "status": false
    },
    {
        "customerid": "HPL00000212",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "薛",
        "firstname": "愉平",
        "status": false
    },
    {
        "customerid": "HPL00000214",
        "iu_point": "63.58",
        "iu_dt": 1580,
        "lastname": "韩",
        "firstname": "旭",
        "status": true
    },
    {
        "customerid": "HPL00000219",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "海",
        "status": false
    },{
        "customerid": "HPL00123483",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "戴",
        "firstname": "兰萍",
        "status": false
    },
    {
        "customerid": "HPL00123487",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "翟",
        "firstname": "玉中",
        "status": false
    },
    {
        "customerid": "HPL00123490",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "薇",
        "status": false
    },
    {
        "customerid": "HPL00123492",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "连军",
        "status": false
    },
    {
        "customerid": "HPL00123510",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "宝强",
        "status": false
    },
    {
        "customerid": "HPL00123515",
        "iu_point": "9.9",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "舒丹",
        "status": false
    },
    {
        "customerid": "HPL00123522",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "高",
        "firstname": "劲芬",
        "status": false
    },
    {
        "customerid": "HPL00123526",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "津欣",
        "status": false
    },
    {
        "customerid": "HPL00123532",
        "iu_point": "0",
        "iu_dt": 380,
        "lastname": "黄",
        "firstname": "诚",
        "status": true
    },
    {
        "customerid": "HPL00123533",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "宏韬",
        "status": false
    },
    {
        "customerid": "HPL00123561",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "宝莲",
        "status": false
    },
    {
        "customerid": "HPL00123562",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "皓勋",
        "status": false
    },
    {
        "customerid": "HPL00123563",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "芳",
        "status": false
    },
    {
        "customerid": "HPL00123565",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "彩霞",
        "status": false
    },
    {
        "customerid": "HPL00123568",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "红",
        "status": false
    },
    {
        "customerid": "HPL00123580",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "邵",
        "firstname": "妮",
        "status": false
    },
    {
        "customerid": "HPL00123587",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "谭",
        "firstname": "晓琴",
        "status": false
    },
    {
        "customerid": "HPL00123593",
        "iu_point": "0",
        "iu_dt": 380,
        "lastname": "耿",
        "firstname": "秀英",
        "status": true
    },
    {
        "customerid": "HPL00123594",
        "iu_point": "0",
        "iu_dt": 50,
        "lastname": "崔",
        "firstname": "金平",
        "status": true
    },
    {
        "customerid": "HPL00123599",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "华",
        "status": false
    },
    {
        "customerid": "HPL00123601",
        "iu_point": "6.6",
        "iu_dt": 0,
        "lastname": "梁",
        "firstname": "艳",
        "status": false
    },
    {
        "customerid": "HPL00123603",
        "iu_point": "5.3",
        "iu_dt": 0,
        "lastname": "祁",
        "firstname": "春华",
        "status": false
    },
    {
        "customerid": "HPL00123605",
        "iu_point": "3.3",
        "iu_dt": 580,
        "lastname": "罗",
        "firstname": "正云",
        "status": true
    },
    {
        "customerid": "HPL00123607",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "白",
        "firstname": "玉山",
        "status": false
    },
    {
        "customerid": "HPL00123608",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "宋",
        "firstname": "雁翔",
        "status": false
    },
    {
        "customerid": "HPL00123610",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "亚文",
        "status": false
    },
    {
        "customerid": "HPL00123611",
        "iu_point": "0",
        "iu_dt": 680,
        "lastname": "苏",
        "firstname": "林林",
        "status": true
    },
    {
        "customerid": "HPL00123612",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "首",
        "firstname": "荣芳",
        "status": false
    },
    {
        "customerid": "HPL00123621",
        "iu_point": "1.32",
        "iu_dt": 0,
        "lastname": "雷",
        "firstname": "华利",
        "status": false
    },
    {
        "customerid": "HPL00123622",
        "iu_point": "5.94",
        "iu_dt": 390,
        "lastname": "胡",
        "firstname": "小卒",
        "status": true
    },
    {
        "customerid": "HPL00123623",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "翟",
        "firstname": "茹",
        "status": false
    },
    {
        "customerid": "HPL00123626",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "巩",
        "firstname": "晓蕾",
        "status": false
    },
    {
        "customerid": "HPL00123629",
        "iu_point": "1.32",
        "iu_dt": 680,
        "lastname": "王",
        "firstname": "金朵",
        "status": true
    },
    {
        "customerid": "HPL00123630",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "煜芳",
        "status": false
    },
    {
        "customerid": "HPL00123634",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "彦华",
        "status": false
    },
    {
        "customerid": "HPL00123635",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "樊",
        "firstname": "露",
        "status": false
    },
    {
        "customerid": "HPL00123636",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "一晗",
        "status": false
    },
    {
        "customerid": "HPL00123637",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "学霞",
        "status": false
    },
    {
        "customerid": "HPL00123638",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "韦",
        "firstname": "达江",
        "status": false
    },
    {
        "customerid": "HPL00123641",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "余",
        "firstname": "成恩",
        "status": false
    },
    {
        "customerid": "HPL00123642",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曹",
        "firstname": "群凤",
        "status": false
    },
    {
        "customerid": "HPL00123543",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "海生",
        "status": false
    },
    {
        "customerid": "HPL00123645",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "边",
        "firstname": "雪梅",
        "status": false
    },
    {
        "customerid": "HPL00123646",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "慧",
        "status": false
    },
    {
        "customerid": "HPL00123647",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "马",
        "firstname": "勇",
        "status": false
    },
    {
        "customerid": "HPL00123648",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "闫",
        "firstname": "爽",
        "status": false
    },
    {
        "customerid": "HPL00123649",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孔",
        "firstname": "令义",
        "status": false
    },
    {
        "customerid": "HPL00123650",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "彪",
        "status": false
    },
    {
        "customerid": "HPL00123652",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "程",
        "firstname": "国华",
        "status": false
    },
    {
        "customerid": "HPL00123654",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "勇",
        "status": false
    },
    {
        "customerid": "HPL00123655",
        "iu_point": "0",
        "iu_dt": 515,
        "lastname": "储",
        "firstname": "洪方",
        "status": true
    },
    {
        "customerid": "HPL00123656",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "李滨",
        "status": false
    },
    {
        "customerid": "HPL00123657",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "方",
        "firstname": "婉琼",
        "status": false
    },
    {
        "customerid": "HPL00123660",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "国芹",
        "status": false
    },
    {
        "customerid": "HPL00123661",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "然",
        "status": false
    },
    {
        "customerid": "HPL00123662",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "徐",
        "firstname": "载强",
        "status": false
    },
    {
        "customerid": "HPL00123663",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "舒",
        "firstname": "国凤",
        "status": false
    },
    {
        "customerid": "HPL00123664",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "萍",
        "status": false
    },
    {
        "customerid": "HPL00123665",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "姜",
        "firstname": "颖",
        "status": false
    },
    {
        "customerid": "HPL00123667",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "爱明",
        "status": false
    },
    {
        "customerid": "HPL00123715",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "桂英",
        "status": false
    },
    {
        "customerid": "HPL00123716",
        "iu_point": "3.3",
        "iu_dt": 880,
        "lastname": "徐",
        "firstname": "雯",
        "status": true
    },
    {
        "customerid": "HPL00123719",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曹",
        "firstname": "雪",
        "status": false
    },
    {
        "customerid": "HPL00123720",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "沈",
        "firstname": "志兵",
        "status": false
    },
    {
        "customerid": "HPL00123721",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "邵",
        "firstname": "君瑞",
        "status": false
    },
    {
        "customerid": "HPL00123722",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "凤梅",
        "status": false
    },
    {
        "customerid": "HPL00123723",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "席",
        "firstname": "婷婷",
        "status": false
    },
    {
        "customerid": "HPL00123724",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "徐",
        "firstname": "铜铜",
        "status": false
    },
    {
        "customerid": "HPL00123736",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "肖",
        "firstname": "丽华",
        "status": false
    },
    {
        "customerid": "HPL00123738",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "段",
        "firstname": "玉玲",
        "status": false
    },
    {
        "customerid": "HPL00123739",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "平",
        "status": false
    },
    {
        "customerid": "HPL00123742",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "庄",
        "firstname": "永行",
        "status": false
    },
    {
        "customerid": "HPL00123743",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "小梅",
        "status": false
    },
    {
        "customerid": "HPL00123744",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "米",
        "firstname": "裕湘",
        "status": false
    },
    {
        "customerid": "HPL00123745",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "连东",
        "status": false
    },
    {
        "customerid": "HPL00123746",
        "iu_point": "4.62",
        "iu_dt": 0,
        "lastname": "汤",
        "firstname": "珍",
        "status": false
    },
    {
        "customerid": "HPL00123747",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "淑英",
        "status": false
    },
    {
        "customerid": "HPL00123748",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "石",
        "firstname": "长春",
        "status": false
    },
    {
        "customerid": "HPL00123749",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "杰",
        "status": false
    },
    {
        "customerid": "HPL00123750",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "卢",
        "firstname": "芬",
        "status": false
    },
    {
        "customerid": "HPL00123752",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "罗",
        "firstname": "丽玲",
        "status": false
    },
    {
        "customerid": "HPL00123754",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "段",
        "firstname": "玉竹",
        "status": false
    },
    {
        "customerid": "HPL00123755",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "陇",
        "status": false
    },
    {
        "customerid": "HPL00123756",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "桂红",
        "status": false
    },
    {
        "customerid": "HPL00123757",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "景红",
        "status": false
    },
    {
        "customerid": "HPL00123758",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "颖",
        "status": false
    },
    {
        "customerid": "HPL00123759",
        "iu_point": "0",
        "iu_dt": 380,
        "lastname": "高",
        "firstname": "金波",
        "status": true
    },
    {
        "customerid": "HPL00123762",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "梦宇",
        "status": false
    },
    {
        "customerid": "HPL00123763",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "小艳",
        "status": false
    },
    {
        "customerid": "HPL00123764",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吴",
        "firstname": "亚军",
        "status": false
    },
    {
        "customerid": "HPL00123803",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "唐",
        "firstname": "香娟",
        "status": false
    },
    {
        "customerid": "HPL00123804",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "马",
        "firstname": "铁军",
        "status": false
    },
    {
        "customerid": "HPL00123805",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "兴",
        "status": false
    },
    {
        "customerid": "HPL00123806",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "谭",
        "firstname": "女阮",
        "status": false
    },
    {
        "customerid": "HPL00123807",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "倪",
        "firstname": "世佳",
        "status": true
    },
    {
        "customerid": "HPL00123808",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "陈",
        "firstname": "嘉炜",
        "status": true
    },
    {
        "customerid": "HPL00123810",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "房",
        "firstname": "建丽",
        "status": false
    },
    {
        "customerid": "HPL00123812",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "芬兰",
        "status": false
    },
    {
        "customerid": "HPL00123813",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "建明",
        "status": false
    },
    {
        "customerid": "HPL00123815",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "雁",
        "status": false
    },
    {
        "customerid": "HPL00123821",
        "iu_point": "0",
        "iu_dt": "0",
        "lastname": "秦",
        "firstname": "鹏",
        "status": true
    },
    {
        "customerid": "HPL00123824",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "琴",
        "status": false
    },
    {
        "customerid": "HPL00123825",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "会来",
        "status": false
    },
    {
        "customerid": "HPL00123826",
        "iu_point": "0",
        "iu_dt": 238,
        "lastname": "梁",
        "firstname": "凤英",
        "status": true
    },
    {
        "customerid": "HPL00123827",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "程",
        "status": false
    },
    {
        "customerid": "HPL00123828",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "伍",
        "firstname": "萍",
        "status": false
    },
    {
        "customerid": "HPL00123829",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "雄",
        "status": false
    },
    {
        "customerid": "HPL00123830",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "肖",
        "firstname": "燕",
        "status": false
    },
    {
        "customerid": "HPL00123832",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "饶",
        "firstname": "霞",
        "status": false
    },
    {
        "customerid": "HPL00123833",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "蒋",
        "firstname": "小虎",
        "status": false
    },
    {
        "customerid": "HPL00123837",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "锐",
        "status": false
    },
    {
        "customerid": "HPL00123840",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "魏",
        "firstname": "亚栋",
        "status": false
    },
    {
        "customerid": "HPL00123842",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "姚",
        "firstname": "萍萍",
        "status": false
    },
    {
        "customerid": "HPL00123843",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "会翠",
        "status": false
    },
    {
        "customerid": "HPL00123845",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "俞",
        "firstname": "时敏",
        "status": false
    },
    {
        "customerid": "HPL00123846",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "能勇",
        "status": false
    },
    {
        "customerid": "HPL00123848",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "云霞",
        "status": false
    },
    {
        "customerid": "HPL00123849",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吴",
        "firstname": "朝芳",
        "status": false
    },
    {
        "customerid": "HPL00123851",
        "iu_point": "0",
        "iu_dt": 710,
        "lastname": "聂",
        "firstname": "全利",
        "status": true
    },
    {
        "customerid": "HPL00123853",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "玉芳",
        "status": false
    },
    {
        "customerid": "HPL00000221",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "冰",
        "status": false
    },
    {
        "customerid": "HPL00000229",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "董",
        "firstname": "希阳",
        "status": false
    },
    {
        "customerid": "HPL00000238",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "佳颖",
        "status": false
    },
    {
        "customerid": "HPL00000239",
        "iu_point": "1.32",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "瑶",
        "status": false
    },
    {
        "customerid": "HPL00000242",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "启阳",
        "status": false
    },
    {
        "customerid": "HPL00000243",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "唐",
        "firstname": "俊",
        "status": false
    },
    {
        "customerid": "HPL00000244",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "汪",
        "firstname": "柏英",
        "status": false
    },
    {
        "customerid": "HPL00000245",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "夏",
        "firstname": "美林",
        "status": false
    },
    {
        "customerid": "HPL00000246",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "古",
        "firstname": "运锋",
        "status": false
    },
    {
        "customerid": "HPL00000247",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "宋",
        "firstname": "金凤",
        "status": false
    },
    {
        "customerid": "HPL00000249",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吴",
        "firstname": "庭智",
        "status": false
    },
    {
        "customerid": "HPL00000251",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "果",
        "firstname": "炳含",
        "status": false
    },
    {
        "customerid": "HPL00000253",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "云云",
        "status": false
    },
    {
        "customerid": "HPL00000254",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "振娅",
        "status": false
    },
    {
        "customerid": "HPL00000258",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "毛伟",
        "firstname": "毛伟",
        "status": false
    },
    {
        "customerid": "HPL00000261",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "先贵",
        "status": false
    },
    {
        "customerid": "HPL00000262",
        "iu_point": "0",
        "iu_dt": 380,
        "lastname": "周",
        "firstname": "小平",
        "status": true
    },
    {
        "customerid": "HPL00000263",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "孙",
        "firstname": "立强",
        "status": true
    },
    {
        "customerid": "HPL00000265",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "卢",
        "firstname": "德稳",
        "status": false
    },
    {
        "customerid": "HPL00000267",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "玲",
        "status": false
    },
    {
        "customerid": "HPL00000269",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "牛",
        "firstname": "天军",
        "status": false
    },
    {
        "customerid": "HPL00000270",
        "iu_point": "0.14",
        "iu_dt": 0,
        "lastname": "耿",
        "firstname": "瑞芹",
        "status": false
    },
    {
        "customerid": "HPL00000276",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "徐",
        "firstname": "爱龄",
        "status": false
    },
    {
        "customerid": "HPL00000280",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "萍",
        "status": false
    },
    {
        "customerid": "HPL00000281",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "沅禾",
        "status": false
    },
    {
        "customerid": "HPL00000283",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "小花",
        "status": false
    },
    {
        "customerid": "HPL00123461",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "顾",
        "firstname": "福根",
        "status": false
    },
    {
        "customerid": "HPL00123464",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "黄",
        "firstname": "展如",
        "status": false
    },
    {
        "customerid": "HPL00123465",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "汤",
        "firstname": "国琴",
        "status": false
    },
    {
        "customerid": "HPL00123466",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "天赛",
        "status": false
    },
    {
        "customerid": "HPL00123668",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "蒋",
        "firstname": "连萍",
        "status": false
    },
    {
        "customerid": "HPL00123669",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "万",
        "firstname": "小敏",
        "status": false
    },
    {
        "customerid": "HPL00123670",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "万",
        "firstname": "正学",
        "status": false
    },
    {
        "customerid": "HPL00123671",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "呂",
        "firstname": "学军",
        "status": false
    },
    {
        "customerid": "HPL00123672",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "徐",
        "firstname": "桂兰",
        "status": false
    },
    {
        "customerid": "HPL00123674",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "耿",
        "firstname": "淑艳",
        "status": false
    },
    {
        "customerid": "HPL00123675",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "严",
        "firstname": "月欣",
        "status": false
    },
    {
        "customerid": "HPL00123676",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "娜",
        "status": false
    },
    {
        "customerid": "HPL00123677",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "解",
        "firstname": "玺恩",
        "status": false
    },
    {
        "customerid": "HPL00123683",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "段",
        "firstname": "桓宇",
        "status": false
    },
    {
        "customerid": "HPL00123684",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "晓蕾",
        "status": false
    },
    {
        "customerid": "HPL00123685",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "文华",
        "status": false
    },
    {
        "customerid": "HPL00123689",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吴",
        "firstname": "迪",
        "status": false
    },
    {
        "customerid": "HPL00123690",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "朱芳",
        "status": false
    },
    {
        "customerid": "HPL00123693",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "春涛",
        "status": false
    },
    {
        "customerid": "HPL00123694",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "付",
        "firstname": "秋真",
        "status": false
    },
    {
        "customerid": "HPL00123697",
        "iu_point": "3.3",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "璐",
        "status": false
    },
    {
        "customerid": "HPL00123698",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "珏",
        "status": false
    },
    {
        "customerid": "HPL00123699",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "唐",
        "firstname": "翠华",
        "status": false
    },
    {
        "customerid": "HPL00123700",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "宁",
        "status": false
    },
    {
        "customerid": "HPL00123702",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "任",
        "firstname": "潇",
        "status": false
    },
    {
        "customerid": "HPL00123703",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "鲁",
        "firstname": "振宇",
        "status": false
    },
    {
        "customerid": "HPL00123704",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "俊",
        "status": false
    },
    {
        "customerid": "HPL00123705",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "秋莹",
        "status": false
    },
    {
        "customerid": "HPL00123706",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曹",
        "firstname": "莹莹",
        "status": false
    },
    {
        "customerid": "HPL00123707",
        "iu_point": "9.24",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "伟",
        "status": false
    },
    {
        "customerid": "HPL00123710",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "乐助",
        "status": false
    },
    {
        "customerid": "HPL00123711",
        "iu_point": "3.32",
        "iu_dt": 0,
        "lastname": "柳",
        "firstname": "金凤",
        "status": false
    },
    {
        "customerid": "HPL00123713",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曹",
        "firstname": "飞",
        "status": false
    },
    {
        "customerid": "HPL00123714",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "黄",
        "firstname": "桥爽",
        "status": false
    },
    {
        "customerid": "HPL00123768",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "星辰",
        "status": false
    },
    {
        "customerid": "HPL00123770",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "宗",
        "firstname": "鸣",
        "status": false
    },
    {
        "customerid": "HPL00123771",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "琴",
        "status": false
    },
    {
        "customerid": "HPL00123772",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "廷孝",
        "status": false
    },
    {
        "customerid": "HPL00123773",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "小文",
        "status": false
    },
    {
        "customerid": "HPL00123774",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "成成",
        "status": false
    },
    {
        "customerid": "HPL00123775",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "艳玉",
        "status": false
    },
    {
        "customerid": "HPL00123777",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "婵",
        "status": false
    },
    {
        "customerid": "HPL00123778",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "郑",
        "firstname": "孝娟",
        "status": false
    },
    {
        "customerid": "HPL00123779",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "孟姣",
        "status": false
    },
    {
        "customerid": "HPL00123780",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "胡",
        "firstname": "美凤",
        "status": false
    },
    {
        "customerid": "HPL00123781",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "曲",
        "firstname": "淑田",
        "status": false
    },
    {
        "customerid": "HPL00123782",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "观武",
        "status": false
    },
    {
        "customerid": "HPL00123783",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "啜",
        "firstname": "英汉",
        "status": false
    },
    {
        "customerid": "HPL00123784",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "粟",
        "firstname": "英",
        "status": false
    },
    {
        "customerid": "HPL00123785",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "巍",
        "status": false
    },
    {
        "customerid": "HPL00123787",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "芹",
        "status": false
    },
    {
        "customerid": "HPL00123788",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "英",
        "status": false
    },
    {
        "customerid": "HPL00123789",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "周",
        "firstname": "铭",
        "status": false
    },
    {
        "customerid": "HPL00123790",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "段",
        "firstname": "桂玲",
        "status": false
    },
    {
        "customerid": "HPL00123791",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "明孝",
        "status": false
    },
    {
        "customerid": "HPL00123792",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "胡",
        "firstname": "翠琼",
        "status": false
    },
    {
        "customerid": "HPL00123793",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "游",
        "firstname": "超",
        "status": false
    },
    {
        "customerid": "HPL00123795",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "段",
        "firstname": "圣平",
        "status": false
    },
    {
        "customerid": "HPL00123796",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "长利",
        "status": false
    },
    {
        "customerid": "HPL00123797",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "雪梅",
        "status": false
    },
    {
        "customerid": "HPL00123798",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "红",
        "status": false
    },
    {
        "customerid": "HPL00123799",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "佩",
        "status": false
    },
    {
        "customerid": "HPL00123800",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "蒋",
        "firstname": "和艳",
        "status": false
    },
    {
        "customerid": "HPL00123801",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "红",
        "status": false
    },
    {
        "customerid": "HPL00123854",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "薛",
        "firstname": "春林",
        "status": false
    },
    {
        "customerid": "HPL00123856",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "文",
        "firstname": "红霞",
        "status": false
    },
    {
        "customerid": "HPL00123857",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "姚",
        "firstname": "慧丽",
        "status": false
    },
    {
        "customerid": "HPL00123859",
        "iu_point": "0",
        "iu_dt": 680,
        "lastname": "苏",
        "firstname": "新苗",
        "status": true
    },
    {
        "customerid": "HPL00123872",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吴",
        "firstname": "昊",
        "status": false
    },
    {
        "customerid": "HPL00123873",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "魏",
        "firstname": "庆",
        "status": false
    },
    {
        "customerid": "HPL00123874",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "悦",
        "status": false
    },
    {
        "customerid": "HPL00123875",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "宪",
        "status": false
    },
    {
        "customerid": "HPL00123876",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陆",
        "firstname": "韵菲",
        "status": false
    },
    {
        "customerid": "HPL00123877",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "小敏",
        "status": false
    },
    {
        "customerid": "HPL00123878",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "吴",
        "firstname": "根弟",
        "status": true
    },
    {
        "customerid": "HPL00123879",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "洪",
        "firstname": "彦明",
        "status": false
    },
    {
        "customerid": "HPL00123880",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "薛",
        "firstname": "真",
        "status": true
    },
    {
        "customerid": "HPL00123881",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "刘",
        "firstname": "汉东",
        "status": true
    },
    {
        "customerid": "HPL00123882",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "玉霞",
        "status": false
    },
    {
        "customerid": "HPL00123883",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "古",
        "firstname": "婷婷",
        "status": false
    },
    {
        "customerid": "HPL00123884",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "苏",
        "firstname": "新佳",
        "status": true
    },
    {
        "customerid": "HPL00123885",
        "iu_point": "0",
        "iu_dt": 150,
        "lastname": "刘",
        "firstname": "丁怡",
        "status": true
    },
    {
        "customerid": "HPL00123886",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "红琳",
        "status": false
    },
    {
        "customerid": "HPL00123887",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "彦均",
        "status": false
    },
    {
        "customerid": "HPL00123888",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "振山",
        "status": false
    },
    {
        "customerid": "HPL00123889",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "明哲",
        "status": false
    },
    {
        "customerid": "HPL00123890",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "崔",
        "firstname": "航诚",
        "status": true
    },
    {
        "customerid": "HPL00123891",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "汪",
        "firstname": "怡彤",
        "status": false
    },
    {
        "customerid": "HPL00123892",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "苏",
        "firstname": "丹",
        "status": false
    },
    {
        "customerid": "HPL00123893",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "智华",
        "status": false
    },
    {
        "customerid": "HPL00123894",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "霞",
        "status": false
    },
    {
        "customerid": "HPL00123895",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "钟",
        "firstname": "李红",
        "status": true
    },
    {
        "customerid": "HPL00123896",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "谢",
        "firstname": "红",
        "status": false
    },
    {
        "customerid": "HPL00123897",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "谢",
        "firstname": "利涛",
        "status": false
    },
    {
        "customerid": "HPL00123934",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "谭",
        "firstname": "斌",
        "status": false
    },
    {
        "customerid": "HPL00123935",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "东英",
        "status": false
    },
    {
        "customerid": "HPL00123936",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "凤兰",
        "status": false
    },
    {
        "customerid": "HPL00123937",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "王双",
        "status": false
    },
    {
        "customerid": "HPL00123938",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "崔",
        "firstname": "娟",
        "status": false
    },
    {
        "customerid": "HPL00123939",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "昌英",
        "status": false
    },
    {
        "customerid": "HPL00123941",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "冯",
        "firstname": "巍",
        "status": false
    },
    {
        "customerid": "HPL00123952",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "欣凌",
        "status": false
    },
    {
        "customerid": "HPL00123953",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "玥",
        "status": false
    },
    {
        "customerid": "HPL00123954",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "芳",
        "status": false
    },
    {
        "customerid": "HPL00123955",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "迎芹",
        "status": false
    },
    {
        "customerid": "HPL00123957",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孙",
        "firstname": "元春",
        "status": false
    },
    {
        "customerid": "HPL00123965",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "潘",
        "firstname": "玉革",
        "status": false
    },
    {
        "customerid": "HPL00123966",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "荣芳",
        "status": false
    },
    {
        "customerid": "HPL00123967",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "金凤",
        "status": false
    },
    {
        "customerid": "HPL00123968",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "慧兰",
        "status": false
    },
    {
        "customerid": "HPL00123969",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "潘",
        "firstname": "静兰",
        "status": false
    },
    {
        "customerid": "HPL00123970",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "源",
        "status": false
    },
    {
        "customerid": "HPL00123971",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "任",
        "firstname": "家斌",
        "status": true
    },
    {
        "customerid": "HPL00123972",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "夏",
        "firstname": "建兴",
        "status": false
    },
    {
        "customerid": "HPL00123973",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "刘",
        "firstname": "红",
        "status": false
    },
    {
        "customerid": "HPL00123974",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "明",
        "firstname": "存英",
        "status": false
    },
    {
        "customerid": "HPL00123975",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吕",
        "firstname": "文娟",
        "status": false
    },
    {
        "customerid": "HPL00123976",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "王",
        "firstname": "伟",
        "status": false
    },
    {
        "customerid": "HPL00123977",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "项",
        "firstname": "惠",
        "status": false
    },
    {
        "customerid": "HPL00123978",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "周",
        "firstname": "晓玲",
        "status": false
    },
    {
        "customerid": "HPL00123979",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "黄",
        "firstname": "玉华",
        "status": false
    },
    {
        "customerid": "HPL00123980",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "许",
        "firstname": "秀兰",
        "status": false
    },
    {
        "customerid": "HPL00123981",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "高",
        "firstname": "建荣",
        "status": false
    },
    {
        "customerid": "HPL00123982",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "黄",
        "firstname": "俊艳",
        "status": false
    },
    {
        "customerid": "HPL00124024",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "万",
        "firstname": "永",
        "status": true
    },
    {
        "customerid": "HPL00124025",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "苑",
        "firstname": "永敏",
        "status": true
    },
    {
        "customerid": "HPL00124027",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "杨",
        "firstname": "云霞",
        "status": true
    },
    {
        "customerid": "HPL00124028",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "万",
        "firstname": "秀红",
        "status": true
    },
    {
        "customerid": "HPL00124029",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "张",
        "firstname": "朔",
        "status": true
    },
    {
        "customerid": "HPL00124030",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "赵",
        "firstname": "东英",
        "status": true
    },
    {
        "customerid": "HPL00124031",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "赵",
        "firstname": "警予",
        "status": true
    },
    {
        "customerid": "HPL00124032",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "骆",
        "firstname": "春荣",
        "status": true
    },
    {
        "customerid": "HPL00124033",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "韩",
        "firstname": "萍",
        "status": true
    },
    {
        "customerid": "HPL00123983",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "裴",
        "firstname": "世云",
        "status": false
    },
    {
        "customerid": "HPL00123984",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "虹羽",
        "status": false
    },
    {
        "customerid": "HPL00123985",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "范",
        "firstname": "荣庆",
        "status": false
    },
    {
        "customerid": "HPL00123986",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "文",
        "status": false
    },
    {
        "customerid": "HPL00123987",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "赵",
        "firstname": "琴",
        "status": false
    },
    {
        "customerid": "HPL00123997",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "孔",
        "firstname": "德峰",
        "status": false
    },
    {
        "customerid": "HPL00123998",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "邱",
        "firstname": "建峰",
        "status": false
    },
    {
        "customerid": "HPL00123999",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "崔",
        "firstname": "淑娥",
        "status": false
    },
    {
        "customerid": "HPL00124000",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "吕",
        "firstname": "才芝",
        "status": false
    },
    {
        "customerid": "HPL00124001",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "于",
        "firstname": "丹",
        "status": false
    },
    {
        "customerid": "HPL00124002",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "丁",
        "firstname": "宁",
        "status": false
    },
    {
        "customerid": "HPL00124003",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "朱",
        "firstname": "斌斌",
        "status": false
    },
    {
        "customerid": "HPL00124004",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "房",
        "firstname": "建丽",
        "status": false
    },
    {
        "customerid": "HPL00124005",
        "iu_point": "0",
        "iu_dt": 200,
        "lastname": "王",
        "firstname": "淑媛",
        "status": true
    },
    {
        "customerid": "HPL00124006",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "安琪",
        "status": false
    },
    {
        "customerid": "HPL00124007",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "景志",
        "status": false
    },
    {
        "customerid": "HPL00124008",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "景月",
        "status": false
    },
    {
        "customerid": "HPL00124009",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杨",
        "firstname": "云龙",
        "status": false
    },
    {
        "customerid": "HPL00124010",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "耿",
        "firstname": "瑞芹",
        "status": false
    },
    {
        "customerid": "HPL00124011",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "张",
        "firstname": "凤英",
        "status": false
    },
    {
        "customerid": "HPL00124012",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陶",
        "firstname": "惠",
        "status": false
    },
    {
        "customerid": "HPL00124013",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "巨珍",
        "status": false
    },
    {
        "customerid": "HPL00124015",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "陈",
        "firstname": "东琛",
        "status": false
    },
    {
        "customerid": "HPL00124016",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "李",
        "firstname": "海涛",
        "status": false
    },
    {
        "customerid": "HPL00124018",
        "iu_point": "0",
        "iu_dt": 0,
        "lastname": "杜",
        "firstname": "耀丹",
        "status": false
    },
    {
        "customerid": "HPL00124019",
        "iu_point": "0",
        "iu_dt": 3,
        "lastname": "储",
        "firstname": "玥",
        "status": true
    },
    {
        "customerid": "HPL00124020",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "徐",
        "firstname": "艳梅",
        "status": true
    },
    {
        "customerid": "HPL00124021",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "王",
        "firstname": "宗润",
        "status": true
    },
    {
        "customerid": "HPL00124022",
        "iu_point": "0",
        "iu_dt": 100,
        "lastname": "李",
        "firstname": "莉",
        "status": true
    },
    {
        "customerid": "HPL00124023",
        "iu_point": "0",
        "iu_dt": 45,
        "lastname": "李",
        "firstname": "雪莲",
        "status": true
    }









		]';
        $data = array_sort(json_decode($json,true),'customerid','DESC');
		$export_excel = D('Receipt')->special_excel($data);
		
	}
}