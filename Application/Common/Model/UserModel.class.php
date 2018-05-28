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
    public function getPage($model,$word,$order='',$status,$starttime,$endtime,$limit=20){
        switch ($status) {
        	case '-1':
		        if(empty($word)){
					$count=$model->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))->count();
				}else{
					$count=$model
					->alias('u')
			        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
			        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
		            ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->field($field)
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();
			        }
		        }else{
		        	if(empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where(array('iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }
        		break;
        	default:
        		if(empty($word)){
					$count=$model
						->alias('u')
						->join('left join hapylife_receipt c on u.iuid = c.riuid')
						->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
						->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
						->count();
				}else{
					$count=$model
					->alias('u')
			        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
			        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
		            ->where(array('ir_status'=>$status,'iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->field($field)
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }else{
		        	if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->where(array('ir_status'=>$status,'iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where(array('ir_status'=>$status,'iuid|CustomerID|SponsorID|EnrollerID|Placement|CustomerStatus|LastName|FirstName'=>array('like','%'.$word.'%'),'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }
        		break;
        }
	        
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }

	public function export_excel($data){
		if($_SESSION['user']['username'] == 'mary' || $_SESSION['user']['username'] == 'admin2'){
			$title   = array('UREGTIME','Happy Life ID','Payment Date Time (Dallas time)','Sponsor ID W & H','Product','Gender','Last Name','First Name','En Last Name','En First Name','email address','password','phone1','mailing address1','mailing city','mailing province','mailing country','Identification Card (upload)','Account Type');
			foreach ($data as $k => $v) {
				$content[$k]['joinedon']   = date('Y-m-d H:i:s',$v['joinedon']);
				$content[$k]['customerid'] = $v['customerid'];
				if($v['ir_paytime'] == 0){
					$content[$k]['paymentdateTime'] = '';
				}else{
					$content[$k]['paymentdateTime'] = date('Y-m-d H:i:s',$v['ir_paytime']-13*3600);
				}
				$content[$k]['enrollerid']         = $v['enrollerid'];
				$content[$k]['product']            = $v['product_name'];
				$content[$k]['sex']                = $v['sex'];
				$content[$k]['lastname']           = $v['lastname'];
				$content[$k]['firstname']          = $v['firstname'];
				$content[$k]['enlastname']         = $v['enlastname'];
				$content[$k]['enfirstname']        = $v['enfirstname'];
				$content[$k]['email']              = $v['email'];
				$content[$k]['password']           = $v['password'];
				$content[$k]['phone']              = $v['phone'];
				$content[$k]['shopaddress1']       = $v['shopaddress1'];
				$content[$k]['shopcity']           = $v['shopcity'];
				$content[$k]['shopprovince']       = $v['shopprovince'];
				$content[$k]['shopcountry']        = $v['shopcountry'];
				$content[$k]['idcard']             = $v['idcard'];
				$content[$k]['accounttype']        = $v['accounttype'];
			}
	    	create_csv($content,$title);
			return;
		}else{
			$title   = array('登记时间','账号名','支付时间','邀请人','产品名称','性别','中文姓','中文名','英文姓','英文名','电子邮箱','用户密码','手机号码','详细地址','所在城市','所在省份','所在国家','身份证号码','销售类型');
			foreach ($data as $k => $v) {
				$content[$k]['joinedon']   = date('Y-m-d H:i:s',$v['joinedon']);
				$content[$k]['customerid'] = $v['customerid'];
				if($v['ir_paytime'] == 0){
					$content[$k]['paymentdateTime'] = '';
				}else{
					$content[$k]['paymentdateTime'] = date('Y-m-d H:i:s',$v['ir_paytime']-13*3600);
				}
				$content[$k]['enrollerid']         = $v['enrollerid'];
				$content[$k]['product']            = $v['product_name'];
				$content[$k]['sex']                = $v['sex'];
				$content[$k]['lastname']           = $v['lastname'];
				$content[$k]['firstname']          = $v['firstname'];
				$content[$k]['enlastname']         = $v['enlastname'];
				$content[$k]['enfirstname']        = $v['enfirstname'];
				$content[$k]['email']              = $v['email'];
				$content[$k]['password']           = $v['password'];
				$content[$k]['phone']              = $v['phone'];
				$content[$k]['shopaddress1']       = $v['shopaddress1'];
				$content[$k]['shopcity']           = $v['shopcity'];
				$content[$k]['shopprovince']       = $v['shopprovince'];
				$content[$k]['shopcountry']        = $v['shopcountry'];
				$content[$k]['idcard']             = $v['idcard'];
				$content[$k]['accounttype']        = $v['accounttype'];
			}
	    	create_csv($content,$title);
			return;
		}
    }
}