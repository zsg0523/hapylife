<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 银行信息
**/
class HapylifeBankController extends HomeBaseController{

	/**
    * 添加用户银行账号信息
    **/
    public function addBank(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户银行账号信息
            $iuid        = I('post.iuid');
            // 查询注册信息
            $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 


            $bankaccount = I('post.bankaccount');
            $bankprovince = I('post.bankprovince');
            $banktown = I('post.banktown');
            $bankregion = I('post.bankregion');
            $bankname    = I('post.bankname');
            $bankbranch  = I('post.bankbranch');
            $iu_name = $userinfo['lastname'].$userinfo['firstname'];

            if(empty($iuid)||$iuid==0){
                $data['status'] = 0;
                $data['message']='银行信息添加失败';
                $this->ajaxreturn($data);
            }else{
                $arr  = D('Bank')
                      ->where(array('iuid'=>$iuid))
                      ->select();
                //判断是否存在银行账户。有则is_show为0，没有则为1
                if($arr){
                    $tmp        = array(
                        'iuid'        =>$iuid,
                        'iu_name'      => $iu_name,
                        'bankaccount'  => $bankaccount,
                        'bankprovince' => $bankprovince,
                        'banktown'     => $banktown,
                        'bankregion'   => $bankregion,
                        'bankname'     => $bankname,
                        'bankbranch'   => $bankbranch,
                        'createtime'  =>time(),
                        'isshow'      =>0
                    );
                }else{
                    $tmp        = array(
                        'iuid'        =>$iuid,
                        'iu_name'      => $iu_name,
                        'bankaccount'  => $bankaccount,
                        'bankprovince' => $bankprovince,
                        'banktown'     => $banktown,
                        'bankregion'   => $bankregion,
                        'bankname'     => $bankname,
                        'bankbranch'   => $bankbranch,
                        'createtime'  =>time(),
                        'isshow'      =>1
                    );
                }
                //添加
                $addBank = D('Bank')->add($tmp);
                if($addBank){
                    $data['status'] = 1;
                    $data['message']='银行信息添加成功';
                    $this->ajaxreturn($data);
                }
                else{
                    $data['status'] = 0;
                    $data['message']='银行信息添加失败';
                    $this->ajaxreturn($data);
                }
            }
        }
    }
    
    /**
    * 银行信息列表
    **/
    public function bankList(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户iuid
            $iuid   = I('post.iuid');
            // 查询注册信息
            $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 
            // 查询银行表信息
            $bankaccount = M('Bank')->where(array('iuid'=>$iuid))->getField('bankaccount',true); 
            
            if(!in_array($userinfo['bankaccount'], $bankaccount) && $userinfo['is_login'] == 0 && !empty($userinfo['bankaccount'])){
               $message = array(
                        'iuid'         => $userinfo['iuid'],
                        'iu_name'      => $userinfo['lastname'].$userinfo['firstname'],
                        'bankaccount'  => $userinfo['bankaccount'],
                        'bankprovince' => $userinfo['bankprovince'],
                        'banktown'     => $userinfo['bankcity'],
                        'bankregion'   => $userinfo['bankarea'],
                        'bankname'     => $userinfo['bankname'],
                        'bankbranch'   => $userinfo['subname'],
                        'createtime'   => time(),
                        'isshow'       => 1,
                    );
                $result = M('Bank')->add($message);
                if($result){
                    $arr['is_login'] = 1;
                    $res = M('User')->where(array('iuid'=>$iuid))->save($arr);
                }
            }
            //列表信息
            $data   = D('Bank')
                    ->where(array('iuid'=>$iuid))
                    ->order('isshow DESC')
                    ->select(); 
            if($data){
                $this->ajaxreturn($data);
            }else{
            	$data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 显示默认银行信息
    **/
    public function defaultBank(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid        = I('post.iuid');
            $tmp         = array(
                'iuid'   =>$iuid,
                'isshow' =>1
             );
            $data   = D('Bank')->where($tmp)->find(); 
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 修改默认银行
    **/
    public function setDefaultBank(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid = I('post.iuid');
            $bid  = I('post.bid');
            //0变为1
            $tmp  = D('Bank')->where(array('bid'=>$bid))->setField('isshow',1);
            if($tmp){
                //1变为0
                $map    = array(
                    'bid'=>array('NEQ',$bid),
                    'iuid'=>$iuid
                );
                $arr    = D('Bank')->where($map)->setField('isshow',0);
            }
            if($tmp){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 获取银行详细信息
    **/
    public function bankInfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $bid    = I('post.bid');
            $data   = D('Bank')->where(array('bid'=>$bid))->find(); 
            if($data){
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    * 编辑银行信息
    **/
    public function editBank(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $tmp = I('post.');
            //修改
            $save = D('Bank')->save($tmp); 
            if($save){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 删除银行信息
    **/
    public function deleteBank(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //地址id
            $bid = I('post.bid');
            //删除
            $data = D('Bank')->where(array('bid'=>$bid))->delete(); 
            if($data){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
}