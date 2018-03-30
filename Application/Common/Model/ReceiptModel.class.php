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
    public function getPage($model,$word,$order='',$status,$starttime,$endtime,$limit=20){

        switch ($status) {
        	case '-1':
		        if(empty($word)){
					$count=$model->where(array('ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))->count();
				}else{
					$count=$model
					->alias('r')
			        ->join('left join hapylife_user u on r.iuid = u.iuid')
		            ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->field($field)
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();
			                p($list);die;        
			        }
		        }else{
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			                ->field($field)
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }
        		break;
        	default:
        		if(empty($word)){
					$count=$model
						->where(array('ir_status'=>$status,'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
						->count();
				}else{
					$count=$model
					->alias('r')
			        ->join('left join hapylife_user u on r.iuid = u.iuid')
		            ->where(array('ir_status'=>$status,'r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_status'=>$status,'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_status'=>$status,'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->field($field)
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }else{
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			                ->where(array('ir_status'=>$status,'r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			                ->field($field)
			                ->where(array('ir_status'=>$status,'r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%'),'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }
        		break;
        }
	        
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }


    public function export_excel($data){
		$title   = array('用户ID','订单号','畅捷订单号','畅捷订单状态','订单状态','订单总积分','订单总价','收货人','联系电话','收货地址','创建日期','创建时间');
		foreach ($data as $k => $v) {
			$content[$k]['customerid']     = $v['customerid'];
			$content[$k]['ir_receiptnum']  = $v['ir_receiptnum'];
			$content[$k]['inner_trade_no'] = $v['inner_trade_no'];
			$content[$k]['trade_status']   = $v['trade_status'];
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
			$content[$k]['ir_date']        = date('Y-m-d',$v['ir_date']);
			$content[$k]['ir_time']        = date('H:i:s',$v['ir_date']);
		}
    	create_csv($content,$title);
		return;
    }

  	

}