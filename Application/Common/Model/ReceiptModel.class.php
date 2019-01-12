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
    public function getPage($model,$ir_receiptnum,$customerid,$lastname,$firstname,$ir_price,$ir_unpaid,$starttime,$endtime,$status,$order='',$timeType,$limit=20){
        
        if(!empty($starttime) && empty($endtime)){
            $where = array($timeType=>array('egt',$starttime));
        }else if(!empty($starttime) && !empty($endtime)){
            $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
        }else if(empty($starttime) && empty($endtime)){
            $where = array();
        }
        
        if($ir_receiptnum){
            $where['ir_receiptnum'] = array('like','%'.$ir_receiptnum.'%');
        }
        if($customerid){
            $where['r.rCustomerID'] = array('like','%'.$customerid.'%');
        }
        if($lastname){
            $where['u.LastName'] = array('like','%'.$lastname.'%');
        }
        if($firstname){
            $where['u.FirstName'] = array('like','%'.$firstname.'%');
        }
        if($ir_price){
            $where['ir_price'] = array('like','%'.$ir_price.'%');
        }
        if($ir_unpaid){
            $where['ir_unpaid'] = array('like','%'.$ir_unpaid.'%');
        }

        if($where){
            $count=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$status)))
                    ->where($where)
                    ->count();
            // p($count);die;
            $page=new_page($count,$limit);
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$status)))
                    ->where($where)
                    ->order($order)
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$status)))
                    ->where($where)
                    ->order($order)
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();         
            }

            $data=array(
                'data'=>$list,
                'page'=>$page->show()
            );
            return $data;
        }

    }
    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function FinanceGetPage($model,$word,$starttime,$endtime,$status,$test,$timeType,$order='',$limit=20){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'),$timeType=>array('egt',$starttime));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }

        if($where){
            $count=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$status),'r.ia_name'=>array('NOT IN',$test)))
                    ->where($where)
                    ->count();

            $page=new_page($count,$limit);
            // 获取分页数据
            if(empty($field)){
                    $list=$model
                        ->alias('r')
                        ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                        ->where(array('r.ia_name'=>array('NOT IN',$test)))
                        ->where($where)
                        ->order($order)
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }else{
                    $list=$model
                        ->alias('r')
                        ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                        ->field($field)
                        ->where(array('ir_status'=>array('in',$status),'r.ia_name'=>array('NOT IN',$test)))
                        ->order($order)
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();         
                }
            
            foreach ($list as $key => $value) {
                $arr = D('Receiptlist')
                        ->join('hapylife_product on hapylife_receiptlist.ipid = hapylife_product.ipid')
                        ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                        ->select();
                if($value['coucode']){
                    $data = array(
                        'coupon_code' => $value['coucode'],
                    );
                    $data    = json_encode($data);
                    $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/codeGetCouponInfo";
                    // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/codeGetCouponInfo";
                    $result  = post_json_data($sendUrl,$data);
                    $back_message = json_decode($result['result'],true);
                    $list[$key]['operator'] = $back_message['operator'];
                }else{
                    $list[$key]['operator'] = '';
                }
                $productname = '';
                foreach ($arr as $k => $v) {
                    $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
                    $list[$key]['productno']   = $v['ip_bh'];
                    $list[$key]['productnams'] = $v['product_name'];
                }
                $list[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    switch ($v['ir_paytype']) {
                        case '1':
                            $ir_paytype .= 'IPS'.'<br/>';
                            $receiptson .= $v['ips_trade_no'].'<br/>';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            // $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.'<br/>';
                            $receiptson .= $v['inner_trade_no'].'<br/>';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                    }
                }
                $list[$key]['receiptson']  = substr($receiptson,0,-1);
                $list[$key]['paytype']     = substr($ir_paytype,0,-1);
            }
            // p($list);die;
            $data=array(
                'data'=>$list,
                'page'=>$page->show()
                );
            return $data;
        }
        
        
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
    public function getSendPage($model,$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='',$limit=50,$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }
        if($where){
            $count=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
                    ->count();
            // p($count);die;
            $page=new_page($count,$limit);
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);

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
    public function getSendPages($model,$word,$starttime,$endtime,$ir_status,$timeType,$array,$ipid,$order='',$limit=50,$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }

        if($where){
            $count=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>array('NOT IN',$ipid),'u.LastName|u.FirstName'=>array('NOT IN',$array)))
                    ->where($where)
                    ->count();
            // p($count);die;
            $page=new_page($count,$limit);
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>array('NOT IN',$ipid),'u.LastName|u.FirstName'=>array('NOT IN',$array)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>array('NOT IN',$ipid),'u.LastName|u.FirstName'=>array('NOT IN',$array)))
                    ->where($where)
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
                $productname = '';
                foreach ($arr as $k => $v) {
                    $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);

                $time = D('Receipt')
                            ->alias('r')
                            ->join('hapylife_activation AS a ON r.riuid = a.iuid')
                            ->where(array('r.ir_receiptnum'=>$value['ir_receiptnum']))
                            ->select();
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
    public function getSendPagesearch($model,$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='',$limit=50,$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }

        if($where){
            $count=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ia_name'=>array('NOT IN',$array),'r.ipid'=>48))
                    ->where($where)
                    ->count();
            // p($count);die;
            $page=new_page($count,$limit);
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ia_name'=>array('NOT IN',$array),'r.ipid'=>48))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ia_name'=>array('NOT IN',$array),'r.ipid'=>48))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);

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
                $ir_paid = D('Receipt')
                            ->alias('r')
                            ->join('hapylife_receiptson AS rs ON r.ir_receiptnum = rs.ir_receiptnum')
                            ->where(array('r.ir_receiptnum'=>$value['ir_receiptnum']))
                            ->select();
                            // p($ir_paid);
                foreach($ir_paid as $v){
                    if($v['status'] == 2){
                        $mape[$key]['paid_price'] += $v['ir_price'];
                    }else if($v['status'] == 0){
                        $mape[$key]['paid_price'] += 0;
                    }
                }

            }
            // p($mape);
            // die;
            $data=array(
                'data'=>$mape,
                'page'=>$page->show()
                );
            return $data;
        }
        
    }

    /**
    * 订单管理导出数据查询
    **/
    public function getAllSendData($model,$ir_receiptnum,$customerid,$lastname,$firstname,$ir_price,$ir_unpaid,$starttime,$endtime,$ir_status,$timeType,$order='',$field=''){
        if(!empty($starttime) && empty($endtime)){
            $where = array($timeType=>array('egt',$starttime));
        }else if(!empty($starttime) && !empty($endtime)){
            $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
        }else if(empty($starttime) && empty($endtime)){
            $where = array();
        }

        if($ir_receiptnum){
            $where['ir_receiptnum'] = array('like','%'.$ir_receiptnum.'%');
        }
        if($customerid){
            $where['r.rCustomerID'] = array('like','%'.$customerid.'%');
        }
        if($lastname){
            $where['u.LastName'] = array('like','%'.$lastname.'%');
        }
        if($firstname){
            $where['u.FirstName'] = array('like','%'.$firstname.'%');
        }
        if($ir_price){
            $where['ir_price'] = array('like','%'.$ir_price.'%');
        }
        if($ir_unpaid){
            $where['ir_unpaid'] = array('like','%'.$ir_unpaid.'%');
        }
        // p($where);die;
        if($where){
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    switch ($v['ir_paytype']) {
                       case '1':
                            $ir_paytype .= 'IPS'.'<br/>';
                            $receiptson .= $v['ips_trade_no'].'<br/>';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            // $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.'<br/>';
                            $receiptson .= $v['inner_trade_no'].'<br/>';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
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
        
        
        
    }
    /**
    * 订单管理导出数据查询
    * 去除测试和用券
    **/
    public function FinanceGetAllSendData($model,$word,$starttime,$endtime,$ir_status,$timeType,$test,$order='',$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }
        if($where){
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ia_name'=>array('not in',$test)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ia_name'=>array('not in',$test)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();         
            }

            foreach ($list as $key => $value) {
                $ia_address = '';
                $mape[$key] = $value;
                $address    = explode(' ',$value['ia_address']);
                $mape[$key]['ia_address'] = $ia_address;
                $arr = D('Receiptlist')
                        ->join('hapylife_product on hapylife_receiptlist.ipid = hapylife_product.ipid')
                        ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                        ->select();
                if($value['coucode']){
                    $data = array(
                        'coupon_code' => $value['coucode'],
                    );
                    $data    = json_encode($data);
                    $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/codeGetCouponInfo";
                    // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/codeGetCouponInfo";
                    $result  = post_json_data($sendUrl,$data);
                    $back_message = json_decode($result['result'],true);
                    $mape[$key]['operator'] = $back_message['operator'];
                }else{
                    $mape[$key]['operator'] = '';
                }
                foreach ($address as $k => $v) {
                    $ia_address.= $v;
                }
                $productname = '';
                foreach ($arr as $k => $v) {
                    $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    switch ($v['ir_paytype']) {
                        case '1':
                            $ir_paytype .= 'IPS'.'<br/>';
                            $receiptson .= $v['ips_trade_no'].'<br/>';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            // $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.'<br/>';
                            $receiptson .= $v['inner_trade_no'].'<br/>';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
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
        
    }
    /**
    * 送货单管理导出数据查询
    **/
    public function getAllData($model,$word,$starttime,$endtime,$ir_status,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('hapylife_user u on r.riuid = u.iuid')
            ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
            ->count();
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
                ->order('ir_paytime desc')
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->field($field)
                ->where(array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
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
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }
        if($where){
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status)))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    switch ($v['ir_paytype']) {
                        case '1':
                            $ir_paytype .= 'IPS'.'<br/>';
                            $receiptson .= $v['ips_trade_no'].'<br/>';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            // $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.'<br/>';
                            $receiptson .= $v['inner_trade_no'].'<br/>';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
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
        
        
    }

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  string   $ir_receiptnum   父订单号
     * @return array            分页数据
     */
    public function getSendPageSonAlls($model,$word,$starttime,$endtime,$ir_status,$timeType,$array,$ipid,$order='',$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(!empty($word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }
        if($where){
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>array('NOT IN',$ipid),'u.LastName|u.FirstName'=>array('NOT IN',$array)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>array('NOT IN',$ipid),'u.LastName|u.FirstName'=>array('NOT IN',$array)))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                
                $mape[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    switch ($v['ir_paytype']) {
                        case '1':
                            $ir_paytype .= 'IPS'.'<br/>';
                            $receiptson .= $v['ips_trade_no'].'<br/>';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            // $receiptson .= $v['pay_receiptnum'].'<br/>';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.'<br/>';
                            $receiptson .= $v['inner_trade_no'].'<br/>';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.'<br/>';
                            $receiptson .= $v['pay_receiptnum'].'<br/>';
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
            // p($mape);die;
            $data=array(
                'data'=>$mape,
                );
            return $data;
        }
        }
        

    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  string   $ir_receiptnum   父订单号
     * @return array            分页数据
     */
    public function getSendPageSonAllsearch($model,$word,$starttime,$endtime,$ir_status,$timeType,$array,$order='',$field=''){
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array('egt',$starttime));
            }else{
                $where = array($timeType=>array('egt',$starttime),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)));
            }else{
                $where = array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }
        }else if(empty($starttime) && empty($endtime)){
            if(empty(!$word)){
                $where = array('r.rCustomerID|ir_receiptnum|ir_price|u.LastName|u.FirstName|ir_desc'=>array('like','%'.$word.'%'));
            }else{
                $where = array();
            }
        }
        if($where){
            // 获取分页数据
            if(empty($field)){
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>48,'r.ia_name'=>array('NOT IN',$array)))
                    ->where($where)
                    ->order('ir_paytime desc')
                    ->select();
            }else{
                $list=$model
                    ->alias('r')
                    ->join('LEFT JOIN hapylife_user u on r.riuid = u.iuid')
                    ->field($field)
                    ->where(array('ir_status'=>array('in',$ir_status),'r.ipid'=>48,'r.ia_name'=>array('NOT IN',$array)))
                    ->where($where)
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
                    $mape[$key]['productno']   = $v['ip_bh'];
                    $mape[$key]['productnams'] = $v['product_name'];
                    $mape[$key]['ip_type'] = $v['ip_type'];
                    $mape[$key]['productnum'] = $v['product_name'].'*'.$v['productnum'].'瓶';
                }
                $mape[$key]['productname'] = substr($productname,0,-1);
                $son = D('Receiptson')
                     ->where(array('ir_receiptnum'=>$value['ir_receiptnum'],'status'=>2))
                     ->select();
                $receiptson = '';
                $ir_paytype = '';
                foreach ($son as $k => $v) {
                    $receiptson .= $v['pay_receiptnum'].' /';
                    switch ($v['ir_paytype']) {
                        case '1':
                            $ir_paytype .= 'IPS'.' /';
                            break;
                        case '2':
                            $ir_paytype .= '积分'.' /';
                            break;
                        case '3':
                            // $ir_paytype .= '积分'.'/';
                            break;
                        case '4':
                            $ir_paytype .= '畅捷'.' /';
                            break;
                        case '5':
                            $ir_paytype .= '现金'.' /';
                                break;
                        case '6':
                            $ir_paytype .= '接龙易'.' /';
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
            // p($mape);die;
            $data=array(
                'data'=>$mape,
                );
            return $data;
        }
        
    }

    /**
    * 送货单导出excel
    **/
    public function export_send_excel($data){
        $title   = array('创建日期','订单编号','会员账号','会员姓名','会员电话','团队标签','订单金额','订单备注','商品信息','商品编号','商品数量','支付日期','收货人姓名','收货电话','收货地址');
        foreach ($data as $k => $v) {
            // 创建日期
            $content[$k]['ir_date']      = date('Y-m-d',$v['ir_date']).'/'.date('H:i:s',$v['ir_date']);
            // 订单编号
            $content[$k]['ir_receiptnum']    = $v['ir_receiptnum'];
            // 会员账号
            $content[$k]['rcustomerid']      = $v['rcustomerid'];
            // 会员姓名
            $content[$k]['username']      = $v['lastname'].$v['firstname'];
            // 会员电话
            if(empty($v['phone'])){
                $content[$k]['phone']  = $v['ia_phone'];
            }else{
                $content[$k]['phone']  = $v['phone'];
            }
            // 团队标签
            $content[$k]['teamcode']  = 'ABC';
            // 订单金额
            $content[$k]['ir_price'] = $v['ir_price'];
            // 订单备注
            $content[$k]['ir_desc'] = $v['ir_desc']; 
            // 商品信息
            $content[$k]['productnams'] = $v['productnams'];
            // 商品编号
            $content[$k]['productno'] = $v['productno'];
            // 商品数量
            $content[$k]['productname'] = $v['productnum'];
            // 支付日期
            $content[$k]['ir_paytime'] = date('Y-m-d',$v['ir_paytime']).'/'.date('H:i:s',$v['ir_paytime']);
            // 收货人姓名
            $content[$k]['ia_name'] = $v['ia_name'];
            // 收货电话
            $content[$k]['ia_phone'] = $v['ia_phone'];
            // 收货地址
            if($v['ia_province'] && $v['ia_city'] && $v['ia_area'] && $v['ia_address']){
                $content[$k]['ia_address'] = $v['ia_province'].$v['ia_city'].$v['ia_area'].$v['ia_address'];
            }else{
                $content[$k]['ia_address'] = $v['shopprovince'].$v['shopcity'].$v['shoparea'].$v['shopaddress1'];
            }
        }
        create_csv($content,$title,date('YmdHis',time()));
        return;
    }
}