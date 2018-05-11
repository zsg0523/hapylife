<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{
	public function register(){
		$data = I('post.');
		
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