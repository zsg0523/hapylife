<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*HRAC后台管理
**/
class HracController extends AdminBaseController{

// ***********************广告*******************
	/**
	广告列表
	**/
	public function show(){
		$keyword =I('get.keyword');
		$assign  =D('HracShow')->getAllData(D('HracShow'),$map,$keyword,'hsid desc'); 
		$hracnews   =D('HracNews')->select();
		$hracproject=D('HracProject')->select();
		// 推广类型
		$hractype   =array(
			array('hs_type'=>1,'tyname'=>'链接推广位'),
			array('hs_type'=>2,'tyname'=>'新闻推广位'),
			array('hs_type'=>3,'tyname'=>'项目推广位')
		);
		$this->assign($assign);
		$this->assign('keyword',$keyword);
		$this->assign('hracnews',$hracnews);
		$this->assign('hractype',$hractype);
		$this->assign('hracproject',$hracproject);
		$this->display();
	}

	/**
	添加广告
	**/
	public function add_show(){
		$data=I('post.');
		$upload=post_upload();
		$data['hp_name'] = D('HracProject')->where(array('hpid'=>$data['hpid']))->getfield('hp_name');
		$data['hs_pic']=C('WEB_URL').$upload['name'];
		$result=D('HracShow')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/show');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑广告
	**/
	public function edit_show(){
		$data=I('post.');
		$map=array(
				'hsid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hs_pic']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracShow')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/show');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除广告
	**/
	public function delete_show(){
		$id=I('get.id');
		$map=array(
			'hsid'=>$id
			);
		$info  =D('HracShow')->where(array('hsid'=>$id))->find();
		$result=D('HracShow')->deleteData($map);
		if($result){
			if($info['hs_pic']){
				unlink($info['hs_pic']);
			}
			$this->redirect('Admin/Hrac/show');
		}else{
			$this->error('删除失败');
		}
	}


// ***********************排班时间*******************
	/**
	排班列表
	**/
	public function book(){
		$hracshop=D('HracShop')->select();
		$cid     =I('get.cid');
 		$keyword =I('get.keyword');
		$assign  =D('HracBook')->getAllData(D('HracBook'),$map,$cid,$keyword,'hbid desc');
		$this->assign('cid',$cid);
		$this->assign('keyword',$keyword);
		$this->assign('hracshop',$hracshop);
		$this->assign($assign);
		$this->display();
	}
	/**
	 编辑预约状态
	**/
	public function is_book(){
		$data = array(
			'is_booking'=>I('get.is_booking')?0:1,
		);
		$map  = array(
			'hbid'		=>I('get.hbid')
		);
		$result=D('HracBook')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/book/');
		}else{
			$this->error('修改失败');
		}
	}


//***********************新闻*******************
	/**
	新闻列表
	**/
	public function news(){
		$keyword =I('get.keyword');
		$assign  =D('HracNews')->getAllData(D('HracNews'),$map,$keyword,'hnid desc');
		$this->assign($assign);
		$this->display();
	}

