<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{
	public function register(){
		$data = I('post.');
		$upload = post_upload();

		if(IS_POST){

			print_r($data);
			if($data['Sex']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				    var_dump($error);
				}else{
				     // 验证通过 可以进行其他数据操作
					echo '成功';
				}
			}else{
				$error['Sex'] = '请选择性别';
			}
			if($data['DistributorType']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				    var_dump($error);
				}else{
				     // 验证通过 可以进行其他数据操作
					echo '成功';
				}
			}else{
				$error['DistributorType'] = '请选择销售类型';
			}
			if($data['clause']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				    var_dump($error);
				}else{
				     // 验证通过 可以进行其他数据操作
					echo '成功';
				}
			}else{
				$error['clause'] = '未选择同意条款';
			}
			
		}
		









		$assign = array(
            		'error' => $error,
            		'data' => $data
            		);

        $this->assign($assign);
		$this->display();
	}
}



 ?>