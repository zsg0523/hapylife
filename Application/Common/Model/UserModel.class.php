<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* Hapylife�û�model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$map,$keyword='',$order='',$limit=100,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		//��ȡ��ҳ����
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
	//��ȡ���ҵ�λ
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

	//��ȡ������
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
	* �Զ��ŵ�λ
	* @param account  ע���Ա������memberNo
	* @param iu_logic ѡ���λ����
	**/
	public function getMemberPlacement($account,$iu_logic){
	    //��ѯ�û�Ա�İ������
	    $placement = $this->getPlacement($account);
	    //�õ�λ���û�Ա������
	    $memberNumber = count($placement);
	    switch ($memberNumber) {
	        case '0':
	            //�Զ����ڵ�λ���,�޷�ѡ���λ����
	            $account = $account;
	            break;
	        case '1':
	            //��ߣ����ڵ�λ��ߵ�����
	            //�ұߣ����ڵ�λ���ұ�
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
	            //��ߣ����ڵ�λ��ߵ�����
	            //�ұߣ����ڵ�λ�ұߵ�����
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