	/**
	添加新闻(默认不置顶is_top 0)
	**/
	public function add_news(){
		$data=I('post.');
		$upload=post_upload();
		$data['hn_pic'] = C('WEB_URL').$upload['name'];
		$data['hn_time']= date('Y-m-d H:i:s');
		$data['hn_desc']= I('post.hn_desc')?I('post.hn_desc'):mb_substr(I('post.hn_content'),0,20).'.....';
		$result=D('HracNews')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/news');
		}else{
			$this->error('添加失败');
		}
	}
	/**
	编辑新闻
	**/
	public function edit_news(){
		$data=I('post.');
		$map=array(
			'hnid'=>$data['id']
			);
		$upload=post_upload();
		// p($upload);die;
		if(isset($upload['name'])){
			$data['hn_pic']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/news');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	编辑置顶状态
	**/
	public function top_news(){
		$data = array(
			'is_top'=>I('get.is_top')?0:1,
		);
		$map  = array(
			'hnid'	=>I('get.hnid')
		);
		$result=D('HracNews')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/news');
		}else{
			$this->error('修改失败');
		}
	}

	/**
	删除新闻
	**/
	public function delete_news(){
		$id=I('get.id');
		$map=array(
			'hnid'=>$id
			);
		$info  =D('HracNews')->where(array('hnid'=>$id))->find();
		$result=D('HracNews')->deleteData($map);
		if($result){
			if($info['hn_pic']){
				unlink($info['hn_pic']);
			}
			$this->redirect('Admin/Hrac/news');
		}else{
			$this->error('删除失败');
		}
	}


//***********************门店***************
	/**
	门店列表
	**/
	public function shop(){
		$keyword =I('get.keyword');
		$area    =D('HracArea')->select();
		$assign  =D('HracShop')->getAllData(D('HracShop'),$map,$keyword,'sid desc'); 
		$this->assign('keyword',$keyword);
		$this->assign($assign);
		$this->assign('area',$area);
		$this->display();
	}

	/**
	添加门店
	**/
	public function add_shop(){
		$data=I('post.');
		$upload=post_upload();
		$data['s_photo'] = C('WEB_URL').$upload['name'];
		$result=D('HracShop')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/shop');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑门店
	**/
	public function edit_shop(){
		$data=I('post.');
		$map=array(
			'sid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['s_photo']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracShop')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/shop');
		}else{
			$this->error('编辑失败');
		}

	}

	/**
	删除门店
	**/
	public function delete_shop(){
		$id=I('get.id');
		$map=array(
			'sid'=>$id
			);
		$info  =D('HracShop')->where(array('sid'=>$id))->find();
		$result=D('HracShop')->deleteData($map);
		if($result){
			if($info['s_photo']){
				unlink($info['s_photo']);
			}
			$this->redirect('Admin/Hrac/shop');
		}else{
			$this->error('删除失败');
		}
	}			

//**********************职员管理*********************
	/**
	职员列表
	**/
	public function staff(){
		$hracshop=D('HracShop')->select();
		$hragrade=D('HracGrade')->select();
		$cid     =I('get.cid');
 		$keyword =I('get.keyword');
		$assign  =D('HracDocter')->getAllData(D('HracDocter'),$map,$cid,$keyword,'hdid desc');
		$this->assign('cid',$cid);
		$this->assign('keyword',$keyword);
		$this->assign('hracshop',$hracshop);
		$this->assign('hragrade',$hragrade);
		$this->assign($assign);
		$this->display();
	}


	/**
	添加职员
	**/
	public function add_staff(){
		$data=I('post.');
		$result=D('HracDocter')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/staff');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑职员
	**/
	public function edit_staff(){
		$data=I('post.');
		$map=array(
			'hdid'=>$data['id']
			);
		$result=D('HracDocter')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/staff');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除职员
	**/
	public function delete_staff(){
		$id=I('get.id');
		$map=array(
			'hdid'=>$id
			);
		$result=D('HracDocter')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/staff');
		}else{
			$this->error('删除失败');
		}
	}
//***********************商品分类***************
	/**
	商品分类列表
	**/
	public function category(){
		$data=D('HracCategory')->getTreeData('tree','order_number','hcat_name');
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}
	/**
	添加商品分类
	**/
	public function add_category(){
		$data=I('post.');
		unset($data['id']);
		$upload=post_upload();
		$data['hcat_picture']=C('WEB_URL').$upload['name'];
		$result=D('HracCategory')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/category');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑商品分类
	**/
	public function edit_category(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hcat_picture']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracCategory')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/category');
		}else{
			$this->error('编辑失败');
		}

	}

	/**
	删除商品分类
	**/
	public function delete_category(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$result=D('HracCategory')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/category');
		}else{
			$this->error('删除失败');
		}
	}
	/**
	 商品分类排序
	*/
	public function order_category(){
		$data=I('post.');
		$result=D('HracCategory')->orderData($data,$id='id');
		if ($result) {
			$this->redirect('Admin/Hrac/category');
		}else{
			$this->error('排序失败');
		}
	}
	/**
	 商品分类是否显示
	*/
	public function show_category(){
		$data=I('get.');
		$map =array(
			'id'=>$data['id']
			);
		$result=D('HracCategory')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/category');
		}else{
			$this->error('编辑失败');
		}
	}
//***********************服务分类***************
	/**
	服务分类列表
	**/
	public function projectclass(){
		$keyword =I('get.keyword');
		$assign  =D('HracProjectclass')->getAllData(D('HracProjectclass'),$map,$keyword,'hpcid desc'); 
		$this->assign('keyword',$keyword);
		$this->assign($assign);
		$this->display();
	}

	/**
	添加服务分类
	**/
	public function add_class(){
		$data=I('post.');
		$result=D('HracProjectclass')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/projectclass');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑服务分类
	**/
	public function edit_class(){
		$data=I('post.');
		$map=array(
			'hpcid'=>$data['id']
			);
		$result=D('HracProjectclass')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/projectclass');
		}else{
			$this->error('编辑失败');
		}

	}

	/**
	删除服务分类
	**/
	public function delete_class(){
		$id=I('get.id');
		$map=array(
			'hpcid'=>$id
			);
		$result=D('HracProjectclass')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/projectclass');
		}else{
			$this->error('删除失败');
		}
	}	

