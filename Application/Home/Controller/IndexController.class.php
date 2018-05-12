<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * 商城首页Controller
 */
class IndexController extends HomeBaseController{
	/**
	 * 后台登录首页
	 */
	public function admin(){
        if(IS_POST){
            // 做一个简单的登录 组合where数组条件 
            $map=I('post.');
            $map['password']=md5($map['password']);
            $data=M('admin')->where($map)->find();
            if (empty($data)) {
                $this->error('账号或密码错误');
            }else{
                $_SESSION['user']=array(
                    'id'=>$data['id'],
                    'username'=>$data['username'],
                    'avatar'=>$data['avatar']
                    );
                $this->success('登录成功、前往管理后台',U('Admin/Index/index'));
            }
        }else{
            $data=check_login() ? $_SESSION['user']['username'].'已登录' : '未登录';
            $assign=array(
                'data'=>$data
                );
            $this->assign($assign);
            $this->display();
        }
	}

    /**
    * 前台登录
    **/
    public function index(){
        if(IS_POST){
            $tmpe = I('post.');
            if(strlen($tmpe['CustomerID'])==8){
            $this->error('账号格式错误');  
            }else{
                $where= array(
                    'CustomerID'=>trim($tmpe['CustomerID']),
                    'PassWord'  =>md5($tmpe['PassWord'])
                );
                $data = D('User')->where($where)->find();
                if (empty($data)) {
                    $this->error('账号或密码错误');
                }else{
                    $_SESSION['user']=array(
                        'id'       =>$data['iuid'],
                        'username' =>$data['customerid'],
                        );

                    $this->success('登录成功',U('Home/Purchase/main'));
                }
            }
        }else{
            $data=check_login();
            if($data){
                $this->success('登录成功',U('Home/Purchase/main'));
            }else{
                $this->display('Login/login');
            }
        }
    }

    /**
     * 后台退出
     */
    public function logout(){
        session('user',null);
        $this->success('退出成功、前往登录页面',U('Home/Index/index'));
    }

    /**
    * 注册
    **/
    public function register(){
        if(IS_POST){
            $tmpe = I('post.');
            if(strlen($tmpe['CustomerID'])==8){
            $this->error('账号格式错误');  
            }else{
                $where= array(
                    'CustomerID'=>$tmpe['CustomerID'],
                    'PassWord'  =>md5($tmpe['PassWord'])
                );
                $data = D('User')->where($where)->find();
                if (empty($data)) {
                    $this->error('账号或密码错误');
                }else{
                    $_SESSION['user']=array(
                        'id'       =>$data['iuid'],
                        'username' =>$data['customerid'],
                        );
                    $this->success('登录成功',U('Home/Purchase/main'));
                }
            }
        }else{
            $this->display('Register/register');
        }
    }


    /**
    * 前台退出登录
    **/
    public function log_out(){
        session('user',null);
        $this->success('退出成功,前往登录页面',U('Home/Index/index'));
    }



}

