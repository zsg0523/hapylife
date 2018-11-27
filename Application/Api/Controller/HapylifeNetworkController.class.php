<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class HapylifeNetworkController extends HomeBaseController{

	/**
    * 推荐网查询
    **/
    public function getUserBinary(){
        $account = I('post.CustomerID');
        $time = time()-14*86400;
        $data    = D('User')->where(array('EnrollerID'=>$account,'CustomerID'=>array('like','%'.'HPL'.'%')))->select();
        // p($data);die;
        foreach($data as $key=>$value){
            // 去除非正式会员
            if($value['distributortype'] != 'Pc'){
                $newData[] = $value;
            }else{
                if($value['joinedon']>$time){
                    $newData[] = $value;
                }
            }
        }

        // 查询下线的订单支付状态
        foreach($newData as $key=>$value){
            $newData[$key]['receiptlist'] = M('Receipt')->where(array('rCustomerID'=>$value['customerid'],'ir_ordertype'=>array('IN','1,2')))->order('irid DESC')->select();
        }

        // 检测下线订单支付情况
        foreach($newData as $key=>$value){
            if($value['receiptlist']){
                // 判断最新一条月费单支付状态
                switch($value['receiptlist'][0]['ir_status']){
                    // 未支付
                    case '0':
                        // 订单支付时间
                        $newData[$key]['payTime']  = '';
                        // 订单创建时间
                        $newData[$key]['crateTime']  = date('Y-m-d H:i:s',$value['receiptlist'][0]['ir_date']);
                        // 订单价格
                        $newData[$key]['payMoney'] = $value['receiptlist'][0]['ir_price'];
                        $newData[$key]['payNote']  = '未缴费';
                        $newData[$key]['payStatus']  = '1';
                        break;
                    // 未支付
                    case '7':
                        // 订单支付时间
                        $newData[$key]['payTime']  = '';
                        // 订单创建时间
                        $newData[$key]['crateTime']  = date('Y-m-d H:i:s',$value['receiptlist'][0]['ir_date']);
                        // 订单价格
                        $newData[$key]['payMoney'] = $value['receiptlist'][0]['ir_price'];
                        $newData[$key]['payNote']  = '未缴费';
                        $newData[$key]['payStatus']  = '1';
                        break;
                    // 已支付
                    default:
                        $newData[$key]['payTime']  = date('Y-m-d H:i:s',$value['receiptlist'][0]['ir_paytime']);
                        $newData[$key]['crateTime']  = date('Y-m-d H:i:s',$value['receiptlist'][0]['ir_date']);
                        $newData[$key]['payMoney'] = $value['receiptlist'][0]['ir_price'];
                        $newData[$key]['payNote']  = '已支付';
                        $newData[$key]['payStatus']  = '0';
                        break;
                }
            }
        }

        if($newData){
            $this->ajaxreturn($newData);
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }
    }

    /**
    * 推荐--下级信息
    **/
    public function getUserInfo(){
        $account = I('post.CustomerID');
        $data    = D('User')->where(array('CustomerID'=>$account))->find();
        // 查询下线的订单支付状态
        $receiptlist = M('Receipt')->where(array('rCustomerID'=>$account,'ir_ordertype'=>array('IN','1,2')))->order('irid DESC')->select();
        // 检测下线订单支付情况
        // 判断最新一条月费单支付状态
        switch($receiptlist[0]['ir_status']){
            // 未支付
            case '0':
                // 订单支付时间
                $data['payTime']  = '';
                // 订单创建时间
                $data['crateTime']  = date('Y-m-d H:i:s',$receiptlist[0]['ir_date']);
                // 订单价格
                $data['payMoney'] = $receiptlist[0]['ir_price'];
                $data['payNote']  = '未缴费';
                break;
            case '7':
                // 订单支付时间
                $data['payTime']  = '';
                // 订单创建时间
                $data['crateTime']  = date('Y-m-d H:i:s',$receiptlist[0]['ir_date']);
                // 订单价格
                $data['payMoney'] = $receiptlist[0]['ir_price'];
                $data['payNote']  = '未缴费';
                break;
            // 已支付
            default:
                $data['payTime']  = date('Y-m-d H:i:s',$receiptlist[0]['ir_paytime']);
                $data['crateTime']  = date('Y-m-d H:i:s',$receiptlist[0]['ir_date']);
                $data['payMoney'] = $receiptlist[0]['ir_price'];
                $data['payNote']  = '已支付';
                break;
        }

        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }
    }
    /**
    * 修改左右脚及双轨id
    **/
    public function editUserInfo(){
        $CustomerID = I('post.CustomerID');
        $para       = I('post.para');
        $paravalue  = I('post.paravalue');
        switch ($para) {
            case 'Placement':
                $data['Placement'] = $paravalue;
                break;
            case 'SponsorID':
                $data['SponsorID'] = $paravalue;
                break;
        }
        $mape['CustomerID'] = $CustomerID;
        $save = D('User')->where($mape)->save($data);  
        if($save){
            $data['status']=1;
            $this->ajaxreturn($data); 
        }else{
            $data['status']=0;
            $this->ajaxreturn($data); 
        }  
    }
	
}