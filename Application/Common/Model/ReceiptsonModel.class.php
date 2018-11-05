<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 订单model
**/
class ReceiptsonModel extends BaseModel{
    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  string   $ir_receiptnum   父订单号
     * @return array            分页数据
     */
    public function getSendPageSon($model,$ir_receiptnum,$field,$limit=50){
        $count=$model
            ->alias('rs')
            ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
            ->join('hapylife_user u on rs.riuid = u.iuid')
            ->where(array('rs.ir_receiptnum'=>$ir_receiptnum))
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('rs')
                ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                ->join('hapylife_user u on rs.riuid = u.iuid')
                ->where(array('rs.ir_receiptnum'=>$ir_receiptnum))
                ->order('ir_paytime desc')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->alias('rs')
                ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                ->join('hapylife_user u on rs.riuid = u.iuid')
                ->where(array('rs.ir_receiptnum'=>$ir_receiptnum))
                ->field($field)
                ->order('ir_paytime desc')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }
        foreach ($list as $key => $value) {
            $ia_address = '';
            $mape[$key] = $value;
            $address    = explode(' ',$value['ia_address']);
            foreach ($address as $k => $v) {
                $ia_address.= $v;
            }
            $mape[$key]['ia_address'] = $ia_address;
            $arr = D('Receiptlist')
                    ->join('hapylife_product on hapylife_receiptlist.ipid = hapylife_product.ipid')
                    ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                    ->select();
                    // p($arr);
            $productname = '';
            foreach ($arr as $k => $v) {
                $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
            }
            $mape[$key]['productname'] = substr($productname,0,-1);
            $mape[$key]['productno']   = $v['productno'];
            $mape[$key]['productnams'] = $v['product_name'];

            $time = D('Receipt')
                        ->alias('r')
                        ->join('hapylife_activation AS a ON r.riuid = a.iuid')
                        ->where(array('r.ir_receiptnum'=>$value['ir_receiptnum']))
                        ->select();
                        // p($time);
            foreach($time as $v){
                // $times[$k] = $v['endtime'];
                $mape[$key]['endtime'] = $v['endtime'];
            }
            // 获取会籍到期时间
            // $mape[$key]['endtime'] = $times[$k];
        }
        $data=array(
            'data'=>$mape,
            'page'=>$page->show()
            );
        return $data;
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  string   $ir_receiptnum   父订单号
     * @return array            分页数据
     */
    public function getSendPageSonE($model,$word,$starttime,$endtime,$ir_status,$timeType,$order='',$limit=50,$field=''){
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('rs')
                ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                ->join('hapylife_user u on rs.riuid = u.iuid')
                ->order('ir_paytime desc')
                ->select();
        }else{
            $list=$model
                ->alias('rs')
                ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                ->join('hapylife_user u on rs.riuid = u.iuid')
                ->field($field)
                ->order('ir_paytime desc')
                ->select();         
        }

        foreach ($list as $key => $value) {
            $ia_address = '';
            $mape[$key] = $value;
            $address    = explode(' ',$value['ia_address']);
            foreach ($address as $k => $v) {
                $ia_address.= $v;
            }
            $mape[$key]['ia_address'] = $ia_address;
            $arr = D('Receiptlist')
                    ->join('hapylife_product on hapylife_receiptlist.ipid = hapylife_product.ipid')
                    ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                    ->select();
            $productname = '';
            foreach ($arr as $k => $v) {
                $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
            }
            $mape[$key]['productname'] = substr($productname,0,-1);
            $mape[$key]['productno']   = $v['productno'];
            $mape[$key]['productnams'] = $v['product_name'];
            $son = D('Receiptson')
                 ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                 ->select();
            $receiptson = '';
            $ir_paytype = '';
            foreach ($son as $k => $v) {
                $receiptson .= $v['pay_receiptnum'].'/';
                switch ($v['ir_paytype']) {
                    case '1':
                        $ir_paytype .= 'IPS'.'/';
                        break;
                    case '2':
                        $ir_paytype .= '积分'.'/';
                        break;
                    case '3':
                        // $ir_paytype .= '积分'.'/';
                        break;
                    case '4':
                        $ir_paytype .= '畅捷'.'/';
                        break;
                }
            }
            $mape[$key]['receiptson']  = substr($receiptson,0,-1);
            $mape[$key]['paytype']     = substr($ir_paytype,0,-1);
            $time = D('Receipt')
                        ->alias('r')
                        ->join('hapylife_activation AS a ON r.riuid = a.iuid')
                        ->where(array('r.ir_receiptnum'=>$value['ir_receiptnum']))
                        ->select();
                        // p($time);
            foreach($time as $v){
                // $times[$k] = $v['endtime'];
                $mape[$key]['endtime'] = $v['endtime'];
            }
            // 获取会籍到期时间
            // $mape[$key]['endtime'] = $times[$k];
        }

