<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 订单model
**/
class IbosReceiptModel extends BaseModel{
	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $word   搜索关键词
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$starttime,$endtime,$ir_status,$ir_area,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('nulife_ibos_users u on r.iuid = u.iuid ')
            ->where(
            	array(
            		'u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),
            		'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),
            		'ir_status'=>array('in',$ir_status),
            		'ir_area'=>$ir_area
            		)
            	)
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('r')
            	->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
            	->alias('r')
            	->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }
        foreach ($list as $key => $value) {
            $mape[$key] = $value;
            $arr = D('IbosReceiptlist')
                    ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
                    ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                    ->select();
            $productname = '';
            foreach ($arr as $k => $v) {
                $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
            }
            $mape[$key]['productname'] = substr($productname,0,-1);
        }
        // p($mape);die;
        $data=array(
            'data'=>$mape,
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
    public function getSendPage($model,$word,$starttime,$endtime,$ir_status,$ir_area,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('nulife_ibos_users u on r.iuid = u.iuid ')
            ->where(
                array(
                    'u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),
                    'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),
                    'ir_status'=>array('in',$ir_status),
                    'ir_area'=>$ir_area,
                    'ir_change'=>1
                    )
                )
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area,'ir_change'=>1))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area,'ir_change'=>1))
                ->order($order)
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
            $arr = D('IbosReceiptlist')
                    ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
                    ->where(array('ir_receiptnum'=>$value['ir_receiptnum']))
                    ->select();
            $productname = '';
            foreach ($arr as $k => $v) {
                $productname .= $v['product_name'].'(*'.$v['product_num'].'),';
            }
            $mape[$key]['productname'] = substr($productname,0,-1);
            $mape[$key]['productno']   = $v['productno'];
        }
        $data=array(
            'data'=>$mape,
            'page'=>$page->show()
            );
        return $data;
    }


    /**
    * 订单管理导出数据查询
    **/
    public function getAllSendData($model,$word,$starttime,$endtime,$ir_status,$ir_area,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('nulife_ibos_users u on r.iuid = u.iuid ')
            ->where(
            	array(
            		'u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),
            		'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),
            		'ir_status'=>array('in',$ir_status),
            		'ir_area'=>$ir_area,
                    'ir_change'=>1
            		)
            	)
            ->count();
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('r')
            	->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area,'ir_change'=>1))
                ->order($order)
                ->select();
        }else{
            $list=$model
            	->alias('r')
            	->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area,'ir_change'=>1))
                ->order($order)
                ->select();         
        }
        return $list;
    }

    /**
    * 送货单管理导出数据查询
    **/
    public function getAllData($model,$word,$starttime,$endtime,$ir_status,$ir_area,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('nulife_ibos_users u on r.iuid = u.iuid ')
            ->where(
                array(
                    'u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),
                    'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),
                    'ir_status'=>array('in',$ir_status),
                    'ir_area'=>$ir_area
                    )
                )
            ->count();
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->alias('r')
                ->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area))
                ->order($order)
                ->select();
        }else{
            $list=$model
                ->alias('r')
                ->join('nulife_ibos_users u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_area'=>$ir_area))
                ->order($order)
                ->select();         
        }
        return $list;
    }

    /**
    * 订单导出excel
    **/
    public function export_excel($data){
		$title   = array('用户ID','订单号','360订单ID','360订单编号','订单状态','订单总积分','订单总价','收货人','联系电话','收货地址','创建日期','创建时间');
		foreach ($data as $k => $v) {
			$content[$k]['hu_nickname']     = $v['hu_nickname'];
			$content[$k]['ir_receiptnum']   = $v['ir_receiptnum'];
			$content[$k]['ir_360_orderId']  = $v['ir_360_orderid'];
			$content[$k]['ir_360_orderNo']  = $v['ir_360_orderno'];
			switch ($v['ir_status']) {
				case '0':
					$content[$k]['ir_status'] = '待付款';
					break;
				case '2':
					$content[$k]['ir_status'] = '已支付';
					break;
				case '3':
					$content[$k]['ir_status'] = '交易完成';
					break;
			}
			$content[$k]['ir_point']       = $v['ir_point'];
			$content[$k]['ir_price']       = $v['ir_price'];
			$content[$k]['ia_name']        = $v['ia_name'];
			$content[$k]['ia_phone']       = $v['ia_phone'];
			$content[$k]['ia_address']     = $v['ia_address'];
			$content[$k]['create_time']    = date('Y-m-d',$v['create_time']);
			$content[$k]['ir_time']        = date('H:i:s',$v['create_time']);
		}
    	create_csv($content,$title);
		return;
    }

    /**
    * 送货单导出excel
    **/
    public function export_send_excel($data){
        $title   = array('用户ID','姓名','订单编号','360订单ID','360订单编号','总金额','总积分','支付类型','备注','订单状态','收货人','收货电话','收货地址','创建日期','支付日期','发货日期','送达日期');
        foreach ($data as $k => $v) {
            $content[$k]['hu_nickname']     = $v['hu_nickname'];
            $content[$k]['hu_username']     = $v['hu_username'];
            $content[$k]['ir_receiptnum']   = $v['ir_receiptnum'];
            $content[$k]['ir_360_orderId']  = $v['ir_360_orderid'];
            $content[$k]['ir_360_orderNo']  = $v['ir_360_orderno'];
            $content[$k]['ir_price']        = $v['ir_price'];
            $content[$k]['ir_point']        = $v['ir_point'];
            switch ($v['ir_paytype']) {
                case '0':
                    $content[$k]['ir_paytype'] = '未支付';
                    break;
                case '1':
                    $content[$k]['ir_paytype'] = '微信支付';
                    break;
                case '2':
                    $content[$k]['ir_paytype'] = '积分支付';
                    break;
                case '3':
                    $content[$k]['ir_paytype'] = '单据转账';
                    break;
                case '4':
                    $content[$k]['ir_paytype'] = '快钱支付';
                    break;
            }
            $content[$k]['ir_desc']        = $v['ir_desc'];
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
            }
            $content[$k]['ia_name']      = $v['ia_name'];
            $content[$k]['ia_phone']     = $v['ia_phone'];
            $content[$k]['ia_address']   = $v['ia_address'];
            $content[$k]['create_time']  = date('Y-m-d H:i:s',$v['create_time']);
            $content[$k]['to360_time']   = date('Y-m-d H:i:s',$v['to360_time']);
            if($content[$k]['send_time'] == null){
                $content[$k]['send_time'] = '暂未发货';
            }else{
                $content[$k]['send_time'] = date('Y-m-d H:i:s',$v['send_time']);
            }
            if($content[$k]['receive_time'] == null){
                $content[$k]['receive_time'] = '暂未发货';
            }else{
                $content[$k]['receive_time'] = date('Y-m-d H:i:s',$v['receive_time']);
            }
        }
        create_csv($content,$title);
        return;
    }


	
}