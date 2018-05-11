<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* HapylifeÓÃ»§model
**/
class UserModel extends BaseModel{
	public function getAllData($model,$map,$word='',$order='',$limit=50,$field=''){
		$count=$model
			  ->where($map)
			  ->count();
		$page=new_page($count,$limit);
		
		if(empty($word)){
			if(empty($field)){
				$list=$model
					->where($map)
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->where($map)
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
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->where($map)
					->field($field)
					->order($order)
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
	//»ñÈ¡×óÓÒµãÎ»
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

	//»ñÈ¡¼«×ó°²ÖÃ
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
	* ×Ô¶¯ÅÅµãÎ»
	* @param account  ×¢²á»áÔ±µÄÉÏÏßmemberNo
	* @param iu_logic Ñ¡ÔñµãÎ»×óÓÒ
	**/
	public function getMemberPlacement($account,$iu_logic){
	    //²éÑ¯¸Ã»áÔ±µÄ°²ÖÃÇé¿ö
	    $placement = $this->getPlacement($account);
	    //¸ÃµãÎ»°²ÖÃ»áÔ±µÄÊýÁ¿
	    $memberNumber = count($placement);
	    switch ($memberNumber) {
	        case '0':
	            //×Ô¶¯ÅÅÔÚµãÎ»×ó±ß,ÎÞ·¢Ñ¡ÔñµãÎ»×óÓÒ
	            $account = $account;
	            break;
	        case '1':
	            //×ó±ß£¬ÅÅÔÚµãÎ»×ó±ßµÄ×î×ó
	            //ÓÒ±ß£¬ÅÅÔÚµãÎ»µÄÓÒ±ß
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
	            //×ó±ß£¬ÅÅÔÚµãÎ»×ó±ßµÄ×î×ó
	            //ÓÒ±ß£¬ÅÅÔÚµãÎ»ÓÒ±ßµÄ×î×ó
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

	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$order='',$starttime,$endtime,$limit=20){
    		if(empty($word)){
				$count=$model
					->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
					->count();
			}else{
				$count=$model
	            ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
	            ->count();
			}
	        $page=new_page($count,$limit);
	        // 获取分页数据
	        if(empty($word)){
	        	if (empty($field)) {
		            $list=$model
		            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		                ->order($order)
		                ->limit($page->firstRow.','.$page->listRows)
		                ->select();         
		        }else{
		            $list=$model
		            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		                ->field($field)
		                ->order($order)
		                ->limit($page->firstRow.','.$page->listRows)
		                ->select();         
		        }
	        }else{
	        	if (empty($field)) {
		            $list=$model
		                ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		                ->order($order)
		                ->limit($page->firstRow.','.$page->listRows)
		                ->select();         
		        }else{
		            $list=$model
		                ->field($field)
		                ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		                ->order($order)
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

	public function export_excel($data){
		$title   = array('注册时间','用户ID','支付成功','支付日期','新用户注册','邀请人','产品编号','性别','中文名','中文姓','英文名','英文姓','邮箱地址','密码','手机号码','邮寄地址1','邮寄地址2','邮寄城市','邮政编码','邮寄省份','邮寄国家','身份证号码','同意条款','设备地理位置','设备类型','浏览器类型','浏览器版本','支付类型');
		foreach ($data as $k => $v) {
			$content[$k]['joinedon']     	= date('Y-m-d',$v['joinedon']);
			$content[$k]['customerid']  	= $v['customerid'];
			$content[$k]['paymentreceived'] = $v['paymentreceived'];
			$content[$k]['paymentdateTime'] = $v['paymentdateTime'];
			$content[$k]['isNew']       	= $v['isNew'];
			$content[$k]['enrollerid']      = $v['enrollerid'];
			$content[$k]['product']        = $v['product'];
			$content[$k]['sex']       = $v['sex'];
			$content[$k]['lastname']     = $v['lastname'];
			$content[$k]['firstname']     = $v['firstname'];
			$content[$k]['enlastname']     = $v['enlastname'];
			$content[$k]['enfirstname']     = $v['enfirstname'];
			$content[$k]['email']     = $v['email'];
			$content[$k]['password']     = $v['password'];
			$content[$k]['phone']     = $v['phone'];
			$content[$k]['shopaddress1']     = $v['shopaddress1'];
			$content[$k]['shopaddress2']     = $v['shopaddress2'];
			$content[$k]['shopcity']     = $v['shopcity'];
			$content[$k]['shopcode']     = $v['shopcode'];
			$content[$k]['shopprovince']     = $v['shopprovince'];
			$content[$k]['shopcountry']     = $v['shopcountry'];
			$content[$k]['idcard']     = $v['idcard'];
			$content[$k]['termsandconditions']     = $v['termsandconditions'];
			$content[$k]['devicegeolocation']     = $v['devicegeolocation'];
			$content[$k]['devicetype']     = $v['devicetype'];
			$content[$k]['browser']     = $v['browser'];
			$content[$k]['browserversion']     = $v['browserversion'];
			$content[$k]['paymenttype']     = $v['paymenttype'];
		}
    	create_csv($content,$title);
		return;
    }
}