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
     * [checkAccount 验证新旧用户,显示不同界面]
     * @param  [type] $CustomerID [会员id]
     * @return [type]             [description]
     * testAccount 71429994
     */
    public function checkAccount(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            $this->redirect('Home/Index/end');
        }
        $CustomerID = trim(I('post.CustomerID'));
        $checkAccount = D('User')->where(array('CustomerID|wvCustomerID'=>$CustomerID))->find();
        if($checkAccount){
            $data = array(
                'status'=>1,
                'message'=>'账号已存在，直接登录'
            );
            $this->ajaxreturn($data);   
        }else{
            //核对usa 账号是否正确存在
            $usa    = new \Common\UsaApi\Usa;
            $result = $usa->validateHpl($CustomerID);
            switch ($result['isActive']) {
                case true:
                    $data = array(
                        'status'=>2,
                        'message'=>'正确旧用户，旧用户界面登录'
                    );
                    $this->ajaxreturn($data);
                    break;
                default:
                    $data = array(
                        'status'=>0,
                        'message'=>'输入账号有误'
                    );
                    $this->ajaxreturn($data);
                    break;
            }
        }
    }

    public function index(){
        $this->redirect('Home/Index/login');
    }

    
    /**
    * 前台登录
    **/
    public function login(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            $this->redirect('Home/Index/end');
        }
        $iuid = I('get.iuid');
        if(!empty($iuid)){
            $data = D('User')->where(array('iuid'=>$iuid))->find();
            if(substr($data['customerid'],0,3) == 'HPL'){
                $_SESSION['user']=array(
                                    'id'       =>$data['iuid'],
                                    'username' =>$data['customerid'],
                                    'name_cn'  =>$data['lastname'].$data['firstname'],
                                    'status'   =>1,
                                );
            }else{
                $_SESSION['user']=array(
                        'id'       =>$data['iuid'],
                        'username' =>$data['customerid'],
                        'name_cn'  =>$data['lastname'].$data['firstname'],
                        'status'   =>2,
                    );
            }
            $this->redirect('Home/Purchase/center');
        }else{
            if(IS_POST){
                $tmpe = I('post.');
                $data = D('User')->where(array('CustomerID|wvCustomerID'=>$tmpe['CustomerID'],'isexit'=>1))->find();
                if($data){
                    if($data['password']==md5($tmpe['PassWord'])){
                        $_SESSION['user']=array(
                            'id'       =>$data['iuid'],
                            'username' =>$data['customerid'],
                            'name_cn'  =>$data['lastname'].$data['firstname'],
                            'status'   =>1,
                        );
                        $this->redirect('Home/Purchase/center');
                    }else{
                        $this->error('账号或密码错误');
                    }
                }else{
                    //检查WV api用户信息
                    $usa      = new \Common\UsaApi\Usa;
                    $userinfo = $usa->validateHpl($tmpe['CustomerID']);
                    //检查wv是否存在该账号 Y创建该账号  N登录失败
                    switch ($userinfo['isActive']) {
                        case true:
                            //创建该新账号在本系统
                            $map = array(
                                'CustomerID'  =>$tmpe['CustomerID'],
                                'PassWord'    =>md5($tmpe['PassWord']),
                                'WvPass'      =>$tmpe['PassWord'],
                                'LastName'    =>$userinfo['lastName'],
                                'FirstName'   =>$userinfo['firstName'],
                                'isActive'    =>$userinfo['isActive'],
                            );
                            $createUser = D('User')->add($map);
                            if($createUser){
                                $_SESSION['user']=array(
                                    'id'       =>$createUser,
                                    'username' =>$tmpe['CustomerID'],
                                    'name_cn'  =>$userinfo['lastName'].$userinfo['firstName'],
                                    'status'   =>1,
                                );
                                $this->redirect('Home/Purchase/center');
                            }else{
                                $this->error('账号或密码错误');
                            }
                            break;
                        default:
                            $this->error('账号或密码错误');
                            break;
                    }
                }
            }else{
                $data=check_login();
                if($data){
                    $this->redirect('Home/Purchase/center');
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

