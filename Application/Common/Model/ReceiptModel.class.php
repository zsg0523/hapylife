<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 订单model
**/
class ReceiptModel extends BaseModel{
    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$starttime,$endtime,$status,$order='',$timeType,$limit=20){
        $count=$model
            ->alias('r')
            ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
            ->where(array('ir_status'=>array('in',$status),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime))))
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->where(array('ir_status'=>array('in',$status),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime))))
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->where(array('ir_status'=>array('in',$status),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime))))
                ->select();         
        }

        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $word   搜索关键词
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getSendPage($model,$word,$starttime,$endtime,$ir_status,$timeType,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('hapylife_user u on r.riuid = u.iuid')
            ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                ->order('ir_paytime desc')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                ->order('ir_paytime desc')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }
        // p($list);
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
        // p($mape);
        $data=array(
            'data'=>$mape,
            'page'=>$page->show()
            );
        return $data;
    }

    /**
    * 订单管理导出数据查询
    **/
    public function getAllSendData($model,$word,$starttime,$endtime,$ir_status,$timeType,$order='',$field=''){
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                ->order('ir_paytime desc')
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
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

    /**
    * 送货单管理导出数据查询
    **/
    public function getAllData($model,$word,$starttime,$endtime,$ir_status,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('hapylife_user u on r.riuid = u.iuid')
            ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
            ->count();
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
                ->order('ir_paytime desc')
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
                ->order('ir_paytime desc')
                ->select();         
        }
        return $list;
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  string   $ir_receiptnum   父订单号
     * @return array            分页数据
     */
    public function getSendPageSonAll($model,$word,$starttime,$endtime,$ir_status,$timeType,$order='',$field=''){
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                ->order('ir_paytime desc')
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
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

    /**
    * 送货单导出excel
    **/
    public function export_send_excel($data){
        $title   = array('用户ID','订单号','订单状态','流水号','流水方式','产品信息','订单总价','订货人','收货人','收货地址','收货人电话','创建日期','创建时间','支付日期','支付时间','发货日期','送达日期');
        foreach ($data as $k => $v) {
            $content[$k]['rcustomerid']      = $v['rcustomerid'];
            $content[$k]['ir_receiptnum']    = $v['ir_receiptnum'];
            $content[$k]['receiptson']  = $v['receiptson'];
            $content[$k]['paytype']  = $v['paytype'];
            switch ($v['ir_status']) {
                case '0':
                    $content[$k]['ir_status'] = '待付款';
                    break;
                case '202':
                    $content[$k]['ir_status'] = '未全额付款';
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
            }
            // 产品信息
            $content[$k]['ir_desc']              = $v['ir_desc'];
            $content[$k]['ir_price']             = $v['ir_price'];
            //订货人
            $content[$k]['ia_name']              = $v['lastname'].$v['firstname'];
            //收货人
            $content[$k]['ib_name']              = $v['ia_name'];
            //收货详细地址
            $content[$k]['ia_address']           = $v['shopprovince'].$v['shopcity'].$v['shoparea'].$v['shopaddress1'];
            //收货人电话
            $content[$k]['ia_phone']             = $v['ia_phone'];
            // 创建日期
            $content[$k]['ir_datetime']          = date('Y-m-d',$v['ir_date']);
            // 创建时间
            $content[$k]['ir_datetimes']         = date('H:i:s',$v['ir_date']);
            // 支付日期
            $content[$k]['ir_paytime']           = date('Y-m-d',$v['ir_paytime']);
            // 支付时间 
            $content[$k]['ir_paytimes']          = date('H:i:s',$v['ir_paytime']);

            if(empty($v['send_time'])){
                $content[$k]['send_time']        = '暂未发货';
                $content[$k]['receive_time']     = '暂未发货';
            }else{
                $content[$k]['send_time']        = date('Y-m-d H:i:s',$v['send_time']);
                if(empty($v['receive_time'])){
                    $content[$k]['receive_time'] = '暂未收货';
                }else{
                    $content[$k]['receive_time'] = date('Y-m-d H:i:s',$v['receive_time']);
                }
            }
        }
        create_csv($content,$title);
        return;
    }


    

}