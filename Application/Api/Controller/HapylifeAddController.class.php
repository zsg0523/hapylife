<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 添加用户积分
**/
class HapylifeAddController extends HomeBaseController{
    /**
    * 添加用户积分
    **/ 
    public function addPoint(){
        $CustomerID = strtoupper(trim(I('post.customerid')));
        // 获取用户信息
        $userinfo = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        // 增加EP
        $iu_point = trim(I('post.point'));
        // 用户剩余EP
        $last_point    = bcadd($userinfo['iu_point'],$iu_point,4);
        $point['iu_point'] = $last_point;
        $result = M('User')->where(array('CustomerID'=>$CustomerID))->save($point);
        if($result){
            //生成唯一订单号
            $order_num = date('YmdHis').rand(10000, 99999);
            // 日志内容
            $content = '系统在'.date('Y-m-d H:i:s').'时,转入EP到'.$userinfo['customerid'].',剩EP余额'.$last_point;
            $arr = array(
                        'pointNo' => $order_num,
                        'iuid' => $userinfo['iuid'],
                        'hu_username' => $userinfo['lastname'].$userinfo['firstname'],
                        'hu_nickname' => $userinfo['customerid'],
                        'send' => '系统转入',
                        'received' => $userinfo['customerid'],
                        'opename' => '',
                        'getpoint' => $iu_point,
                        'pointtype' => 5,
                        'feepoint' => 0,
                        'realpoint' => $iu_point,
                        'unpoint' => 0,
                        'leftpoint' => $userinfo['iu_point']+$iu_point,
                        'iu_bank' => $userinfo['bankname'],
                        'iu_bankbranch' => $userinfo['subname'],
                        'iu_bankaccount' => $userinfo['bankaccount'],
                        'iu_bankuser' => $userinfo['lastname'].$userinfo['firstname'],
                        'iu_bankprovince' => $userinfo['bankprovince'],
                        'iu_bankcity' => $userinfo['bankcity'],
                        'date' => date('Y-m-d H:i:s'),
                        'handletime' => date('Y-m-d H:i:s'),
                        'content' => $content,
                        'status' => 2,
                        'whichApp' => 5,
                    );
            $res = M('Getpoint')->add($arr);
            if($res){
                $sample['msg'] = '添加成功';
                $sample['status'] = 1;
                $this->ajaxreturn($sample);
            }else{
                $sample['msg'] = '添加失败';
                $sample['status'] = 0;
                $this->ajaxreturn($sample);
            }
        }
    }
}