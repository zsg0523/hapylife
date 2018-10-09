<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* DT商店
**/
class HapylifeDTPointController extends HomeBaseController{
    /**
    * 购买DT礼包
    **/
    public function dtPurchase(){
        $iuid    = I('post.iuid');
        $find    = M('User')->where(array('iuid'=>$iuid))->find();
        $products= M('Product')->where(array('ip_type'=>5,'is_pull'=>1))->select();
        $array   = array('HPL00000181','HPL00123539');//显示测试产品账号]
        if(in_array($find['customerid'],$array)){
            $an_pro = M('Product')->where(array('ip_type'=>5,'is_pull'=>0))->select();
            $product = array_merge($products,$an_pro);
            foreach ($product as $key => $value) {
                $data[$key]         = $value; 
                $data[$key]['show'] = 1; 
            }
        }else{
            $data = $products;
        }
        $this->ajaxreturn($data);
    }
    /**
    * 购买DT礼包详情
    **/
    public function dtPurchaseInfo(){
        $ipid = I('post.ipid');
        $data = M('Product')
              ->where(array('ipid'=>$ipid))
              ->find();
        $data['status'] = 1;
        $this->ajaxreturn($data);
    }
    /**
    *检查用户DT
    **/
    public function dtpay(){
        $iuid    = I('post.iuid');
        $ip_dt   = I('post.ip_dt');
        $ipid    = I('post.ipid');
        $data    = M('User')->where(array('iuid'=>$iuid))->find();
        $bcsub   = bcsub($data['iu_dt'],$ip_dt,2);
        $data['bc_dt'] =$bcsub;
        $data['ipid']  =$ipid;
        $data['ip_dt'] =$ip_dt;
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    *我的DT记录
    **/
    public function myDTPoint(){
        $iuid = I('post.iuid');
        $page  = trim(I('post.page'));
        if($iuid){
            $getdt = M('Getdt')->where(array('iuid'=>$iuid))->limit($page)->order('igid desc')->select();
            $assign= array(
                'getdt'  => $getdt,
                'status' => 1
            );
            $this->ajaxreturn($assign);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    *个人每月DT
    **/
    public function myDtMonth(){
        $iuid = I('post.iuid');
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $status = '2';
        $assign = D('Getdt')->getAllDt(D('Getdt'),$CustomerID,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd($value['realpoint2'],$value['realpoint3'],4);
            $data[$key]['reduce']   = bcadd($value['realpoint1'],$value['realpoint4'],4);
        }
        if($iuid){
            $assign = array(
                'data' => $data,
                'status' => 1
            );
            $this->ajaxreturn($assign);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    * 每日DT
    **/ 
    public function myDtDay(){
        $iuid = I('post.iuid');
        $CustomerID  = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('post.date');
        $status      = '2';
        $assign      = D('Getdt')->getDtDay(D('Getdt'),$CustomerID,$date,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd($value['realpoint2'],$value['realpoint3'],4);
            $data[$key]['reduce']   = bcadd($value['realpoint1'],$value['realpoint4'],4);
        }
        if($iuid){
            $assign = array(
                'data' => $data,
                'status' => 1
            );
            $this->ajaxreturn($assign);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    * 每日DT详情
    **/ 
    public function myDtDaily(){
        $iuid = I('post.iuid');
        $CustomerID  = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('post.date');
        $dates       = date('Y-m',trim(strtotime($date)));
        $assign      = D('Getdt')->getAllDtInfo(D('Getdt'),$type,$date,$CustomerID);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['point'] = $value['getdt'];
            switch ($value['dttype']) {
                case '1':
                    $data[$key]['type']  = '系统减少';
                    $data[$key]['syslog']= '-';
                    break;
                case '2':
                    $data[$key]['type']  = '系统增加';
                    $data[$key]['syslog']= '+';
                    break;
                case '3':
                    $data[$key]['type']  = '新增入';
                    $data[$key]['syslog']= '+';
                    break;
                case '4':
                    $data[$key]['type']  = '消费出';
                    $data[$key]['syslog']= '-';
                    break;
            }
        }
        if($iuid){
            $assign = array(
                'data' => $data,
                'dates'=> $dates,
                'status' => 1
            );
            $this->ajaxreturn($assign);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
}