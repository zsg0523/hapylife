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
                    if(substr($data['customerid'],0,3) == 'HPL'){
                        $_SESSION['user']=array(
                                            'id'       =>$data['iuid'],
                                            'username' =>$data['customerid'],
                                            'name_cn'  =>$data['lastname'].$data['firstname'],
                                            'status'   =>1,
                                            'address'  =>0,
                                            'bank'     =>0,
                                        );
                    }else{
                        $_SESSION['user']=array(
                                'id'       =>$data['iuid'],
                                'username' =>$data['customerid'],
                                'name_cn'  =>$data['lastname'].$data['firstname'],
                            );
                    }
                   
                    $this->redirect('Home/Purchase/purchase');
                }
            }
        }else{
            $data=check_login();
            if($data){
                $this->redirect('Home/Purchase/purchase');
            }else{
                $this->display('Login/login');
            }
        }
    }
    
    /**
    * 前台登录
    **/
    public function login(){
        $iuid = I('get.iuid');
        if(!empty($iuid)){
            $data = D('User')->where(array('iuid'=>$iuid))->find();
            if(substr($data['customerid'],0,3) == 'HPL'){
                $_SESSION['user']=array(
                                    'id'       =>$data['iuid'],
                                    'username' =>$data['customerid'],
                                    'name_cn'  =>$data['lastname'].$data['firstname'],
                                    'status'   =>1,
                                    'address'  =>0,
                                    'bank'     =>0,
                                );
            }else{
                $_SESSION['user']=array(
                        'id'       =>$data['iuid'],
                        'username' =>$data['customerid'],
                        'name_cn'  =>$data['lastname'].$data['firstname'],
                        'status'   =>2,
                    );
            }
            $this->redirect('Home/Purchase/purchase');
        }else{
            if(IS_POST){
                $tmpe = I('post.');
                if(strlen($tmpe['CustomerID'])==8){
                    //检查WV api用户信息
                    $key      = "Z131MZ8ZV29H5EQ9LGVH";
                    $url      = "https://signupapi.wvhservices.com/api/Account/ValidateHpl?customerId=".$tmpe['CustomerID']."&"."key=".$key;
                    $wv       = file_get_contents($url);
                    $userinfo = json_decode($wv,true);
                    //检查wv是否存在该账号 Y创建该账号  N登录失败
                    switch ($userinfo['isActive']) {
                        case 'true':
                        //检查系统是否存在该账号 Y无密码登录 N创建账号
                        $checkAccount = D('User')->where(array('CustomerID'=>trim($tmpe['CustomerID'])))->find();
                            switch ($checkAccount) {
                                case null:
                                    //创建该新账号在本系统
                                    $map      = array(
                                            'CustomerID'  =>$tmpe['CustomerID'],
                                            'PassWord'    =>md5($userinfo['password']),
                                            'LastName'    =>$userinfo['lastName'],
                                            'FirstName'   =>$userinfo['firstName'],
                                            'isActive'    =>$userinfo['isActive']
                                        );
                                    $createUser = D('User')->add($map);
                                    break;
                                default:
                                    //更新相关信息在本系统
                                    $map      = array(
                                            'PassWord'    =>md5($userinfo['password']),
                                            'LastName'    =>$userinfo['lastName'],
                                            'FirstName'   =>$userinfo['firstName'],
                                            'isActive'    =>$userinfo['isActive']
                                        );
                                    $createUser = D('User')->where(array('CustomerID'=>trim($tmpe['CustomerID'])))->save($map);
                                    break;
                            }
                            $data = D('User')->where(array('CustomerID'=>trim($tmpe['CustomerID'])))->find();
                            //登录后看不到产品
                            $_SESSION['user']=array(
                                    'id'       =>$data['iuid'],
                                    'username' =>$data['customerid'],
                                    'name_cn'  =>$data['lastname'].$data['firstname'],
                                    'status'   =>2,
                                );
                            // p($_SESSION);die;
                            $this->redirect('Home/Purchase/purchase');
                            break;

                        default:
                            $this->error('账号格式错误');
                            break;
                    }
                }else{
                    $where = array(
                        'CustomerID'=>trim($tmpe['CustomerID']),
                        'PassWord'  =>md5($tmpe['PassWord'])
                    );
                    $data = D('User')->where($where)->find();
                    if (empty($data)) {
                        $this->error('账号或密码错误');
                    }else{
                        if(substr($data['customerid'],0,3) == 'HPL'){
                            $_SESSION['user']=array(
                                                'id'       =>$data['iuid'],
                                                'username' =>$data['customerid'],
                                                'name_cn'  =>$data['lastname'].$data['firstname'],
                                                'status'   =>1,
                                                'address'  =>0,
                                                'bank'     =>0,
                                                'password' =>$tmpe['PassWord'],
                                            );
                        }else{
                            $_SESSION['user']=array(
                                    'id'       =>$data['iuid'],
                                    'username' =>$data['customerid'],
                                    'name_cn'  =>$data['lastname'].$data['firstname'],
                                    'status'   =>2,
                                );
                        }
                        $this->redirect('Home/Purchase/purchase');
                    }
                }
            }else{
                $data=check_login();
                if($data){
                    $this->redirect('Home/Purchase/purchase');
                }else{
                    $this->display('Login/login');
                }
            }
        }
    }

    /**
     * 后台退出
     */
    public function logout(){
        session('user',null);
        $this->success('退出成功、前往登录页面',U('Home/Index/admin'));
    }


    /**
    * 前台退出登录
    **/
    public function log_out(){
        session('user',null);
        $this->redirect('Home/Index/login');
    }



}

