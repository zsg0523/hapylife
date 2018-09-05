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
		if($data['gidArr']){
			foreach($keys as $value){
				unset($data['gidArr'][$value]);
			}
			foreach ($data['gidArr'] as $key => $value) {
				$mape         = explode(',',$value);
				$getCoupn    .= $mape[0].',';
			}
			$data['get_coupon'] = substr($getCoupn,0,-1);
		}
		if($data['gidnumArr']){
			foreach($keys as $value){
				unset($data['gidnumArr'][$value]);
			}
			foreach ($data['gidnumArr'] as $key => $value) {
				$mape         = explode(',',$value);
				$traversenum .= $mape[0].',';
			}
			$data['traverse_num'] = substr($traversenum,0,-1);
		}
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
		foreach($data['gidnumArr'] as $key => $value){
			if(empty($value)){
				$keys[] = $key;
			}
		}
		if($data['gidArr']){
			foreach($keys as $value){
				unset($data['gidArr'][$value]);
			}
			foreach ($data['gidArr'] as $key => $value) {
				$mape         = explode(',',$value);
				$getCoupn    .= $mape[0].',';
			}
			$data['get_coupon'] = substr($getCoupn,0,-1);
		}
		if($data['gidnumArr']){
			foreach($keys as $value){
				unset($data['gidnumArr'][$value]);
			}
			foreach ($data['gidnumArr'] as $key => $value) {
				$mape         = explode(',',$value);
				$traversenum .= $mape[0].',';
			}
			$data['traverse_num'] = substr($traversenum,0,-1);
		}
		if(isset($upload['name'])){
			$data['ip_picture_zh']=C('WEB_URL').$upload['name'];
		}
		if($data['ip_type']==5){
			if(!$data['ip_sprice'] || !$data['ip_dt']){
				$this->error('请填写DT折扣价和可折扣DT数量');
			}
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
			$status = '0,1,2,3,4,5,7,8,202';
		}else{
			$status = (string)$order_status;
		}
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('Receipt')->getPage(D('Receipt'),$word,$starttime,$endtime,$status,$order='ir_date desc',$timeType);
		//导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getAllSendData(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$order='ir_paytime desc');
			$export_excel = D('Receiptson')->export_excel($data['data']);
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
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$assign    = D('Receipt')->FinanceGetPage(D('Receipt'),$word,$starttime,$endtime,$status,$test,$timeType,$order='ir_date desc');
		//导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->FinanceGetAllSendData(D('Receipt'),$word,$starttime,$endtime,$status,$timeType,$test,$order='ir_paytime desc');
			$export_excel = D('Receiptson')->export_excel($data['data']);
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
		$field = '*,rs.ir_price as r_price,rs.ir_point as r_point';
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
	 			'ia_address' => $value[I].$value[J].$value[K].$value[L],
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
		$session = session();
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
			$status = '2,3,4,5,6,8';
		}else{
			$status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$array  = '测,测试,test,试,试点,testtest';
		$ipid = '47,48';
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
		$issend = I('post.is_send');
		$remove = explode('-',$data['username']);
		
		if(!empty($issend)){
			// 发送续费短信
			$spotemplate = 146228;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($remove[0],$data['productnams']);
		}

        $sponsorSms    = D('Smscode')->sms($data['acnumber'],$data['phone'],$spoparams,$spotemplate);
        
        if($sponsorSms['errmsg']=='OK'){
        	if(!empty($issend)){
        		$is_send['is_send'] = 2;
				$result = M('Receipt')->where(array('irid'=>$data['irid']))->save($is_send);
				if($result){
	        		$mape  = array(
	                    'phone'   =>$data['phone'],
	                    'content'    =>'亲爱的会员'.$remove[0].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。',
	                    'acnumber'=>$data['acnumber'],
	                    'date'    =>date('Y-m-d H:i:s'),
	                    'operator' => $_SESSION['user']['username'],
	                    'product_name' => $data['productnams'],
	                    'addressee' => $remove[0],
	                    'customerid' => $remove[1]
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
		$remove = explode('-',$data['username']);
		if($data['psd'] == 146228){
			// 物流信息通知
			$spotemplate = 146228;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($remove[0],$data['productnams']);
		}

		if($data['psd'] == 146227){
			// 续费信息通知
			$spotemplate = 146227;	// NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
			$sposmsSign  = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
			$spoparams = array($remove[0],$data['endtime']);
		}

        $sponsorSms    = D('Smscode')->sms($data['acnumber'],$data['phone'],$spoparams,$spotemplate);
        
        if($sponsorSms['errmsg']=='OK'){
        	if($data['psd'] == 146227){
				$mape  = array(
                    'phone'   =>$data['phone'],
                    'content'    =>'亲爱的会员'.$remove[0].'，这是系统提醒消息，请在'.$data['endtime'].'之前购买月费包。',
                    'acnumber'=>$data['acnumber'],
                    'date'    =>time(),
                    'operator' => $_SESSION['user']['username'],
                    'addressee' => $remove[0],
                    'customerid' => $remove[1]
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
                    'content'    =>'亲爱的会员'.$remove[0].'，您购买的'.$data['productnams'].'物流信息出现问题，我们会有电话通知您，请留意接听。',
                    'acnumber'=>$data['acnumber'],
                    'date'    =>time(),
                    'operator' => $_SESSION['user']['username'],
                    'product_name' => $data['productnams'],
                    'addressee' => $remove[0],
                    'customerid' => $remove[1]
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

	/**
	* 查询买四送一订单
	**/ 
	public function search(){
		//202 未全额支付 2已支付
		$order_status = I('get.status')-1;
		if($order_status== -1){
			//所有订单
			$ir_status = '0,1,2,3,4,5,6,8,202';
		}else{
			$ir_status = (string)$order_status;
		}
		$excel     = I('get.excel');
		$word      = trim(I('get.word',''));
		$timeType  = I('get.timeType')?I('get.timeType'):'ir_date';
		$starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
		$endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+24*3600:time();
		$array  = '测试,测,试,测试点,test,testtest,测试测试,新建测试,测试地,测试点,测试账号';
		$ipid = '47,48';
		$assign    = D('Receipt')->getSendPagesearch(D('Receipt'),$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='ir_paytime asc');
		// 导出excel
		if($excel == 'excel'){
			$data = D('Receipt')->getSendPageSonAlls(D('Receipt'),$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='ir_paytime asc');
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
}