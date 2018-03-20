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
		//userid
		$userid = trim(I('post.userid'));
		$data  = M('weekbonus')->select();
		//左右点位,第一层
		$firstLevel=M('weekbonus')->where(array('sponsorid'=>$userid))->select();
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
		if($tmpe[0]){
			$leftNumber   = count($tmpe[1])+1;
			$rightNumber  = count($tmpe[2])+1;
		}else{
			$leftNumber   = count($tmpe[1]);
			$rightNumber  = COUNT($tmpe[2]);
		}

		//是否可发生对碰
		if($leftNumber>=3 && $rightNumber>=3){
			echo "左大区总人数".$leftNumber.'****'."右大区总人数".$rightNumber.":准备对碰".'<br>';
			//量碰次数
			if(floor($leftNumber/3)-floor($rightNumber/3)>=0){
				$loopNumber = $rightNumber-2;
				$bonusNumber= floor($rightNumber/3);
			}else{
				$loopNumber = $leftNumber-2;
				$bonusNumber= floor($leftNumber/3);
			}
			$map = array(
				'bonus_time'=>time(),
				'is_bonus'=>1
			);
			//左右点位
			foreach ($firstLevel as $key => $value) {
				$save = M('weekbonus')->where(array('userid'=>$value['userid']))->save($map);
			}
			//左右大区对碰
			for ($i=0; $i <= $loopNumber; $i++) {
				$saveLeft = M('weekbonus')->where(array('userid'=>$tmpe[1][$i]['userid']))->save($map); 
				$saveRight = M('weekbonus')->where(array('userid'=>$tmpe[2][$i]['userid']))->save($map); 
			}
			//核算奖金
			$bonus = $bonusNumber*100;
			//判断是否超过上限值

			echo $bonus;

		}else{
			echo "左大区总人数".$leftNumber.'****'."右大区总人数".$rightNumber.":不符合发生对碰条件";
		}
	}
}