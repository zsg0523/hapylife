<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 退货model
**/
class SaleReturnModel extends BaseModel{
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
        if(!empty($starttime) && empty($endtime)){
            if(empty($word)){
                $count=$model
                    ->alias('sr')
                    ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                    ->join('hapylife_product AS p ON r.ipid = p.ipid')
                    ->where(array($timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                    ->count();
            }else{
                $count=$model
                    ->alias('sr')
                    ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                    ->join('hapylife_product AS p ON r.ipid = p.ipid')
                    ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                    ->count();
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($word)){
                $count=$model
                    ->alias('sr')
                    ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                    ->join('hapylife_product AS p ON r.ipid = p.ipid')
                    ->where(array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                    ->count();
            }else{
                $count=$model
                    ->alias('sr')
                    ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                    ->join('hapylife_product AS p ON r.ipid = p.ipid')
                    ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                    ->count();
            }
        }
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if(!empty($starttime) && empty($endtime)){
            if(empty($field)){
                if(empty($word)){
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->where(array($timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }else{
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }
            }else{
                if(empty($word)){
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->field($field)
                        ->where(array($timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }else{
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->field($field)
                        ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array('egt',$starttime),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }
            }
        }else if(!empty($starttime) && !empty($endtime)){
            if(empty($field)){
                if(empty($word)){
                    $list=$model
                       ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->where(array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }else{
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }
            }else{
                if(empty($word)){
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->field($field)
                        ->where(array($timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }else{
                    $list=$model
                        ->alias('sr')
                        ->join('hapylife_receipt AS r ON sr.rir_receiptnum = r.ir_receiptnum')
                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                        ->field($field)
                        ->where(array('r.rCustomerID|ir_receiptnum'=>$word,$timeType=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status)))
                        ->order('applytime desc')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
                }
            }
        }
        
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
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
            // $content[$k]['productname'] = $v['productname'];
            switch ($v['ipid']) {
                case '31':
                    $content[$k]['productname'] = $v['productnams'].'*8瓶';
                    break;
                case '39':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '61':
                    $content[$k]['productname'] = $v['productnams'].'*4瓶';
                    break;
                case '62':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                case '64':
                    $content[$k]['productname'] = $v['productnams'].'*2瓶';
                    break;
                default:
                    $content[$k]['productname'] = $v['productnams'].'*1瓶';
                    break;
            }
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