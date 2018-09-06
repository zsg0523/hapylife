<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class PurchaseController extends HomeBaseController{
    /**
    * 
    **/
    public function test(){
        $order = M('Receipt')
                ->alias('r')
                ->join('hapylife_user u on r.riuid = u.iuid')
                ->select();
        // p($order);die;
        foreach ($order as $key => $value) {
            if($value['ir_status'] == 2){
                //添加支付时间
                $ir_paytime = $value['ir_date']+600;
                $map = array(
                    'ir_paytime'=>$ir_paytime
                );
                $save = M('Receipt')->where(array('ir_receiptnum'=>$value['ir_receiptnum']))->save($map);
            }
        }
    }
    
    /**
    * 主界面
    **/
    public function main(){
        $iuid  = $_SESSION['user']['id'];
        $find  = M('User')->where(array('iuid'=>$iuid))->find();
        $type  = trim($find['distributortype']);
        $mtype = trim($find['customertype']);
        //判断用户等级 show--1、可点击 2、不可点击
        // p($type);
        $tmpe    = array(
            'ip_grade' =>$type,
            'is_pull'  =>1
        ); 
        $product = D('Product')->where($tmpe)->order('is_sort desc')->select();

        foreach ($product as $key => $value) {
            $data[$key]         = $value; 
            $data[$key]['show'] = 1; 
        }
        // p($data);die;
        $this->assign('product',$data);
        $this->display();
    }


    /**
    * 购买礼包
    **/
    public function purchase(){
        $iuid  = $_SESSION['user']['id'];
        $find  = M('User')->where(array('iuid'=>$iuid))->find();
        $type  = trim($find['distributortype']);
        $mtype = trim($find['customertype']);
        $status= $find['status'];
        //判断用户等级 show--1、可点击 2、不可点击
        // p($type);die;
        switch (strlen($find['customerid'])) {
            case '8':
                //核对usa 账号是否正确存在
                $usa    = new \Common\UsaApi\Usa;
                $result = $usa->validateHpl($find['customerid']);
                switch ($result['isActive']) {
                    case true:
                        $products = M('Product')->where(array('ip_type'=>4,'is_pull'=>1))->select();
                        break;
                    default:
                        $products = array();
                        break;
                }
                break;
            default:
                $tmpe  = array(
                    'ip_grade' =>$type,
                    'is_pull'  =>1
                );
                $proArr  = D('Product')->where($tmpe)->order('is_sort desc')->select();
                $an_pro  = M('Product')->where(array('ip_type'=>4,'is_pull'=>1))->select();
                $product = array_merge($proArr,$an_pro);
                foreach ($product as $key => $value) {
                    $products[$key]         = $value; 
                    $products[$key]['show'] = 1; 
                }
                break;
        }  
        $array   = array('HPL00000181','HPL00123539');//显示测试产品账号
        $arrayTo = array('61338465','64694832','65745561','HPL00123556','61751610','61624356','61695777','68068002');//显示真实产品账号
        if(in_array($find['customerid'],$array)){
            $an_pro = M('Product')->where(array('ip_type'=>4,'is_pull'=>0))->select();
            $product = array_merge($products,$an_pro);
            foreach ($product as $key => $value) {
                $data[$key]         = $value; 
                $data[$key]['show'] = 1; 
            }
        // }else if(in_array($find['customerid'],$arrayTo)){
        //     $an_pro = M('Product')->where(array('ipid'=>48,'is_pull'=>0))->select();
        //     $product = array_merge($products,$an_pro);
        //     foreach ($product as $key => $value) {
        //         $data[$key]         = $value; 
        //         $data[$key]['show'] = 1; 
        //     }
        }else{
            $data = $products;
        }
        $this->assign('product',$data);
        $this->display();
    }
    /**
    * 购买DT礼包
    **/
    public function dtPurchase(){
        $iuid    = $_SESSION['user']['id'];
        $find    = M('User')->where(array('iuid'=>$iuid))->find();
        $products= M('Product')->where(array('ip_type'=>5,'is_pull'=>1))->select();
        $array   = array('HPL00000181','HPL00123539');//显示测试产品账号]
        if(in_array($find['customerid'],$array)){
            $an_pro = M('Product')->where(array('ip_type'=>5,'is_pull'=>0))->select();
            $product = array_merge($products,$an_pro);
            foreach ($product as $key => $value) {
                $data[$key]         = $value; 
                $data[$key]['show'] = 1; 
            }
        }else{
            $data = $products;
        }
        $this->assign('product',$data);
        $this->display();
    }
    /**
    * 购买DT礼包详情
    **/
    public function dtPurchaseInfo(){
        $ipid = I('get.ipid');
        $data = M('Product')
              ->where(array('ipid'=>$ipid))
              ->find();
        $this->assign('data',$data);
        $this->display();
    }

    /**
    * 会籍激活记录
    **/
    public function activaction(){
        $iuid     = $_SESSION['user']['id'];
        $orderTime= D('User')->where(array('iuid'=>$iuid))->getfield('OrderDate');
        if($orderTime){
            $date     = date('Y-m',time());
            $time     = date('Y-m',strtotime($orderTime));
            $day      = date('d',strtotime($orderTime));
            if($day>=28){
                $allday = 28;
            }else{
                $allday = $day;
            }
            $ddd    = $allday-1;
            if($ddd>=10){
                $oneday = $ddd;
            }else{
                $oneday = '0'.$ddd;
            }
            $date1    = explode('-',$date);
            $date2    = explode('-',$time);
            $number   = ($date1[0]-$date2[0])*12+($date1[1]-$date2[1]);
            for($i=0;$i<$number;$i++){
                $arr.=date("Y-m",strtotime("+1 month",strtotime($time))).',';
                $time= date("Y-m",strtotime("+1 month",strtotime($time)));
            }
            $str    = chop($arr,',');
            $strarr = explode(',', $str);
            $mape   = D('Activation')->where(array('iuid'=>$iuid))->order('datetime asc')->getfield('datetime',true);
            $end    = D('Activation')->where(array('iuid'=>$iuid))->order('datetime desc')->getfield('datetime');
            $endtime= strtotime($end.'-'.$oneday);
            if(empty($mape)){  
                $diffarr[0]= $time;
                foreach ($diffarr as $key => $value) {
                    if(!empty($value)){
                       $diff[] = $value;
                    }
                }
                if($diff){
                    foreach ($diff as $key => $value) {
                        $year  = date("Y年m月",strtotime($value)).$allday.'日';
                        $endday= date("Y年m月",strtotime("+1 month",strtotime($value))).$oneday.'日';
                        $where = array('iuid'=>$iuid,'datetime'=>$value,'is_tick'=>1,'hatime'=>$year,'endtime'=>$endday);
                        D('Activation')->add($where);
                    }   
                }
            }else{ 
                $diffarr   = array_diff($strarr,$mape);
                foreach ($diffarr as $key => $value) {
                    if(!empty($value)){
                       $diff[] = $value;
                    }
                }
                if($diff){
                    foreach ($diff as $key => $value) {
                        $year  = date("Y年m月",strtotime($value)).$allday.'日';
                        $endday= date("Y年m月",strtotime("+1 month",strtotime($value))).$oneday.'日';
                        $where = array('iuid'=>$iuid,'datetime'=>$value,'is_tick'=>0,'hatime'=>$year,'endtime'=>$endday);
                        if(time()>$endtime){
                            D('Activation')->add($where);
                        }
                    }   
                }
            }
        }
        $data = D('Activation')->where(array('iuid'=>$iuid))->select();
        $this->assign('data',$data);
        $this->display();
    }


    /**
    * 我的订单列表
    **/
    public function myOrder(){
        $customerid = $_SESSION['user']['username'];
        $data['status'] = $_SESSION['user']['status'];
        $map  = array(
                'rCustomerID'=>$customerid,
                'ir_status'=>array('in','2,3,4,5,202')
            );
        $data = M('Receipt')
                ->alias('r')
                ->join('hapylife_receiptlist hr on r.ir_receiptnum = hr.ir_receiptnum')
                ->join('hapylife_product hp on hr.ipid=hp.ipid')
                ->where($map)
                ->order('r.ir_date DESC')
                ->select();
        $this->assign('data',$data);
        $this->display();
    }
    /**
    * 订单详情
    **/
    public function myOrderInfo(){
        $ir_receiptnum = I('get.ir_receiptnum'); 
        $data = M('Receipt')
                ->alias('r')
                ->join('hapylife_receiptlist hr on r.ir_receiptnum = hr.ir_receiptnum')
                ->join('hapylife_product hp on hr.ipid=hp.ipid')
                ->where(array('r.ir_receiptnum'=>$ir_receiptnum))
                ->find();
        $data['receiptson'] = D('Receiptson')->where(array('status'=>2,'ir_receiptnum'=>$ir_receiptnum))->select();
        $this->assign('data',$data);
//      p($data);
        $this->display();
    }


    /**
    * 删除订单
    **/
    public function delete_order(){
        //订单号
        $order_num  = I('get.ir_receiptnum');
        $result = M('receipt')->where(array('ir_receiptnum'=>$order_num))->delete();
        if($result){
            redirect($_SERVER['HTTP_REFERER']);
        }else{
            $this->error('删除失败');
        }
    }


    /**
    * 个人资料
    **/
    public function myProfile(){
        $iuid = $_SESSION['user']['id'];
        $data = D('User')->where(array('iuid'=>$iuid))->find();
        $right= D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Right'))->select();
        $left = D('User')->where(array('SponsorID'=>$data['customerid'],'Placement'=>'Left'))->select();
        //right右脚、left左脚
        if($right){
            $data['right'] = count($right);
        }else{
            $data['right'] = 0;
        }
        if($left){
            $data['left'] = count($left);
        }else{
            $data['left'] = 0;
        }
        // p($data);die;
        $this->assign('userinfo',$data);
        $this->display();
    }

    /**
    * 购买详情
    **/
    public function purchaseInfo(){
        $ipid = I('get.ipid');
        // p($ipid);die;
        $data = M('Product')
              ->where(array('ipid'=>$ipid))
              ->find();
        $this->assign('data',$data);
        $this->display();
    }


    /**
    * 生成订单
    **/
    public function order(){
        $iuid = $_SESSION['user']['id'];
        $ipid = trim(I('get.ipid'));
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
        // 查询是否存在未支付的订单
        $ir_receiptnum = M('Receipt')->where(array('riuid'=>$iuid,'ir_ordertype'=>$product['ip_type'],'ir_status'=>0))->getfield('ir_receiptnum');
        
        if(!empty($ir_receiptnum)){
            $result = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->delete();
            if($result){
                $res = M('Receiptlist')->where(array('ir_receiptnum'=>$ir_receiptnum))->delete();
            }
        }
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
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
        }
        // if(empty($userinfo['shopaddress1'])||empty($userinfo['shopaddress1'])){
        //     $this->error('请先填写个人信息的地区和详细地址');
        // }
        $ia_name  = $userinfo['lastname'].$userinfo['firstname'];
        $order = array(
            //订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'       =>time(),
            //订单的状态(0待生成订单，1待支付订单，2已付款订单)
            'ir_status'     =>0,
            //下单用户id==
            'riuid'          =>$iuid,
            //下单用户
            'rCustomerID'    =>$userinfo['customerid'],
            //收货人
            'ia_name'       =>$ia_name,
            //收货人电话
            'ia_phone'      =>$userinfo['phone'],
            //收货地址
            'ia_address'    =>$userinfo['shopaddress1'],
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
        }
         //生成日志记录
        $content = '您的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        // echo 2;
        $log = array(
            'iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>1,
            'type'      =>2,
            'date'      =>date('Y-m-d H:i:s')          
        );
        $addlog = M('Log')->add($log);
        // 设置session时间
        if($addlog){
            if($product['ip_type'] == 1){
                $this->redirect('Home/Pay/choosePay1',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }else if($product['ip_type'] == 4){
                $this->redirect('Home/Pay/choosePay2',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }else{
                $this->redirect('Home/Pay/choosePay',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }
        }else{
            $this->error('订单生成失败');
        }
    }

    //购买产品IPS支付
    public function ipsPayment(){
        //订单号
        $ir_receiptnum  = I('post.ir_receiptnum');
        //用户iuid
        $iuid           = I('post.iuid');
        //订单信息查询
        $order          = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        // wsdl模式访问wsdl程序
        $client = new \SoapClient("https://pay.hkipsec.com/webservice/GetQRCodeWebService.asmx?wsdl",
            array(
                'trace' => true,
                'exceptions' => true,
                'stream_context'=>stream_context_create(array('ssl' => array('verify_peer'=>false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        )
                    )
                )
            ));
        //E0001904
        // $merchantcert = "GB30j0XP0jGZPVrJc6G69PCLsmPKNmDiISNvrXc0DB2c7uLLFX9ah1zRYHiXAnbn68rWiW2f4pSXxAoX0eePDCaq3Wx9OeP0Ao6YdPDJ546R813x2k76ilAU8a3m8Sq0";
        //E000404
        $merchantcert = "1mtfZAJ3sGPc22Vq20LUaJ9Z8w0S8BBP3Jc5uJkwM7v7099nbmwwvVfICu7CkQVGea9JzzVIpzh3xb9YNmRvpp47DtTam7lWCF20aPOBrDgVOCvAL9PXZ91P6bff6U6H";

        try{
            // $merAccNo       = 'E0001904';
            $merAccNo       = "E0004004";
            $orderId        = $ir_receiptnum;
            $fee_type       = "CNY";
            $amount         = $order['ir_price'];
            // $amount         = '0.1';
            $goodsInfo      = "Product";
            $strMerchantUrl = "http://apps.hapy-life.com/hapylife/index.php/Home/Purchase/getResponse";
            $cert           = $merchantcert;
            $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
            $signMD5_lower = strtolower(md5($signMD5));

            $para = array(
                'merAccNo'      => $merAccNo,
                'orderId'       => $orderId,
                'fee_type'      => $fee_type,
                'amount'        => $amount,
                'goodsInfo'     => $goodsInfo,
                'strMerchantUrl'=> $strMerchantUrl,
                'signMD5'       => $signMD5_lower
            );

            $result      = $client->GetQRCodeXml($para);
            //对象操作
            $xmlstr      = $result->GetQRCodeXmlResult;
            //构造SimpleXMLEliement对象
            $xml         = new \SimpleXMLElement($xmlstr);
            //微信支付链接
            $code_url    = (string)$xml->code_url;
            $return_code = (string)$xml->return_code;
            $return_msg  = (string)$xml->return_msg;

            //返回数据
            $para['code_url']    = $code_url;
            $para['return_code'] = $return_code;
            $para['return_msg']  = $return_msg;
            //生成二维码
            $url            = createCode(urldecode($code_url),'Upload/avatar/'.$ir_receiptnum.'.png');
            $para['qrcode'] = C('WEB_URL').'/Upload/avatar/'.$ir_receiptnum.'.png';
            $this->ajaxreturn($para);
            
        }catch(SoapFault $f){
            echo "Error Message:{$f->getMessage()}";
        }
    }

    /**
    * 支付成功订单状态修改
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    **/
    public function getResponse(){
        //获取ips回调数据
        $data = I('post.');

        //写入日志记录
        $jsonStr = json_encode($data);
        $log     = logTest($jsonStr);
        
        //查询订单信息
        $receipt = M('Receiptson')->where(array('pay_receiptnum'=>$data['billno']))->find();

        //支付返回数据验证,是否支付成功验证
        if($data['succ'] == 'Y'){
            //签名验证
            //订单数量&订单金额
            if($data['amount'] == $receipt['ir_price']){                
                //修改订单状态
                $map = array(
                    'ir_paytype' =>1,
                    'status'  =>2,
                    'paytime'=>time(),
                    'ips_trade_no' => $data['ipsbillno'],
                    'ips_trade_status' => $data['msg']
                );
                $change_orderstatus = M('Receiptson')->where(array('pay_receiptnum'=>$data['billno']))->save($map);
                if($change_orderstatus){
                    $order   = D('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();  
                    $userinfo   = D('User')->where(array('iuid'=>$receipt['riuid']))->find();  
                    $product_name   = D('Receiptlist')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->getfield('product_name');  
                    $OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                    $subnum= bcsub($order['ir_unpaid'],$receipt['ir_price'],2);
                    if($subnum=='0.00'){
                        $sub      = 0;
                        $unp      = 0;
                        $ir_status= 2;
                        $ir_paytime= time();
                    }else{
                        $sub      = $subnum;
                        $unp      = bcdiv($sub,100,4);
                        $ir_status= 202;
                        $ir_paytime= 0;
                    }
                    if($sub==0){
                        $addactivation     = D('Activation')->addAtivation($OrderDate,$receipt['riuid'],$receipt['ir_receiptnum']); 
                        if($order['ir_ordertype'] == 4){
                            // 添加通用券
                            $product = M('Receipt')
                                            ->alias('r')
                                            ->join('hapylife_product AS p ON r.ipid = p.ipid')
                                            ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
                                            ->find();
                            $data = array(
                                    'product' => $product,
                                    'userinfo' => $userinfo,
                                );
                            $data    = json_encode($data);
                            $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
                            $result  = post_json_data($sendUrl,$data);
                            // $back_msg = json_decode($result['result'],true);
                        }
                    }
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp,
                        'ir_paytime' =>$ir_paytime,
                    );
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    if($upreceipt){
                        if($sub == 0){
                            // 发送短信提示
                            $templateId ='178959';
                            $params     = array($receipt['ir_receiptnum'],$product_name);
                            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                            if($sms['errmsg'] == 'OK'){
                                $contents = array(
                                    'acnumber' => $userinfo['acnumber'],
                                    'phone' => $userinfo['phone'],
                                    'operator' => '系统',
                                    'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                    'product_name' => $product_name,
                                    'date' => time(),
                                    'content' => '订单编号：'.$receipt['ir_receiptnum'].'，产品：'.$product_name.'，支付成功。',
                                    'customerid' => $userinfo['customerid']
                                );
                                $logs = M('SmsLog')->add($contents);
                            }
                        }else{
                            // 共总支付
                            $total = bcsub($order['ir_unpaid'],$sub,2);
                            // 发送短信提示
                            $templateId ='178957';
                            $params     = array($receipt['ir_receiptnum'],$receipt['ir_price'],$total,$sub);
                            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                            if($sms['errmsg'] == 'OK'){
                                $contents = array(
                                    'acnumber' => $userinfo['acnumber'],
                                    'phone' => $userinfo['phone'],
                                    'operator' => '系统',
                                    'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                    'product_name' => '',
                                    'date' => time(),
                                    'content' => '订单编号：'.$receipt['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$sub,
                                    'customerid' => $userinfo['customerid']
                                );
                                $logs = M('SmsLog')->add($contents);
                            }
                        }
                    }
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);  
                }
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);         
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 购买产品畅捷支付
    **/
    public function cjPayment(){
        //订单号
        $order_num  = I('get.ir_receiptnum');
        // p($order_num);die;
        $order      = M('Receiptson')->where(array('pay_receiptnum'=>$order_num))->find();
        // p($order);die;
        // p($order);die;
        $postData                      = array();   
        // 基本参数
        $postData['Service']           = 'nmg_quick_onekeypay';
        $postData['Version']           = '1.0';
        // $postData['PartnerId']         = '200001280051';//商户号
        $postData['PartnerId']         = '200001380239';//商户号
        $postData['InputCharset']      = 'UTF-8';
        $postData['TradeDate']         = date('Ymd').'';
        $postData['TradeTime']         = date('His').'';
        $postData['ReturnUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/Purchase/getPayUrl?ir_receiptnum='.$order_num;// 前台跳转url
        $postData['Memo']              = '备注';
        // 4.4.2.8. 直接支付请求接口（畅捷前台） 业务参数++++++++++++++++++
        $postData['TrxId']             = $order_num; //外部流水号
        $postData['SellerId']          = '200001380239'; //商户编号，调用畅捷子账户开通接口获取的子账户编号;该字段可以传入平台id或者平台id下的子账户号;作为收款方使用；与鉴权请求接口中MerchantNo保持一致
        $postData['SubMerchantNo']     = '200001380239'; //子商户，在畅捷商户自助平台申请开通的子商户，用于自动结算
        $postData['ExpiredTime']       = '48h'; //订单有效期，取值范围：1m～48h。单位为分，如1.5h，可转换为90m。用来标识本次鉴权订单有效时间，超过该期限则该笔订单作废
        $postData['MerUserId']         = $order['riuid']; //用户标识
        $postData['BkAcctTp']          = ''; //卡类型（00 –银行贷记账户;01 –银行借记账户;）
        // $postData['BkAcctNo']       =   rsaEncrypt('XXXXX'); //卡号
        $postData['BkAcctNo']          = ''; //卡号
        $postData['IDTp']              = ''; //证件类型，01：身份证
        //$postData['IDNo']            =   rsaEncrypt('XXXX'); //证件号
        $postData['IDNo']              = ''; //证件号
        // $postData['CstmrNm']        =   rsaEncrypt('XX'); //持卡人姓名
        $postData['CstmrNm']           = ''; //持卡人姓名
        // $postData['MobNo']          =   rsaEncrypt('XXXXX'); //银行预留手机号
        $postData['MobNo']             = ''; //银行预留手机号      
        $postData['CardCvn2']          = rsaEncrypt(''); //CVV2码，当卡类型为信用卡时必填
        $postData['CardExprDt']        = rsaEncrypt(''); //有效期，当卡类型为信用卡时必填
        $postData['TradeType']         = '11'; //交易类型（即时 11 担保 12）
        $postData['TrxAmt']            = $order['ir_price']; //交易金额
        $postData['EnsureAmount']      = ''; //担保金额
        $postData['OrdrName']          = '商品名称'; //商品名称
        $postData['OrdrDesc']          = ''; //商品详情
        $postData['RoyaltyParameters'] = '';      //"[{'userId':'13890009900','PID':'2','account_type':'101','amount':'100.00'},{'userId':'13890009900','PID':'2','account_type':'101','amount':'100.00'}]"; //退款分润账号集
        $postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Home/Purchase/notifyVerify';//异步通知地址
        $postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
        $postData['Extension']         = '';//扩展字段s
        $postData['Sign']              = rsaSign($postData);
        $postData['SignType']          = 'RSA'; //签名类型      
        $query                         = http_build_query($postData);
        $url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
        $data['url']                   = $url;
        header("Location:".$url);
    }

    //购买产品畅捷返回结果
    public function notifyVerify(){
        //I('post')，$_POST 无法获取API post过来的字符串数据
        $jsonStr = file_get_contents("php://input");
        //写入服务器日志文件
        $log     = logTest($jsonStr);
        $data    = explode('&', $jsonStr);
        //签名数据会被转码，需解码urldecode
        foreach ($data as $key => $value) {
            $temp = explode('=', $value);
            $map[$temp[0]]=urldecode(trim($temp[1]));
        }
        // $map['outer_trade_no'] = '20180808104800320253';
        $receipt = M('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->find();
        $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
        $cjPayStatus = M('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->save($map);
        //验签
        $return = rsaVerify($map);
        //更改订单状态
        if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
            $whereSon= array(
                'status'    =>2,
                'ir_paytype'=>4
            );
            $saveSon = D('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->save($whereSon);
            if($saveSon){
                D('Receiptson')->where(array('pay_receiptnum'=>$map['outer_trade_no']))->setfield('paytime',time());
                $order = D('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->find();
                $subnum= bcsub($order['ir_unpaid'],$receipt['ir_price'],2);
                if($subnum=='0.00'){
                    $sub      = 0;
                    $unp      = 0;
                    $ir_status= 2;
                    $ir_paytime = time();
                }else{  
                    $sub      = $subnum;
                    $unp      = bcdiv($sub,100,4);
                    $ir_status= 202;
                    $ir_paytime = 0;
                }
                if($sub==0){
                    //判断是否新代理注册
                    if($order['htid']){
                        $tmpeArr = M('Tempuser')->where(array('htid'=>$order['htid']))->find();
                        //添加新用户
                        $keyword= 'HPL';
                        $custid = M('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
                        if(empty($custid)){
                            $CustomerID = 'HPL00000001';
                        }else{
                            $num   = substr($custid,3);
                            $nums  = $num+1;
                            $count = strlen($nums);
                            switch ($count) {
                                case '1':
                                    $CustomerID = 'HPL0000000'.$nums;
                                    break;
                                case '2':
                                    $CustomerID = 'HPL000000'.$nums;
                                    break;
                                case '3':
                                    $CustomerID = 'HPL00000'.$nums;
                                    break;
                                case '4':
                                    $CustomerID = 'HPL0000'.$nums;
                                    break;
                                case '5':
                                    $CustomerID = 'HPL000'.$nums;
                                    break;
                                case '6':
                                    $CustomerID = 'HPL00'.$nums;
                                    break;
                                case '7':
                                    $CustomerID = 'HPL0'.$nums;
                                    break;
                                default:
                                    $CustomerID = 'HPL'.$nums;
                                    break;
                             } 
                        }
                        //用户资料
                        $tmpe = array(
                            'EnrollerID'  =>$tmpeArr['enrollerid'],
                            'Sex'         =>$tmpeArr['sex'],
                            'LastName'    =>$tmpeArr['lastname'],
                            'FirstName'   =>$tmpeArr['firstname'],
                            'Email'       =>$tmpeArr['email'],
                            'PassWord'    =>md5($tmpeArr['password']),
                            'acid'        =>$tmpeArr['acid'],
                            'acnumber'    =>$tmpeArr['acnumber'],
                            'Phone'       =>$tmpeArr['phone'],
                            'ShopAddress1'=>$tmpeArr['shopaddress1'],
                            'ShopArea'    =>$tmpeArr['shoparea'],
                            'ShopCity'    =>$tmpeArr['shopcity'],
                            'ShopProvince'=>$tmpeArr['shopprovince'],
                            'ShopCountry' =>$tmpeArr['shopcountry'],
                            'Idcard'      =>$tmpeArr['idcard'],
                            'JustIdcard'  =>$tmpeArr['justidcard'],
                            'BackIdcard'  =>$tmpeArr['backidcard'],
                            'Language'    =>$tmpeArr['language'],
                            'EnLastName'  =>$tmpeArr['enlastname'],
                            'EnFirstName' =>$tmpeArr['enfirstname'],
                            'EnMiddleName'=>$tmpeArr['enmiddlename'],
                            'DeviceType'  =>$tmpeArr['devicetype'],
                            'Browser'     =>$tmpeArr['browser'],
                            'PaymentType' =>$tmpeArr['paymenttype'],
                            'BankName'    =>$tmpeArr['bankname'],
                            'BankAccount' =>$tmpeArr['bankaccount'],
                            'BankProvince'=>$tmpeArr['bankprovince'],
                            'BankCity'    =>$tmpeArr['bankcity'],
                            'BankArea'    =>$tmpeArr['bankarea'],
                            'SubName'     =>$tmpeArr['subname'],
                            'AccountType' =>$tmpeArr['accounttype'],
                            'CustomerID'  =>$CustomerID,
                            'OrderDate'   =>date("m/d/Y h:i:s A"),
                            'Number'      =>1,
                            'MailingProvince'    =>$tmpeArr['mailingprovince'],
                            'TermsAndConditions' =>1,
                            'DeviceGeolocation'  =>$tmpeArr['devicegeolocation'],
                            'BrowserVersion'     =>$tmpeArr['browserversion'],
                            'DistributorType'    =>D('Product')->where(array('ipid'=>$order['ipid']))->getfield('ip_after_grade'),
                            'JoinedOn'    => time(),
                            'WvPass' => $tmpeArr['password'],
                        );
                        $update     = M('User')->add($tmpe);       
                        $riuid      = $update;
                        $OrderDate  = date("Y-m-d",strtotime("-1 month",time()));
                        $userinfo= M('User')->where(array('CustomerID'=>$CustomerID))->find();
                        $status  = array(
                            'ir_status'  =>$ir_status,
                            'rCustomerID'=>$CustomerID,
                            'riuid'      =>$userinfo['iuid'],
                            'ia_name'    =>$userinfo['lastname'].$userinfo['firstname'],
                            'ia_name_en' =>$userinfo['enlastname'].$userinfo['enfirstname'],
                            'ia_phone'   =>$userinfo['phone'],
                            'ia_address' =>$userinfo['shopaddress1'],
                            'ir_unpaid'  =>$sub,
                            'ir_unpoint' =>$unp,
                            'ir_paytime' =>$ir_paytime,
                        );
                        //更新订单信息
                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    }else if($order['ir_ordertype'] == 4){
                        // 添加通用券
                        $product = M('Receipt')
                                        ->alias('r')
                                        ->join('hapylife_product AS p ON r.ipid = p.ipid')
                                        ->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))
                                        ->find();
                        $data = array(
                                'product' => $product,
                                'userinfo' => $userinfo,
                                'ir_receiptnum' => $receipt['ir_receiptnum'],
                            );
                        $data    = json_encode($data);
                        $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/addCoupon";
                        // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/addCoupon";
                        $result  = post_json_data($sendUrl,$data);
                        $back_msg = json_decode($result['result'],true);
                        if($back_msg['status']){
                            $this->success('完成支付',U('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$receipt['ir_receiptnum'])));
                        }
                    }else{
                        $userinfo   = D('User')->where(array('iuid'=>$order['riuid']))->find();
                        //修改用户最近订单日期/是否通过/等级/数量
                        $tmpe['iuid'] = $order['riuid'];
                        //产品等级
                        $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$order['ipid']))->getfield('ip_after_grade');
                        //购买产品次数+1
                        $tmpe['Number']          = $userinfo['number']+1;
                        //number 购买产品的次数
                        if($userinfo['number']==0){
                            //支付日期
                            $tmpe['OrderDate']= date("m/d/Y h:i:s A");
                            $OrderDate        = date("Y-m-d",strtotime("-1 month",time()));
                        }else{
                            $OrderDate        = $userinfo['orderdate'];
                        }
                        //修改用户信息
                        $update    = D('User')->save($tmpe);
                        $riuid     = $order['riuid'];
                        $status  = array(
                            'ir_status'  =>$ir_status,
                            'ir_unpaid'  =>$sub,
                            'ir_unpoint' =>$unp,
                            'ir_paytime' =>$ir_paytime,
                        );                   
                        //更新订单信息
                        $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                        $tmpeArr['password'] = $userinfo['wvpass'];
                        $status['ia_name']   = $userinfo['shopaddress1'];
                    }
                    if($upreceipt){ 
                        $addactivation = D('Activation')->addAtivation($OrderDate,$riuid,$order['ir_receiptnum']);
                        if($tmpeArr['password']){
                            $usa    = new \Common\UsaApi\Usa;
                            $result = $usa->createCustomer($userinfo['customerid'],$tmpeArr['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],'RBS,DTP');
                            if(!empty($result['result'])){
                                $log = addUsaLog($result['result']);
                                $maps = json_decode($result['result'],true);
                                $wv  = array(
                                            'wvCustomerID' => $maps['wvCustomerID'],
                                            'wvOrderID'    => $maps['wvOrderID'],
                                            'Products'     => 'RBS,DTP'
                                        );
                                $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
                                if($res){
                                    $templateId ='178952';
                                    $params     = array($CustomerID);
                                    $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                                    if($sms['errmsg'] == 'OK'){
                                        $contents = array(
                                                    'acnumber' => $userinfo['acnumber'],
                                                    'phone' => $userinfo['phone'],
                                                    'operator' => '系统',
                                                    'addressee' => $userinfo['shopaddress1'],
                                                    'product_name' => $receiptlist['product_name'],
                                                    'date' => time(),
                                                    'content' => '恭喜您创建成功，您的会员号码是'.$CustomerID.'，同时注意查收Rovia邮件。',
                                                    'customerid' => $CustomerID
                                        );
                                        $logs = M('SmsLog')->add($contents);
                                    }
                                }
                            }    
                        }
                    }

                }else{
                    $status  = array(
                        'ir_status'  =>$ir_status,
                        'ir_unpaid'  =>$sub,
                        'ir_unpoint' =>$unp
                    );                   
                    //更新订单信息
                    $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$receipt['ir_receiptnum']))->save($status);
                    if($upreceipt){
                        // 总共已经支付金额
                        $total = bcsub($receipt['ir_price'],$sub,2);
                        // 发送短信提示
                        $templateId ='178957';
                        $params     = array($receipt['ir_receiptnum'],$receiptson['ir_price'],$total,$sub);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $contents = array(
                                        'acnumber' => $userinfo['acnumber'],
                                        'phone' => $userinfo['phone'],
                                        'operator' => '系统',
                                        'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                        'product_name' => $receiptlist['product_name'],
                                        'date' => time(),
                                        'content' => '订单编号：'.$receipt['ir_receiptnum'].'，收到付款'.$receiptson['ir_price'].'，总共已支付'.$total.'剩余需支付'.$sub,
                                        'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                    }
                }
            }
        }
    }
    /**
    *
    **/
    public function getPayUrl(){
        $pay_receiptnum = I('get.ir_receiptnum');
        $ir_receiptnum  = D('Receiptson')->where(array('pay_receiptnum'=>$pay_receiptnum))->getField('ir_receiptnum');
        $order          = D('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($order['ir_unpaid']==0){
            if($order['htid']){
                $this->redirect('Home/Register/new_regsuccess',array('ir_receiptnum'=>$order['ir_receiptnum']));
            }else{
                $this->redirect('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$order['ir_receiptnum']));
            }
        }else{
            if($order['ir_ordertype'] == 1){
                $this->redirect('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$order['ir_receiptnum']));
            }else{
                $this->redirect('Home/Purchase/myOrderInfo',array('ir_receiptnum'=>$order['ir_receiptnum']));
            }
        }
     }

    /**
    * 购买产品订单状态查询
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单编号
    **/
    public function checkreceipt(){
        $ir_receiptnum = I('post.ir_receiptnum');
        //订单状态查询
        $receipt       = M('Receiptson')->where(array('pay_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['status'] == 2){
            //支付成功
            $data['ir_price'] = $receipt['ir_price'];
            $data['status'] = 1;
            $data['msg'] = '支付成功，请跳转...';
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg'] = '正在支付，请等待...';
            $this->ajaxreturn($data);
        }
    }

// *****************收货地址********************
    /**
    * 收货地址列表
    **/ 
    public function addressList(){
        $iuid = $_SESSION['user']['id'];
        // 查询注册信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 
        // 查询地址表信息
        $ia_road = M('Address')->where(array('iuid'=>$iuid))->getField('ia_road',true); 
        
        if(!in_array($userinfo['shopaddress1'], $ia_road) && $userinfo['is_login'] == 0 && !empty($userinfo['shopaddress1'])){
           $message = array(
                    'iuid'            => $userinfo['iuid'],
                    'ia_name'         => $userinfo['lastname'].$userinfo['firstname'],
                    'ia_phone'        => $userinfo['phone'],
                    'ia_province'     => $userinfo['shopprovince'],
                    'ia_town'         => $userinfo['shopcity'],
                    'ia_region'       => $userinfo['shoparea'],
                    'ia_road'         => $userinfo['shopaddress1'],
                    'is_address_show' => 1
                );
            $result = M('Address')->add($message);
            if($result){
                $arr['is_login'] = 1;
                $res = M('User')->where(array('iuid'=>$iuid))->save($arr);
            }
        }
        
        $data = M('Address')->where(array('iuid'=>$iuid))->order('iaid DESC')->select();
        $assign = array(
                    'data' => $data
                );
        $this->assign($assign);
        $this->display();
    } 

    /**
    * 添加收货地址
    **/ 
    public function addressAdd(){
        $data = array(
            'iuid'        => I('post.iuid'),
            'ia_name'     => I('post.ia_name'),
            'ia_phone'    => I('post.ia_phone'),
            'ia_province' => I('post.ia_province'),
            'ia_town'     => I('post.ia_town'),
            'ia_region'   => I('post.ia_region'),
            'ia_road'     => I('post.ia_road'),
        );
        $address = D('Address')->where(array('iuid'=>I('post.iuid'),'is_address_show'=>1))->find();
        if($address){
            $data['is_address_show'] = 0;
        }else{
            $data['is_address_show'] = 1;
        }
        if(!empty($data['ia_name']) && !empty($data['ia_phone']) && !empty($data['ia_province']) && !empty($data['ia_town']) && !empty($data['ia_region']) && !empty($data['ia_road'])){
            $result = M('Address')->add($data);   
        }
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('添加失败');
        }
    }

    /**
    * 编辑收货地址
    **/ 
    public function addressEdit(){
        $iuid = $_SESSION['user']['id'];
        $iaid = M('Address')->where(array('iuid'=>$iuid,'is_address_show'=>1))->getfield('iaid');

        $data = I('post.');
        
        if($data['is_address_show']){
            $result = M('Address')->where(array('iaid'=>$data['iaid']))->save($data);
            if($result){
                $message = array(
                             'is_address_show' => 0,
                        );
                $res = M('Address')->where(array('iaid'=>$iaid))->save($message);
            }
        }else{
            $result = M('Address')->where(array('iaid'=>$data['iaid']))->save($data);
        }
        
        if($result || $res){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('修改失败');
        }
    }

    /**
    * 删除收货地址
    **/ 
    public function addressDelect(){
        $iaid = I('post.iaid');

        $result = M('Address')->where(array('iaid'=>$iaid))->delete();
        
        if($result){
            $this->redirect('Home/Purchase/addressList');
        }else{
            $this->error('删除失败');
        }
    }


// *****************银行地址********************
    /**
    * 银行地址列表
    **/ 
    public function bankList(){
        $iuid = $_SESSION['user']['id'];
        // 查询注册信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 
        // 查询银行表信息
        $bankaccount = M('Bank')->where(array('iuid'=>$iuid))->getField('bankaccount',true); 
        
        if(!in_array($userinfo['bankaccount'], $bankaccount) && $userinfo['is_login'] == 0 && !empty($userinfo['bankaccount'])){
           $message = array(
                    'iuid'         => $userinfo['iuid'],
                    'iu_name'      => $userinfo['lastname'].$userinfo['firstname'],
                    'bankaccount'  => $userinfo['bankaccount'],
                    'bankprovince' => $userinfo['bankprovince'],
                    'banktown'     => $userinfo['bankcity'],
                    'bankregion'   => $userinfo['bankarea'],
                    'bankname'     => $userinfo['bankname'],
                    'bankbranch'   => $userinfo['subname'],
                    'createtime'   => time(),
                    'isshow'       => 1,
                );
            $result = M('Bank')->add($message);
            if($result){
                $arr['is_login'] = 1;
                $res = M('User')->where(array('iuid'=>$iuid))->save($arr);
            }
        }
        
        $data = M('Bank')->where(array('iuid'=>$iuid))->order('bid DESC')->select();

        $assign = array(
                    'data' => $data,
                    'userinfo' => $userinfo
                );
        $this->assign($assign);
        $this->display();
    } 


    /**
    * 添加银行地址
    **/ 
    public function bankAdd(){
        $iuid = $_SESSION['user']['id'];
        // 查询注册信息
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find(); 

        $data = I('post.');
        $data = array(
                'iuid'         => I('post.iuid'),
                'iu_name'      => $userinfo['lastname'].$userinfo['firstname'],
                'bankaccount'  => I('post.bankaccount'),
                'bankprovince' => I('post.bankprovince'),
                'banktown'     => I('post.banktown'),
                'bankregion'   => I('post.bankregion'),
                'bankname'     => I('post.bankname'),
                'bankbranch'   => I('post.bankbranch'),
                'createtime'   => time(),
                );
        if(!empty($data['bankaccount']) && !empty($data['bankprovince']) && !empty($data['banktown']) && !empty($data['bankregion']) && !empty($data['bankname']) && !empty($data['bankbranch'])){
            $result = M('Bank')->add($data);          
        }
        if($result){
            $this->redirect('Home/Purchase/bankList');
        }else{
            $this->error('添加失败');
        }
    }

    /**
    * 编辑银行地址
    **/ 
    public function bankEdit(){
        $iuid = $_SESSION['user']['id'];
        $bid = M('Bank')->where(array('iuid'=>$iuid,'isshow'=>1))->getfield('bid');

        $data = I('post.');

        if($data['isshow']){
            $result = M('Bank')->where(array('bid'=>$data['bid']))->save($data);
            if($result){
                $message = array(
                             'isshow' => 0,
                        );
                $res = M('Bank')->where(array('bid'=>$bid))->save($message);
            }
        }else{
            $result = M('Bank')->where(array('bid'=>$data['bid']))->save($data);
        }
        
        if($result || $res){
            $this->redirect('Home/Purchase/bankList');
        }else{
            $this->error('修改失败');
        }
    }

    /**
    * 删除银行地址
    **/ 
    public function bankDelect(){
        $bid = I('post.bid');

        $result = M('Bank')->where(array('bid'=>$bid))->delete();
        
        if($result){
            $this->redirect('Home/Purchase/bankList');
        }else{
            $this->error('删除失败');
        }
    }

// **************我的推荐人*****************
    public function recommenderList(){
        $customerid = $_SESSION['user']['username'];
        $data = M('User')->where(array('enrollerid'=>$customerid))->select();

        $assign = array(
                    'data' => $data,
                );
        $this->assign($assign);
        $this->display();
    }

// ***********获取验证码*************
    public function changePassword(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('data',$data);
        $this->display();
    }

// ***********我的积分**************
    /**
    * 我的积分
    **/
    public function myPoint(){
        $iuid = $_SESSION['user']['id'];
        $data = M('User')->where(array('iuid'=>$iuid))->find();
        $log  = M('Log')->where(array('iuid'=>$iuid,'type'=>1))->order('create_time DESC')->limit(50)->select();
        $assign = array(
                    'data' => $data,
                    'log' => $log
                );
        $this->assign($assign);
        $this->display();
    }

    /**
    * 用户EP转账
    **/ 
    public function EPtransfer(){
        $iuid = $_SESSION['user']['id'];
        //app服务器用户信息
        $data  = M('User')->where(array('iuid'=>$iuid))->find();
        // p($data);
        $log  = M('Log')->where(array('iuid'=>$iuid,'type'=>1,'action'=>array('in','1,2')))->order('create_time DESC')->limit(50)->select();
        // p($log);
        $assign = array(
                    'data' => $data,
                    'log' => $log,
                    );
        $this->assign($assign);
        $this->display();
    }

    /**
    * 返回用户名称
    **/ 
    public function checkName(){
        $CustomerID = strtoupper(I('post.CustomerID'));
        $data = M('User')->where(array('CustomerID'=>$CustomerID))->find();
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 转账EP确认密码
    **/ 
    public function toCheckPasswordEP(){
        $data = I('post.');
        $assign = array(
                    'data' => $data,
                    );
        $this->assign($assign);
        $this->display();
    }

    /**
    * 生成订单
    **/
    public function receiptlist(){
        $iuid = $_SESSION['user']['id'];
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $password = md5(trim(I('post.password')));
        if($userinfo['password'] != $password){
            $this->error('密码错误',U('Home/Purchase/myPoint'));
        }else{
            $tohu_nickname  = strtoupper(trim(I('post.CustomerID')));
            $whichApp       = 5;
            $point          = trim(I('post.point'));
            // $userinfo   = M('User')->where(array('CustomerID'=>$hu_nickname))->find();
            $touserinfo = M('User')->where(array('CustomerID'=>$tohu_nickname))->find();
            //如果是转账，调取默认的银行账户信息
            $bank       = M('Bank')->where(array('iuid'=>$iuid,'isshow'=>1))->find();

            if($tohu_nickname === $userinfo['customerid']){
                //提现
                $iuid       = $userinfo['iuid'];
                $unpoint    = $point;
                $iu_point   = $userinfo['iu_point'];
                $iu_unpoint = $userinfo['iu_unpoint'];
                //更新用户积分
                $point      = bcsub($iu_point,$unpoint,4);
                $newunpoint = bcadd($iu_unpoint,$unpoint,4);
                
                if($point<0){
                    $this->error('积分余额不足',U('Home/Purchase/myPoint'));
                }
                $map     = array(
                            'iuid'      =>$userinfo['iuid'],
                            'iu_point'  =>$point,
                            'iu_unpoint'=>$newunpoint
                        );
                $feepoint =$unpoint*0.05;
                $realpoint=bcsub($unpoint,$feepoint,4);
                //生成提现订单
                $pointNo = 'EP'.date('YmdHis').rand(10000, 99999);
                //更新用户积分
                $save   = M('User')->save($map);
                if($save){
                    $content = '单号:'.$pointNo.',提取积分:'.$unpoint.',剩余积分:'.$point;
                    $tmp     = array(
                        'iuid'           =>$userinfo['iuid'],
                        'pointNo'        =>$pointNo,
                        'hu_username'    =>$userinfo['lastname'].$userinfo['firstname'],
                        'hu_nickname'    =>$userinfo['customerid'],
                        'iu_bank'        =>$bank['bankname'],
                        'iu_bankbranch'  =>$bank['bankbranch'],
                        'iu_bankaccount' =>$bank['bankaccount'],
                        'iu_bankprovince'=>$bank['bankprovince'],
                        'iu_bankcity'    =>$bank['bankregion'],
                        'iu_bankuser'    =>$bank['iu_name'],
                        'getpoint'       =>$unpoint,
                        'feepoint'       =>$feepoint,
                        'realpoint'      =>$realpoint,
                        'unpoint'        =>$newunpoint,
                        'leftpoint'      =>$point,
                        'date'           =>date('Y-m-d H:i:s'),
                        'status'         =>0,
                        'pointtype'      =>6,
                        'whichApp'       =>$whichApp,
                        'send'           =>$userinfo['customerid'],
                        'received'       =>'系统冻结',
                        'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,提现冻结'.$unpoint.'EP到'.'系统冻结'.',剩EP余额'.$point
                    );
                    $addtmp = M('Getpoint')->add($tmp);
                    //写入日志记录
                    $add     = addLog($iuid,$content,$action=0,$type=1);
                    if($add && $addtmp){
                        $this->success('提现成功',U('Home/Purchase/myPoint'));
                    }else{
                        $this->error('提现失败',U('Home/Purchase/myPoint'));
                    }
                }
            }else{
                $leftpoint_user  = bcsub($userinfo['iu_point'],$point,4);
                if($leftpoint_user<0){
                    $this->error('积分不足',U('Home/Purchase/myPoint'));
                }else{
                    $leftpoint_touser = bcadd($touserinfo['iu_point'],$point,4);
                    $user = array(
                            'iuid'      =>$userinfo['iuid'],
                            'iu_point'  =>$leftpoint_user
                        );
                    $touser = array(
                            'iuid'      =>$touserinfo['iuid'],
                            'iu_point'  =>$leftpoint_touser
                        );
                    $save_userpoint  = M('User')->save($user);
                    $save_touserpoint= M('User')->save($touser);
                    if($save_userpoint && $save_touserpoint){
                        //写入日志记录
                        //转出
                        $content   = '你给'.$touserinfo['customerid'].'转赠积分'.$point; 
                        $tmp1     = array(
                            'iuid'           =>$userinfo['iuid'],
                            'pointNo'        =>'EP'.date('YmdHis').rand(10000, 99999),
                            'hu_username'    =>$userinfo['lastname'].$userinfo['firstname'],
                            'hu_nickname'    =>$userinfo['customerid'],
                            'getpoint'       =>$point,
                            'feepoint'       =>0,
                            'realpoint'      =>$point,
                            'unpoint'        =>$userinfo['iu_unpoint'],
                            'leftpoint'      =>$leftpoint_user,
                            'date'           =>date('Y-m-d H:i:s'),
                            'handletime'     =>date('Y-m-d H:i:s'),
                            'status'         =>2,
                            'pointtype'      =>4,
                            'whichApp'       =>$whichApp,
                            'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,转账出'.$point.'EP到'.$touserinfo['customerid'].',剩EP余额'.$leftpoint_user,
                            'send'           =>$userinfo['customerid'],
                            'received'       =>$touserinfo['customerid']
                        );
                        $addtmp1 = M('Getpoint')->add($tmp1);
                        //转入
                        $add_userlog    = addLog($userinfo['iuid'],$content,$action=1,$type=1);
                        $ucontent = $userinfo['customerid'].'给你转赠积分'.$point;
                        $tmp2     = array(
                            'iuid'           =>$touserinfo['iuid'],
                            'pointNo'        =>'EP'.date('YmdHis').rand(10000, 99999),
                            'hu_username'    =>$touserinfo['lastname'].$touserinfo['firstname'],
                            'hu_nickname'    =>$touserinfo['customerid'],
                            'getpoint'       =>$point,
                            'feepoint'       =>0,
                            'realpoint'      =>$point,
                            'unpoint'        =>$touserinfo['iu_unpoint'],
                            'leftpoint'      =>$leftpoint_touser,
                            'date'           =>date('Y-m-d H:i:s'),
                            'handletime'     =>date('Y-m-d H:i:s'),
                            'status'         =>2,
                            'pointtype'      =>5,
                            'whichApp'       =>$whichApp,
                            'content'        =>$userinfo['customerid'].'在'.date('Y-m-d H:i:s').'时,转账入'.$point.'EP到'.$touserinfo['customerid'].',剩EP余额'.$leftpoint_touser,
                            'send'           =>$userinfo['customerid'],
                            'received'       =>$touserinfo['customerid']
                        );
                        $addtmp2 = M('Getpoint')->add($tmp2); 
                        $add_touserlog  = addLog($touserinfo['iuid'],$ucontent,$action=2,$type=1);
                        if($add_userlog && $add_touserlog){
                            $this->success('转出成功',U('Home/Purchase/myPoint'));
                        }else{
                            $this->error('转出失败',U('Home/Purchase/myPoint'));
                        }
                    } 
                } 
            }
        }
    }

    /**
    * 每月积分
    **/ 
    public function myPointMonth(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $status = '0,1,2,3';
        $assign = D('Getpoint')->getAllPoint(D('Getpoint'),$CustomerID,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4);
            $data[$key]['reduce']   = bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4);
        }
        // p($data);
        // die;
        $assign = array(
                    'data' => $data
                    );
        $this->assign($assign);
        $this->display();
    }

    /**
    * 每日积分
    **/ 
    public function myPointDay(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('get.date');
        $status      = '0,1,2,3';
        $assign      = D('Getpoint')->getPointDay(D('Getpoint'),$CustomerID,$date,$status);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['increase'] = bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4);
            $data[$key]['reduce']   = bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4);
        }
        // p($data);
        $assign = array(
                    'data' => $data
                    );
        $this->assign($assign);
        $this->display();
    }

    /**
    * 每日积分详情
    **/ 
    public function myPointDaily(){
        $iuid = $_SESSION['user']['id'];
        $CustomerID = M('User')->where(array('iuid'=>$iuid))->getfield('CustomerID');
        $date        = I('get.date');
        $dates = substr($date,0,7);
        $assign      = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$CustomerID);
        foreach ($assign['data'] as $key => $value) {
            $data[$key]['date']  = $value['date'];
            $data[$key]['point'] = $value['getpoint'];
            switch ($value['pointtype']) {
                case '1':
                    $data[$key]['type']  = '系统减少';
                    $data[$key]['syslog']= '-';
                    break;
                case '2':
                    $data[$key]['type']  = '系统增加';
                    $data[$key]['syslog']= '+';
                    break;
                case '3':
                    $data[$key]['type']  = '新增奖金';
                    $data[$key]['syslog']= '+';
                    break;
                case '4':
                    $data[$key]['type']  = '转账出';
                    $data[$key]['syslog']= '-';
                    break;
                case '5':

                    $data[$key]['type'] = '转账入';
                    $data[$key]['syslog']= '+';
                    break;
                case '6':
                    $data[$key]['type']  = '提现出';
                    $data[$key]['syslog']= '-';
                    break;
                case '7':
                    $data[$key]['type']  = '消费出';
                    $data[$key]['syslog']= '-';
                    break;
                case '8':
                    $data[$key]['type']  = '驳回';
                    $data[$key]['syslog']= '+';
                    break;
            }
        }
        $assign = array(
                    'data' => $data,
                    'dates' => $dates
                    );
        $this->assign($assign);
        $this->display();
    }

}