        $data=array(
            'data'=>$mape,
            );
        return $data;
    }

    public function export_excel($data){
        $title   = array('创建日期','创建时间','用户ID','订单号','流水号','流水方式','订单状态','订单总价','订货人','收货人','收货地址','收货人电话','产品数量','产品信息');
        foreach ($data as $k => $v) {
            $content[$k]['ir_date']        = date('Y-m-d',$v['ir_date']);
            $content[$k]['ir_time']        = date('H:i:s',$v['ir_date']);
            $content[$k]['rcustomerid']    = $v['rcustomerid'];
            $content[$k]['ir_receiptnum']  = $v['ir_receiptnum'];
            $content[$k]['receiptson']  = $v['receiptson'];
            $content[$k]['paytype']  = $v['paytype'];
            switch ($v['ir_status']) {
                case '0':
                    $content[$k]['ir_status'] = '待付款';
                    break;
                case '2':
                    $content[$k]['ir_status'] = '已支付待发货';
                    break;
                case '3':
                    $content[$k]['ir_status'] = '已发货待收货';
                    break;
                case '4':
                    $content[$k]['ir_status'] = '已送达';
                    break;
                case '5':
                    $content[$k]['ir_status'] = '已申请退货';
                    break;
                case '7':
                    $content[$k]['ir_status'] = '待付款';
                    break;
                case '8':
                    $content[$k]['ir_status'] = '已退货';
                    break;
                case '202':
                    $content[$k]['ir_status'] = '未全额支付';
                    break;
            }
            $content[$k]['ir_price']       = $v['ir_price'];
            //订货人
            if(!$v['ia_name']){
                $content[$k]['ia_name']        = ' ';
            }else{
                $content[$k]['ia_name']        = $v['ia_name'];
            }
            //收货人
            if(!$v['ia_name']){
                $content[$k]['ib_name']        = ' ';
            }else{
                $content[$k]['ib_name']        = $v['ia_name'];
            }
            //收货详细地址
            $content[$k]['ia_address']     = $v['shopprovince'].$v['shopcity'].$v['shoparea'].$v['ia_address'];
            //收货人电话
            $content[$k]['ia_phone']       = $v['ia_phone'];
            //产品数量
            switch ($v['ipid']) {
                case '31':
                    $content[$k]['productname'] = $v['productnams'].'*8瓶';
                    break;
                case '39':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '61':
                    $content[$k]['productname'] = $v['productnams'].'*4瓶';
                    break;
                case '62':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '64':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                default:
                    $content[$k]['productname'] = $v['productnams'].'*1瓶';
                    break;
            }
            // 产品信息
            $content[$k]['ir_desc']       = $v['ir_desc'];
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }

    public function export_excels($data){
        $title   = array('创建日期','创建时间','用户ID','订单号','流水号','流水方式','订单状态','订单总价','订货人','收货人','产品数量','产品信息','通用券的由来');
        foreach ($data as $k => $v) {
            $content[$k]['ir_date']        = date('Y-m-d',$v['ir_date']);
            $content[$k]['ir_time']        = date('H:i:s',$v['ir_date']);
            $content[$k]['rcustomerid']    = $v['rcustomerid'];
            $content[$k]['ir_receiptnum']  = $v['ir_receiptnum'];
            $content[$k]['receiptson']  = $v['receiptson'];
            $content[$k]['paytype']  = $v['paytype'];
            switch ($v['ir_status']) {
                case '0':
                    $content[$k]['ir_status'] = '待付款';
                    break;
                case '2':
                    $content[$k]['ir_status'] = '已支付待发货';
                    break;
                case '3':
                    $content[$k]['ir_status'] = '已发货待收货';
                    break;
                case '4':
                    $content[$k]['ir_status'] = '已送达';
                    break;
                case '5':
                    $content[$k]['ir_status'] = '已申请退货';
                    break;
                case '7':
                    $content[$k]['ir_status'] = '待付款';
                    break;
                case '8':
                    $content[$k]['ir_status'] = '已退货';
                    break;
                case '202':
                    $content[$k]['ir_status'] = '未全额支付';
                    break;
            }
            $content[$k]['ir_price']       = $v['ir_price'];
            //订货人
            if(!$v['ia_name']){
                $content[$k]['ia_name']        = ' ';
            }else{
                $content[$k]['ia_name']        = $v['ia_name'];
            }
            //收货人
            if(!$v['ia_name']){
                $content[$k]['ib_name']        = ' ';
            }else{
                $content[$k]['ib_name']        = $v['ia_name'];
            }
            //产品数量
            switch ($v['ipid']) {
                case '31':
                    $content[$k]['productname'] = $v['productnams'].'*8瓶';
                    break;
                case '39':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '61':
                    $content[$k]['productname'] = $v['productnams'].'*4瓶';
                    break;
                case '62':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '64':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                default:
                    $content[$k]['productname'] = $v['productnams'].'*1瓶';
                    break;
            }
            // 产品信息
            $content[$k]['ir_desc']       = $v['ir_desc'];
            // 通用券的由来
            $content[$k]['operator']      = $v['operator'];
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }

    /**
    *第三方支付月综合报表 $merchant商户号 $ir_paytype支付方式
    **/
    public function getAllMonthPay($model,$merchant,$ir_paytype){
        //获取订单数据
        if($merchant==0){
            $list=$model
                ->where(array('status'=>2,'ir_paytype'=>$ir_paytype))
                ->select();
        }
        foreach ($list as $key => $value) {
            $listdate[$key] = date('Y-m',$value['paytime']);
        }
        //去除相同值
        $ceipt = array_unique($listdate);
        foreach ($ceipt as $key => $value) {
            $ceiptarr[] = $value;
        }
        foreach ($ceiptarr as $key => $value) {
            foreach ($list as $k => $v) {
                if(date('Y-m',$v['paytime'])==$value){
                    $mape[$value][] = $v;
                }
            }
        }
        foreach ($mape as $key => $value) {
            foreach ($value as $k => $v) {
                $data[$key]['count'] = bcadd($data[$key]['count'],$v['ir_price'],2);
                $data[$key]['date']  = $key;
            }
        }
        //排序
        $die = array_sort($data,'date','desc');
        $data=array(
            'data'=>$die,
            );
        return $data;
    }
    /**
    *第三方支付月综合报表 $merchant商户号 $ir_paytype支付方式 日期$date
    **/
    public function getAllDayPay($model,$merchant,$ir_paytype,$date){
        //获取订单数据
        if($merchant==0){
            $list=$model
                ->where(array('status'=>2,'ir_paytype'=>$ir_paytype))
                ->select();
        }
        foreach ($list as $key => $value) {
            if(date('Y-m',$value['paytime'])==$date){
                $listMonth[] = $value;
            }
        }
        // p($list);die;
        foreach ($listMonth as $key => $value) {
            $listdate[$key] = date('Y-m-d',$value['paytime']);
        }
        //去除相同值
        $ceipt = array_unique($listdate);
        foreach ($ceipt as $key => $value) {
            $ceiptarr[] = $value;
        }
        foreach ($ceiptarr as $key => $value) {
            foreach ($listMonth as $k => $v) {
                if(date('Y-m-d',$v['paytime'])==$value){
                    $mape[$value][] = $v;
                }
            }
        }
        foreach ($mape as $key => $value) {
            foreach ($value as $k => $v) {
                $data[$key]['count'] = bcadd($data[$key]['count'],$v['ir_price'],2);
                $data[$key]['date']  = $key;
            }
        }
        // p($data);die;
        //排序
        $die = array_sort($data,'date','desc');
        $data=array(
            'data'=>$die,
            );
        return $data;
    }
    /**
     * 第三方支付每天综合订单 $merchant商户号 $ir_paytype支付方式 日期$date
     */
    public function getAllDayPayInfo($model,$starttime,$endtime,$ir_paytype,$merchant,$word,$order='',$limit=50){
        if($merchant==0){
            $count=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid ')
                ->join('hapylife_receipt AS hr ON r.ir_receiptnum = hr.ir_receiptnum')
                ->where(
                    array(
                        'u.customerid|hr.ir_receiptnum|inner_trade_no|trade_status|hr.ia_name'=>array('like','%'.$word.'%'),
                        'paytime'=>array(array('egt',$starttime),array('elt',$endtime)),
                        'status'  =>2,
                        'ir_paytype' =>$ir_paytype,
                        )
                    )
                ->count();
        }
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if($merchant==0){
            $list=$model
            ->alias('r')
            ->join('hapylife_user u on r.riuid = u.iuid ')
            ->join('hapylife_receipt AS hr ON r.ir_receiptnum = hr.ir_receiptnum')
            ->where(
                array(
                    'u.customerid|hr.ir_receiptnum|inner_trade_no|trade_status|hr.ia_name'=>array('like','%'.$word.'%'),
                    'paytime'=>array(array('egt',$starttime),array('elt',$endtime)),
                    'status'  =>2,
                    'ir_paytype' =>$ir_paytype,
                    )
                )
            ->order($order)
            ->field(array('*,r.ir_price'=>'r_price'))
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        }
        // p($list);die;
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }

    /**
     * 第三方支付每天综合订单 $merchant商户号 $ir_paytype支付方式 日期$date
     */
    public function getAllDayPayInfos($model,$starttime,$endtime,$ir_paytype,$merchant,$word,$order='',$limit=50){
        if($merchant==0){
            $count=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid ')
                ->join('hapylife_receipt AS hr ON r.ir_receiptnum = hr.ir_receiptnum')
                ->where(
                    array(
                        'u.customerid|hr.ir_receiptnum|ips_trade_no|ips_trade_status|hr.ia_name'=>array('like','%'.$word.'%'),
                        'paytime'=>array(array('egt',$starttime),array('elt',$endtime)),
                        'status'  =>2,
                        'ir_paytype' =>$ir_paytype,
                        )
                    )
                ->count();
        }
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if($merchant==0){
            $list=$model
            ->alias('r')
            ->join('hapylife_user u on r.riuid = u.iuid ')
            ->join('hapylife_receipt AS hr ON r.ir_receiptnum = hr.ir_receiptnum')
            ->where(
                array(
                    'u.customerid|hr.ir_receiptnum|ips_trade_no|ips_trade_status|hr.ia_name'=>array('like','%'.$word.'%'),
                    'paytime'=>array(array('egt',$starttime),array('elt',$endtime)),
                    'status'  =>2,
                    'ir_paytype' =>$ir_paytype,
                    )
                )
            ->order($order)
            ->field(array('*,r.ir_price'=>'r_price'))
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        }
        // p($list);die;
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }
}