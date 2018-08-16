<?php
namespace Common\Model;
use Think\Model;
/**
* warehouse model
**/
class WarehouseModel extends BaseModel{
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
        	->alias('w')
        	->join('LEFT JOIN vendor v ON w.wh_vid = v.vid')
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