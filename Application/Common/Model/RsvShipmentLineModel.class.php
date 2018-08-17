<?php
namespace Common\Model;
use Think\Model;
/**
* RsvShipmentLine model
**/
class RsvShipmentLineModel extends BaseModel{
	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$map,$order='',$limit=10,$field=''){
        $count=$model
            ->where($map)
            ->count();
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('rsvl')
                //验货单表头关联验货单表行
                ->join('rsv_shipment_head rsvh on rsvl.rsl_item = rsvh.rs_item')
                //采购单表头关联验货单表头
            	->join('purchase_order_head pol on rsvh.rs_po_item = pol.po_order_item')
                //物料信息
            	->join('product p on rsvl.pid=p.pid')
                //该物料所属供应商信息
            	->join('vendor v on p.vid=v.vid')
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }else{
            $list=$model
            	->alias('rsvl')
                ->join('rsv_shipment_head rsvh on rsvl.rsl_item = rsvh.rs_item')
            	->join('purchase_order_head pol on rsvh.rs_po_item=pol.po_order_item')
            	->join('product p on rsvl.pid=p.pid')
            	->join('vendor v on p.vid=v.vid')
                ->field($field)
                ->where($map)
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