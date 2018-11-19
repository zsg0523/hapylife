<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 添加用户积分
**/
class HapylifePointController extends HomeBaseController{
    // ***********我的积分**************
    /**
    * 确认密码
    **/ 
    public function checkPassWord(){
        $iuid = I('post.iuid');
        $password = md5(trim(I('post.password')));
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        if($userinfo['password'] != $password){
            // 密码错误
            $data['status'] = 0;
            $data['msg'] = '密码错误';
            $this->ajaxreturn($data);
        }else{
            // 密码正确
            $data['status'] = 1;
            $data['msg'] = '密码正确';
            $this->ajaxreturn($data);
        }
    }

    /**
    * 查询银行账号
    **/ 
    public function checkBank(){
        $iuid = I('post.iuid');
        $data = M('Bank')->where(array('iuid'=>$iuid,'isshow'=>1))->find();
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 我的积分
    **/
    public function myPoint(){
        $iuid = I('post.iuid');
        $data = M('User')->where(array('iuid'=>$iuid))->find();
        $usa = new \Common\UsaApi\Usa;
        $result = $usa->dtPoint($data['customerid']);
        if(!$result['errors']){
            foreach($result['softCashCategories'] as $key=>$value){
                if($value['categoryType'] == 'DreamTripPoints'){
                    $data['iu_dt'] = $value['balance'];
                }else{
                    $data['iu_dt'] = 0;
                }
                if($value['categoryType'] == 'DreamTripPoints_Accrued'){
                    $data['iu_ac'] = $value['balance'];
                }else{
                    $data['iu_ac'] = 0;
                }
            }
            $data['endTime'] = date('l,F d,Y',$data['joinedon']+24*3600*365);
        }else{
            $data['iu_dt'] = 0;
            $data['iu_ac'] = 0;
            $data['endTime'] = '';
        }
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    // 转账记录
    public function transferList(){
        $iuid  = trim(I('post.iuid'));
        $page  = trim(I('post.page'));
        $action= trim(I('post.action'));
        if(!empty($action)){
            switch ($action) {
                case '0':
                    $map = array(
                        'iuid'  =>$iuid,
                        'type'  =>1,
                        'action'=>array('in','0')
                    ); 
                    break;
                case '1':
                    //积分的转入，转出
                    $map = array(
                        'iuid'  =>$iuid,
                        'type'  =>1,
                        'action'=>array('in','1,2')
                    ); 
                    break;
                case '2':
                    //包含积分的提取，转入，转出
                    $map = array(
                        'iuid'  =>$iuid,
                        'type'  =>1,
                        'action'=>array('in','2')
                    ); 
                    break;
                case '3':
                    $map = array(
                        'iuid'  =>$iuid,
                        'type'  =>1,
                        'action'=>array('in','3')
                    ); 
                    break;
                case '4':
                    $map = array(
                        'iuid'  =>$iuid,
                        'type'  =>1,
                        'action'=>array('in','4')
                    ); 
                    break;        
                case '5':
                    $map = array(
                        'iuid'=>$iuid,
                        'type'  =>1,
                        'action'=>array('in','0,1,2,3,4,5,6')
                    );
                    break;
                case '6':
                    $map = array(
                        'iuid'=>$iuid,
                        'type'  =>1,
                        'action'=>array('in','6')
                    ); 
                    break;
            }               
            $data['count']= M('Log')->where($map)->count();
            $data['log']  = M('Log')
                            ->field('lid,iuid,content,create_month,from_unixtime(create_time,"%Y-%m-%d %H:%i:%s") as create_time,action,type')
                            ->where($map)
                            ->order('create_time desc')
                            ->limit($page)
                            ->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
        
    }

    /**
    * 用户EP转账
    **/ 
    public function EPtransfer(){
        $iuid = I('post.iuid');
        //app服务器用户信息
        $data  = M('User')->where(array('iuid'=>$iuid))->find();
        // p($data);
        $log  = M('Log')->where(array('iuid'=>$iuid,'type'=>1,'action'=>array('in','1,2')))->order('create_time DESC')->limit(50)->select();
        // p($log);
        $assign = array(
                    'data' => $data,
                    'log' => $log,
                    );
        if(!empty($assign['data'])){
            $assign['status'] = 1;
            $this->ajaxreturn($assign);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 返回用户名称
    **/ 
    public function checkName(){
        $CustomerID = strtoupper(I('post.CustomerID'));
        $data = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 生成订单
    **/
    public function receiptlist(){
        $iuid = I('post.iuid');
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $password = md5(trim(I('post.password')));
        if($userinfo['password'] != $password){
            // 密码错误
            $data['status'] = 0;
            $data['msg'] = '密码错误';
            $this->ajaxreturn($data);
        }else{
            $tohu_nickname  = strtoupper(trim(I('post.CustomerID')));
            $whichApp       = 5;
            $point          = trim(I('post.point'));
            // $userinfo   = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
            $touserinfo = M('User')->where(array('CustomerID'=>$tohu_nickname))->find();
            //如果是转账，调取默认的银行账户信息
            $bank       = M('Bank')->where(array('iuid'=>$iuid,'isshow'=>1))->find();
            // 获取手续费
            $change = M('WvBonusParities')->where(array('pid'=>2))->getfield('parities');
            if($tohu_nickname === $userinfo['customerid']){
                if(!empty($bank)){
                    //提现
                    $iuid       = $userinfo['iuid'];
                    $unpoint    = $point;
                    $iu_point   = $userinfo['iu_point'];
                    $iu_unpoint = $userinfo['iu_unpoint'];
                    //更新用户积分
                    $point      = bcsub($iu_point,$unpoint,4);
                    $newunpoint = bcadd($iu_unpoint,$unpoint,4);
                    
                    if($point<0){
                        // 积分不足
                        $data['status'] = 2;
                        $data['msg'] = '积分不足';
                        $this->ajaxreturn($data);
                    }
                    $map     = array(
                                'iuid'      =>$userinfo['iuid'],
                                'iu_point'  =>$point,
                                'iu_unpoint'=>$newunpoint
                            );
                    if($change == 0){
                        $feepoint =$unpoint;
                    }else{
                        $feepoint = bcmul($unpoint,bcdiv($change,100,2),2);
                    }
                    $realpoint=bcsub($unpoint,$feepoint,4);
                    //生成提现订单
                    $pointNo = 'EP'.date('YmdHis').rand(10000, 99999);
                    //更新用户积分
                    $save   = M('User')->save($map);
                    if($save){
                        $content = '单号:'.$pointNo.',提取积分:'.$unpoint.',剩余积分:'.$point;
                        $tmp     = array(
                            'iuid'           =>$userinfo['iuid'],
                            'pointNo'        =>$pointNo,
                            'hu_username'    =>$userinfo['lastname'].$userinfo['firstname'],
                            'hu_nickname'    =>$userinfo['customerid'],
                            'iu_bank'        =>$bank['bankname'],
                            'iu_bankbranch'  =>$bank['bankbranch'],
                            'iu_bankaccount' =>$bank['bankaccount'],
                            'iu_bankprovince'=>$bank['bankprovince'],
                            'iu_bankcity'    =>$bank['bankregion'],
                            'iu_bankuser'    =>$bank['iu_name'],
                            'getpoint'       =>$unpoint,
                            'feepoint'       =>$feepoint,
                            'realpoint'      =>$realpoint,
                            'unpoint'        =>$newunpoint,
                            'leftpoint'      =>$point,
                            'date'           =>date('Y-m-d H:i:s'),
                            'status'         =>0,
                            'pointtype'      =>6,
                            'whichApp'       =>$whichApp,
                            'send'           =>$userinfo['customerid'],
                            'received'       =>'系统冻结',
                            'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,提现冻结'.$unpoint.'EP到'.'系统冻结'.',剩EP余额'.$point
                        );
                        $addtmp = M('Getpoint')->add($tmp);
                        //写入日志记录
                        $add     = addLog($iuid,$content,$action=0,$type=1);
                        if($add && $addtmp){
                            // 提现成功
                            $data['status'] = 1;
                            $data['msg'] = '提现成功';
                            $this->ajaxreturn($data);
                        }else{
                            // 提现失败
                            $data['status'] = 3;
                            $data['msg'] = '提现失败';
                            $this->ajaxreturn($data);
                        }
                    }
                }else{
                    // 请填写银行账号
                    $data['status'] = 6;
                    $data['msg'] = '请添加或选择银行信息';
                    $this->ajaxreturn($data);
                }
            }else{
                $leftpoint_user  = bcsub($userinfo['iu_point'],$point,4);
                if($leftpoint_user<0){
                    // 积分不足
                    $data['status'] = 2;
                    $data['msg'] = '积分不足';
                    $this->ajaxreturn($data);
                }else{
                    $leftpoint_touser = bcadd($touserinfo['iu_point'],$point,4);
                    $user = array(
                            'iuid'      =>$userinfo['iuid'],
                            'iu_point'  =>$leftpoint_user
                        );
                    $touser = array(
                            'iuid'      =>$touserinfo['iuid'],
                            'iu_point'  =>$leftpoint_touser
                        );
                    $save_userpoint  = M('User')->save($user);
                    $save_touserpoint= M('User')->save($touser);
                    if($save_userpoint && $save_touserpoint){
                        //写入日志记录
                        //转出
                        $content   = '你给'.$touserinfo['customerid'].'转赠积分'.$point; 
                        $tmp1     = array(
                            'iuid'           =>$userinfo['iuid'],
                            'pointNo'        =>'EP'.date('YmdHis').rand(10000, 99999),
                            'hu_username'    =>$userinfo['lastname'].$userinfo['firstname'],
                            'hu_nickname'    =>$userinfo['customerid'],
                            'getpoint'       =>$point,
                            'feepoint'       =>0,
                            'realpoint'      =>$point,
                            'unpoint'        =>$userinfo['iu_unpoint'],
                            'leftpoint'      =>$leftpoint_user,
                            'date'           =>date('Y-m-d H:i:s'),
                            'handletime'     =>date('Y-m-d H:i:s'),
                            'status'         =>2,
                            'pointtype'      =>4,
                            'whichApp'       =>$whichApp,
                            'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,转账出'.$point.'EP到'.$touserinfo['customerid'].',剩EP余额'.$leftpoint_user,
                            'send'           =>$userinfo['customerid'],
                            'received'       =>$touserinfo['customerid']
                        );
                        $addtmp1 = M('Getpoint')->add($tmp1);
                        //转入
                        $add_userlog    = addLog($userinfo['iuid'],$content,$action=1,$type=1);
                        $ucontent = $userinfo['customerid'].'给你转赠积分'.$point;
                        $tmp2     = array(
                            'iuid'           =>$touserinfo['iuid'],
                            'pointNo'        =>'EP'.date('YmdHis').rand(10000, 99999),
                            'hu_username'    =>$touserinfo['lastname'].$touserinfo['firstname'],
                            'hu_nickname'    =>$touserinfo['customerid'],
                            'getpoint'       =>$point,
                            'feepoint'       =>0,
                            'realpoint'      =>$point,
                            'unpoint'        =>$touserinfo['iu_unpoint'],
                            'leftpoint'      =>$leftpoint_touser,
                            'date'           =>date('Y-m-d H:i:s'),
                            'handletime'     =>date('Y-m-d H:i:s'),
                            'status'         =>2,
                            'pointtype'      =>5,
                            'whichApp'       =>$whichApp,
                            'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,转账入'.$point.'EP到'.$touserinfo['customerid'].',剩EP余额'.$leftpoint_touser,
                            'send'           =>$userinfo['customerid'],
                            'received'       =>$touserinfo['customerid']
                        );
                        $addtmp2 = M('Getpoint')->add($tmp2); 
                        $add_touserlog  = addLog($touserinfo['iuid'],$ucontent,$action=2,$type=1);
                        if($add_userlog && $add_touserlog){
                            // 转出成功
                            $data['status'] = 4;
                            $data['msg'] = '转出成功';
                            $this->ajaxreturn($data);
                        }else{
                            // 转出失败
                            $data['status'] = 5;
                            $data['msg'] = '转出失败';
                            $this->ajaxreturn($data);
                        }
                    } 
                } 
            }
        }
    }

    /**
    * 每月积分
    **/ 
    public function myPointMonth(){
        $iuid = I('post.iuid');
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $status = '0,1,2,3';
        $assign = D('Getpoint')->getAllPoint(D('Getpoint'),$CustomerID,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4);
            $data[$key]['reduce']   = bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4);
        }
        $assign = array(
                    'data' => $data
                    );
        
        if(!empty($assign['data'])){
            $assign['status'] = 1;
            $this->ajaxreturn($assign);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 每日积分
    **/ 
    public function myPointDay(){
        $iuid = I('post.iuid');
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('post.date');
        $status      = '0,1,2,3';
        $assign      = D('Getpoint')->getPointDay(D('Getpoint'),$CustomerID,$date,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4);
            $data[$key]['reduce']   = bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4);
        }
        // p($data);
        $assign = array(
                    'data' => $data
                    );
        if(!empty($assign['data'])){
            $assign['status'] = 1;
            $this->ajaxreturn($assign);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 每日积分详情
    **/ 
    public function myPointDaily(){
        $iuid = I('post.iuid');
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('post.date');
        $dates = substr($date,0,7);
        $type = '1,2,3,4,5,6,7,8';
        $assign      = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$CustomerID);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['point'] = $value['getpoint'];
            switch ($value['pointtype']) {
                case '1':
                    $data[$key]['type']  = '系统减少';
                    $data[$key]['syslog']= '-';
                    break;
                case '2':
                    $data[$key]['type']  = '系统增加';
                    $data[$key]['syslog']= '+';
                    break;
                case '3':
                    $data[$key]['type']  = '新增奖金';
                    $data[$key]['syslog']= '+';
                    break;
                case '4':
                    $data[$key]['type']  = '转账出';
                    $data[$key]['syslog']= '-';
                    break;
                case '5':

                    $data[$key]['type'] = '转账入';
                    $data[$key]['syslog']= '+';
                    break;
                case '6':
                    $data[$key]['type']  = '提现出';
                    $data[$key]['syslog']= '-';
                    break;
                case '7':
                    $data[$key]['type']  = '消费出';
                    $data[$key]['syslog']= '-';
                    break;
                case '8':
                    $data[$key]['type']  = '驳回';
                    $data[$key]['syslog']= '+';
                    break;
            }
        }
        $assign = array(
                    'data' => $data,
                    'dates' => $dates
                    );
        if(!empty($assign['data'])){
            $assign['status'] = 1;
            $this->ajaxreturn($assign);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

}