<?php
namespace Common\Model;
use Think\Model;
/**
* RsvShipmentHead model
**/
class RsvShipmentHeadModel extends BaseModel{
	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$starttime,$endtime,$limit=10,$field=''){
        $count=$model
            ->where($map)
            ->count();
        $page=new_page($count,$limit);
        // 获取分页数据
        $list=$model
        	->alias('rsvh')
        	->join('purchase_order_head poh on poh.po_order_item=rsvh.rs_po_item')
            ->join('staff s on poh.po_order_buyer = s.sid')
            ->join('warehouse w on rsvh.rs_wid=w.wid')
            ->where(array('rs_po_item|rs_item|wh_number|wh_address'=>array('like','%'.$word.'%'),'rs_create_time'=>array(array('egt',$starttime),array('elt',$endtime))))
            ->order($order)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();         
        
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }



    /**
    * 订单导出excel
    **/
    public function export_excel($data){
        $title   = array('rsv item','po item','total quantity','rsv quantity','wh','wh_address','create time');
        foreach ($data as $k => $v) {
            $content[$k]['rs_item']            = $v['rs_item'];
            $content[$k]['rs_po_item']         = $v['rs_po_item'];
            $content[$k]['po_quantity_total']  = $v['po_quantity_total'];
            $content[$k]['rsv_quantity_total'] = $v['rsv_quantity_total'];
            $content[$k]['wh_number']          = $v['wh_number'];
            $content[$k]['wh_address']         = $v['wh_address'];
            $content[$k]['rs_create_date']     = date('Y-m-d',$v['rs_create_date']);
        }
        create_csv($content,$title);
        return;
    }
















}