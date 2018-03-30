<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* Hapylife用户model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=100,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		//获取分页数据
		if(empty($keyword)){
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}else{
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->where(array('CustomerID'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->field($field)
					->order($order)
					->where(array('CustomerID'=>array('like','%'.$keyword.'%')))
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
	//获取左右点位
	public function getPlacement($account){
		$mape = M('User')->where(array('upperid'=>$account))->select();
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
		$data = D('User')->select();
		$binary = getAllBinary($data,$account);
		if(!empty($binary)){
			foreach ($binary as $key => $value) {
				if($value['Placement'] == 'Left'){
					$LeftPlacement = $value['customerid'];
				}
			}
			if($LeftPlacement){
				$mostLeftPlacement['customerid'] = $LeftPlacement;
			}else{
				$mostLeftPlacement['customerid'] = $account;
			}
		}else{
			$mostLeftPlacement['customerid'] = $account;
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
	                case 'Left':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['customerid']);
	                    $account = $memberInfo['customerid'];
	                    break;
	                case 'Right':
	                    $account = $account;
	                    break;
	            }
	            break;
	        case '2':
	            //左边，排在点位左边的最左
	            //右边，排在点位右边的最左
	            switch ($iu_logic) {
	                case 'Left':
	                    $memberInfo = $this->getMostLeftPlacement($placement[0]['customerid']);
	                    // p($memberInfo);die;
	                    $account = $memberInfo['customerid'];
	                    break;
	                case 'Right':
	                    $memberInfo = $this->getMostLeftPlacement($placement[1]['customerid']);
	                    $account = $memberInfo['customerid'];
	                    break;
	            }
	        break;
	    }
	    return($account);
	}
}