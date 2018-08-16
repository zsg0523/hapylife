<?php
/*
商場註冊控制器
Market Register Controller
Edit by philliptsai
*/

namespace FrontPage\Controller;
use Think\Controller;

class RegisterController extends Controller{
	public function register(){
		$this->display();		//display register.html
	}      

	public function action(){
		$User = M('User');		//connect to DB shop TABLE User
		if(IS_POST){		//thinkphp version 3.2
			$data['account'] = I('post.account','ACCOUNT_ERROR');
			$data['password'] = I('post.password','PASSWORD_ERROR');
			
			if($data['account'] == 'ACCOUNT_ERROR') $this->error($data['account']);
			else if($data['password'] == 'PASSWORD_ERROR') $this->error($data['password']);
			else{
				$data['password'] = md5( $data['password']  );		//trans password to md5 on server side
				$User->add($newUser);		//insert data
			}
		}else{		//not using post method
			$this->error('METHOD_ERROR');
		}
		
		
		/*=======FOR TEST=======
		$newUser = array(
			'account' => 'philliptsai',
			'password' => md5('nyanpass'),
			'name' => 'philliptsai',
			'sex' => 'male',
			'phone' => '123456789',
			'email' => 'philliptsai@gmail.com'
		);
		*/
	}
}
?>