<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*综合支付报表
**/
class PayController extends AdminBaseController{
    /**
    * 每月综合报表
    **/ 
    public function payMonthList(){
        $map = I('get.');
        $map['starttime'] = $map['starttime']?$map['starttime']:date('Y-m',strtotime("-6 month",time()));
        $map['endtime']   = $map['endtime']?$map['endtime']:date('Y-m');
        // p($map);
        if($map['starttime'] && $map['endtime'] && $map['type']){
            $starttime = strtotime($map['starttime']);
            $endtime   = strtotime(date("Y-m",strtotime("+1 month",strtotime($map['endtime']))));
            $assign   = D('Receiptson')->getAllFullMonthPay(D('Receiptson'),$starttime,$endtime,$map['type']);
        }else{
            $assign   = array();
        }
        $this->assign($assign);
        $this->assign('starttime',$map['starttime']);
        $this->assign('endtime',$map['endtime']);
        $this->assign('type',implode(',',$map['type']));
        $this->assign('starttime',$map['starttime']);
        $this->assign('endtime',$map['endtime']);
        $this->display();
    }

    /**
    * 每日总额
    **/
    public function payDayList(){
        $map      = I('get.');
        $type     = explode(',',$map['type']);
        $assign   = D('Receiptson')->getAllFullDayPay(D('Receiptson'),$map['date'],$type);
        $this->assign('date',$map['date']);
        $this->assign('type',$map['type']);
        $this->assign($assign);
        $this->display();
    }
    /**
    *支付流水
    **/
    public function payDayListInfo(){
        $map      = I('get.');
        $type     = explode(',',$map['type']);
        $word     = array(
            'ir_receiptnum'=>$map['ir_receiptnum'],
            'customerid'   =>$map['customerid'],
        );
        $assign   = D('Receiptson')->getAllFullDayPayInfo(D('Receiptson'),$map['date'],$type,$word,$limit='50');
        $this->assign('date',$map['date']);
        $this->assign('app',$map['app']);
        $this->assign('type',$map['type']);
        $this->assign('day',$map['day']);
        $this->assign('ir_receiptnum',$map['ir_receiptnum']);
        $this->assign('customerid',$map['customerid']);
        $this->assign($assign);
        $this->display();
    }

}