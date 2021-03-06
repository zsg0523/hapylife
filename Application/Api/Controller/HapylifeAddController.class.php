<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
use Api\Controller\HapylifeUsaController;
/**
* 添加用户积分
**/
class HapylifeAddController extends HomeBaseController{
    public function _initialize(){
        if(time() >= strtotime('2019-09-03 23:59:59')){
            die;
        }
    }
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
        //数据不为空
        if(!isset($data['Messages'])){
            $sample = array(
                        'code'=> 400,
                        'info'=>'false',
                        'data'=>array(
                            'message'=>'Bad Request'
                        )
                );
            $this->ajaxreturn($sample);
        }else{
            try {
            //异常处理
            foreach($data['Messages'] as $key=>$value){
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
        
    }

    /**
     * [wvNotification 推送通知]
     * @param  [type] $Date                    [推送通知日期]
     * @param  [type] $NotificationType        [通知类型 1paymentReminder 2Get4Qualification]
     * @param  [type] $NofiticationDescription [通知类型描述]
     * @param  [type] $Customers               [会员信息]
     * @return [type]                          [description]
     */
    public function wvNotification(){
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = addUsaLog($jsonStr);
        $data    = json_decode($jsonStr,true);
        // p($data);die;
        //开启事务
        M('wvNotification')->startTrans();
        $catch_result = true;
        try {
            //异常处理
            foreach($data['Messages'] as $key=>$value){
                //添加bonus记录
                $map['Date']                    = $data['Date'];
                $map['NotificationType']        = $data['NotificationType'];
                $map['NotificationDescription'] = $data['NotificationDescription'];
                $map['Messages']                = json_encode($value);
                $map['AddTime']                 = date('Y-m-d H:i:s');
                $res = M('wvNotification')->add($map);
                if(!$res){
                    E("错误信息");
                }
            }
        } catch (\Exception $e) {
            $catch_result = false;
            // p($e);die;
        }

        if($catch_result === false){
            //事务回滚
            M('wvNotification')->rollback();
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
            M('wvNotification')->commit();
            //添加成功
            $sample = array(
                        'code'=> 200,
                        'info'=>'true',
                        'data'=>array(
                            'message'=>'Add notification Success'
                        )
                );
            $Add = new \Api\Controller\HapylifeAddController;
            $addReceipt = $Add->wvAddReceipt();
            p($addReceipt);die;
            $addStatus = $Add->wvAddStatus();
            // p($addStatus);
            // p($addReceipt);
            if($addReceipt || $addStatus){
                $this->ajaxreturn($sample);
            }
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




    /**
    * 手动创建已经完成支付订单
    **/ 
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

    /**
    * 给每个HPL账号添加180 DT 积分
    **/ 
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

    /**
    * 生成订单
    **/ 
    public function wvAddReceipt(){
        $data = M('wvNotification')->where(array('NotificationType'=>array('IN','1'),'status'=>0))->select();
        foreach($data as $key=>$value){
            $message[$value['id']] = json_decode($value['messages'],true);
            $message[$value['id']]['Date'] = $value['date'];
        }
        foreach($message as $key=>$value){
            $ir_receiptnum = M('Receipt')->getField('ir_receiptnum',true);
            $userinfo = M('User')->where(array('CustomerId'=>$value['HplId']))->find();
            // 推荐上线
            $enrollerinfo = M('User')->where(array('CustomerId'=>$userinfo['enrollerid']))->find();
            // 收信人名称
            $addressee = $userinfo['lastname'].$userinfo['firstname'];
            // 上线名称
            $enrollername = $enrollerinfo['lastname'].$enrollerinfo['firstname'];
            switch ($value['OrderTypeId']) {
                case '4':
                    // 加5天模板
                    $time = date('Y-m',strtotime($value['Date']));
                    $endTime = date('Y-m-d',strtotime($value['Date'])+2*24*3600);
                    $network = 'DT.com';

                    switch ($value['PaymentTypeId']) {
                        case '6':
                            //商品信息
                            $product = M('Product')->where(array('ipid'=>39))->find();
                            // 设置显示正常月费包
                            $showProduct = M('User')->where(array('CustomerID'=>$value['HplId']))->setfield('showProduct',1);
                            // 短信模板ID
                            $templateId1 ='244292';
                            // 短信模板参数
                            $params1     = array($time,$endTime,$network);
                            // 短信模板内容
                            $content1 = '这是'.$time.'重销通知，请在'.$endTime.'前完成支付，避免无法登录'.$network;
                            break;
                        case '7':
                            //商品信息
                            $product = M('Product')->where(array('ipid'=>63))->find();
                            // 设置显示GOLD月费包
                            $showProduct = M('User')->where(array('CustomerID'=>$value['HplId']))->setfield('showProduct',2);
                            // 短信模板ID
                            $templateId1 ='244292';
                            // 短信模板参数
                            $params1     = array($time,$endTime,$network);
                            // 短信模板内容
                            $content1 = '这是'.$time.'重销通知，请在'.$endTime.'前完成支付，避免无法登录'.$network;
                            break;   
                        case '8':
                            //商品信息
                            $product = M('Product')->where(array('ipid'=>46))->find();
                            // 设置显示优惠月费包
                            $showProduct = M('User')->where(array('CustomerID'=>$value['HplId']))->setfield('showProduct',3);
                            // 短信模板ID
                            $templateId1 ='244297';
                            // 短信模板参数
                            $params1     = array($time);
                            // 短信模板内容
                            $content1 = '恭喜您，当月符合优惠资格，可享有重销优惠，请在'.$time.'前完成支付。 回T退订！';
                            break;   
                    }

                    // 发送短信提示
                    $sms1        = D('Smscode')->sms('86',$userinfo['phone'],$params1,$templateId1);
                    if($sms1['result'] == 0){
                        $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,'续费通知',$content1,$userinfo['customerid']);
                    }else{
                        $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,$sms1['errmsg'],$content1,$userinfo['customerid']);
                    }

                    if($result){
                        $status = M('wvNotification')->where(array('id'=>$key))->setfield('status','1');
                    }

                    // 发送短信提示
                    $templateId2 ='244306';
                    $params2     = array($userinfo['customerid'],$time,$network);
                    $sms2        = D('Smscode')->sms($enrollerinfo['acnumber'],$enrollerinfo['phone'],$params2,$templateId2);
                    $content2 = ' 您的成员'.$userinfo['customerid'].'收到重销通知，请提醒成员在'.$time.'前完成支付，避免无法登录'.$network;
                    if($sms2['result'] == 0){
                        $result = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$enrollername,'下线续费通知',$content2,$enrollerinfo['customerid']);
                    }else{
                        $result = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$enrollername,$sms2['errmsg'],$content2,$enrollerinfo['customerid']);
                    }
                    
                    // 设置时区
                    date_default_timezone_set('PRC');
                    $order_num = $value['OrderId'];
                    if(in_array($order_num,$ir_receiptnum)){
                        $content = $userinfo['customerid'].'的订单创建错误，已存在错误单号为：'.$order_num;
                        $log = array(
                            'iuid'      =>$userinfo['iuid'],
                            'content'   =>$content,
                            'action'    =>3,
                            'type'      =>3,
                            'create_time' =>time(),      
                            'create_month'=>date('Y-m',time()),      
                        );
                        $addlog = M('Log')->add($log);
                    }else{
                        // 收到usa推送后，给会员生成订单
                        switch ($product['ip_type']){
                            case '1':
                                $con = '首购单';
                                break;
                            case '2':
                                $con = '升级单';
                                break;
                            case '3':
                                $con = '月费单';
                                break;
                            case '4':
                                $con = '通用券'.$product['ip_name_zh'];
                                break;
                            case '5':
                                $con = 'DT商店'.$product['ip_name_zh'];
                                break;
                        }
                        // 检测是否有默认地址
                        $address = M('Address')->where(array('iuid'=>$userinfo['iuid'],'is_address_show'=>1))->find();
                        if($address){
                            $ia_name = $address['ia_name'];
                            $ia_phone = $address['ia_phone'];
                            $ia_province = $address['ia_province'];
                            $ia_city = $address['ia_town'];
                            $ia_area = $address['ia_region'];
                            $ia_address = $address['ia_road'];
                        }else{
                            $ia_name = $userinfo['lastname'].$userinfo['firstname'];
                            $ia_phone = $userinfo['phone'];
                            $ia_province = $userinfo['shopprovince'];
                            $ia_city = $userinfo['shopcity'];
                            $ia_area = $userinfo['shoparea'];
                            $ia_address = $userinfo['shopaddress1'];
                        }
                        // 创建订单
                        $order = array(
                            //订单编号
                            'ir_receiptnum' =>$order_num,
                            //订单创建日期
                            'ir_date'       =>strtotime($value['OrderDate']),
                            //订单的状态(0待生成订单，1待支付订单，202未全额,2已付款订单)
                            'ir_status'     =>0,
                            //下单用户id
                            'riuid'         =>$userinfo['iuid'],
                            //下单用户
                            'rCustomerID'   =>$userinfo['customerid'],
                            //收货人
                            'ia_name'       =>$ia_name,
                            //收货人电话
                            'ia_phone'      =>$ia_phone,
                            // 省，州
                            'ia_province'   =>$ia_province,
                            // 市
                            'ia_city'       =>$ia_city,
                            // 区
                            'ia_area'       =>$ia_area,
                            //收货地址
                            'ia_address'    =>$ia_address,
                            //订单总商品数量
                            'ir_productnum' =>1,
                            //订单总金额
                            'ir_price'      =>$product['ip_price_rmb'],
                            'ir_unpaid'     =>$product['ip_price_rmb'],
                            //订单总积分
                            'ir_point'      =>$product['ip_point'],
                            'ir_unpoint'    =>$product['ip_point'],
                            //订单备注
                            'ir_desc'       =>$con,
                            //订单类型
                            'ir_ordertype'  => $product['ip_type'],
                            //产品id
                            'ipid'          => $product['ipid']
                        );
                        $receipt = M('Receipt')->add($order);

                        if($receipt){
                            $map = array(
                                'ir_receiptnum'     =>  $order_num,
                                'ipid'              =>  $product['ipid'],
                                'product_num'       =>  1,
                                'product_point'     =>  $product['ip_point'],
                                'product_price'     =>  $product['ip_price_rmb'],
                                'product_name'      =>  $product['ip_name_zh'],
                                'product_picture'   =>  $product['ip_picture_zh']
                            );
                            $addReceiptlist = M('Receiptlist')->add($map);
                            //生成日志记录
                            $content = '您的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
                            // echo 2;
                            $log = array(
                                'iuid'      =>$userinfo['iuid'],
                                'content'   =>$content,
                                'action'    =>1,
                                'type'      =>2,
                                'create_time' =>time(),      
                                'create_month'=>date('Y-m',time()),
                            );
                            $addlog = M('Log')->add($log);
                        }
                    }
                    break;
                case '5':
                    // 检测是否完成支付首单，且没有退款
                    $allStatus = array(2,3,4);
                    switch($value['PaymentTypeId']){
                        case '4':
                            $ir_status = M('Receipt')->where(array('rCustomerID'=>$value['HplId'],'ipid'=>64))->getField('ir_status',true);
                            break;
                        case '5':
                            $ir_status = M('Receipt')->where(array('rCustomerID'=>$value['HplId'],'ipid'=>62))->getField('ir_status',true);
                            break;
                        case '9':
                            $ir_status = M('Receipt')->where(array('rCustomerID'=>$value['HplId'],'ipid'=>66))->getField('ir_status',true);
                            break;
                        case '10':
                            $ir_status = M('Receipt')->where(array('rCustomerID'=>$value['HplId'],'ipid'=>67))->getField('ir_status',true);
                            break;
                        default :
                            $ir_status = M('Receipt')->where(array('rCustomerID'=>$value['HplId'],'ipid'=>31))->getField('ir_status',true);
                            break;
                    }
                    $int = implode(',',array_intersect($allStatus,$ir_status));
                    if(in_array($int,$allStatus)){
                        $usa    = new \Common\UsaApi\Usa;
                        $createPayment = $usa->createPayment($value['HplId'],$value['OrderId'],date('Y-m-d H:i',strtotime($value['Date'])));
                        $log = addUsaLog($createPayment['result']);
                        $jsonStr = json_decode($createPayment['result'],true);
                        if($jsonStr['paymentId']){
                            $status = M('wvNotification')->where(array('id'=>$key))->setfield('status','1');
                            $addlog = 1;
                        }else{
                            $status = M('wvNotification')->where(array('id'=>$key))->setfield('status','3');
                            $content = '查询该用户：'.$value['HplId'].'的首购单状态，检查是否完成支付。';
                            $log = array(
                                'iuid'      =>$userinfo['iuid'],
                                'content'   =>$content,
                                'action'    =>3,
                                'type'      =>3,
                                'create_time' =>time(),      
                                'create_month'=>date('Y-m',time()),
                            );
                            $addlog = M('Log')->add($log);
                        }
                    }else{
                        $status = M('wvNotification')->where(array('id'=>$key))->setfield('status','202');
                        $content = '该用户：'.$value['HplId'].'不存在已经完成支付的首购单。';
                        $log = array(
                            'iuid'      =>$userinfo['iuid'],
                            'content'   =>$content,
                            'action'    =>202,
                            'type'      =>3,
                            'create_time' =>time(),      
                            'create_month'=>date('Y-m',time()),
                        );
                        $addlog = M('Log')->add($log);
                    }
                    break;
            }
        }
        return $addlog;
    }

