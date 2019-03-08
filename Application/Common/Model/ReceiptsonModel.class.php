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
        $title   = array('标签','创建日期','创建时间','支付日期','支付时间','用户ID','订单号','流水号','流水方式','订单状态','订单总价','订货人','收货人','收货地址','收货人电话','产品数量','产品信息');
        foreach ($data as $k => $v) {
            $content[$k]['code']           = 'HPL';
            $content[$k]['ir_date']        = date('Y-m-d',$v['ir_date']);
            $content[$k]['ir_time']        = date('H:i:s',$v['ir_date']);
            $content[$k]['ir_pay']         = date('Y-m-d',$v['ir_paytime']);
            $content[$k]['ir_paytime']     = date('H:i:s',$v['ir_paytime']);
            $content[$k]['rcustomerid']    = $v['rcustomerid'];
            $content[$k]['ir_receiptnum']  = $v['ir_receiptnum'];
            $content[$k]['receiptson']     = $v['receiptson'];
            $content[$k]['paytype']        = $v['paytype'];
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
            if($v['ia_province'] && $v['ia_city'] && $v['ia_area'] && $v['ia_address']){
                $content[$k]['ia_address'] = $v['ia_province'].$v['ia_city'].$v['ia_area'].$v['ia_address'];
            }else{
                $content[$k]['ia_address'] = $v['shopprovince'].$v['shopcity'].$v['shoparea'].$v['shopaddress1'];
            }
            // $content[$k]['ia_address']     = $v['shopprovince'].$v['shopcity'].$v['shoparea'].$v['ia_address'];
            //收货人电话
            $content[$k]['ia_phone']       = $v['ia_phone'];
            //产品数量
            $content[$k]['productname'] = $v['productnum'];
            // 产品信息
            $content[$k]['ir_desc']       = $v['ir_desc'];
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }

    public function export_excels($data){
        $title   = array('标签','创建日期','创建时间','支付日期','支付时间','用户ID','订单号','流水号','流水方式','订单状态','订单总价','订货人','收货人','产品数量','产品信息','通用券的由来');
        foreach ($data as $k => $v) {
            $content[$k]['code']           = 'HPL';
            $content[$k]['ir_date']        = date('Y-m-d',$v['ir_date']);
            $content[$k]['ir_time']        = date('H:i:s',$v['ir_date']);
            $content[$k]['ir_paytime']     = date('Y-m-d',$v['ir_paytime']);
            $content[$k]['ir_paytimes']    = date('H:i:s',$v['ir_paytime']);
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
            $content[$k]['productname'] = $v['productnum'];
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
                ->join('left join hapylife_user u on r.riuid = u.iuid ')
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
            ->join('left join hapylife_user u on r.riuid = u.iuid ')
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

    public function getAllFullMonthPay($model,$starttime,$endtime,$paytype,$unArr){
        $type = implode(',',$paytype);
        $list = $model
                    ->alias('rs')
                    ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                    ->where(array('status'=>2,'ir_paytype'=>array('in',$type),'paytime'=>array(array('egt',$starttime),array('elt',$endtime))))
                    ->field('paytime,ir_ordertype,rs.ir_price,r.ia_name')
                    ->select();
        $date1 = explode('-',date('Y-m',$starttime)); 
        $date2 = explode('-',date("Y-m",strtotime("-1 month",$endtime))); 
        //两个时间相隔月份
        $month_number= abs($date2[0]-$date1[0])*12+abs($date2[1]-$date1[1]);
        // p($month_number);die;
        for($i=0;$i<=$month_number;$i++){
            $month[$i] = date("Y-m",strtotime($i."month",$starttime));
        }
        foreach($month as $key => $value){
            foreach($list as $k => $v){
                if(date('Y-m',$v['paytime'])==$value){
                    switch($v['ir_ordertype']){
                        case '1':
                            if(in_array($v['ia_name'],$unArr)){
                                $signupTest[] = $v;
                                // 当月累计测试注册人数
                                $data[$key]['signupTest'] = count($signupTest);
                            }else{
                                $signup[] = $v;
                                // 当月累计实际注册人数
                                $data[$key]['signup'] = count($signup);
                            }
                            break;
                        case '3':
                            if(in_array($v['ia_name'],$unArr)){
                                $monthlyTest[] = $v;
                                // 当月累计测试月费人数
                                $data[$key]['monthlyTest'] = count($monthlyTest);
                            }else{
                                $monthly[] = $v;
                                // 当月累计实际月费人数
                                $data[$key]['monthly'] = count($monthly);
                            }
                            break;
                        case '5':
                            if(in_array($v['ia_name'],$unArr)){
                                $dtTest[] = $v;
                                // 当月累计测试DT订单数
                                $data[$key]['dtTest'] = count($dtTest);
                            }else{
                                $dt[] = $v;
                                // 当月累计实际DT订单数
                                $data[$key]['dt'] = count($dt);
                            }
                            break;
                    }
                    $data[$key]['count'] = bcadd($data[$key]['count'],$v['ir_price'],2);
                    $data[$key]['date']  = $value;
                }
            }
        }
        // p($data);die;
        foreach($data as $key=>$value){
            $newData[] = $value;
        }
        foreach($newData as $key=>$value){
            if(empty($value['signup']) && $key == 0){
                $newData[$key]['signup'] = 0;
            }else{
                if(empty($value['signup'])){
                    $newData[$key]['signup'] = $newData[$key-1]['signup'];
                }
            }

            if(empty($value['signupTest']) && $key == 0){
                $newData[$key]['signupTest'] = 0;
            }else{
                if(empty($value['signupTest'])){
                    $newData[$key]['signupTest'] = $newData[$key-1]['signupTest'];
                }
            }

            if(empty($value['monthly']) && $key == 0){
                $newData[$key]['monthly'] = 0;
            }else{
                if(empty($value['monthly'])){
                    $newData[$key]['monthly'] = $newData[$key-1]['monthly'];
                }
            }

            if(empty($value['monthlyTest']) && $key == 0){
                $newData[$key]['monthlyTest'] = 0;
            }else{
                if(empty($value['monthlyTest'])){
                    $newData[$key]['monthlyTest'] = $newData[$key-1]['monthlyTest'];
                }
            }

            if(empty($value['dt']) && $key == 0){
                $newData[$key]['dt'] = 0;
            }else{
                if(empty($value['dt'])){
                    $newData[$key]['dt'] = $newData[$key-1]['dt'];
                }
            }

            if(empty($value['dtTest']) && $key == 0){
                $newData[$key]['dtTest'] = 0;
            }else{
                if(empty($value['dtTest'])){
                    $newData[$key]['dtTest'] = $newData[$key-1]['dtTest'];
                }
            }
        }
        // p($newData);die;
        for($i=0;$i<count($newData);$i++){
            // 当月实际注册人数
            $newData[$i]['signs'] = abs(bcsub($newData[$i]['signup'],$newData[$i-1]['signup'],0));
            // 当月测试注册人数
            $newData[$i]['signTests'] = abs(bcsub($newData[$i]['signupTest'],$newData[$i-1]['signupTest'],0));
            // 当月总注册人数
            $newData[$i]['fullsignup'] = bcadd($newData[$i]['signup'],$newData[$i]['signupTest'],0);

            // 当月实际月费人数
            $newData[$i]['monthlys'] = abs(bcsub($newData[$i]['monthly'],$newData[$i-1]['monthly'],0));
            // 当月测试月费人数
            $newData[$i]['monthlyTests'] = abs(bcsub($newData[$i]['monthlyTest'],$newData[$i-1]['monthlyTest'],0));
            // 当月总月费人数
            $newData[$i]['fullmonthly'] = bcadd($newData[$i]['monthly'],$newData[$i]['monthlyTest'],0);

            // 当月实际DT人数
            $newData[$i]['dts'] = abs(bcsub($newData[$i]['dt'],$newData[$i-1]['dt'],0));
            // 当月测试DT人数
            $newData[$i]['dtTests'] = abs(bcsub($newData[$i]['dtTest'],$newData[$i-1]['dtTest'],0));
        }
        // p($newData);die;
        //排序
        $die = array_sort($newData,'date','desc');
        $data=array(
            'data'=>$die,
        );
        return $data;
    }

    /**
    *第三方支付某月的综合报表 $model $app $paytype支付方式
    **/
    public function getAllFullDayPay($model,$date,$paytype,$unArr){
        $type = implode(',',$paytype);
        $j    = date("t",strtotime($date)); //获取当前月份天数
        $start_time  = strtotime($date.'-'.'01');  //获取本月第一天时间戳
        $day = array();
        for($i=0;$i<$j;$i++){
            $day[] = date('Y-m-d',$start_time+$i*86400); //每隔一天赋值给数组
        }
        $starttime   = strtotime($day[0]);
        $endtime     = strtotime("+1 day",strtotime($day[$j-1]));
        $list = $model
                    ->alias('rs')
                    ->join('hapylife_receipt AS r ON rs.ir_receiptnum = r.ir_receiptnum')
                    ->where(array('status'=>2,'ir_paytype'=>array('in',$type),'paytime'=>array(array('egt',$starttime),array('elt',$endtime))))
                    ->field('paytime,ir_ordertype,rs.ir_price,r.ia_name')
                    ->select();
        foreach($day as $key => $value){
            foreach ($list as $k => $v){
                if(date('Y-m-d',$v['paytime'])==$value){
                    switch($v['ir_ordertype']){
                        case '1':
                            if(in_array($v['ia_name'],$unArr)){
                                $signupTest[] = $v;
                                // 当月累计测试注册人数
                                $data[$key]['signupTest'] = count($signupTest);
                            }else{
                                $signup[] = $v;
                                // 当月累计实际注册人数
                                $data[$key]['signup'] = count($signup);
                            }
                            break;
                        case '3':
                            if(in_array($v['ia_name'],$unArr)){
                                $monthlyTest[] = $v;
                                // 当月累计测试月费人数
                                $data[$key]['monthlyTest'] = count($monthlyTest);
                            }else{
                                $monthly[] = $v;
                                // 当月累计实际月费人数
                                $data[$key]['monthly'] = count($monthly);
                            }
                            break;
                        case '5':
                            if(in_array($v['ia_name'],$unArr)){
                                $dtTest[] = $v;
                                // 当月累计测试DT订单数
                                $data[$key]['dtTest'] = count($dtTest);
                            }else{
                                $dt[] = $v;
                                // 当月累计实际DT订单数
                                $data[$key]['dt'] = count($dt);
                            }
                            break;
                    }
                    $data[$key]['count'] = bcadd($data[$key]['count'],$v['ir_price'],2);
                    $data[$key]['date']  = $value;
                }
            }
        }
        // p($data);
        foreach($data as $key=>$value){
            $newData[] = $value;
        }
        foreach($newData as $key=>$value){
            if(empty($value['signup']) && $key == 0){
                $newData[$key]['signup'] = 0;
            }else{
                if(empty($value['signup'])){
                    $newData[$key]['signup'] = $newData[$key-1]['signup'];
                }
            }

            if(empty($value['signupTest']) && $key == 0){
                $newData[$key]['signupTest'] = 0;
            }else{
                if(empty($value['signupTest'])){
                    $newData[$key]['signupTest'] = $newData[$key-1]['signupTest'];
                }
            }

            if(empty($value['monthly']) && $key == 0){
                $newData[$key]['monthly'] = 0;
            }else{
                if(empty($value['monthly'])){
                    $newData[$key]['monthly'] = $newData[$key-1]['monthly'];
                }
            }

            if(empty($value['monthlyTest']) && $key == 0){
                $newData[$key]['monthlyTest'] = 0;
            }else{
                if(empty($value['monthlyTest'])){
                    $newData[$key]['monthlyTest'] = $newData[$key-1]['monthlyTest'];
                }
            }

            if(empty($value['dt']) && $key == 0){
                $newData[$key]['dt'] = 0;
            }else{
                if(empty($value['dt'])){
                    $newData[$key]['dt'] = $newData[$key-1]['dt'];
                }
            }

            if(empty($value['dtTest']) && $key == 0){
                $newData[$key]['dtTest'] = 0;
            }else{
                if(empty($value['dtTest'])){
                    $newData[$key]['dtTest'] = $newData[$key-1]['dtTest'];
                }
            }
        }
        // p($newData);die;
        for($i=0;$i<count($newData);$i++){
            // 当月实际注册人数
            $newData[$i]['signs'] = abs(bcsub($newData[$i]['signup'],$newData[$i-1]['signup'],0));
            // 当月测试注册人数
            $newData[$i]['signTests'] = abs(bcsub($newData[$i]['signupTest'],$newData[$i-1]['signupTest'],0));
            // 当月总注册人数
            $newData[$i]['fullsignup'] = bcadd($newData[$i]['signup'],$newData[$i]['signupTest'],0);
            

            // 当月实际月费人数
            $newData[$i]['monthlys'] = abs(bcsub($newData[$i]['monthly'],$newData[$i-1]['monthly'],0));
            // 当月测试月费人数
            $newData[$i]['monthlyTests'] = abs(bcsub($newData[$i]['monthlyTest'],$newData[$i-1]['monthlyTest'],0));
            // 当月总月费人数
            $newData[$i]['fullmonthly'] = bcadd($newData[$i]['monthly'],$newData[$i]['monthlyTest'],0);

            // 当月实际DT人数
            $newData[$i]['dts'] = abs(bcsub($newData[$i]['dt'],$newData[$i-1]['dt'],0));
            // 当月测试DT人数
            $newData[$i]['dtTests'] = abs(bcsub($newData[$i]['dtTest'],$newData[$i-1]['dtTest'],0));
            
        }
        // p($newData);
        // //排序
        $die = array_sort($newData,'date','desc');
        $data=array(
            'data'=>$die,
        );
        return $data;
    }
    /**
    *第三方支付某天流水 $model $app $paytype支付方式
    **/
    public function getAllFullDayPayInfo($model,$date,$paytype,$word,$unArr,$limit='50'){
        $type = implode(',',$paytype);
        $starttime   = strtotime($date);
        $endtime     = strtotime("+1 day",strtotime($date));
        if($word['ir_receiptnum']){
            $where['r.ir_receiptnum'] = $word['ir_receiptnum'];
        }
        if($word['customerid']){
            $where['u.customerid'] = $word['customerid'];
        }
        $data = $model
                    ->alias('r')
                    ->join('hapylife_user AS u ON r.riuid = u.iuid')
                    ->where($where)
                    ->where(array('status'=>2,'ir_paytype'=>array('in',$type),'paytime'=>array(array('egt',$starttime),array('elt',$endtime))))
                    ->select();
        //排序
        $die   = array_sort($data,'paytime','desc');
        $count = count($die);
        $page  = new_page($count,$limit);;
        $lists = array_slice($die, $page->firstRow,$page->listRows);
        $data=array(
            'data'=>$lists,
            'page'=>$page->show()
        );
        return $data;
    }
}