<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 章节model
**/
class CouponModel extends BaseModel{
	/**
     * 获取分页数据
     * @param  subject  $model  model对象
     * @param  array    $word   搜索关键词
     * @param  string   $order  排序规则
     * @param  integer  $limit  每页数量
     * @param  integer  $field  $field
     * @return array            分页数据
     */
    public function getPage($model,$word,$starttime,$endtime,$status,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('c')
			->join('hapylife_coupon_groups AS g ON c.id = g.cid')
            ->where(array('c.name|g.c_name'=>array('like','%'.$word.'%'),'g.start_time|g.end_time'=>array(array('egt',$starttime),array('elt',$endtime)),'c.id'=>array('in',$status)))
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('c')
				->join('hapylife_coupon_groups AS g ON c.id = g.cid')
                ->where(array('c.name|g.c_name'=>array('like','%'.$word.'%'),'g.start_time|g.end_time'=>array(array('egt',$starttime),array('elt',$endtime)),'c.id'=>array('in',$status)))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
            	->alias('c')
				->join('hapylife_coupon_groups AS g ON c.id = g.cid')
                ->field($field)
                ->where(array('c.name|g.c_name'=>array('like','%'.$word.'%'),'g.start_time|g.end_time'=>array(array('egt',$starttime),array('elt',$endtime)),'c.id'=>array('in',$status)))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }
        // p($status);
        // p($list);die;
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }
}