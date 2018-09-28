<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 奖金计算
**/
class HapylifeBonusController extends HomeBaseController{
	/**
	* 每有3个新会员购买首单进行计算
	**/
	public function FirstPurchaseBonus(){
        $id     = I('post.id')?I('post.id'):"1";  
        //根节点
        $users  = M('Wvusers')
                ->where(array('uid'=>$id))
                ->find();
        if($users['time']){
            $time = strtotime($users['time']);
            $date1= $users['time'];
            $all  = strtotime("$date1 -27 day");
            //hrac所有用户
            $tree = M('Info')->where(array('pid'=>$id,'type'=>1,'iscount'=>0))->select();
            foreach ($tree as $key => $value) {
                if(strtotime($value['time'])>=$all){
                    $arr1[]=$value;
                }else{
                    $arr2[]=$value;
                }
            }
            foreach ($arr2 as $key => $value) {
                $where = array('ifid'=>$value['ifid'],'iscount'=>1);
                $save  = D('Info')->save($where);
            }
            $count=count($arr1);
            $int  =floor($count/3);
            if($int>0){
                for($i=0;$i<$int;$i++){
                    $starttime  = M('Wvusers')->where(array('uid'=>$id))->getfield('starttime');
                    $strtotime  = strtotime(date('Y-m-d',time()))-strtotime($starttime);
                    if(empty($starttime)){
                        $num = 100;
                    }else{
                        if($strtotime>2332800){
                            $num = 100;
                        }else{
                            $num = 150; 
                        }
                    }
                    $tree1 = M('Info')->where(array('pid'=>$id,'type'=>1,'iscount'=>0))->select();
                    foreach($tree1 as $key => $value) {
                        if($key<3){
                            $save = D('Info')->where(array('ifid'=>$value['ifid']))->setField('iscount',1);
                            $user = D('Wvusers')->where(array('uid'=>$id))->setField('starttime',date('Y-m-d',time()));

                        } 
                    }
                    $echo .= $num;
                }
            }
            if($save){
                echo '成功'.$echo;
            }else{
                echo '失败'.$echo;
            }
        }
    }



















































}