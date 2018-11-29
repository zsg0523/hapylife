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
        $level = I('post.level');
        $ir_status = I('post.ir_status');
        $account = I('post.CustomerID');
        $wvId = M('User')->where(array('CustomerID'=>$account))->getfield('wvCustomerID');
        $time = time()-14*86400;

        if($wvId){
            $CustomerID = $account.','.$wvId;
            switch($level){
                case '0':
                    $data = M('User')->where(array('isexit'=>array('IN','0,1'),'enrollerid'=>array('IN',$CustomerID)))->select();
                    break;
                case '1':
                    $data = M('User')->where(array('isexit'=>1,'enrollerid'=>array('IN',$CustomerID),'distributortype'=>'Gold'))->select();
                    break;
                case '2':
                    $data = M('User')->where(array('isexit'=>1,'enrollerid'=>array('IN',$CustomerID),'distributortype'=>'Platinum'))->select();
                    break;
                case '3':
                    $data = M('User')->where(array('isexit'=>0,'enrollerid'=>array('IN',$CustomerID)))->select();
                    break;
            }
        }else{
            switch($level){
                case '0':
                    $data = M('User')->where(array('isexit'=>array('IN','0,1'),'enrollerid'=>$account))->select();
                    break;
                case '1':
                    $data = M('User')->where(array('isexit'=>1,'enrollerid'=>$account,'distributortype'=>'Gold'))->select();
                    break;
                case '2':
                    $data = M('User')->where(array('isexit'=>1,'enrollerid'=>$account,'distributortype'=>'Platinum'))->select();
                    break;
                case '3':
                    $data = M('User')->where(array('isexit'=>0,'enrollerid'=>$account))->select();
                    break;
            }
        }
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
        // p($newData);die;
        foreach($newData as $key=>$value){
            if($value['receiptlist'] == array()){
                unset($newData[$key]);
            }else{
                switch($ir_status){
                    case '0':
                        switch($value['receiptlist'][0]['ir_status']){
                            case '0':
                                $newData[$key]['payNote'] = '未缴费';
                                break;
                            case '7':
                                $newData[$key]['payNote'] = '未缴费';
                                break;
                            case '8':
                                $newData[$key]['payNote'] = '已退会';
                                break;
                            case '202':
                                $newData[$key]['payNote'] = '未全额支付';
                                break;
                            default:
                                $newData[$key]['payNote'] = '已支付';
                                break;
                        }
                        break;
                    case '1':
                        if($value['receiptlist'][0]['ir_status'] == 0 || $value['receiptlist'][0]['ir_status'] == 7){
                            $newData[$key]['payNote'] = '未缴费';
                        }else{
                            unset($newData[$key]);
                        }
                        break;
                    case '2':
                        if($value['receiptlist'][0]['ir_status'] == 2 ||$value['receiptlist'][0]['ir_status'] == 3 || $value['receiptlist'][0]['ir_status'] == 4){
                            $newData[$key]['payNote'] = '已支付';
                        }else{
                            unset($newData[$key]);
                        }
                        break;
                }
            }
        }

        if($newData){
            $this->ajaxreturn($newData);
        }else{
            $map['status']=0;
            $this->ajaxreturn($map); 
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

        $usa = new \Common\UsaApi\Usa();
        $activities = $usa->activities($account);
        // p($activities);
        if(!$activities['errors']){
            $monthly = $activities['monthly'];
            if($monthly){
                // 拆分数据
                $resolve = explode(' ',$monthly['titleRank']);
                $data['titleRank'] = substr($resolve[0],0,1).substr($resolve[1],0,1);
                $data['personalActive'] = $monthly['personalActive'];
                $data['newTotal'] = bcadd($monthly['newBinaryUnlimitedLevelsLeft'],$monthly['newBinaryUnlimitedLevelsRight']);
                $data['activeTotal'] = bcadd($monthly['activeLeftLegWithAutoPlacement'],$monthly['activeRightLegWithAutoPlacement']);
                $data['Total'] = bcadd($monthly['leftLegTotal'],$monthly['rightLegTotal']);
                $data['volumeTotal'] = bcadd($monthly['volumeLeft'],$monthly['volumeRight']);
            }   
        }else{
            $data['titleRank'] = '';
            $data['personalActive'] = '';
            $data['newTotal'] = '';
            $data['activeTotal'] = '';
            $data['Total'] = '';
            $data['volumeTotal'] = '';
        }
        // p($data);die;
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