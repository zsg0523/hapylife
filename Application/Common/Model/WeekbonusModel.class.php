<?php
namespace Common\Model;
use Common\Model\BaseModel;
/*
* 量碰周奖金计算model
*/
class WeekbonusModel extends BaseModel{
	/**
	* 单人bonus奖金计算
	**/
	public function weekBonus($iuid,$userid){
		//该用户bonus数据
		$data  = D('weekbonus')->where(array('iuid'=>$iuid))->select();
		//左右点位,第一层
		$firstLevel=D('weekbonus')->where(array('sponsorid'=>$userid,'iuid'=>$iuid))->select();
		$AllUser[0] = $firstLevel;
		//获取左大区 右大区人数,第二层
		$AllUser[1]  = leftTree($data,$firstLevel[0]['userid']);
		$AllUser[2]  = rightTree($data,$firstLevel[1]['userid']);
		foreach ($AllUser as $key => $value) {
			foreach ($value as $k => $v) {
				if($v['is_bonus'] == 0 && time()-$v['create_time']<=26*7*86400){
					$tmpe[$key][] = $v;
				}
			}
		}
		//区分左右脚第一层是否已对碰
		if($tmpe[0]){
			$leftNumber   = count($tmpe[1])+1;
			$rightNumber  = count($tmpe[2])+1;
		}else{
			$leftNumber   = count($tmpe[1]);
			$rightNumber  = count($tmpe[2]);
		}
		// p($tmpe);die;
		//是否可发生对碰
		if($leftNumber>=3 && $rightNumber>=3){
			
			//量碰次数,区分左右脚第一层是否已经对碰
			if(floor($leftNumber/3)-floor($rightNumber/3)>=0){
				if($tmpe[0]){
					$loopNumber = floor($rightNumber/3)*3-2;
				}else{
					$loopNumber = floor($rightNumber/3)*3-1;
				}
				$bonusNumber= floor($rightNumber/3);
			}else{
				if($tmpe[0]){
					$loopNumber = floor($leftNumber/3)*3-2;
				}else{
					$loopNumber = floor($leftNumber/3)*3-1;
				}
				$bonusNumber= floor($leftNumber/3);
			}
			$map = array(
				'bonus_time'=>time(),
				'is_bonus'=>1
			);
			//左右点位
			foreach ($firstLevel as $key => $value) {
				$save = D('weekbonus')->where(array('userid'=>$value['userid'],'iuid'=>$iuid))->save($map);
			}
			//左右大区对碰
			for ($i=0; $i <= $loopNumber; $i++) {
				$saveLeft = D('weekbonus')->where(array('userid'=>$tmpe[1][$i]['userid'],'iuid'=>$iuid))->save($map); 
				$saveRight = D('weekbonus')->where(array('userid'=>$tmpe[2][$i]['userid'],'iuid'=>$iuid))->save($map); 
			}
			//核算奖金
			$bonus = $bonusNumber*100;
			//根据会员等级，判断是否超过上限值

			return "左大区总人数".$leftNumber.'****'."右大区总人数".$rightNumber.'<br>对碰成功,总奖金：'.$bonus;
		}else{
			return "左大区总人数".$leftNumber.'****'."右大区总人数".$rightNumber."<br>不符合发生对碰条件";
		}
	}


























}