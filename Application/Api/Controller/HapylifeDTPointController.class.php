<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* DT商店
**/
class HapylifeDTPointController extends HomeBaseController{
    public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            die;
        }
    }
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
        // 获取用户在美国的dtp
        $usa = new \Common\UsaApi\Usa;
        $result = $usa->dtPoint($data['customerid']);
        if(!$result['errors']){
            foreach($result['softCashCategories'] as $key=>$value){
                switch ($value['categoryType']) {
                    case 'DreamTripPoints':
                        $data['iu_dt'] = $value['balance'];
                        break;
                }
            }
        }else{
           $data['iu_dt'] = 0;
        }
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
            // $getdt = M('Getdt')->where(array('iuid'=>$iuid))->limit($page)->order('igid desc')->select();
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

    // 369
    public function test(){
        $limit1 = I('post.limit1');
        $limit2 = I('post.limit2');
        $test = '测试,测,试,测试点,test,testtest,测试测试,新建测试,测试地,测试点,测试账号';
        $data = M('User')->where(array('DistributorType'=>array('NOT IN','Pc'),'isexit'=>1,'LastName'=>array('NOT IN',$test),'FirstName'=>array('NOT IN',$test),'wvCustomerID'=>array('NEQ','')))->limit($limit1,$limit2)->field('customerid,iu_point,iu_dt,lastname,firstname')->select();
        $usa    = new \Common\UsaApi\Usa;
        foreach ($data as $key => $value) {
            $result = $usa->dtPoint($value['customerid']);
            $activities = $usa->validateHpl($value['customerid']);
            if(!$result['errors']){
                foreach($result['softCashCategories'] as $k=>$v){
                    switch ($v['categoryType']) {
                        case 'DreamTripPoints':
                            $data[$key]['iu_dt'] = $v['balance'];
                            break;
                    }
                }
            }else{
               $data[$key]['iu_dt'] = 0;
            }
            $data[$key]['status'] = $activities['isActive'];
        }
        $this->ajaxreturn($data);
    }
}