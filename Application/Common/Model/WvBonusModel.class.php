<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 订单model
**/
class WvbonusModel extends BaseModel{
    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $word   搜索关键词
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getSendPage($model,$customerid,$hplid,$starttime,$endtime,$status,$order='',$limit=50,$field=''){
        if(empty($field)){
            if($starttime && empty($endtime)){
                $count=$model
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                        ->count();
            }else if($starttime && $endtime){
                $count=$model
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                        ->count();
            }else{
                $count=$model
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                        ->count();
            }
        }else{
            if($starttime && empty($endtime)){
                $count=$model
                        ->field($field)
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                        ->count();
            }else if($starttime && $endtime){
                $count=$model
                        ->field($field)
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                        ->count();
            }else{
                $count=$model
                        ->field($field)
                        ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                        ->count();
            }
        }
        $page=new_page($count,$limit);
        
        // 获取分页数据
        if (empty($field)) {
            if($starttime && empty($endtime)){
                $list=$model
                ->order($order)
                ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
            }else if($starttime && $endtime){
                $list=$model
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }
        }else{
            if($starttime && empty($endtime)){
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else if($starttime && $endtime){
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }
        }
        // p($list);die;
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }

    /**
     * 获取分页筛选数据
     * @param  subject  $model   model对象
     * @param  subject  $status  发放状态
     * @return array             分页数据
     */
    public function getAll($model,$customerid,$hplid,$starttime,$endtime,$status,$order=''){
        // 获取分页数据
        if (empty($field)) {
            if($starttime && empty($endtime)){
                $list=$model
                ->order($order)
                ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                ->select();
            }else if($starttime && $endtime){
                $list=$model
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                    ->select();
            }else{
                $list=$model
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                    ->select();
            }
        }else{
            if($starttime && empty($endtime)){
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime)),'bonusstatus'=>array('in',$status)))
                    ->select();
            }else if($starttime && $endtime){
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonuspaymenttime'=>array(array('egt',$starttime),array('elt',$endtime)),'bonusstatus'=>array('in',$status)))
                    ->select();
            }else{
                $list=$model
                    ->field($field)
                    ->order($order)
                    ->where(array('customerid'=>array('like','%'.$customerid.'%'),'hplid'=>array('like','%'.$hplid.'%'),'bonusstatus'=>array('in',$status)))
                    ->select();
            }
        }
        
        $data=array(
            'data'=>$list,
            );
        return $data;
    }

    public function export_excel($data){
        $title   = array('CustomerId','HplId','PeriodId','PeriodDescription','BonusDescription','USD Amount','ExchangeRate','EP','BonusPaymentTime','Operator','BonusStatus');
        foreach ($data as $k => $v) {
            $content[$k]['customerid']           = $v['customerid'];
            $content[$k]['hplid']                = $v['hplid'];
            $content[$k]['periodid']             = $v['periodid'];
            $content[$k]['perioddescription']    = $v['perioddescription'];
            $content[$k]['bonusdescription']     = $v['bonuses'][0]['BonusDescription'];
            $content[$k]['usdamount']            = $v['bonuses'][0]['Amount'];
            $content[$k]['exchangerate']         = $v['parities'];
            $content[$k]['ep']                   = $v['ep'];
            if($v['bonuspaymenttime']){
                $content[$k]['bonuspaymenttime'] = date('Y-m-d H:i:s',$v['bonuspaymenttime']);
            }else{
                $content[$k]['bonuspaymenttime'] = '';
            }
            $content[$k]['operator']             = $v['operator'];
            if($v['bonusstatus'] == 0){
                $content[$k]['bonusstatus']      = '未发放';
            }else{
                $content[$k]['bonusstatus']      = '已发放';
            }
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }
}