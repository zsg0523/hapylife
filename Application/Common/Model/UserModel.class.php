<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* HapylifeÓÃ»§model
**/
class UserModel extends BaseModel{
	// 批量验证
    protected $patchValidate = true;
    // 验证规则
    protected $_validate = array(
        array('LastName','require','请填写中文姓'), 
        array('FirstName','require','请填写中文名'), 
        array('EnLastName','require','请填写英文姓，如果没有英文姓请填写中文拼音'), 
        array('EnFirstName','require','请填写英文名，如果没有英文名请填写中文拼音'), 
        array('PassWord','/^[a-zA-Z0-9]{7,}/','密码最少7位数'), 
        array('ConfirmPassWord','PassWord','两次输入密码不一致',0,'confirm'), 
        array('EnrollerID','require','推荐人不能为空！'), 
        array('Email','/^\w+([.]\w+)?[@]\w+[.]\w+([.]\w+)?$/','请输入正确的电子邮箱'),
        array('Phone','/^0?(13[0-9]|14[579]|15[012356789]|16[6]|17[0135678]|18[0-9]|19[89])[0-9]{8}$/','请输入正确的电话号码'),
        // array('Phone','','该号码已被注册',0,'unique'),
        array('ShopCountry','require','请填写国家'), 
        array('ShopProvince','require','请填写所在省'), 
        array('ShopCity','require','请填写所在市'), 
        array('ShopArea','require','请填写所在区'), 
        array('ShopAddress1','require','请填写详细地址'), 
        // array('BankName','require','银行名称不能为空'), 
        // array('BankAccount','require','银行账号不能为空'), 
        // array('BankNum','/^([1-9]{1})(\d{14}|\d{18})$/','请填写有效的银行账号'), 
        // array('BankProvince','require','请填写银行所在省'), 
        // array('BankCity','require','请填写银行所在市'), 
        // array('BankArea','require','请填写银行所在区'), 
        // array('SubName','require','支行名称不能为空'), 
        // array('Idcard','/(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)|[A-Za-z]{1}\d{6}[(\d)]{3}/','请输入有效身份证号码'),
        // array('Idcard','/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX])$/','请输入有效身份证号码'),
        // array('Idcard','/[^\w\s]+/','存在非法字符'),
   );
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
    public function getPage($model,$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='',$status,$starttime,$endtime,$limit=20){

    	if($customerid){
			$where = array('CustomerID|wvCustomerID|EnrollerID'=>array('like','%'.$customerid.'%'));
		}
		if($wvcustomerid){
			$where['wvCustomerID'] = array('like','%'.$wvcustomerid.'%');
		}
		if($lastname){
			$where['LastName'] = array('like','%'.$lastname.'%');
		}
		if($firstname){
			$where['FirstName'] = array('like','%'.$firstname.'%');
		}
		if($phone){
			$where['Phone'] = array('like','%'.$phone.'%');
		}
		if($enrollerid){
			$where['EnrollerID'] = array('like','%'.$enrollerid.'%');
		}

        switch ($status) {
        	case '-1':
	        	if(!empty($starttime) && empty($endtime)){
	        		$count=$model
						->alias('u')
				        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
				        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				        ->where($where)
			            ->where(array('joinedon'=>array('egt',$starttime)))
			            ->count();
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if(empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
	        	}else if(!empty($starttime) && !empty($endtime)){
	        		$count=$model
						->alias('u')
				        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
				        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				        ->where($where)
			            ->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			            ->count();
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if(empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
	        	}else{
	        		if($where){
	        			$count=$model
							->alias('u')
					        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
					        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
					        ->where($where)
				            ->count();
	        		}
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if($where){
			        	if (empty($field)) {
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				            	->where($where)
				                ->order($order)
				                ->limit($page->firstRow.','.$page->listRows)
				                ->select();         
				        }else{
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				            	->where($where)
				                ->field($field)
				                ->order($order)
				                ->limit($page->firstRow.','.$page->listRows)
				                ->select();
				        }
			        }
	        	}
        		break;
        	default:
	        	if(!empty($starttime) && empty($endtime)){
	        		$count=$model
							->alias('u')
					        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
					        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
					        ->where($where)
				            ->where(array('ir_status'=>$status,'joinedon'=>array('egt',$starttime)))
				            ->count();
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
	        	}else if(!empty($starttime) && !empty($endtime)){
	        		$count=$model
						->alias('u')
				        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
				        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				        ->where($where)
			            ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			            ->count();
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
	        	}else{
	        		if($where){
						$count=$model
							->alias('u')
					        ->join('left join hapylife_receipt c on u.iuid = c.riuid')
					        ->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
					        ->where($where)
					        ->where(array('ir_status'=>$status))
				            ->count();
					}
			        $page=new_page($count,$limit);
			        // 获取分页数据
			        if($where){
			        	if (empty($field)) {
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				            	->where($where)
				                ->where(array('ir_status'=>$status))
				                ->order($order)
				                ->limit($page->firstRow.','.$page->listRows)
				                ->select();         
				        }else{
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				                ->field($field)
				                ->where($where)
				                ->where(array('ir_status'=>$status))
				                ->order($order)
				                ->limit($page->firstRow.','.$page->listRows)
				                ->select();         
				        }
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

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPageAllmemBer($model,$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='',$status,$starttime,$endtime,$limit=20){

    	if($customerid){
			$where = array('CustomerID|wvCustomerID|EnrollerID'=>array('like','%'.$customerid.'%'));
		}
		if($wvcustomerid){
			$where['wvCustomerID'] = array('like','%'.$wvcustomerid.'%');
		}
		if($lastname){
			$where['LastName'] = array('like','%'.$lastname.'%');
		}
		if($firstname){
			$where['FirstName'] = array('like','%'.$firstname.'%');
		}
		if($phone){
			$where['Phone'] = array('like','%'.$phone.'%');
		}
		if($enrollerid){
			$where['EnrollerID'] = array('like','%'.$enrollerid.'%');
		}

        switch ($status) {
        	case '-1':
		        // 获取分页数据
	        	if(!empty($starttime) && empty($endtime)){
	        		if(empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->select();         
			        }
	        	}else if(!empty($starttime) && !empty($endtime)){
	        		if(empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->select();         
			        }
	        	}else{
	        		if($where){
			        	if(empty($field)) {
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				                ->where($where)
				                ->order($order)
				                ->select();         
				        }else{
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				                ->field($field)
				                ->where($where)
				                ->order($order)
				                ->select();         
				        }
			        }
	        	} 
        		break;
        	default:
		        // 获取分页数据
		        if(!empty($starttime) && empty($endtime)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array('egt',$starttime)))
			                ->order($order)
			                ->select();         
			        }
		        }else if(!empty($starttime) && !empty($endtime)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			            	->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('u')
			            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
			            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
			                ->field($field)
			                ->where($where)
			                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->select();         
			        }
		        }else{
		        	if($where){
			        	if (empty($field)) {
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				            	->where($where)
				                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
				                ->order($order)
				                ->select();         
				        }else{
				            $list=$model
				            	->alias('u')
				            	->join('left join hapylife_receipt c on u.iuid = c.riuid')
				            	->join('left join hapylife_receiptlist l on c.ir_receiptnum = l.ir_receiptnum')
				                ->field($field)
				                ->where($where)
				                ->where(array('ir_status'=>$status,'joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
				                ->order($order)
				                ->select();         
				        }
			        }
		        }
        		break;
        }
	        
        $data=array(
            'data'=>$list,
            );
        return $data;
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
    public function getPageS($model,$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='',$starttime,$endtime,$limit=20){
    	if($customerid){
			$where = array('CustomerID|wvCustomerID|EnrollerID'=>array('like','%'.$customerid.'%'));
		}
		if($wvcustomerid){
			$where['wvCustomerID'] = array('like','%'.$wvcustomerid.'%');
		}
		if($lastname){
			$where['LastName'] = array('like','%'.$lastname.'%');
		}
		if($firstname){
			$where['FirstName'] = array('like','%'.$firstname.'%');
		}
		if($phone){
			$where['Phone'] = array('like','%'.$phone.'%');
		}
		if($enrollerid){
			$where['EnrollerID'] = array('like','%'.$enrollerid.'%');
		}
    	if(!empty($starttime) && empty($endtime)){
			$count=$model
				->alias('u')
				->where($where)
				->where(array('joinedon'=>array('egt',$starttime)))
				->count();
    	}else if(!empty($starttime) && !empty($endtime)){
			$count=$model
				->alias('u')
				->where($where)
				->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
				->count();
    	}else{
    		if($where){
				$count=$model
						->alias('u')
						->where($where)
						->count();
    		}
    	}
        $page=new_page($count,$limit);
	    // 获取分页数据
        if(!empty($starttime) && empty($endtime)){
       		if (empty($field)) {
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array('egt',$starttime)))
	                ->order($order)
	                ->limit($page->firstRow.','.$page->listRows)
	                ->select();         
	        }else{
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array('egt',$starttime)))
	                ->field($field)
	                ->order($order)
	                ->limit($page->firstRow.','.$page->listRows)
	                ->select();         
	        }
       	}else if(!empty($starttime) && !empty($endtime)){
       		if (empty($field)) {
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
	                ->order($order)
	                ->limit($page->firstRow.','.$page->listRows)
	                ->select();         
	        }else{
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
	                ->field($field)
	                ->order($order)
	                ->limit($page->firstRow.','.$page->listRows)
	                ->select();         
	        }
       	}else{
       		if($where){
	       		if(empty($field)) {
	            	$list=$model
		            	->alias('u')
		            	->where($where)
		                ->order($order)
		                ->limit($page->firstRow.','.$page->listRows)
		                ->select();         
		        }else{
		            $list=$model
		            	->alias('u')
		                ->field($field)
		                ->where($where)
		                ->order($order)
		                ->limit($page->firstRow.','.$page->listRows)
		                ->select();         
		        }
       		}
       	}

        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
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
    public function getAllmemBer($model,$customerid,$wvcustomerid,$lastname,$firstname,$phone,$enrollerid,$order='',$starttime,$endtime){
	    if($customerid){
			$where = array('CustomerID|wvCustomerID|EnrollerID'=>array('like','%'.$customerid.'%'));
		}
		if($wvcustomerid){
			$where['wvCustomerID'] = array('like','%'.$wvcustomerid.'%');
		}
		if($lastname){
			$where['LastName'] = array('like','%'.$lastname.'%');
		}
		if($firstname){
			$where['FirstName'] = array('like','%'.$firstname.'%');
		}
		if($phone){
			$where['Phone'] = array('like','%'.$phone.'%');
		}
		if($enrollerid){
			$where['EnrollerID'] = array('like','%'.$enrollerid.'%');
		}

    	if(!empty($starttime) && empty($endtime)){
    		if (empty($field)) {
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array('egt',$starttime)))
	                ->order($order)
	                ->select();         
	        }else{
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array('egt',$starttime)))
	                ->field($field)
	                ->order($order)
	                ->select();         
	        }
    	}else if(!empty($starttime) && !empty($endtime)){
    		if (empty($field)) {
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
	                ->order($order)
	                ->select();         
	        }else{
	            $list=$model
	            	->alias('u')
	            	->where($where)
	            	->where(array('joinedon'=>array(array('egt',$starttime),array('elt',$endtime))))
	                ->field($field)
	                ->order($order)
	                ->select();         
	        }
    	}else{
    		if (empty($field)) {
	            $list=$model
	            	->alias('u')
	            	->where($where)
	                ->order($order)
	                ->select();         
	        }else{
	            $list=$model
	            	->alias('u')
	            	->where($where)
	                ->field($field)
	                ->order($order)
	                ->select();         
	        }
    	}
	        
        $data = array(
        	'data'=>$list,
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
	    	create_csv($content,$title,date('YmdHis',time()));
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
	    	create_csv($content,$title,date('YmdHis',time()));
			return;
		}
    }

    public function export_excelDt($data){
		$title   = array('账号','中文姓名','英文姓名','可用DT','已消费DT');
		// p($data);die;
		foreach ($data as $k => $v) {
			$content[$k]['customerid'] 		= $v['hu_nickname'];
			$content[$k]['name_cn']    		= $v['hu_username'];
			$content[$k]['name_en']    		= $v['hu_username_en'];
			$content[$k]['iu_dt']     		= $v['iu_dt'];
			$content[$k]['realpoint4']      = $v['realpoint4'];
		}
    	create_csv($content,$title,date('YmdHis',time()));
		return;
    }

    /**
	* 用户EP列表
	**/
	public function getAllPoint($model,$word,$order='',$limit=50){
		if(empty($word)){
			$count=$model->order($order)->count();
		}else{
			$count=$model
				->order($order)
				->where(array('CustomerID|teamCode'=>array('like','%'.$word.'%')))
				->count();
		}
		$page=new_page($count,$limit);
		if(empty($word)){
			$list=$model
				->order($order)
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->order($order)
				->where(array('CustomerID|teamCode'=>array('like','%'.$word.'%')))
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}
		// p($list);
		$point = D('Getpoint')->where(array('status'=>array('in','0,1,2')))->select();
		foreach ($list as $key => $value) {
			foreach ($point as $k => $v) {
				if($v['hu_nickname']==$value['customerid']){
					$mape[$key][] = $v;
				}
			}
		}
		// p($mape);die;
		foreach ($mape as $key => $value) {
        	foreach ($value as $k => $v) {
        		switch ($v['pointtype']) {
					case '1':
        				$arr[$key]['realpoint1'] = bcadd($arr[$key]['realpoint1'],$v['getpoint'],4);
        				$arr[$key]['hu_nickname']= $v['hu_nickname'];
        				break;
	        		case '2':
	        			$arr[$key]['realpoint2'] = bcadd($arr[$key]['realpoint2'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '3':
	        			$arr[$key]['realpoint3'] = bcadd($arr[$key]['realpoint3'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '4':
	        			$arr[$key]['realpoint4'] = bcadd($arr[$key]['realpoint4'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '5':
	        			$arr[$key]['realpoint5'] = bcadd($arr[$key]['realpoint5'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '6':
	        			if($v['status']==2){
	        				$arr[$key]['realpoint6'] = bcadd($arr[$key]['realpoint6'],$v['getpoint'],4);
	        				$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			}else{
	        				$arr[$key]['realpoint9'] = bcadd($arr[$key]['realpoint9'],$v['getpoint'],4);
	        				$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			}
	        			break;
	        		case '7':
	        			$arr[$key]['realpoint7'] = bcadd($arr[$key]['realpoint7'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '8':
	        			$arr[$key]['realpoint8'] = bcadd($arr[$key]['realpoint8'],$v['getpoint'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
        		}
        	}
        }
        // p($arr);
        // die;
        foreach ($arr as $key => $value) {
        	$arrtmp[$key] = $value;
        	if($value['realpoint1']>0){
        		$arrtmp[$key]['realpoint1'] = $value['realpoint1'];
        	}else{
        		$arrtmp[$key]['realpoint1'] = 0;
        	}
        	if($value['realpoint2']>0){
        		$arrtmp[$key]['realpoint2'] = $value['realpoint2'];
        	}else{
        		$arrtmp[$key]['realpoint2'] = 0;
        	}
	        if($value['realpoint3']>0){
	        		$arrtmp[$key]['realpoint3'] = $value['realpoint3'];
        	}else{
        		$arrtmp[$key]['realpoint3'] = 0;
        	}
	        if($value['realpoint4']>0){
	        		$arrtmp[$key]['realpoint4'] = $value['realpoint4'];
        	}else{
        		$arrtmp[$key]['realpoint4'] = 0;
        	}
	        if($value['realpoint5']>0){
	        		$arrtmp[$key]['realpoint5'] = $value['realpoint5'];
        	}else{
        		$arrtmp[$key]['realpoint5'] = 0;
        	}
	        if($value['realpoint6']>0){
	        		$arrtmp[$key]['realpoint6'] = $value['realpoint6'];
        	}else{
        		$arrtmp[$key]['realpoint6'] = 0;
        	}
	        if($value['realpoint7']>0){
	        		$arrtmp[$key]['realpoint7'] = $value['realpoint7'];
        	}else{
        		$arrtmp[$key]['realpoint7'] = 0;
        	}
	        if($value['realpoint8']>0){
		        $arrtmp[$key]['realpoint8'] = $value['realpoint8'];
        	}else{
        		$arrtmp[$key]['realpoint8'] = 0;
        	}
        }
        // p($arrtmp);
        // die;
		foreach ($list as $key => $value) {
			// p($value);
			foreach ($arrtmp as $k => $v) {
				if($value['customerid']==$v['hu_nickname']){
					$userArr[$key] = $value;
					$users[0][$key]['hu_nickname'] = $value['customerid'];
					$users[0][$key]['hu_username'] = $value['lastname'].$value['firstname'];
					$users[0][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
					$users[0][$key]['teamcode'] = $value['teamcode'];
					$users[0][$key]['iuid']        = $value['iuid'];
					$users[0][$key]['iu_point']    = $value['iu_point'];
					$users[0][$key]['iu_unpoint']  = $value['iu_unpoint'];
	                $increase = bcadd(bcadd($v['realpoint2'],$v['realpoint3'],4),$v['realpoint5'],4);
	                $reduce   = bcadd(bcadd(bcadd(bcadd($v['realpoint1'],$v['realpoint4'],4),$v['realpoint6'],4),$v['realpoint7'],4),$v['realpoint9'],4);
	                $number1  = bcsub($increase,$reduce,4);
					$users[0][$key]['deviation'] = bcsub($value['iu_point'],$number1,4);
					$users[0][$key]['realpoint1'] = $v['realpoint1'];
					$users[0][$key]['realpoint2'] = $v['realpoint2'];
					$users[0][$key]['realpoint3'] = $v['realpoint3'];
					$users[0][$key]['realpoint4'] = $v['realpoint4'];
					$users[0][$key]['realpoint5'] = $v['realpoint5'];
					$users[0][$key]['realpoint6'] = $v['realpoint6'];
					$users[0][$key]['realpoint7'] = $v['realpoint7'];
					$users[0][$key]['realpoint8'] = $v['realpoint8'];
				}
			}
		}
        // p($users);die;
	    foreach ($list as $key => $value) {
	        if(!in_array($value,$userArr)){
	            $noall[$key]=$value;
	        }
	    }
		foreach ($noall as $key => $value) {
			$users[1][$key]['hu_nickname'] = $value['customerid'];
			$users[1][$key]['hu_username'] = $value['lastname'].$value['firstname'];
			$users[1][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
			$users[1][$key]['teamcode'] = $value['teamcode'];
			$users[1][$key]['iuid']        = $value['iuid'];
			$users[1][$key]['iu_point']    = $value['iu_point'];
			$users[1][$key]['iu_unpoint']  = $value['iu_unpoint'];
			$users[1][$key]['deviation']   = bcsub($value['iu_point'],0,4);
			$users[1][$key]['realpoint1']  = 0;
			$users[1][$key]['realpoint2']  = 0;
			$users[1][$key]['realpoint3']  = 0;
			$users[1][$key]['realpoint4']  = 0;
			$users[1][$key]['realpoint5']  = 0;
			$users[1][$key]['realpoint6']  = 0;
			$users[1][$key]['realpoint7']  = 0;
			$users[1][$key]['realpoint8']  = 0;
		}
		foreach ($users as $key => $value) {
			foreach ($value as $k => $v) {
				$Date[] = $v;
			}
		}
		$userDate = array_sort($Date,'deviation',$type='desc');
		// p($userDate);die;
        $data=array(
			'data'=>$userDate,
			'page'=>$page->show()
		);        
		return $data;
	}
	/**
	* 用户DT列表
	**/
	public function getDtPoint($model,$word,$order='',$limit=50){
		if(empty($word)){
			$count=$model->order($order)->count();
		}else{
			$count=$model
				->order($order)
				->where(array('CustomerID|teamCode'=>array('like','%'.$word.'%')))
				->count();
		}
		$page=new_page($count,$limit);
		if(empty($word)){
			$list=$model
				->order($order)
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->order($order)
				->where(array('CustomerID|teamCode'=>array('like','%'.$word.'%')))
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}
		// p($list);
		$point = D('Getdt')->where(array('status'=>array('in','0,1,2')))->select();
		foreach ($list as $key => $value) {
			foreach ($point as $k => $v) {
				if($v['hu_nickname']==$value['customerid']){
					$mape[$key][] = $v;
				}
			}
		}
		// p($mape);die;
		foreach ($mape as $key => $value) {
        	foreach ($value as $k => $v) {
        		switch ($v['dttype']) {
					case '1':
        				$arr[$key]['realpoint1'] = bcadd($arr[$key]['realpoint1'],$v['getdt'],4);
        				$arr[$key]['hu_nickname']= $v['hu_nickname'];
        				break;
	        		case '2':
	        			$arr[$key]['realpoint2'] = bcadd($arr[$key]['realpoint2'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '3':
	        			$arr[$key]['realpoint3'] = bcadd($arr[$key]['realpoint3'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '4':
	        			$arr[$key]['realpoint4'] = bcadd($arr[$key]['realpoint4'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
        		}
        	}
        }
        // p($arr);
        // die;
        foreach ($arr as $key => $value) {
        	$arrtmp[$key] = $value;
        	if($value['realpoint1']>0){
        		$arrtmp[$key]['realpoint1'] = $value['realpoint1'];
        	}else{
        		$arrtmp[$key]['realpoint1'] = 0;
        	}
        	if($value['realpoint2']>0){
        		$arrtmp[$key]['realpoint2'] = $value['realpoint2'];
        	}else{
        		$arrtmp[$key]['realpoint2'] = 0;
        	}
	        if($value['realpoint3']>0){
	        		$arrtmp[$key]['realpoint3'] = $value['realpoint3'];
        	}else{
        		$arrtmp[$key]['realpoint3'] = 0;
        	}
	        if($value['realpoint4']>0){
	        		$arrtmp[$key]['realpoint4'] = $value['realpoint4'];
        	}else{
        		$arrtmp[$key]['realpoint4'] = 0;
        	}
        }
        // p($arrtmp);
        // die;
		foreach ($list as $key => $value) {
			// p($value);
			foreach ($arrtmp as $k => $v) {
				if($value['customerid']==$v['hu_nickname']){
					$userArr[$key] = $value;
					$users[0][$key]['hu_nickname'] = $value['customerid'];
					$users[0][$key]['hu_username'] = $value['lastname'].$value['firstname'];
					$users[0][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
					$users[0][$key]['teamcode']    = $value['teamcode'];
					$users[0][$key]['iuid']        = $value['iuid'];
					$users[0][$key]['iu_dt']       = $value['iu_dt'];
	                $increase = bcadd($v['realpoint2'],$v['realpoint3'],4);
	                $reduce   = bcadd($v['realpoint1'],$v['realpoint4'],4);
	                $number1  = bcsub($increase,$reduce,4);
					$users[0][$key]['deviation'] = bcsub($value['iu_dt'],$number1,4);
					$users[0][$key]['realpoint1'] = $v['realpoint1'];
					$users[0][$key]['realpoint2'] = $v['realpoint2'];
					$users[0][$key]['realpoint3'] = $v['realpoint3'];
					$users[0][$key]['realpoint4'] = $v['realpoint4'];
				}
			}
		}
        // p($users);die;
	    foreach ($list as $key => $value) {
	        if(!in_array($value,$userArr)){
	            $noall[$key]=$value;
	        }
	    }
		foreach ($noall as $key => $value) {
			$users[1][$key]['hu_nickname'] = $value['customerid'];
			$users[1][$key]['hu_username'] = $value['lastname'].$value['firstname'];
			$users[1][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
			$users[1][$key]['teamcode'] = $value['teamcode'];
			$users[1][$key]['iuid']        = $value['iuid'];
			$users[1][$key]['iu_dt']    = $value['iu_dt'];
			$users[1][$key]['deviation']   = bcsub($value['iu_dt'],0,4);
			$users[1][$key]['realpoint1']  = 0;
			$users[1][$key]['realpoint2']  = 0;
			$users[1][$key]['realpoint3']  = 0;
			$users[1][$key]['realpoint4']  = 0;
		}
		foreach ($users as $key => $value) {
			foreach ($value as $k => $v) {
				$Date[] = $v;
			}
		}
		$userDate = array_sort($Date,'deviation',$type='desc');
		// p($userDate);die;
        $data=array(
			'data'=>$userDate,
			'page'=>$page->show()
		);        
		return $data;
	}

	/**
	* 用户DT列表
	**/
	public function getDtPoints($model,$word,$order='',$array){
		if(empty($word)){
			$lisst=$model
				->order($order)
				->select();
		}else{
			$lisst=$model
				->order($order)
				->where(array('CustomerID|teamCode'=>array('like','%'.$word.'%'),'lastname|firstname'=>array('NOT IN',$array)))
				->select();
		}
		$array = explode(',',$array);
		foreach($lisst as $key=>$value){
			if(!in_array($value['lastname'],$array) && !in_array($value['firstname'],$array)){
				$list[] = $value;
			}
		}

		$point = D('Getdt')->where(array('status'=>array('in','0,1,2')))->select();
		foreach ($list as $key => $value) {
			foreach ($point as $k => $v) {
				if($v['hu_nickname']==$value['customerid']){
					$mape[$key][] = $v;
				}
			}
		}
		// p($mape);die;
		foreach ($mape as $key => $value) {
        	foreach ($value as $k => $v) {
        		switch ($v['dttype']) {
					case '1':
        				$arr[$key]['realpoint1'] = bcadd($arr[$key]['realpoint1'],$v['getdt'],4);
        				$arr[$key]['hu_nickname']= $v['hu_nickname'];
        				break;
	        		case '2':
	        			$arr[$key]['realpoint2'] = bcadd($arr[$key]['realpoint2'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '3':
	        			$arr[$key]['realpoint3'] = bcadd($arr[$key]['realpoint3'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
	        		case '4':
	        			$arr[$key]['realpoint4'] = bcadd($arr[$key]['realpoint4'],$v['getdt'],4);
	        			$arr[$key]['hu_nickname']= $v['hu_nickname'];
	        			break;
        		}
        	}
        }
        // p($arr);
        // die;
        foreach ($arr as $key => $value) {
        	$arrtmp[$key] = $value;
        	if($value['realpoint1']>0){
        		$arrtmp[$key]['realpoint1'] = $value['realpoint1'];
        	}else{
        		$arrtmp[$key]['realpoint1'] = 0;
        	}
        	if($value['realpoint2']>0){
        		$arrtmp[$key]['realpoint2'] = $value['realpoint2'];
        	}else{
        		$arrtmp[$key]['realpoint2'] = 0;
        	}
	        if($value['realpoint3']>0){
	        		$arrtmp[$key]['realpoint3'] = $value['realpoint3'];
        	}else{
        		$arrtmp[$key]['realpoint3'] = 0;
        	}
	        if($value['realpoint4']>0){
	        		$arrtmp[$key]['realpoint4'] = $value['realpoint4'];
        	}else{
        		$arrtmp[$key]['realpoint4'] = 0;
        	}
        }
        // p($arrtmp);
        // die;
		foreach ($list as $key => $value) {
			// p($value);
			foreach ($arrtmp as $k => $v) {
				if($value['customerid']==$v['hu_nickname']){
					$userArr[$key] = $value;
					$users[0][$key]['hu_nickname'] = $value['customerid'];
					$users[0][$key]['hu_username'] = $value['lastname'].$value['firstname'];
					$users[0][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
					$users[0][$key]['teamcode']    = $value['teamcode'];
					$users[0][$key]['iuid']        = $value['iuid'];
					$users[0][$key]['iu_dt']       = $value['iu_dt'];
	                $increase = bcadd($v['realpoint2'],$v['realpoint3'],4);
	                $reduce   = bcadd($v['realpoint1'],$v['realpoint4'],4);
	                $number1  = bcsub($increase,$reduce,4);
					$users[0][$key]['deviation'] = bcsub($value['iu_dt'],$number1,4);
					$users[0][$key]['realpoint1'] = $v['realpoint1'];
					$users[0][$key]['realpoint2'] = $v['realpoint2'];
					$users[0][$key]['realpoint3'] = $v['realpoint3'];
					$users[0][$key]['realpoint4'] = $v['realpoint4'];
					$users[0][$key]['distributortype'] = $value['distributortype'];
				}
			}
		}
        // p($users);die;
	    foreach ($list as $key => $value) {
	        if(!in_array($value,$userArr)){
	            $noall[$key]=$value;
	        }
	    }
		foreach ($noall as $key => $value) {
			$users[1][$key]['hu_nickname'] = $value['customerid'];
			$users[1][$key]['hu_username'] = $value['lastname'].$value['firstname'];
			$users[1][$key]['hu_username_en'] = $value['enlastname'].$value['enfirstname'];
			$users[1][$key]['teamcode'] = $value['teamcode'];
			$users[1][$key]['iuid']        = $value['iuid'];
			$users[1][$key]['iu_dt']    = $value['iu_dt'];
			$users[1][$key]['deviation']   = bcsub($value['iu_dt'],0,4);
			$users[1][$key]['realpoint1']  = 0;
			$users[1][$key]['realpoint2']  = 0;
			$users[1][$key]['realpoint3']  = 0;
			$users[1][$key]['realpoint4']  = 0;
		}
		foreach ($users as $key => $value) {
			foreach ($value as $k => $v) {
				$Date[] = $v;
			}
		}
		$userDate = array_sort($Date,'iuid',$type='desc');
		// p($userDate);die;
        $data=array(
			'data'=>$userDate,
		);        
		return $data;
	}

	public function export_excelBD($data){
        $title   = array('HapylifeID','wvCustomerID','LastName','FirstName','EnLastName','EnFirstName','Birthday');
        foreach ($data as $k => $v) {
            // HpID
            $content[$k]['HpID'] = $v['customerid'];
            // wvID
            $content[$k]['wvID'] = $v['wvcustomerid'];
            // lN
            $content[$k]['lN'] = $v['lastname'];
            // fN
            $content[$k]['fN'] = $v['firstname'];
            // elN
            $content[$k]['elN'] = $v['enlastname'];
            // efN
            $content[$k]['efN'] = $v['enfirstname'];
            // bD
            $content[$k]['bD'] = $v['birthday'];
            
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }

    public function getMemberList($model,$wv,$order='',$limit=20){
    	if(!empty($wv)){
			$count = $model->where(array('CustomerID'=>$wv))->count();
		}
		$page=new_page($count,$limit);

    	if(!empty($wv)){
			$data = $model->where(array('CustomerID'=>$wv))->select();
		}
		foreach($data as $key=>$value){
			if(strlen($value['customerid']) == 8){
				$data[$key]['son'] = $model->where(array('EnrollerID'=>$value['customerid']))->select();	
			}
		}
		foreach($data as $key=>$value){
			if($value['son']){
				$data[$key]['num'] = count($value['son']);
			}else{
				$data[$key]['num'] = 0;
			}
		}
		$map=array(
            'data'=>$data,
            'page'=>$page->show()
            );
        return $map;
    }

    /**
	* 多功能查询工具
    **/ 
    public function getIndexPage($model,$customerid,$order='',$limit=20){
    	if($customerid){
    		$where['CustomerID'] = array('LIKE','%'.$customerid.'%');
    	}
    	if($where){
			$count = $model->where($where)->count();
			$page=new_page($count,$limit);

			$data = $model->where($where)->select();
			
			foreach ($data as $key => $value) {
				// 获取USA编码
				$usa = new \Common\UsaApi\Usa();
		    	$activities = $usa->activities($value['customerid']);
		    	$validate = $usa->validateHpl($value['customerid']);
		    	$getCustomer = $usa->getCustomer($value['customerid']);
		    	$data[$key]['activities'] = $validate['isActive'];
		    	$data[$key]['binaryplacementpreference'] = $getCustomer['binaryPlacementPreference'];
		    	if(!$activities['errors']){
		            $weekly = $activities['weekly'];
		            $monthly = $activities['monthly'];
		            $code['weekly'] = array(
		                'description' => $weekly['description'],
		                'personalActive' => $weekly['personalActive'],
		            	'l' => array(
			                'newTotal' => $weekly['newBinaryUnlimitedLevelsLeft'],
			                'activeTotal' => $weekly['activeLeftLegWithAutoPlacement'],
			                'Total' => $weekly['leftLegTotal'],
			                'volumeTotal' => $weekly['volumeLeft'],
		            	),
		            	'r' => array(
		            		'newTotal' => $weekly['newBinaryUnlimitedLevelsRight'],
			                'activeTotal' => $weekly['activeRightLegWithAutoPlacement'],
			                'Total' => $weekly['rightLegTotal'],
			                'volumeTotal' => $weekly['volumeRight'],
		            	),
		            );
		            $code['monthly'] = array(
		                'description' => $monthly['description'],
		                'personalActive' => $monthly['personalActive'],
		                'l' => array(
			                'newTotal' => $monthly['newBinaryUnlimitedLevelsLeft'],
			                'activeTotal' => $monthly['activeLeftLegWithAutoPlacement'],
			                'Total' => $monthly['leftLegTotal'],
			                'volumeTotal' => $monthly['volumeLeft'],
		                ),
		                'r' => array(
		            		'newTotal' => $monthly['newBinaryUnlimitedLevelsRight'],
			                'activeTotal' => $monthly['activeRightLegWithAutoPlacement'],
			                'Total' => $monthly['rightLegTotal'],
			                'volumeTotal' => $monthly['volumeRight'],
		            	),
		            );
		        }else{
		            // 无数据
		            $code['weekly'] = array(
		                'description' => '无',
		                'personalActive' => '无',
		                'l' => array(
			                'newTotal' => '无',
			                'activeTotal' => '无',
			                'Total' => '无',
			                'volumeTotal' => '无',
		                ),
		                'r' => array(
		                	'newTotal' => '无',
			                'activeTotal' => '无',
			                'Total' => '无',
			                'volumeTotal' => '无',
		                ),
		            );
		            $code['monthly'] = array(
		                'description' => '无',
		                'personalActive' => '无',
		                'l' => array(
			                'newTotal' => '无',
			                'activeTotal' => '无',
			                'Total' => '无',
			                'volumeTotal' => '无',
		                ),
		                'r' => array(
		                	'newTotal' => '无',
			                'activeTotal' => '无',
			                'Total' => '无',
			                'volumeTotal' => '无',
		                ),
		            );
		        }
				
		        // 用户订单
		        $receipt = M('Receipt')->where(array('rCustomerID'=>$value['customerid'],'ir_status'=>array('NOT IN','404,')))->select();

		        // 用户奖金
		        $bonus = M('WvBonus')->where(array('HplId'=>$value['customerid']))->select();
		        $parities = M('WvBonusParities')->where(array('pid'=>1))->getfield('parities');
		        foreach($bonus as $key => $v){
		        	$bonus[$key]['json'] = json_decode($v['bonuses'],true);
		        	$bonus[$key]['bonus'] = bcdiv(bcmul($bonus[$key]['json'][0]['Amount'],$parities,2),100,2);
		        }

		        // 用户通用券
		        $map = array(
					'customerid' => $customerid,
				);
				$map    = json_encode($map);
				$sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/getUserCoupon";
				// $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/getUserCoupon";
				$result  = post_json_data($sendUrl,$map);
				$back_result = json_decode($result['result'],true);

				// 上线信息
				$enroller = M('User')->where(array('CustomerID'=>$value['enrollerid']))->select();
				// 查询账号状态
				foreach($enroller as $keys => $values){
					$validateHpl = $usa->validateHpl($values['customerid']);
					$enroller[$keys]['activities'] = $validateHpl['isActive'];
					$getCustomer = $usa->getCustomer($values['customerid']);
					$enroller[$keys]['binaryplacementpreference'] = $getCustomer['binaryPlacementPreference'];
				}

				// 下线信息
				$lower = M('User')->where(array('EnrollerID'=>$customerid,'DistributorType'=>array('NOT IN','Pc')))->select();
				foreach($lower as $key => $value){
					$lower[$key]['receiptlist'] = M('Receipt')->where(array('rCustomerID'=>$value['customerid']))->select();
				}


			}

	        // p($code);
	    	$map=array(
	            'data'=>$data,
	            'code'=>$code,
	            'receipt'=>$receipt,
	            'bonus'=>$bonus,
	            'coupon'=>$back_result,
	            'enroller'=>$enroller,
	            'lower'=>$lower,
	        );
	        
	        return $map;
    	}
    }

    /**
	* 获取当前用户
    **/ 
    public function getAllFixData($model,$map,$order='',$limit=20,$field=''){
		$count=$model
				->where(array('DistributorType'=>array('NOT IN','Pc'),'isexit'=>1,'LastName'=>array('NOT IN',$map),'FirstName'=>array('NOT IN',$map),'wvCustomerID'=>array('NEQ','')))
			  	->count();
		$page=new_page($count,$limit);
		

		$data=$model
			->where(array('DistributorType'=>array('NOT IN','Pc'),'isexit'=>1,'LastName'=>array('NOT IN',$map),'FirstName'=>array('NOT IN',$map),'wvCustomerID'=>array('NEQ','')))
			->field('customerid,iu_point,iu_dt,lastname,firstname,joinedon')
			->order($order)
			->limit($page->firstRow.','.$page->listRows)
			->select();

		$usa    = new \Common\UsaApi\Usa;
		foreach ($data as $key => $value) {
			$result = $usa->dtPoint($value['customerid']);
            $activities = $usa->validateHpl($value['customerid']);
            if(!$result['errors']){
                foreach($result['softCashCategories'] as $k=>$v){
                    switch ($v['categoryType']) {
                        case 'DreamTripPoints':
                            $data[$key]['iu_dt'] = $v['balance'];
                            break;
                    }
                }
            }else{
               $data[$key]['iu_dt'] = 0;
            }
            $data[$key]['status'] = $activities['isActive'];
		}

		$data=array(
			'data'=>$data,
			'page'=>$page->show()
		);
		return $data;
	}

	/**
	* 获取当前用户
    **/ 
    public function getAllFixDatas($model,$map,$p,$order='',$limit=20,$field=''){
		$data=$model
			->where(array('DistributorType'=>array('NOT IN','Pc'),'isexit'=>1,'LastName'=>array('NOT IN',$map),'FirstName'=>array('NOT IN',$map),'wvCustomerID'=>array('NEQ','')))
			->field('customerid,iu_point,iu_dt,lastname,firstname,joinedon')
			->limit(($p-1)*$limit,$limit)
			->order($order)
			->select();

		$usa    = new \Common\UsaApi\Usa;
		foreach ($data as $key => $value) {
			$result = $usa->dtPoint($value['customerid']);
            $activities = $usa->validateHpl($value['customerid']);
            if(!$result['errors']){
                foreach($result['softCashCategories'] as $k=>$v){
                    switch ($v['categoryType']) {
                        case 'DreamTripPoints':
                            $data[$key]['iu_dt'] = $v['balance'];
                            break;
                    }
                }
            }else{
               $data[$key]['iu_dt'] = 0;
            }
            $data[$key]['status'] = $activities['isActive'];
		}

		return $data;
	}
}