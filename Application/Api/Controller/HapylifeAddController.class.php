<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 添加用户积分
**/
class HapylifeAddController extends HomeBaseController{
    /**
    * 添加多个用户积分
    {
    "sendpoint":[
            {"customerid":"HPL00003","point":"10000"},
            {"customerid":"HPL00003","point":"10000"},
            {"customerid":"HPL00003","point":"10000"},
            {"customerid":"HPL00003","point":"10000"}
        ]
    }
    **/ 
    public function addPoint(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        foreach($data as $key=>$value){
            foreach($value as $k=>$v){
                $CustomerID        = $v['customerid'];
                // 获取用户信息
                $userinfo          = M('User')->where(array('CustomerID'=>$CustomerID))->find ();
                // 增加EP
                $iu_point          = $v['point'];
                $last_point        = bcadd($userinfo['iu_point'],$iu_point,4);
                $point['iu_point'] = $last_point;
                $result            = M('User')->where(array('CustomerID'=>$CustomerID))->save($point);
                if($result){
                    //生成唯一订单号
                    $order_num = date('YmdHis').rand(10000, 99999);
                    // 日志内容
                    $content   = '系统在'.date('Y-m-d H:i:s').'时,转入EP到'.$userinfo['customerid'].',剩EP余额'.$last_point;
                    $arr = array(
                        'pointNo'         => $order_num,
                        'iuid'            => $userinfo['iuid'],
                        'hu_username'     => $userinfo['lastname'].$userinfo['firstname'],
                        'hu_nickname'     => $userinfo['customerid'],
                        'send'            => '系统转入',
                        'received'        => $userinfo['customerid'],
                        'opename'         => '',
                        'getpoint'        => $iu_point,
                        'pointtype'       => 5,
                        'feepoint'        => 0,
                        'realpoint'       => $iu_point,
                        'unpoint'         => 0,
                        'leftpoint'       => $userinfo['iu_point']+$iu_point,
                        'iu_bank'         => $userinfo['bankname'],
                        'iu_bankbranch'   => $userinfo['subname'],
                        'iu_bankaccount'  => $userinfo['bankaccount'],
                        'iu_bankuser'     => $userinfo['lastname'].$userinfo['firstname'],
                        'iu_bankprovince' => $userinfo['bankprovince'],
                        'iu_bankcity'     => $userinfo['bankcity'],
                        'date'            => date('Y-m-d H:i:s'),
                        'handletime'      => date('Y-m-d H:i:s'),
                        'content'         => $content,
                        'status'          => 2,
                        'whichApp'        => 5,
                    );
                    $res = M('Getpoint')->add($arr);
                }
            }
        }
        if($res){
            $sample = array(
                    'msg' => 'Add Success',
                    'status' => 1,
                );
            $this->ajaxreturn($sample);
        }else{
            $sample = array(
                    'msg' => '无效会员账号',
                    'status' => 0,
                );
            $this->ajaxreturn($sample);
        }
        
    }

