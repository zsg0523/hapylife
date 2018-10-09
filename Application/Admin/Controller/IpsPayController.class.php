<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*ips支付报表
**/
class IpsPayController extends AdminBaseController{
    /**
    * ips--每月总额
    **/
    public function Table(){
        $merchant = 0; 
        $paytype  = 1; 
        $assign   = D('Receiptson')->getAllMonthPay(D('Receiptson'),$merchant,$paytype);
        // p($assign);
        $this->assign($assign);
        $this->display();
    }
    /**
    * ips--每日总额
    **/
    public function TableDay(){
        $merchant = 0; 
        $paytype  = 1; 
        $date     = I('get.date'); 
        $assign   = D('Receiptson')->getAllDayPay(D('Receiptson'),$merchant,$paytype,$date);
        $this->assign('date',$date);
        $this->assign($assign);
        $this->display();
    }
    /**
    * ips-详情
    **/
    public function TableDayInfo(){
        $merchant   = 0; 
        $paytype    = 1;
        $word       = I('get.word'); 
        $starttime  = strtotime(I('get.date'));
        $date       = I('get.date');
        $hu_nickname= I('get.hu_nickname');
        $endtime    = strtotime(I('get.date'))+86400;
        $assign     = D('Receiptson')->getAllDayPayInfos(D('Receiptson'),$starttime,$endtime,$paytype,$merchant,$word,$order='paytime desc',$limit=50);
        // p($assign);die;
        $this->assign($assign);
        $this->assign('date',$date);
        $this->assign('word',$word);
        $this->display();
    }

}