<?php
namespace Common\Model;
use Think\Model;
/**
* poHead model
**/
class PurchaseOrderHeadModel extends BaseModel{
	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $map    where条件
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$starttime,$endtime,$limit=50){
        $count=$model
            ->where($map)
            ->count();
        $page=new_page($count,$limit);
        // 获取分页数据
        $list=$model
        	->alias('poh')
        	->join('purchase_order_line pol on poh.po_order_item = pol.po_order_item')
        	->join('staff s on poh.po_order_buyer = s.sid')
        	->join('product p on p.pid = pol.pid')
			->join('vendor v on v.vid = pol.vid')
			->field('poh.po_order_item,po_order_description,sum(po_product_quantity) as po_quantity_total,sum(po_product_quantity*product_price) as po_total_price,sum(po_product_quantity*po_import_price) as cost_price,firstname,lastname,po_eta_date,po_rec_date,po_create_date,is_cashier,vendor_name,vendor_number,po_ship_way')
			->group('poh.po_order_item')
            ->where(array('poh.po_order_item|po_order_description|firstname|lastname'=>array('like','%'.$word.'%'),'po_create_date|po_eta_date|po_rec_date'=>array(array('egt',$starttime),array('elt',$endtime))))
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
    * 获取单个po详细信息
    **/
    public function getData($word){
    	$data = $this
    		->alias('poh')
        	->join('purchase_order_line pol on poh.po_order_item = pol.po_order_item')
        	->join('staff s on poh.po_order_buyer = s.sid')
        	->join('product p on p.pid = pol.pid')
			->join('vendor v on v.vid = pol.vid')
			->field('poh.po_order_item,po_order_description,sum(po_product_quantity) as po_quantity_total,sum(po_product_quantity*product_price) as po_total_price,sum(po_product_quantity*po_import_price) as cost_price,firstname,lastname,po_eta_date,po_rec_date,po_create_date,is_cashier,vendor_name,vendor_number,contact,street,city,v.phone,fax,po_ship_way')
			->group('poh.po_order_item')
            ->where(array('poh.po_order_item|po_order_description|firstname|lastname'=>array('like','%'.$word.'%')))
            ->select();
            return $data;
    }




    /**
    * 订单导出excel
    **/
    public function export_excel($data){
		$title   = array('采购单号','备注','采购人','采购总数','采购总价(总数*单品价)','采购入货价(总数*入货价)','预计到货日期','实际到货日期','创建日期');
		foreach ($data as $k => $v) {
			$content[$k]['po_order_item']        = $v['po_order_item'];
			$content[$k]['po_order_description'] = $v['po_order_description'];
			$content[$k]['name']                 = $v['firstname'].''.$v['lastname'];
			$content[$k]['po_quantity_total']    = $v['po_quantity_total'];
			$content[$k]['po_total_price']       = $v['po_total_price'];
			$content[$k]['cost_price']           = $v['cost_price'];
			$content[$k]['po_eta_date']          = date('Y-m-d',$v['po_eta_date']);
			$content[$k]['po_rec_date']          = date('Y-m-d',$v['po_rec_date']);
			$content[$k]['po_create_date']       = date('Y-m-d',$v['po_create_date']);
		}
    	create_csv($content,$title);
		return;
    }




















}