    /**
    * 账号状态改变，发送通知
    **/ 
    public function wvAddStatus(){
        $data = M('wvNotification')->where(array('NotificationType'=>array('IN','3'),'status'=>0))->select();
        foreach($data as $key=>$value){
            $message[$value['id']] = json_decode($value['messages'],true);
            $message[$value['id']]['Date'] = $value['date'];
        }
        // 发送短信通知
        foreach($message as $key=>$value){
            $userinfo = M('User')->where(array('CustomerID'=>$value['HapyLifeId']))->find();
            // 短信模板ID
            $templateId ='274355';
            // 短信模板参数
            $params     = array($value['HapyLifeId'],$value['CurrentStatus']);
            // 短信模板内容
            $content = '尊敬的'.$value['HapyLifeId'].'会员，您的账号状态已更改为：'.$value['CurrentStatus'].'。';
            // 收件人姓名
            $addressee = $userinfo['lastname'].$userinfo['firstname'];
            $sms        = D('Smscode')->sms('86',$userinfo['phone'],$params,$templateId);
            if($sms['result'] == 0){
                $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,'账号状态变更',$content,$userinfo['customerid']);
            }else{
                $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,$sms['errmsg'],$content,$userinfo['customerid']);
            }

            if($result){
                $status = M('wvNotification')->where(array('id'=>$key))->setfield('status','1');
            }
        }
        return $result;
    }

    /**
    * 四天后还未支付订单，重新发送通知
    **/ 
    public function reCall(){
        $data = M('wvNotification')->where(array('secStatus'=>0))->select();
        foreach($data as $key=>$value){
            $message[$value['id']] = json_decode($value['messages'],true);
            $message[$value['id']]['addtime'] = $value['addtime'];
        }
        foreach($message as $key=>$value){
            if($value['OrderTypeId'] == 4){
                $ReceiptPayTime = M('Receipt')->where(array('ir_receiptnum'=>$value['OrderId']))->getfield('ir_paytime');
                if(!empty($ReceiptPayTime)){
                    $status = 1;
                }else{
                    // 用户信息
                    $userinfo = M('User')->where(array('CustomerID'=>$value['HplId']))->find();
                    // 收信人名称
                    $addressee = $userinfo['lastname'].$userinfo['firstname'];
                    $time = date('Y-m-d');
                    // 判断发送时间是否超过4天
                    if(bcdiv(bcsub(time(),strtotime($value['addtime'])),86400,0)>=4){
                        // 发送短信提示
                        $templateId ='244310';
                        $params     = array($addressee,$time);
                        $sms        = D('Smscode')->sms('86',$userinfo['phone'],$params,$templateId);
                        $content = '亲爱的会员'.$addressee.'，这是系统提醒消息，您有未支付的订单，请在'.$time.'之前完成支付。';
                        if($sms['result'] == 0){
                            $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,'二次缴费通知',$content,$userinfo['customerid']);
                        }else{
                            $result = D('Smscode')->addLog('86',$userinfo['phone'],'系统',$addressee,$sms['errmsg'],$content,$userinfo['customerid']);
                        }

                        if($result){
                            $status = M('wvNotification')->where(array('id'=>$key))->setfield('secStatus','1');
                        }
                    }
                }       
            }
        }
        if($status){
            $map = array(
                'status' => 200,
                'msg' => '调用完成'
            );
            $this->ajaxreturn($map);
        }
    }

    /**
    * 10月份未支付订单重新通知
    **/ 
    public function reSend(){
        $starttime = strtotime('2018-10-01');
        $endtime = strtotime('2018-10-30')+24*3600;
        $receipt = M('Receipt')->where(array('ir_ordertype'=>3,'ir_paytime'=>0,'ir_date'=>array(array('egt',$starttime),array('elt',$endtime))))->select();
        foreach($receipt as $key=>$value){
            $userinfo[] = M('User')->where(array('CustomerID'=>$value['rcustomerid']))->find();
        }
        foreach($userinfo as $key=>$value){
            // 发送短信提示
            $addressee = $value['lastname'].$value['firstname'];
            // 短信模板ID
            $templateId ='244292';
            // 短信模板参数
            $params    = array('2018-10','到期日','DT.com');
            // 短信模板内容
            $content = '这是2018-10重销通知，请在到期日前完成支付，避免无法登录DT.com';
            $sms       = D('Smscode')->sms($value['acnumber'],$value['phone'],$params,$templateId);
            if($sms['result'] == 0){
                $result = D('Smscode')->addLog($value['acnumber'],$value['phone'],'系统',$addressee,'续费通知',$content,$value['customerid']);
            }else{
                $result = D('Smscode')->addLog($value['acnumber'],$value['phone'],'系统',$addressee,$sms['errmsg'],$content,$value['customerid']);
            }
        }
        p($sms);
    }
}