    /**
    * 添加单个用户积
    **/ 
    public function addPoints(){
        $jsonStr           = file_get_contents("php://input");
        //写入服务器日志文件
        $log               = addUsaLog($jsonStr);
        $data              = json_decode($jsonStr,true);
        $CustomerID        = $data['customerid'];
        // 获取用户信息
        $userinfo          = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        // 增加EP
        $iu_point          = $data['point'];
        // 用户剩余EP
        $last_point        = bcadd($userinfo['iu_point'],$iu_point,4);
        $point['iu_point'] = $last_point;
        $result            = M('User')->where(array('CustomerID'=>$CustomerID))->save($point);
        if($result){
            //生成唯一订单号
            $order_num = date('YmdHis').rand(10000, 99999);
            // 日志内容
            $content = '系统在'.date('Y-m-d H:i:s').'时,转入EP到'.$userinfo['customerid'].',剩EP余额'.$last_point;
            $log = array(
                        'pointNo'         => $order_num,
                        'iuid'            => $userinfo['iuid'],
                        'hu_username'     => $userinfo['lastname'].$userinfo['firstname'],
                        'hu_nickname'     => $userinfo['customerid'],
                        'send'            => '系统转入',
                        'received'        => $userinfo['customerid'],
                        'opename'         => '',
                        'getpoint'        => $iu_point,
                        'pointtype'       => 5,
                        'feepoint'        => 0,
                        'realpoint'       => $iu_point,
                        'unpoint'         => 0,
                        'leftpoint'       => $userinfo['iu_point']+$iu_point,
                        'iu_bank'         => $userinfo['bankname'],
                        'iu_bankbranch'   => $userinfo['subname'],
                        'iu_bankaccount'  => $userinfo['bankaccount'],
                        'iu_bankuser'     => $userinfo['lastname'].$userinfo['firstname'],
                        'iu_bankprovince' => $userinfo['bankprovince'],
                        'iu_bankcity'     => $userinfo['bankcity'],
                        'date'            => date('Y-m-d H:i:s'),
                        'handletime'      => date('Y-m-d H:i:s'),
                        'content'         => $content,
                        'status'          => 2,
                        'whichApp'        => 5,
                    );
            $res = M('Getpoint')->add($log);
            if($res){
                $sample = array(
                        'code'=> 200,
                        'info'=>'success',
                        'data'=>array(
                            'customerid'=>$data['customerid'],
                            'point'     =>$data['point'],
                            'userPoint' =>$last_point
                        )
                    );
                $this->ajaxreturn($sample);
            }
        }else{
            $sample = array(
                        'code'=> 1000,
                        'info'=>'failure',
                        'data'=>array(
                            'message'=>'the ID does not exist'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }

    /**
     * [wvBonus description]
     * @param  [type] $customerid [description]
     * @param  [type] $serial     [description]
     * @param  [type] $amount     [description]
     * @return [type]             [description]
     * 
        [
            {"customerid":"HPL00003","serial":"2018-8-17","amount":"10000"},
            {"customerid":"HPL00003","serial":"2018-8-17","amount":"10000"},
            {"customerid":"HPL00003","serial":"2018-8-17","amount":"10000"},
            {"customerid":"HPL00003","serial":"2018-8-17","amount":"10000"}
        ]
     */
    public function wvBonus(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        //开启事务
        M('wvBonus')->startTrans();
        $catch_result = true;
        try {
            //异常处理
            foreach($data as $key=>$value){
                //添加bonus记录
                $res = M('wvBonus')->add($value);
                if(!$res){
                    E("错误信息");
                }
            }
        } catch (\Exception $e) {
            $catch_result = false;
            // dump($e);
        }

        if($catch_result === false){
            //事务回滚
            M('wvBonus')->rollback();
            //添加失败
            $sample = array(
                        'code'=> 400,
                        'info'=>'false',
                        'data'=>array(
                            'message'=>'Add bonus failure'
                        )
                );
            $this->ajaxreturn($sample);
        }else{
            //事务提交
            M('wvBonus')->commit();
            //添加成功
            $sample = array(
                        'code'=> 200,
                        'info'=>'true',
                        'data'=>array(
                            'message'=>'Add bonus Success'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }

    /**
     * [wvNotificication description]
     * @param  [type] $customerid [description]
     * @param  [type] $date       [description]
     * @param  [type] $content    [description]
     * @return [type]             [description]
     * [
        {"customerid":"HPL00001","date":"2018-8-17","content":"请今天购买月费"},
        {"customerid":"HPL00002","date":"2018-8-17","content":"请今天购买月费"},
        {"customerid":"HPL00003","date":"2018-8-17","content":"请今天购买月费"},
        {"customerid":"HPL00004","date":"2018-8-17","content":"请今天购买月费"}
        ]
     */
    public function wvNotification(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        //开启事务
        M('wvBonus')->startTrans();
        $catch_result = true;
        try {
            //异常处理
            foreach($data as $key=>$value){
                //添加bonus记录
                $res = M('wvNotification')->add($value);
                if(!$res){
                    E("错误信息");
                }
            }
        } catch (\Exception $e) {
            $catch_result = false;
            // dump($e);
        }

        if($catch_result === false){
            //事务回滚
            M('wvBonus')->rollback();
            //添加失败
            $sample = array(
                        'code'=> 400,
                        'info'=>'false',
                        'data'=>array(
                            'message'=>'Add notification failure'
                        )
                );
            $this->ajaxreturn($sample);
        }else{
            //事务提交
            M('wvBonus')->commit();
            //添加成功
            $sample = array(
                        'code'=> 200,
                        'info'=>'true',
                        'data'=>array(
                            'message'=>'Add notification Success'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }
























}