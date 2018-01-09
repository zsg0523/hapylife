<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class HapylifeApiController extends HomeBaseController{
	
	public function index(){
		$data = D('Users')->select();
		// foreach ($data as $key => $value) {
		// 	$tmpe             = $value;
		// 	$tmpe['LastName'] = trim($value['LastName']);
		// 	$tmpe['FirstName'] = trim($value['FirstName']);
		// 	$tmpe['CustomerID'] = trim($value['CustomerID']);
		// 	$add = D('User')->add($tmpe);
		// }
		// if($add){
		// 	echo '添加完毕';
		// }
		p($data);
	}
	/**
	* 旧用户注册
	**/
	public function oldregister(){
		$data = I('post.');
		$where= array(
			'LastName'     =>$data['LastName'],
			'FirstName'    =>$data['FirstName'],
			'CustomerID'   =>$data['CustomerID'],
			'WeeklyVolume' =>1
		);
		$find = D('Users')->where($where)->find();
		if($find){
			// $tmpe = array(
			// 	'Phone'     =>$data['Phone'],
			// 	'PassWord'  =>md5($data['PassWord']),
			// 	'JustIdcard'=>$data['JustIdcard'],
			// 	'BackIdcard'=>$data['BackIdcard']
			// );
			// $save = D('Users')->where($where)->save($tmpe);
			// if($save){
				$data['status'] = 1;
				$this->ajaxreturn($data);				
			// }else{
			// 	$data['status'] = 0;
			// 	$this->ajaxreturn($data);
			// }
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 登录
	**/
	public function login(){}
	/**
	* 
	* 置顶新闻
	**/
	public function newstop(){
		$map  = array(
				'news_top'=>1,
				'is_show' =>1
			);
		$data = M('News')
				->where($map)
				->order('addtime desc')
				->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data = array(
					'status'=>0,
					'msg'	=>'无法获取置顶新闻列表'
				);
			$this->ajaxreturn($data);
		}
	}

	/**
	* 新闻列表
	**/	
	public function newslist(){
		$map  = array(
				'is_show' =>1
			);
		$data = M('News')
				->where($map)
				->order('addtime desc')
				->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data = array(
					'status'=>0,
					'msg'	=>'无法获取新闻列表'
				);
			$this->ajaxreturn($data);
		}
	}

	/**
	* 新闻详情
	**/
	public function newscontent(){
		$nid  = I('post.nid');
        $data = M('News')->where(array('nid'=>$nid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
					'status'=>0,
					'msg'	=>'获取新闻详情失败'
				);
            $this->ajaxreturn($data);
        }
	}

	/**
	* 商品列表
	**/
	public function productlist(){
		$map = array(
            'ip_type'=>1
        );
        $data= M('Product')->where($map)->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
					'status'=>0,
					'msg'	=>'获取商品列表失败'
				);
            $this->ajaxreturn($data);
        }
	}

	/**
	* 商品详情
	**/
	public function product(){
			$ipid = I('post.ipid');
            $data = M('Product')
            			->where(array('ipid'=>$ipid))
            			->find();
            if($data){
           		$this->ajaxreturn($data);
        	}else{
	            $data = array(
					'status'=>0,
					'msg'	=>'获取商品详情失败'
				);
	            $this->ajaxreturn($data);
        }
	}

	/**
	* 订单
	**/
	public function order(){}
}