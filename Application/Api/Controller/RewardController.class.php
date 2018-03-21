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
	* binary 自动排网
	**/
	public function binary(){
		$account = trim(I('post.account'));
		$iu_logic= trim(I('post.iu_logic'));
		$data    = D('testuser')->getMemberPlacement($account,$iu_logic);
		// $data    = D('testuser')->getMostLeftPlacement($account);
		echo $data;
	}














































































}