//**********************服务管理*********************
	/**
	商品列表
	**/
	public function project(){
		$grade     =D('HracGrade')->select();
		$class     =D('HracProjectclass')->select();
		$tmp['pid']= array('NEQ',0);
		$category  =D('HracCategory')->where($tmp)->select();
 		$keyword   =I('get.keyword');
 		$cid       =I('get.cid');
		$assign    =D('HracProject')->getAllData(D('HracProject'),$map,$keyword,'sort asc');
		$this->assign('keyword',$keyword);
		$this->assign('cid',$cid);
		$this->assign($assign);
		$this->assign('grade',$grade);
		$this->assign('class',$class);
		$this->assign('category',$category);
		$this->display();
	}


	/**
	添加商品
	**/
	public function add_project(){
		$data=I('post.');
		$upload=post_upload();
		$data['hp_pic'] = C('WEB_URL').$upload['name'];
		if(empty($data['hg_grade'])){
			$data['hp_level']= $data['hg_grade'];
		}else{
			foreach($data['hg_grade'] as $k => $v){
				$level .= $v.',';
			}
			$data['hp_level']= rtrim($level, ",");
		}
		// echo $hp_level;die;
		$result=D('HracProject')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑商品
	**/
	public function edit_project(){
		$data=I('post.');
		$map=array(
			'hpid'=>$data['hpid']
			);
		foreach($data['hg_grade'] as $k => $v){
			$level .= $v.',';
		}
		$data['hp_level'] = rtrim($level, ",");
		if(empty($data['hp_level'])){
			$data['hp_level']= D('HracProject')->where($map)->getfield('hp_level');
		}
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hp_pic']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracProject')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除商品
	**/
	public function delete_project(){
		$id=I('get.id');
		$map=array(
			'hpid'=>$id
			);
		$info  =D('HracProject')->where(array('hpid'=>$id))->find();
		$result=D('HracProject')->deleteData($map);
		if($result){
			if($info['hp_pic']){
				unlink($info['hp_pic']);
			}
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('删除失败');
		}
	}
	/**
	 商品排序
	*/
	public function order_project(){
		$data=I('post.');
		$result=D('HracProject')->orderData($data,$id='hpid','sort');
		if ($result) {
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('排序失败');
		}
	}
	/**
	 商品推荐
	*/
	public function project_top(){
		$data=I('get.');
		$map =array(
			'hpid'=>$data['id']
			);
		$result=D('HracProject')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('编辑失败');
		}
	}
	/**
	 商品是否显示
	*/
	public function show_project(){
		$data=I('get.');
		$map =array(
			'id'=>$data['hpid']
			);
		$result=D('HracProject')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/project');
		}else{
			$this->error('编辑失败');
		}
	}
