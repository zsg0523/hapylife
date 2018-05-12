<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{
	// 用户注册
	public function register(){
		$data = I('post.');

		$upload = several_upload();
		if(IS_POST){
			if($data['Sex']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if(isset($upload['name'])){
						$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
						$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						if($User->create($data)){
							$data['PassWord'] = md5($data['PassWord']);
							$keyword= 'HPL';
	                		$custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
							if(empty($custid)){
		                    	$CustomerID = 'HPL00000001';
			                }else{
			                    $num   = substr($custid,3);
			                    $nums  = $num+1;
			                    $count = strlen($nums);
			                    switch ($count) {
			                        case '1':
			                            $CustomerID = 'HPL0000000'.$nums;
			                            break;
			                        case '2':
			                            $CustomerID = 'HPL000000'.$nums;
			                            break;
			                        case '3':
			                            $CustomerID = 'HPL00000'.$nums;
			                            break;
			                        case '4':
			                            $CustomerID = 'HPL0000'.$nums;
			                            break;
			                        case '5':
			                            $CustomerID = 'HPL000'.$nums;
			                            break;
			                        case '6':
			                            $CustomerID = 'HPL00'.$nums;
			                            break;
			                        case '7':
			                            $CustomerID = 'HPL0'.$nums;
			                            break;
			                        default:
			                            $CustomerID = 'HPL'.$nums;
			                            break;
			                     } 
			                }
			                $data['CustomerID'] = $CustomerID;
							$result	= D('User')->add($data);
							if($result){
								$this->redirect('Home/Register/regsuccess');
							}
						}
					}else{
						if($User->create($data)){
							$data['PassWord'] = md5($data['PassWord']);
							$data['EnrollerID'] = strtoupper($data['EnrollerID']);
							$keyword= 'HPL';
	                		$custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
							if(empty($custid)){
		                    	$CustomerID = 'HPL00000001';
			                }else{
			                    $num   = substr($custid,3);
			                    $nums  = $num+1;
			                    $count = strlen($nums);
			                    switch ($count) {
			                        case '1':
			                            $CustomerID = 'HPL0000000'.$nums;
			                            break;
			                        case '2':
			                            $CustomerID = 'HPL000000'.$nums;
			                            break;
			                        case '3':
			                            $CustomerID = 'HPL00000'.$nums;
			                            break;
			                        case '4':
			                            $CustomerID = 'HPL0000'.$nums;
			                            break;
			                        case '5':
			                            $CustomerID = 'HPL000'.$nums;
			                            break;
			                        case '6':
			                            $CustomerID = 'HPL00'.$nums;
			                            break;
			                        case '7':
			                            $CustomerID = 'HPL0'.$nums;
			                            break;
			                        default:
			                            $CustomerID = 'HPL'.$nums;
			                            break;
			                     } 
			                }
			                $data['CustomerID'] = $CustomerID;
							$result	= D('User')->add($data);
							if($result){
								$this->redirect('Home/Register/regsuccess');
							}
						}
					}
				}
			}else{
				$error['Sex'] = '请选择性别';
			}

			if($data['AccountType']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if(isset($upload['name'])){
						$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
						$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						if($User->create($data)){
							$data['PassWord'] = md5($data['PassWord']);
							$keyword= 'HPL';
	                		$custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
							if(empty($custid)){
		                    	$CustomerID = 'HPL00000001';
			                }else{
			                    $num   = substr($custid,3);
			                    $nums  = $num+1;
			                    $count = strlen($nums);
			                    switch ($count) {
			                        case '1':
			                            $CustomerID = 'HPL0000000'.$nums;
			                            break;
			                        case '2':
			                            $CustomerID = 'HPL000000'.$nums;
			                            break;
			                        case '3':
			                            $CustomerID = 'HPL00000'.$nums;
			                            break;
			                        case '4':
			                            $CustomerID = 'HPL0000'.$nums;
			                            break;
			                        case '5':
			                            $CustomerID = 'HPL000'.$nums;
			                            break;
			                        case '6':
			                            $CustomerID = 'HPL00'.$nums;
			                            break;
			                        case '7':
			                            $CustomerID = 'HPL0'.$nums;
			                            break;
			                        default:
			                            $CustomerID = 'HPL'.$nums;
			                            break;
			                     } 
			                }
			                $data['CustomerID'] = $CustomerID;
							$result	= D('User')->add($data);
							if($result){
								$this->redirect('Home/Register/regsuccess');
							}
						}
					}else{
						if($User->create($data)){
							$data['PassWord'] = md5($data['PassWord']);
							$data['EnrollerID'] = strtoupper($data['EnrollerID']);
							$keyword= 'HPL';
	                		$custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
							if(empty($custid)){
		                    	$CustomerID = 'HPL00000001';
			                }else{
			                    $num   = substr($custid,3);
			                    $nums  = $num+1;
			                    $count = strlen($nums);
			                    switch ($count) {
			                        case '1':
			                            $CustomerID = 'HPL0000000'.$nums;
			                            break;
			                        case '2':
			                            $CustomerID = 'HPL000000'.$nums;
			                            break;
			                        case '3':
			                            $CustomerID = 'HPL00000'.$nums;
			                            break;
			                        case '4':
			                            $CustomerID = 'HPL0000'.$nums;
			                            break;
			                        case '5':
			                            $CustomerID = 'HPL000'.$nums;
			                            break;
			                        case '6':
			                            $CustomerID = 'HPL00'.$nums;
			                            break;
			                        case '7':
			                            $CustomerID = 'HPL0'.$nums;
			                            break;
			                        default:
			                            $CustomerID = 'HPL'.$nums;
			                            break;
			                     } 
			                }
			                $data['CustomerID'] = $CustomerID;
							$result	= D('User')->add($data);
							if($result){
								$this->redirect('Home/Register/regsuccess');
							}
						}
					}
				}
			}else{
				$error['AccountType'] = '请选择销售类型';
			}
		}

		$assign = array(
            		'error' => $error,
            		'data' => $data
            		);
        $this->assign($assign);
		$this->display();
	}

	// 注册成功显示页面
	public function regsuccess(){
		$data = max(D('User')->select());
		$data = D('User')->where(array('iuid'=>$data['iuid']))->find();
		
		$assign = array(
						'data' => $data,
						);
        $this->assign($assign);
		$this->display();
	}





}



 ?>