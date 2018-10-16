<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*畅捷支付报表
**/
class CjPayController extends AdminBaseController{
    /**
    * 畅捷--每月总额
    **/
    public function Table(){
        $merchant = 0; 
        $paytype  = 4; 
        $assign   = D('Receiptson')->getAllMonthPay(D('Receiptson'),$merchant,$paytype);
        // p($assign);
        $this->assign($assign);
        $this->display();
    }
    /**
    * 畅捷--每日总额
    **/
    public function TableDay(){
        $merchant = 0; 
        $paytype  = 4; 
        $date     = I('get.date'); 
        $assign   = D('Receiptson')->getAllDayPay(D('Receiptson'),$merchant,$paytype,$date);
        $this->assign('date',$date);
        $this->assign($assign);
        $this->display();
    }
    /**
    * 畅捷-详情
    **/
    public function TableDayInfo(){
        $merchant   = 0; 
        $paytype    = 4;
        $word       = I('get.word'); 
        $starttime  = strtotime(I('get.date'));
        $date       = I('get.date');
        $hu_nickname= I('get.hu_nickname');
        $endtime    = strtotime(I('get.date'))+86400;
        $assign     = D('Receiptson')->getAllDayPayInfo(D('Receiptson'),$starttime,$endtime,$paytype,$merchant,$word,$order='paytime desc',$limit=50);
        // p($assign);die;
        $this->assign($assign);
        $this->assign('date',$date);
        $this->assign('word',$word);
        $this->display();
    }

}