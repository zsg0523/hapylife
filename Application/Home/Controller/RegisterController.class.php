<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{
	public function register(){
		$data = I('post.');
		print_r($data);

		// $rules = array(
		//      array('verify','require','验证码必须！'), //默认情况下用正则进行验证
		//      array('CustomerID','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		//      array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
		//      array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
		//      array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
		// );
		// $User = M("User1"); // 实例化User对象
		// if (!$User->validate($rules)->create()){
		//      // 如果创建失败 表示验证没有通过 输出错误提示信息
		//      // exit($User->getError());
		// 	$error = $User->getError();

		// }else{
		//      // 验证通过 可以进行其他数据操作
		// }
		if(IS_POST){
			$User = D("User1"); // 实例化User对象
			if (!$User->create($data)){
			     // 如果创建失败 表示验证没有通过 输出错误提示信息
			    $error = $User->getError();
			    var_dump($error);
			}else{
			     // 验证通过 可以进行其他数据操作
				echo '成功';
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