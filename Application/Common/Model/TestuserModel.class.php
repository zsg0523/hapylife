<?php
namespace Common\Model;
use Common\Model\BaseModel;
/*
* testuser Model
*/
class TestuserModel extends BaseModel{
	//获取左右点位
	public function getPlacement($account){
		$data = M('testuser')->where(array('sponsor'=>$account))->select();
		return $data;
	}

	//获取极左安置
	public function getMostLeftPlacement($account){
		$data = M('testuser')->select();
		$binary = getBinary($data,$account);
		if(!empty($binary)){
			foreach ($binary as $key => $value) {
				if($value['placement'] == 'left'){
					$mostLeftPlacement = $value;
				}else{
					return $mostLeftPlacement;
				}
			}
		}else{
			$array['account'] = $account;
			return $array;
		}
	}

	/**
	* 自动排点位
	* @param account  注册会员的上线memberNo
	* @param iu_logic 选择点位左右
	**/
	public function getMemberPlacement($account,$iu_logic){
	    //查询该会员的安置情况
	    $placement = $this->getPlacement($account);
	    //该点位安置会员的数量
	    $memberNumber = count($placement);
	    switch ($memberNumber) {
	        case '0':
	            //自动排在点位左边,无发选择点位左右
	            $account = $account;
	            break;
	        case '1':
	            //左边，排在点位左边的最左
	            //右边，排在点位的右边
	            switch ($iu_logic) {
	                case '0':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['account']);
	                    $account = $memberInfo['account'];
	                    break;
	                case '1':
	                    $account = $account;
	                    break;
	            }
	            break;
	        case '2':
	            //左边，排在点位左边的最左
	            //右边，排在点位右边的最左
	            switch ($iu_logic) {
	                case '0':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['account']);
	                    $account = $memberInfo['account'];
	                    break;
	                case '1':
	                    $memberInfo = $this->getMostLeftPlacement($placement[1]['account']);
	                    $account = $memberInfo['account'];
	                    break;
	            }
	        break;
	    }
	    return($account);
	}


	//下线查询
	public function getUserBinary($account){
		$data = $this->getMostLeftPlacement($account);
		return $data;
	}



}