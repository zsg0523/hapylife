<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 添加用户积分
**/
class HapylifeAddController extends HomeBaseController{

    
    /**
     * [wvBonus description]
     * @param  [type] $customerid [description]
     * @param  [type] $serial     [description]
     * @param  [type] $amount     [description]
     * @return [type]             [description]
     */
    public function wvBonus(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        //开启事务
        M('wvBonus')->startTrans();
        $catch_result = true;
        try {
            //异常处理
            foreach($data as $key=>$value){
                //添加bonus记录
                $value['Bonuses'] = json_encode($value['Bonuses']);
                $res = M('wvBonus')->add($value);
                if(!$res){
                    E("错误信息");
                }
            }
        } catch (\Exception $e) {
            $catch_result = false;
            // dump($e);
        }

        if($catch_result === false){
            //事务回滚
            M('wvBonus')->rollback();
            //添加失败
            $sample = array(
                        'code'=> 400,
                        'info'=>'false',
                        'data'=>array(
                            'message'=>'Add bonus failure'
                        )
                );
            $this->ajaxreturn($sample);
        }else{
            //事务提交
            M('wvBonus')->commit();
            //添加成功
            $sample = array(
                        'code'=> 200,
                        'info'=>'true',
                        'data'=>array(
                            'message'=>'Add bonus Success'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }

    /**
     * [wvNotificication description]
     * @param  [type] $customerid [description]
     * @param  [type] $date       [description]
     * @param  [type] $content    [description]
     * @return [type]             [description]
     */
    public function wvNotification(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        //开启事务
        M('wvBonus')->startTrans();
        // p($data);die;
        $catch_result = true;
        try {
            //异常处理
            foreach($data['Customers'] as $key=>$value){
                //添加bonus记录
                $value['Date']                    = $data['Date'];
                $value['NotificationType']        = $data['NotificationType'];
                $value['NofiticationDescription'] = $data['NofiticationDescription'];
                $value['Customers']               = json_encode($value);
                $res = M('wvNotification')->add($value);
                if(!$res){
                    E("错误信息");
                }
            }
        } catch (\Exception $e) {
            $catch_result = false;
            // dump($e);
        }

        if($catch_result === false){
            //事务回滚
            M('wvBonus')->rollback();
            //添加失败
            $sample = array(
                        'code'=> 400,
                        'info'=>'false',
                        'data'=>array(
                            'message'=>'Add notification failure'
                        )
                );
            $this->ajaxreturn($sample);
        }else{
            //事务提交
            M('wvBonus')->commit();
            //添加成功
            $sample = array(
                        'code'=> 200,
                        'info'=>'true',
                        'data'=>array(
                            'message'=>'Add notification Success'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }


    /**
    * 添加单个用户积
    **/ 
    public function addPoints(){
        $jsonStr           = file_get_contents("php://input");
        //写入服务器日志文件
        $log               = addUsaLog($jsonStr);
        $data              = json_decode($jsonStr,true);
        $CustomerID        = $data['customerid'];
        // 获取用户信息
        $userinfo          = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        // 增加EP
        $iu_point          = $data['point'];
        // 用户剩余EP
        $last_point        = bcadd($userinfo['iu_point'],$iu_point,4);
        $point['iu_point'] = $last_point;
        $result            = M('User')->where(array('CustomerID'=>$CustomerID))->save($point);
        if($result){
            //生成唯一订单号
            $order_num = date('YmdHis').rand(10000, 99999);
            // 日志内容
            $content = '系统在'.date('Y-m-d H:i:s').'时,转入EP到'.$userinfo['customerid'].',剩EP余额'.$last_point;
            $log = array(
                        'pointNo'         => $order_num,
                        'iuid'            => $userinfo['iuid'],
                        'hu_username'     => $userinfo['lastname'].$userinfo['firstname'],
                        'hu_nickname'     => $userinfo['customerid'],
                        'send'            => '系统转入',
                        'received'        => $userinfo['customerid'],
                        'opename'         => '',
                        'getpoint'        => $iu_point,
                        'pointtype'       => 5,
                        'feepoint'        => 0,
                        'realpoint'       => $iu_point,
                        'unpoint'         => 0,
                        'leftpoint'       => $userinfo['iu_point']+$iu_point,
                        'iu_bank'         => $userinfo['bankname'],
                        'iu_bankbranch'   => $userinfo['subname'],
                        'iu_bankaccount'  => $userinfo['bankaccount'],
                        'iu_bankuser'     => $userinfo['lastname'].$userinfo['firstname'],
                        'iu_bankprovince' => $userinfo['bankprovince'],
                        'iu_bankcity'     => $userinfo['bankcity'],
                        'date'            => date('Y-m-d H:i:s'),
                        'handletime'      => date('Y-m-d H:i:s'),
                        'content'         => $content,
                        'status'          => 2,
                        'whichApp'        => 5,
                    );
            $res = M('Getpoint')->add($log);
            if($res){
                $sample = array(
                        'code'=> 200,
                        'info'=>'success',
                        'data'=>array(
                            'customerid'=>$data['customerid'],
                            'point'     =>$data['point'],
                            'userPoint' =>$last_point
                        )
                    );
                $this->ajaxreturn($sample);
            }
        }else{
            $sample = array(
                        'code'=> 1000,
                        'info'=>'failure',
                        'data'=>array(
                            'message'=>'the ID does not exist'
                        )
                );
            $this->ajaxreturn($sample);
        }
    }

    /**
     * [pushNotification description]
     * @return [type] [description]
     */
    public function pushNotification(){
        //选择不同类型的推送数据
        $data       = M('wvNotification')->select();

        //json_decode  customer
        foreach ($data as $key => $value) {
            $temp[$key]['customerid']              = json_decode($value['customerids'],true);
            $temp[$key]['nofiticationdescription'] = $value['nofiticationdescription'];
            $temp[$key]['notificationtype']        = $value['notificationtype'];
            $temp[$key]['date']                    = $value['date'];
        }

        //组装数组
        foreach ($temp as $key => $value) {
            foreach ($value['customerid'] as $k => $v) {
                $map[$key][$k]['customerid']              = $v;
                $map[$key][$k]['nofiticationdescription'] = $value['nofiticationdescription'];
                $map[$key][$k]['notificationtype']        = $value['notificationtype'];
                $map[$key][$k]['date']                    = $value['date'];
            }
        }

        //循环推送数据
        foreach ($map as $key => $value) {
            foreach ($value as $k => $v) {
            $notification = new \Common\PushEvent\Notification;
            $notification->setUser($v['customerid'])->setContent($v['nofiticationdescription'])->push();  
            }
        }
    }





    public function addReceipt(){
        $hu_nickname = I('post.hu_nickname');
        $lastname    = I('post.lastname');
        $firstname   = I('post.firstname');
        $ia_name     = I('post.ia_name');
        $ia_road     = I('post.ia_road');
        $ia_phone    = I('post.ia_phone');
        $time        = strtotime(I('post.time'));
        $ipid        = I('post.ipid');
        $product     = M('Product')->where(array('ipid'=>$ipid))->find();
        $receipt = array(
            'rCustomerID'   => $hu_nickname,
            'ir_receiptnum' => date('YmdHis').rand(10000, 99999),
            'ir_desc'       => $product['ip_name_zh'],
            'ir_status'     => 2,
            'ipid'          => $ipid,
            'ir_productnum' => 1,
            'ir_point'      => $product['ip_point'],
            'ir_unpoint'    => 0,
            'ir_price'      => $product['ip_price_rmb'],
            'ir_unpaid'     => 0,
            'ia_name'       => $ia_name,
            'ia_phone'      => $ia_phone,
            'ia_address'    => $ia_road,
            'ir_ordertype'  => 4,
            'ir_date'       => $time,
            'ir_paytime'    => $time,
        );
        $receipt_result = M('Receipt')->add($receipt);
        $receiptson = array(
            'ir_receiptnum'  => $receipt['ir_receiptnum'],
            'pay_receiptnum' => date('YmdHis').rand(100000, 999999),
            'ir_price'       => $product['ip_price_rmb'],
            'ir_point'       => $product['ip_price_rmb'],
            'ir_paytype'     => 5,
            'cretime'        => $time,
            'paytime'        => $time,
            'status'         => 2,
            'operator'       => '系统',
        );
        $receiptson_result = M('Receiptson')->add($receiptson);
        $receiptlist = array(
            'ir_receiptnum'   => $receipt['ir_receiptnum'],
            'ipid'            => $ipid,
            'ilid'            => 0,
            'product_num'     => 1,
            'product_price'   => $product['ip_price_rmb'],
            'product_point'   => $product['ip_point'],
            'product_name'    => $product['ip_name_zh'],
            'product_picture' => $product['ip_picture_zh'],
        );
        $receiptlist_result = M('receiptlist')->add($receiptlist);
        if($receipt_result && $receiptson_result && $receiptlist_result){
            // 发送短信提示
            $templateId ='178959';
            $params     = array($receipt['ir_receiptnum'],$product['ip_name_zh']);
            $sms        = D('Smscode')->sms(86,$ia_phone,$params,$templateId);
            if($sms['errmsg'] == 'OK'){
                $contents = array(
                    'acnumber'     => 86,
                    'phone'        => $ia_phone,
                    'operator'     => '系统',
                    'addressee'    => $ia_road,
                    'product_name' => $product['ip_name_zh'],
                    'date'         => time(),
                    'content'      => '订单编号：'.$receipt['ir_receiptnum'].'，产品：'.$product['ip_name_zh'].'，支付成功。',
                    'customerid'   => $hu_nickname
                );
                $logs = M('SmsLog')->add($contents);
                if($logs){
                    $userinfo = array(
                        'iuid' => '',
                        'customerid' => $hu_nickname,
                        'lastname' => $lastname,
                        'firstname' => $firstname,
                    );
                    // 添加通用券
                    $product= M('Receipt')
                            ->alias('r')
                            ->join('hapylife_product AS p ON r.ipid = p.ipid')
                            ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
                            ->find();
                    $data = array(
                            'product' => $product,
                            'userinfo' => $userinfo,
                            'ir_receiptnum' =>$receipt['ir_receiptnum']
                        );
                    $data    = json_encode($data);
                    $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
                    // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/addCoupon";
                    $result  = post_json_data($sendUrl,$data);
                    p($result);
                }
            }
        }
    }
    public function addDt(){
        $user = M('User')->select();
        foreach ($user as $key => $value) {
            if(strlen($value['customerid'])!=8){
                $iu_dt= 180;
                $bcsub= bcadd($value['iu_dt'],$iu_dt,2);
                $save = M('User')->where(array('iuid'=>$value['iuid']))->setfield('iu_dt',$bcsub);
                if($save){
                    $dtNo = 'DT'.date('YmdHis').rand(10000, 99999);
                    $tmp     = array(
                        'iuid'           =>$value['iuid'],
                        'pointNo'        =>$dtNo,
                        'hu_username'    =>$value['lastname'].$value['firstname'],
                        'hu_nickname'    =>$value['customerid'],
                        'getdt'          =>$iu_dt,
                        'leftdt'         =>$bcsub,
                        'date'           =>date('Y-m-d H:i:s'),
                        'status'         =>2,
                        'dttype'         =>2,
                        'content'        =>'在'.date('Y-m-d H:i:s').'时系统增加'.$iu_dt.'DT到'.'您的账户'.',剩DT余额'.$bcsub.',流水号为:'.$dtNo,
                        'opename'        =>'系统',
                        'send'           =>'系统',
                        'received'       =>$value['customerid']
                    );
                    $add     = D('Getdt')->add($tmp);
                    $num++;
                }
            }
        }
        p($num);die;
    }




    public function addDt(){
        $user = M('User')->select();
        foreach ($user as $key => $value) {
            if(strlen($value['customerid'])!=8){
                $iu_dt= 180;
                $bcsub= bcadd($value['iu_dt'],$iu_dt,2);
                $save = M('User')->where(array('iuid'=>$value['iuid']))->setfield('iu_dt',$bcsub);
                if($save){
                    $dtNo = 'DT'.date('YmdHis').rand(10000, 99999);
                    $tmp     = array(
                        'iuid'           =>$value['iuid'],
                        'pointNo'        =>$dtNo,
                        'hu_username'    =>$value['lastname'].$value['firstname'],
                        'hu_nickname'    =>$value['customerid'],
                        'getdt'          =>$iu_dt,
                        'leftdt'         =>$bcsub,
                        'date'           =>date('Y-m-d H:i:s'),
                        'status'         =>2,
                        'dttype'         =>2,
                        'content'        =>'在'.date('Y-m-d H:i:s').'时系统增加'.$iu_dt.'DT到'.'您的账户'.',剩DT余额'.$bcsub.',流水号为:'.$dtNo,
                        'opename'        =>'系统',
                        'send'           =>'系统',
                        'received'       =>$value['customerid']
                    );
                    $add     = D('Getdt')->add($tmp);
                    $num++;
                }
            }
        }
        p($num);die;
    }


















}