//**********************等级管理*********************
	/**
	等级列表
	**/
	public function grade(){
 		$keyword =I('get.keyword');
		$assign  =D('HracGrade')->getAllData(D('HracGrade'),$map,$keyword);
		$this->assign('keyword',$keyword);
		$this->assign($assign);
		$this->display();
	}


	/**
	添加等级
	**/
	public function add_grade(){
		$data=I('post.');
		$result=D('HracGrade')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/grade');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑等级
	**/
	public function edit_grade(){
		$data=I('post.');
		$map=array(
			'hgid'=>$data['id']
			);
		$result=D('HracGrade')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/grade');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除等级
	**/
	public function delete_grade(){
		$id=I('get.id');
		$map=array(
			'hgid'=>$id
			);
		$result=D('HracGrade')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/grade');
		}else{
			$this->error('删除失败');
		}
	}


//**********************优惠券管理*********************
	/**
	优惠券列表
	**/
	public function coupon(){
		$project   =D('HracProject')->select();
		$tmp['pid']= array('NEQ',0);
		$category  =D('HracCategory')->where($tmp)->select();
 		$keyword   =I('get.keyword');
 		$cid       =I('get.cid');
		$assign    =D('HracCoupon')->getAllData(D('HracCoupon'),$map,$cid,$keyword,'order asc');
		$this->assign('project',$project);
		$this->assign('category',$category);
		$this->assign('keyword',$keyword);
		$this->assign('cid',$cid);
		$this->assign($assign);
		$this->display();
	}


	/**
	添加优惠券
	**/
	public function add_coupon(){
		$data=I('post.');
		$upload=several_upload();
		$data['hc_pic1'] =C('WEB_URL').$upload['name'][0];
		$data['hc_pic2'] =C('WEB_URL').$upload['name'][1];
		$data['hc_pic3'] =C('WEB_URL').$upload['name'][2];
		$data['hc_pic4'] =C('WEB_URL').$upload['name'][3];
		$data['hc_point']=$data['hc_money']/100;
		// p($upload);die;
		$result=D('HracCoupon')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/coupon');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑优惠券
	**/
	public function edit_coupon(){
		$data=I('post.');
		$map=array(
			'hcid'=>$data['hcid']
			);
		$data['hc_point']=$data['hc_money']/100;
		$upload=several_upload_arr();
		if($upload){
			if($upload['name'][0]){
				$data['hc_pic1']=C('WEB_URL').$upload['name'][0];
			}
			if($upload['name'][1]){
				$data['hc_pic2']=C('WEB_URL').$upload['name'][1];
			}
			if($upload['name'][2]){
				$data['hc_pic3']=C('WEB_URL').$upload['name'][2];
			}
			if($upload['name'][3]){
				$data['hc_pic4']=C('WEB_URL').$upload['name'][3];
			}
		}
		$result=D('HracCoupon')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/coupon');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除优惠券
	**/
	public function delete_coupon(){
		$id=I('get.id');
		$map=array(
			'hcid'=>$id
			);
		$info  =D('HracCoupon')->where(array('hcid'=>$id))->find(); 	
		$result=D('HracCoupon')->deleteData($map);
		if($result){
			if($info['hc_pic1']){
				unlink($info['hc_pic1']);
			}
			if($info['hc_pic2']){
				unlink($info['hc_pic2']);
			}
			if($info['hc_pic3']){
				unlink($info['hc_pic3']);
			}
			$this->redirect('Admin/Hrac/coupon');
		}else{
			$this->error('删除失败');
		}
	}
	/**
	 优惠券排序
	*/
	public function order_coupon(){
		$data=I('post.');
		$result=D('HracCoupon')->orderData($data,$id='hcid','order');
		if ($result) {
			$this->redirect('Admin/Hrac/coupon');
		}else{
			$this->error('排序失败');
		}
	}

// **********************用户优惠券管理*********************
	/**
	用户优惠券列表
	**/
	public function usercoupon(){
		$coupon  =D('HracCoupon')->select();
		$user    =D('IbosUsers')->select();
		$keyword =I('get.keyword');
 		if(!$keyword){
 			$keyid='';
 		}else{
 			$key    =D('IbosUsers')
	 		        ->alias('iu')
	 		        ->join('__HRAC_USERS__ hu ON hu.iuid=iu.iuid')
	 		        ->where(array('hu_nickname'=>$keyword))
	 		        ->getfield('huid');
	 		if($key){
	 			$keyid = $key;
	 		}else{
	 			$keyid ='nowflasekailenkoala';
	 		}

 		}
 		// p($keyid);
 		$cid     =I('get.cid');
		$assign  =D('HracUsercoupon')->getAllData(D('HracUsercoupon'),$map,$cid,$keyid,'hucid desc');
		foreach ($user as $k => $v) {
			foreach ($assign['data'] as $key => $value) {
				if($value['iuid']==$v['iuid']){
					$assign['data'][$key]['hu_nickname'] = $v['hu_nickname'];
				}
			}
		}
		$this->assign('coupon',$coupon);
		$this->assign('keyword',$keyword);
		$this->assign('cid',$cid);
		$this->assign($assign);
		$this->display();
	}

	/**
	删除用户优惠券
	**/
	public function delete_usercoupon(){
		$id=I('get.id');
		$map=array(
			'hucid'=>$id
			);
		$result=D('HracUsercoupon')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/usercoupon');
		}else{
			$this->error('删除失败');
		}
	}


	//**********************用户管理*********************
	/**
	用户列表
	**/
	public function user(){
 		$keyword =I('get.keyword');
 		$user = D('HracUsers')->alias('hu')->join('__IBOS_USERS__ iu ON hu.iuid=iu.iuid')->select();
		$assign  =D('HracUsers')->getAllData(D('HracUsers'),$map,$keyword,'huid desc');
		$this->assign('keyword',$keyword);
		$this->assign($assign);
		$this->display();
	}

	/**
	编辑用户
	**/
	public function edit_user(){
		$data=I('post.');
		$map=array(
			'huid'=>$data['id']
			);
		$result=D('HracUsers')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/user');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除用户
	**/
	public function delete_user(){
		$id=I('get.id');
		$map=array(
			'huid'=>$id
			);
		$result=D('HracUsers')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/user');
		}else{
			$this->error('删除失败');
		}
	}
	/**
	上传用户(Excel)
	**/
	public function upload_user(){
		$upload=post_upload();
		$file  = '.'.$upload['name'];
		$data  = import_excel($file);
		$unlink= C('WEB_URL').$upload['name'];
		// 去除空值
		foreach ($data as $key => $value) {
			if(!empty($value[0])){
				$status[$key] = $value;
			}
		}
		foreach ($status as $key => $value){
			$stamp[$key]['hu_nickname'] = $value[0];
			$stamp[$key]['hu_username'] = $value[1];
			$stamp[$key]['hu_phone'] 	= $value[2];
			$stamp[$key]['hu_email'] 	= $value[3];
			$stamp_date1                = \PHPExcel_Shared_Date::ExcelToPHP($value[4]);//将获取的奇怪数字转成时间戳，该时间戳会自动带上当前日期
      		$stamp[$key]['vipstart']    = gmdate("Y-m-d",$stamp_date1);	
      		$stamp_date2                = \PHPExcel_Shared_Date::ExcelToPHP($value[5]);//将获取的奇怪数字转成时间戳，该时间戳会自动带上当前日期
      		$stamp[$key]['vipend']      = gmdate("Y-m-d",$stamp_date2);
			$stamp[$key]['hu_hpname']	= $value[6];
			$stamp[$key]['hu_num']		= $value[7];
		}
		// P($data);die;
		foreach ($stamp as $key => $value) {
			$nickname[$key] = $value['hu_nickname'];
			$tmp = array(
				'hu_nickname'=>$value['hu_nickname'],
				'hu_username'=>$value['hu_username'],
				'hu_phone'   =>$value['hu_phone'],
				'hu_email'   =>$value['hu_email'],
				'iu_password'=>md5('123456')
			);
			$arr = D('IbosUsers')->add($tmp);
		}
		foreach($nickname as $key => $value) {
			foreach ($stamp as $k => $v) {
				if($key==$k){
					$iuid = D('IbosUsers')->where(array('hu_nickname'=>$value))->getfield('iuid');
					$array= array(
						'iuid'     =>$iuid,
						'vipstart' =>$v['vipstart'],
						'vipend'   =>$v['vipend'],
						'hu_hpname'=>$v['hu_hpname'],
						'hu_num'   =>$v['hu_num'],
						'is_vip'   =>1
					);
					$arrtmp = D('HracUsers')->add($array);
				}
			}
		}
		foreach($nickname as $key => $value) {
			$user = D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
				  ->where(array('hu_nickname'=>$value))->find();
            $data = D('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                  ->where(array('hu_nickname'=>$user['hu_hpname']))
                  ->find();
			if($user&&$data){			
				$save = D('HracUsers')->save(array('huid'=>$user['huid'],'hu_hpid'=>$data['huid'],'stack'=>$data['huid']));
				$tmp = array(
	                'huid'     =>$user['huid'],
	                'pid'      =>$data['huid'],
	                'hpr_date' =>$user['vipstart'],
	                'hpr_time' =>$user['vipstart'].' 00:00:00',
	                'is_vaild' =>1,
	            );
	            $add  = D('HracPartner')->add($tmp);
	            if($add){
	                $page = D('HracPartner')->where(array('pid'=>$data['huid']))->select();
	                $count= count($page);
	                if($count%2==0){
	                    $bill    = array(
	                        'name'       =>$data['hu_nickname'],
	                        'hbi_type'   =>1,
	                        'hbi_content'=>'成功邀请'.$value.'成为合伙人',
	                        'hbi_num'    =>$data['hu_num'],
	                        'symbol'     =>'+',
	                        'hbi_sum'    =>50000,
	                        'hbi_time'   =>$user['vipstart'].' 00:00:00'
	                    );

	                }else{
	                    $bill    = array(
	                        'name'       =>$data['hu_nickname'],
	                        'hbi_type'   =>1,
	                        'hbi_content'=>'成功邀请'.$value.'成为合伙人',
	                        'hbi_num'    =>$data['hu_num'],
	                        'symbol'     =>'+',
	                        'hbi_sum'    =>10000,
	                        'hbi_time'   =>$user['vipstart'].' 00:00:00'
	                    );
	                    if($count==15){
	                        $billss    = array(
	                            'name'       =>$data['hu_nickname'],
	                            'hbi_type'   =>1,
	                            'hbi_content'=>'成功邀请'.$value.'成为合伙人',
	                            'hbi_num'    =>$data['hu_num'],
	                            'symbol'     =>'+',
	                            'hbi_sum'    =>10000,
	                            'hbi_time'   =>$user['vipstart'].' 00:00:00'
	                        );
	                    } 
	                }
	                D('HracBills')->add($bill);
	                if($billss){
	                    D('HracBills')->add($billss);
	                }                       
	            }			
			}
		}
		if($arrtmp){
			unlink($unlink);	
			$this->redirect('Admin/Hrac/user');
		}else{
			$this->error('上传失败');
		}
	}


// ***********************预约订单*******************
	/**
	预约订单列表
	**/
	public function receipt(){
		$user    =D('IbosUsers')->select();
		$hracshop=D('HracShop')->select();
		$cid     =I('get.cid');
 		$keyword =I('get.keyword');
 		if(!$keyword){
 			$keyid='';
 		}else{
 			$key    =D('IbosUsers')
	 		        ->alias('iu')
	 		        ->join('__HRAC_USERS__ hu ON hu.iuid=iu.iuid')
	 		        ->where(array('hu_nickname'=>$keyword))
	 		        ->getfield('huid');
	 		if($key){
	 			$keyid = $key;
	 		}else{
	 			$keyid ='nowflasekailenkoala';
	 		}

 		}
 		// p($keyid);
		$assign  =D('HracReceipt')->getAllData(D('HracReceipt'),$map,$cid,$keyid,'hrid desc');
		foreach ($user as $k => $v) {
			foreach ($assign['data'] as $key => $value) {
				if($value['iuid']==$v['iuid']){
					$assign['data'][$key]['hu_nickname'] = $v['hu_nickname'];
				}
			}
		}
		$this->assign('cid',$cid);
		$this->assign('keyword',$keyword);
		$this->assign('hracshop',$hracshop);
		$this->assign($assign);
		$this->display();
	}
	/**
	上传历史订单(Excel)
	**/
	public function upload_receipt(){
		$upload=post_upload($path='excel',$format='empty',$maxSize='3145728000');
		$file  = '.'.$upload['name'];
		$unlink= C('WEB_URL').$upload['name'];
		$data  = import_excel($file);
		//去除空值
		foreach ($data as $key => $value) {
			if(!empty($value[0])){
				$status[$key] = $value;
			}
		}
		foreach ($status as $key => $value) {
			$stamp_date      = \PHPExcel_Shared_Date::ExcelToPHP($value[0]);//将获取的奇怪数字转成时间戳，该时间戳会自动带上当前日期
      		$stamp[$key]['hr_stardate']     = gmdate("Y-m-d",$stamp_date);//这个就是excel表中的数据了
			$stamp[$key]['isvip']       	= $value[1];
			$stamp[$key]['vipname']       	= $value[2];
			$stamp[$key]['name']        	= $value[3];
			$stamp[$key]['hr_nickname'] 	= $value[4];
			$stamp[$key]['identity'] 	    = $value[5];
			$stamp[$key]['isdr'] 		 	= $value[6];
			$stamp[$key]['sid']		 		= $value[7];
			$stamp[$key]['hd_name']		 	= $value[8];
			$stamp[$key]['hpid']			= $value[9];
			$stamp[$key]['hcid']		 	= $value[10];
			$stamp[$key]['huc_number']	 	= $value[11];
			$stamp[$key]['hr_review']	 	= $value[12];
			$stamp[$key]['hr_money']		= $value[13];
			$stamp[$key]['hr_promoney']	   	= $value[14];
			$stamp[$key]['hr_expmoney']   	= $value[15];
			$stamp[$key]['hr_hra']	   	    = $value[16];
			$stamp[$key]['hr_res']	   	    = $value[17];
			$stamp[$key]['hr_minusmoney'] 	= $value[18];
			$stamp[$key]['hr_dna']	   	    = $value[19];
			$stamp[$key]['givecoupon']   	= $value[20];
			$stamp[$key]['hc_type']   	    = $value[21];
		}
		foreach ($stamp as $key => $value) {
			$users= D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
			      ->where(array('hu_nickname'=>$value['vipname']))->find();
			$vip     = $value['isvip'];
			$name    = $value['vipname'];
			$sdate   = $value['hr_stardate'];
			$isdr    = $value['isdr'];
			if($value['hc_type']==1){
				$is_num = 1;
			}else{
				$is_num = 0;
			}
            $hbitime = $sdate.' 09:00:00';
            $start   = strtotime("+6 months", strtotime($users['vipstart']));
            $nowtime = strtotime($sdate);
			if($value['hr_promoney']==0 && $value['hr_expmoney']==0 &&$value['hr_hra']==0 && $value['hr_res']==0){
				$product = 0;
			}else{
				$product = 1;
			}
			if($value['huc_number']==(string)0){
				$hucid = 0;
			}else{
				$number = explode('-', $value['huc_number']);
				$array  = array(
					'huid'      =>$users['huid'],
					'huc_parent'=>$users['huid'],
					'huc_time'  =>$value['hr_stardate'],
					'hcid'      =>$value['hcid'],
					'hc_type'   =>$value['hc_type'],
					'hp_abb'    =>$number[0],
					'huc_number'=>$number[1],
					'huc_date'  =>$value['hr_stardate'],
					'huc_vaild' =>1
				);
				$coupon = D('HracUsercoupon')->add($array);
				if($coupon){
					$hucid = D('HracUsercoupon')->where($array)->getfield('hucid');
				}else{
					$hucid = 0;
				}
			}
			$hdid = D('HracDocter')->where(array('hd_name'=>$value['hd_name']))->getfield('hdid');
			$tmp = array(
				'hr_number'      =>$value['hr_stardate'].rand(10000, 99999),
				'hr_cretime'     =>$value['hr_stardate'].' 09:00:00',
				'isvip'          =>$value['isvip'],
				'vipname'        =>$value['vipname'],
				'name'           =>$value['name'],
				'hr_nickname'    =>$value['hr_nickname'],
				'identity'       =>$value['identity'],
				'isdr'           =>$value['isdr'],
				'sid'            =>$value['sid'],
				'hdid'           =>$hdid,
				'hd_name'        =>$value['hd_name'],
				'hpid'           =>$value['hpid'],
				'hcid'           =>$value['hcid'],
				'hucid'          =>$hucid,
				'hr_review'      =>$value['hr_review'],
				'hr_money'       =>$value['hr_money'],
				'hr_promoney'    =>$value['hr_promoney'],
				'hr_expmoney'    =>$value['hr_expmoney'],
				'hr_hra'         =>$value['hr_hra'],
				'hr_res'         =>$value['hr_res'],
				'hr_dna'         =>$value['hr_dna'],
				'givecoupon'     =>$value['givecoupon'],
				'hr_minusmoney'  =>$value['hr_minusmoney'],
				'hr_stardate'    =>$value['hr_stardate'],
				'hr_statrservice'=>'09:00',
				'hr_endservice'  =>'09:30',
				'hr_status'      =>5,
				'is_product'     =>$product,
			);
			$receipt = D('HracReceipt')->add($tmp);
			if($vip==1){
				if($isdr==1){
					$bill = array(
                        'name'        => $name,
                        'hbi_sum'     => 0,
                        'hbi_content' => '用券奖励',
                        'hbi_type'    => 2,
                        'symbol'      => '+',
                        'hucid'       => $hucid,
                        'is_num'      => $is_num,
                        'hcid'        => $value['hcid'],
                        'hbi_time'    => $hbitime
                    );
                    D('HracBills')->add($bill);
				}else{
					$mape    = array('name'=>$name,'hbi_type'=>2,'hbi_num'=>$users['hu_num'],'is_num'=>0);
					$altbill = D('HracBills')->where($mape)->select();
		            if($altbill){
						$count = count($altbill);             	
		            }else{
		            	$count   = 0;
		            }
		            if($count<6){
	                    $bill = array(
	                        'name'        => $name,
	                        'hbi_sum'     => 500,
	                        'hbi_content' => '用券奖励',
	                        'hbi_type'    => 2,
	                        'symbol'      => '+',
	                        'hucid'       => $hucid,
	                        'is_num'      => $is_num,
	                        'hcid'        => $value['hcid'],
	                        'hbi_num'     => $users['hu_num'],
	                        'hbi_time'    => $hbitime
	                    );
	                    D('HracBills')->add($bill);
	                }else if($count==6){
	                    if($start>=$nowtime){
	                        $bill = array(
	                            'name'        => $name,
	                            'hbi_sum'     => 1000,
	                            'hbi_content' => '用券奖励',
	                            'hbi_type'    => 2,
	                            'symbol'      => '+',
	                            'hucid'       => $hucid,
	                            'is_num'      => $is_num,
	                            'hcid'        => $value['hcid'],
	                            'hbi_num'     => $users['hu_num'],
	                            'hbi_time'    => $hbitime
	                        );
	                        $where= array(
	                            'name'        => $name,
	                            'hbi_sum'     => 3000,
	                            'hbi_content' => '用券额外奖励',
	                            'hbi_type'    => 2,
	                            'symbol'      => '+',
	                            'hbi_time'    => $hbitime
	                        );
	                    }else{
	                        $bill = array(
	                            'name'        => $name,
	                            'hbi_sum'     => 500,
	                            'hbi_content' => '用券奖励',
	                            'hbi_type'    => 2,
	                            'symbol'      => '+',
	                            'hucid'       => $hucid,
	                            'is_num'      => $is_num,
	                            'hcid'        => $value['hcid'],
	                            'hbi_num'     => $users['hu_num'],
	                            'hbi_time'    => $hbitime
	                    	);
	                    }
	                    D('HracBills')->add($bill);
		            	if($where){
		            		D('HracBills')->add($where);
		            	}
	                }else{
	                    if($start>=$nowtime){
	                        $bill = array(
	                            'name'        => $name,
	                            'hbi_sum'     => 1000,
	                            'hbi_content' => '用券奖励',
	                            'hbi_type'    => 2,
	                            'symbol'      => '+',
	                            'hucid'       => $hucid,
	                            'is_num'      => $is_num,
	                            'hcid'        => $value['hcid'],
	                            'hbi_num'     => $users['hu_num'],
	                            'hbi_time'    => $hbitime
	                        );
	                    }else{
	                        $bill = array(
		                        'name'        => $name,
		                        'hbi_sum'     => 500,
		                        'hbi_content' => '用券奖励',
		                        'hbi_type'    => 2,
		                        'symbol'      => '+',
		                        'hucid'       => $hucid,
		                        'is_num'      => $is_num,
		                        'hcid'        => $value['hcid'],
		                        'hbi_num'     => $users['hu_num'],
		                        'hbi_time'    => $hbitime
	                    	);
	                    }
	                    D('HracBills')->add($bill);
	                }
				}
            }
		}
		if($receipt){
			unlink($unlink);
			$this->redirect('Admin/Hrac/receipt');
		}else{
			$this->error('上传失败');
		}
	}
//**********************房间管理*********************
	/**
	房间列表
	**/
	public function house(){
		$hracshop=D('HracShop')->select();
		$cid     =I('get.cid');
 		$keyword =I('get.keyword');
		$assign  =D('HracHouse')->getAllData(D('HracHouse'),$map,$cid,$keyword,'hhid desc');
		$this->assign('cid',$cid);
		$this->assign('keyword',$keyword);
		$this->assign('hracshop',$hracshop);
		$this->assign($assign);
		$this->display();
	}


	/**
	添加房间
	**/
	public function add_house(){
		$data=I('post.');
		$result=D('HracHouse')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/house');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑房间
	**/
	public function edit_house(){
		$data=I('post.');
		$map=array(
			'hhid'=>$data['id']
			);
		$result=D('HracHouse')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/house');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除房间
	**/
	public function delete_house(){
		$id=I('get.id');
		$map=array(
			'hhid'=>$id
			);
		$result=D('HracHouse')->deleteData($map);
		if($result){
			$this->redirect('Admin/Hrac/house');
		}else{
			$this->error('删除失败');
		}
	}

//********************文件**************************
	/**
	文件夹，文件列表
	**/
	public function file(){
		$data=D('HracFile')->getTreeData('tree','order_number,id');
		$assign=array(
			'data'=>$data
			);
		$this->assign($assign);
		$this->display();
	}


	/**
	添加文件界面
	**/
	public function addfile(){
		$id=I('get.id');
		$this->assign('id',$id);
		$this->display();
	}

	/**
	添加文件夹
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
		$result=D('HracFile')->addData($data);
		if ($result) {
			$this->redirect('Admin/Hrac/file');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑文件夹
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
		}
		$result=D('HracFile')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/file');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	添加文件
	**/
	public function add_files(){
		$data=I('post.');
		$upload=several_upload();
		// p($upload);die;
		if(isset($upload['name'])){
			$arr = explode('.', $upload['name'][1]);
			$data['icon']=C('WEB_URL').$upload['name'][0];
			$data['file']=C('WEB_URL').$upload['name'][1];
			if($arr[1]=='png'||$arr[1]=='jpeg'||$arr[1]=='gif'||$arr[1]=='jpg'){
				$data['type']=3;	
			}else if($arr[1]=='pdf'){
				$data['type']=2;
			}else if($arr[1]=='mp4'){
				$data['type']=1;
			}
		}
		// p($data);die;
		$result=D('HracFile')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/file');
		}else{
			$this->error('添加失败');
		}
	}

	/**
	编辑文件界面
	**/
	public function editfile(){
		$id=I('get.id');
		$fi=D('HracFile')->where(array('id'=>$id))->find();
		$this->assign('fi',$fi);
		$this->display();
	}

	/**
	编辑文件
	**/
	public function edit_files(){
		$data=I('post.');
		$map=array(
			'id'=>$data['id']
		);
		$upload=several_upload();	
		if(isset($upload['name'])){
			$arr = explode('.', $upload['name'][0]);
			$data['file']=C('WEB_URL').$upload['name'][0];
			if($arr[1]=='png'||$arr[1]=='jpeg'||$arr[1]=='gif'||$arr[1]=='jpg'){
				$data['type']=3;	
			}else if($arr[1]=='pdf'){
				$data['type']=2;
			}else if($arr[1]=='mp4'){
				$data['type']=1;
			}
		}
		// p($data);die;
		$result=D('HracFile')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/file');
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	删除文件/文件夹
	**/
	public function delete_file(){
		$id=I('get.id');
		$map=array(
			'id'=>$id
			);
		$info  =D('HracFile')->where(array('id'=>$id))->find(); 
		$result=D('HracFile')->deleteData($map);
		if($result){
			if($info['icon']){
				unlink($info['icon']);
			}
			if($info['file']){
				unlink($info['file']);
			}
			$this->redirect('Admin/Hrac/file');
		}else{
			$this->error('请先删除子菜单');
		}
	}
//**********************消息分类管理*********************
	/**
	消息分类列表
	**/
	public function infoclass(){
		$assign  =D('HracInfoclass')->getAllData(D('HracInfoclass'),$map,'order_number desc');
		$this->assign($assign);
		$this->display();
	}
	/**
	添加消息分类
	**/
	public function add_infoclass(){
		$data=I('post.');
		$upload=post_upload();
		$data['hic_pic']=C('WEB_URL').$upload['name'];
		$result=D('HracInfoclass')->addData($data);
		if($result){
			$this->redirect('Admin/Hrac/infoclass');
		}else{
			$this->error('添加失败');
		}
	}
	/**
	编辑消息分类
	**/
	public function edit_infoclass(){
		$data=I('post.');
		$map=array(
				'hicid'=>$data['id']
			);
		$upload=post_upload();
		if(isset($upload['name'])){
			$data['hic_pic']=C('WEB_URL').$upload['name'];
		}
		$result=D('HracInfoclass')->editData($map,$data);
		if($result){
			$this->redirect('Admin/Hrac/infoclass');
		}else{
			$this->error('编辑失败');
		}
	}
	/**
	删除消息分类
	**/
	public function delete_infoclass(){
		$id=I('get.id');
		$map=array(
			'hicid'=>$id
			);
		$info  =D('HracInfoclass')->where(array('hicid'=>$id))->find();
		$result=D('HracInfoclass')->deleteData($map);
		if($result){
			if($info['hic_pic']){
				unlink($info['hic_pic']);
			}
			$this->redirect('Admin/Hrac/infoclass');
		}else{
			$this->error('删除失败');
		}
	}
	/**
	 消息分类排序
	*/
	public function inorder(){
		$data=I('post.');
		$result=D('HracInfoclass')->orderData($data,$id='hicid');
		if ($result) {
			$this->redirect('Admin/Hrac/infoclass');
		}else{
			$this->error('排序失败');
		}
	}

//**********************优惠券订单*********************
	/**
	优惠券订单列表
	**/
	public function coureceipt(){
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
		$assign = D('HracCoureceipt')->getPage(D('HracCoureceipt'),$map,$order='ir_date desc');
		//p($assign);die;
		$this->assign($assign);
		$this->display();
	}

	public function coureceipt1(){
		$word=I('get.word','');
		$ir_status=I('get.ir_status');
		if(empty($word)){
			$map=array(
				'ir_status'=>0
			);
		}else{
			$map=array(
				'hu_nickname'=>$word,
				'ir_status'=>0
			);
		}
		$assign = D('HracCoureceipt')->getPage(D('HracCoureceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	public function coureceipt2(){
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
		$assign = D('HracCoureceipt')->getPage(D('HracCoureceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}

	public function coureceipt3(){
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
		$assign = D('HracCoureceipt')->getPage(D('HracCoureceipt'),$map,$order='ir_date desc');
		$this->assign($assign);
		$this->display();
	}	
	/**
	订单修改
	**/
	public function edit_coureceipt(){
		$data= I('post.');
		$map=array(
			'irid'=>$data['id']
		);
		$result=D('HracCoureceipt')->editData($map,$data);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('编辑失败');
		}
	}

	/**
	订单删除
	**/
	public function delete_coureceipt(){
		$id=I('get.id');
		$map=array(
			'irid'=>$id
			);
		$result=D('HracCoureceipt')->deleteData($map);
		if($result){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->error('删除失败');
		}
	}
}