<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
 * Vue示例
 */
class DTPointController extends HomeBaseController{
    /**
    *我的DT和记录
    **/
    public function myDTPoint(){
        $iuid = $_SESSION['user']['id'];
        $userinfo  = M('User')->where(array('iuid'=>$iuid))->find();
        if($userinfo){
            // 获取用户在美国的dtp
            $usa = new \Common\UsaApi\Usa;
            $result = $usa->dtPoint($userinfo['customerid']);
            if(!$result['errors']){
                foreach($result['softCashCategories'] as $key=>$value){
                    switch ($value['categoryType']) {
                        case 'DreamTripPoints':
                            $data['iu_dt'] = $value['balance'];
                            break;
                        case 'DreamTripPoints_Accrued':
                            $data['iu_ac'] = $value['balance'];
                            break;
                    }
                }
                $data['endTime'] = date('l,F d,Y',$userinfo['joinedon']+24*3600*365);
            }else{
                $data['iu_dt'] = 0;
                $data['iu_ac'] = 0;
                $data['endTime'] = '';
            }
            // $getdt = M('Getdt')->where(array('iuid'=>$iuid))->order('igid desc')->select();
            $assign= array(
                'data' =>$data,
                'getdt'=>$getdt
            );
            $this->assign('assign',$assign);
            $this->display();
        }else{
            $this->error('登录过期，请重新登录');
        }
    }
	/**
	*个人每月DT
	**/
    public function myDtMonth(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $status = '2';
        $assign = D('Getdt')->getAllDt(D('Getdt'),$CustomerID,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd($value['realpoint2'],$value['realpoint3'],4);
            $data[$key]['reduce']   = bcadd($value['realpoint1'],$value['realpoint4'],4);
        }
        // p($data);
        // die;
        $assign = array(
                    'data' => $data
                    );
        $this->assign($assign);
        $this->display();
    }
    /**
    * 每日DT
    **/ 
    public function myDtDay(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID  = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('get.date');
        $status      = '2';
        $assign      = D('Getdt')->getDtDay(D('Getdt'),$CustomerID,$date,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd($value['realpoint2'],$value['realpoint3'],4);
            $data[$key]['reduce']   = bcadd($value['realpoint1'],$value['realpoint4'],4);
        }
        // p($assign);die;
        $assign = array(
        'data' => $data
        );
        $this->assign($assign);
        $this->display();
    }
    /**
    * 每日DT详情
    **/ 
    public function myDtDaily(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID  = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('get.date');
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
        $assign = array(
	        'data' => $data,
	        'dates'=> $dates
        );
        $this->assign($assign);
        $this->display();
    }
}