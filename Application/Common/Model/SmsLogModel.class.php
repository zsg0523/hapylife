<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 订单model
**/
class SmsLogModel extends BaseModel{
    /**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $word   搜索关键词
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getSendPage($model,$customerid,$phone,$addressee,$starttime,$endtime,$order='',$limit=50,$field=''){
        if($customerid){
            $where['customerid'] = array('like','%'.$customerid.'%');
        }
        if($phone){
            $where['phone'] = array('like','%'.$phone.'%');
        }
        if($addressee){
            $where['addressee'] = array('like','%'.$phone.'%');
        }
        $count=$model
            ->where($where)
            ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
            ->count();
        // p($count);
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
                ->where($where)
                ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
                ->field($field)
                ->where($where)
                ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
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