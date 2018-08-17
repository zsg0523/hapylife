<?php
namespace Common\Model;
use Common\Model\BaseModel;
/*
* testuser Model
*/
class FolderModel extends BaseModel{
	//获取左右点位
	public function getPlacement($account){
		$mape = M('Folder')->where(array('upperid'=>$account))->select();
		foreach ($mape as $key => $value) {
			if($value['foot']=='left'){
				$data[0]=$value;
			}else{
				$data[1]=$value;
			}
		}
		return $data;
	}

	//获取极左安置
	public function getMostLeftPlacement($account){
		$data = M('Folder')->select();
		$binary = getBinary($data,$account);
		if(!empty($binary)){
			foreach ($binary as $key => $value) {
				if($value['foot'] == 'left'){
					$LeftPlacement = $value['myselfid'];
				}
			}
			if($LeftPlacement){
				$mostLeftPlacement['myselfid'] = $LeftPlacement;
			}else{
				$mostLeftPlacement['myselfid'] = $account;
			}
		}else{
			$mostLeftPlacement['myselfid'] = $account;
		}
		// p($mostLeftPlacement);die;
		return $mostLeftPlacement;
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
	                case 'left':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['myselfid']);
	                    $account = $memberInfo['myselfid'];
	                    break;
	                case 'right':
	                    $account = $account;
	                    break;
	            }
	            break;
	        case '2':
	            //左边，排在点位左边的最左
	            //右边，排在点位右边的最左
	            switch ($iu_logic) {
	                case 'left':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['myselfid']);
	                    // p($memberInfo);die;
	                    $account = $memberInfo['myselfid'];
	                    break;
	                case 'right':
	                    $memberInfo = $this->getMostLeftPlacement($placement[1]['myselfid']);
	                    $account = $memberInfo['myselfid'];
	                    break;
	            }
	        break;
	    }
	    return($account);
	}


	/**
	* 推荐网查询
	**/
	public function getUserBinary($account){
		$data = M('Folder')->select();
		$binary = getBinary($data,$account);
		return $binary;
	}


}