<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 奖金计算
**/
class RewardController extends HomeBaseController{

	/**
	* 周奖金
	* 上线 层级 左右位置 是否对碰
	**/
	public function weekReward(){
		$iuid   = trim(I('post.iuid'));
		$userid = trim(I('post.userid'));
		$data   = D('weekbonus')->weekBonus($iuid,$userid);
		echo $data;
	}


	/**
	* 重置对碰数据
	**/
	public function reset(){
		$userid = trim(I('post.userid'));
		$data   = M('weekbonus')->where(array('iuid'=>$userid))->select();
		foreach ($data as $key => $value) {
			$save = M('weekbonus')->where(array('id'=>$value['id']))->setField('is_bonus',0);
		}
		if($save){
			echo "重置对碰数据成功";
		}
	}

	/**
	* 添加月费记录，计算bonus
	**/
	public function addBonusList(){
		//一个人交月费，层层往上添加需要进行对碰的记录
		$iuid        = trim(I('iuid'));
		$user        = M('testuser')->where(array('iuid'=>$iuid))->find();
		//所有数据
		$data        = M('testuser')->select();
		//所有父元素节点(从用户表中获取)
		$sponsorList = getSponsor($data,$user['sponsor']);
		foreach ($sponsorList as $k => $v) {
			//给父元素添加付费记录
			$map  = array(
				'iuid'         =>$v['iuid'],
				'userid'       =>$user['account'],
				'sponsorid'    =>$user['sponsor'],
				'placement'    =>$user['placement'],
				'create_time'  =>time(),
				'create_month' =>date('Y-m',time())
			);
			$add = M('weekbonus')->add($map);
		}
		if($add){
			echo "添加首购list成功";
		}
	}

	/**
	* 自动获取sponsor排网
	* @param account 会员account
	* @param iu_logic会员点位位置 0左 1右
	**/
	public function autoGetSponsor(){
		$account = trim(I('post.account'));
		$iu_logic= trim(I('post.iu_logic'));
		$data    = D('testuser')->getMemberPlacement($account,$iu_logic);
		echo $data;
	}

	/**
	* 推荐网查询
	* @param account 会员account
	* @param sponsor 会员推荐网上线ID
	**/
	public function getUserBinary(){
		$account = trim(I('post.account'));
		$data    = D('testuser')->getUserBinary($account);
		p($data);
	}

	/**
	* 创建网络
	**/
	public function makeBinary(){
		// sponsorID = 35862601
		$SponsorID = trim(I('post.sponsorid'));
		$arr = M('user')->select();
		$data= getAllBinary($arr,$SponsorID);
		p($data);
	}

	public function check(){
		$arr = M('user')->limit(1000)->select();
		$num = 0;
		foreach ($arr as $key => $value) {
			$data = M('user')->where(array('CustomerID'=>$value['enrollerid']))->find();
			if(!$data){
				echo $value['enrollerid'].'********';
				$num++;
			}
		}
		echo $num;
	}

	/**
	* 远程文件采集
	* @param string $remote 远程文件名
	* @param string $local 本地保存文件名
	* @return mixed
	**/
	public function getRemoveFile(){
		$remove = 'http://apps.hapy-life.com/hapylife/http.txt';
		$local  = './log.txt';

		//静态方法调用
		// $http   = \Org\Net\Http::curlDownload($remove,$local);

		//公共方法调用
		$http = new \Org\Net\Http();
		$http->curlDownload($remove,$local);

		echo file_get_contents('./log.txt');
    }


    /**
    * 导出用户信息
    **/
    public function user_excel(){
    	$title = array('CustomerID','Placement','EnrollerID','SponsorID','CustomerType','DistributorType','LastName','FirstName','Phone','CustomerStatus','City','State','Country');
    	$data = M('user')->field('CustomerID,Placement,EnrollerID,SponsorID,CustomerType,DistributorType,LastName,FirstName,Phone,CustomerStatus,City,State,Country')->where(array('PassWord'=>array('neq','')))->select();
    	// p($data);die;
    	create_csv($data,$title);    	
    }


    
    











































































}