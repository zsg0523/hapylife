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
    public function getPage($model,$word,$order='',$status='-1',$limit=20){

        switch ($status) {
        	case '-1':
		        if(empty($word)){
					$count=$model->count();
				}else{
					$count=$model
					->alias('r')
			        ->join('left join hapylife_user u on r.iuid = u.iuid')
		            ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('hapylife_user u on r.iuid = u.iuid')
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
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('hapylife_user u on r.iuid = u.iuid')
			                ->field($field)
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }
		        }
        		break;
        	default:
        		if(empty($word)){
					$count=$model->where(array('ir_status'=>$status))->count();
				}else{
					$count=$model
					->alias('r')
			        ->join('left join hapylife_user u on r.iuid = u.iuid')
		            ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
		            ->where(array('ir_status'=>$status))
		            ->count();
				}
		        $page=new_page($count,$limit);
		        // 获取分页数据
		        if(empty($word)){
		        	if (empty($field)) {
			            $list=$model
			            	->alias('r')
			            	->join('left join hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_status'=>$status))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('hapylife_user u on r.iuid = u.iuid')
			            	->where(array('ir_status'=>$status))
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
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
			                ->where(array('ir_status'=>$status))
			                ->order($order)
			                ->limit($page->firstRow.','.$page->listRows)
			                ->select();         
			        }else{
			            $list=$model
			            	->alias('r')
			            	->join('hapylife_user u on r.iuid = u.iuid')
			                ->field($field)
			                ->where(array('r.CustomerID|ir_receiptnum|inner_trade_no|trade_status|ir_price|u.LastName|u.FirstName'=>array('like','%'.$word.'%')))
			                ->where(array('ir_status'=>$status))
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

}