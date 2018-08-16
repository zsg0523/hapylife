<?php
namespace Common\Model;
use Think\Model;
/**
* poLine model
**/
class PurchaseOrderLineModel extends BaseModel{
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
        $list=$model
        	->alias('pol')
        	->join('product p on pol.pid=p.pid')
        	->join('vendor v on pol.vid=v.vid')
            ->join('warehouse w on pol.wid=w.wid')
            ->field('po_lid,po_order_item,p.pid,po_product_quantity,po_product_quantity*product_price as total_price,po_import_price,w.wid,icid,product_name,product_number,product_price,product_picture,product_inventory,cid,wh_number,wh_address,wh_inventory')
            ->where($map)
            ->order($order)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();         
        
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }












}