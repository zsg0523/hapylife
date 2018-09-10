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
		if($iuid){
			$data  = M('User')->where(array('iuid'=>$iuid))->find();
			$getdt = M('Getdt')->where(array('iuid'=>$iuid))->select();
			$assign= array(
				'data' =>$data,
				'getdt'=>$getdt
			);
			// p($assign);die;
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