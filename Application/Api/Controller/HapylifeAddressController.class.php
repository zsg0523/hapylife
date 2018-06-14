<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 银行信息
**/
class HapylifeAddressController extends HomeBaseController{

    /**
    * 添加收货地址
    **/
    public function addressadd(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户iuid,收件人姓名、地址，电话
            $iuid           = I('post.iuid');
            $ia_name        = I('post.ia_name');
            $ia_phone       = I('post.ia_phone');
            $ia_province       = I('post.ia_province');
            $ia_town       = I('post.ia_town');
            $ia_region       = I('post.ia_region');
            $ia_road       = I('post.ia_road');

            $arr  = M('Address')
                  ->where(array('iuid'=>$iuid))
                  ->select();
            //判断是否存在地址。有则is_show为0，没有则为1
            if($arr){
                $tmp        = array(
                'iuid'      =>$iuid,
                'ia_name'   =>$ia_name,
                'ia_phone'  =>$ia_phone,
                'ia_province'=>$ia_province,
                'ia_town'=>$ia_town,
                'ia_region'=>$ia_region,
                'ia_road'=>$ia_road,
                'is_address_show'   =>0
                );
            }else{
                $tmp        = array(
                'iuid'      =>$iuid,
                'ia_name'   =>$ia_name,
                'ia_phone'  =>$ia_phone,
                'ia_province'=>$ia_province,
                'ia_town'=>$ia_town,
                'ia_region'=>$ia_region,
                'ia_road'=>$ia_road,
                'is_address_show'   =>1
                );
            }
            //添加
            $result       = M('Address')
                            ->add($tmp);
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    
    /**
    * 收货地址列表
    **/
    public function addresslist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户iuid
            $iuid   = I('post.iuid');
            //列表信息
            $data   = M('Address')
                    ->where(array('iuid'=>$iuid))
                    ->order('is_address_show DESC')
                    ->select(); 
            if($data){
                $this->ajaxreturn($data);
            }
            else{
                 $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 显示默认地址
    **/
    public function defaultaddress(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户iuid
            $iuid        = I('post.iuid');
            $tmp         = array(
                'iuid'   =>$iuid,
                'is_address_show'=>1
                );
            //默认地址信息
            $data   = M('Address')
                    ->where($tmp)
                    ->find(); 
            if($data){
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 修改默认地址
    **/
    public function faultaddress(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取用户iuid,地址id
            $iuid        = I('post.iuid');
            $iaid        = I('post.iaid');
            //默认地址信息
            //0变为1
            $tmp    = M('Address')
                    ->where(array('iaid'=>$iaid))
                    ->setField('is_address_show',1);
            if($tmp){
                //1变为0
                $map    = array(
                        'iaid'=>array('NEQ',$iaid),
                        'iuid'=>$iuid
                    );
                $arr    = M('Address')
                        ->where($map)
                        ->setField('is_address_show',0);
            }
            
            if($tmp || $arr){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 获取地址详情
    **/
    public function addressinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取收货地址iaid
            $iaid   = I('post.iaid');
            //地址信息
            $data   = M('Address')
                    ->where(array('iaid'=>$iaid))
                    ->order('is_address_show DESC')
                    ->find(); 
            if($data){
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    * 编辑收货地址
    **/
    public function addressedit(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //收件人姓名、地址，电话,地址id
            $iaid           = I('post.iaid');
            $ia_name        = I('post.ia_name');
            $ia_phone       = I('post.ia_phone');
            $ia_province       = I('post.ia_province');
            $ia_town       = I('post.ia_town');
            $ia_region       = I('post.ia_region');
            $ia_road       = I('post.ia_road');

            $tmp            = array(
                'ia_name'   =>$ia_name,
                'ia_phone'  =>$ia_phone,
                'ia_province'=>$ia_province,
                'ia_town'=>$ia_town,
                'ia_region'=>$ia_region,
                'ia_road'=>$ia_road,
                'iaid'      =>$iaid
                );
            //修改
            $data = M('Address')
                  ->save($tmp); 
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
            else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 删除收货地址
    **/
    public function addressdelete(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //地址id
            $iaid = I('post.iaid');
            //删除
            $data = M('Address')
                  ->where(array('iaid'=>$iaid))
                  ->delete(); 
            if($data){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }
            else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }

}