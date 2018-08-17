<?php
namespace Common\Model;
use Think\Model;
/**
* product model
**/
class ProductModel extends BaseModel{
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
            	->alias('p')
            	->join('left join vendor v on p.vid=v.vid')
                ->join('left join category c on p.icid=c.id')
                ->where($map)
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }else{
            $list=$model
            	->alias('p')
            	->join('left join vendor v on p.vid=v.vid')
                ->join('left join category c on p.icid=c.id')
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