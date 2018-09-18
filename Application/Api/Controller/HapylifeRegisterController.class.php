<?php 
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
 * 用户注册Controller
 **/
class HapylifeRegisterController extends HomeBaseController{
    /**
    *注册手机区号 is_show值为1
    **/
    public function registerCode(){
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $data[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->ajaxreturn($data);
    }
    /**
    *腾讯云发送短信(注册)
    *参数：phoneNumber(手机号),whichapp(指定app),acnumber(区号)
    **/
    public function smsCode(){
        // require __DIR__ . "/vendor/autoload.php";
        if(!IS_POST){
            $data['status'] = 100;
            $data['msg']    = '请填写手机号';
            $this->ajaxreturn($data);
        }else{
            vendor('SmsSing.SmsSingleSender');
            // 短信应用SDK AppID
            $appid = 1400096409; // 1400开头
            // 短信应用SDK AppKey
            $appkey = "fc1c7e21ab36fef1865b0a3110709c51";
            // 需要发送短信的手机号码
            $phoneNumber = I('post.phoneNumber');
            //手机区号
            $acnumber    = I('post.acnumber');
            // 短信模板ID，需要在短信应用中申请$templateId
            // 签名
            if($acnumber==86){
                $templateId = 127203;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
                $smsSign = "三次猿"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
            }else if($acnumber==886 || $acnumber==852 || $acnumber==853){
                $templateId = 127206;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }else{
                $templateId = 127204;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请      
                $smsSign = "eggcarton";
            }
            $code   =rand(100000,999999);
            $minute ='1';
            // 指定模板ID单发短信
            try {
                $ssender = new \SmsSingleSender($appid, $appkey);
                $params = array($code,$minute);
                $result = $ssender->sendWithParam($acnumber,$phoneNumber,$templateId,$params,$smsSign,"","");  // 签名参数未提供或者为空时，会使用默认签名发送短信
                $rsp = json_decode($result,true);
                if($rsp['errmsg']=='OK'){
                    $mape  = array(
                        'phone'   =>$phoneNumber,
                        'code'    =>$code,
                        'acnumber'=>$acnumber,
                        'date'    =>date('Y-m-d H:i:s')
                    );
                    $add = D('Smscode')->add($mape);
                    $data['status'] = 101;
                    $data['msg']    = '验证码发送成功,请留意短信';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 103;
                    $data['msg']    = '发送失败,请确认手机号码正确并有效';
                    $this->ajaxreturn($data);
                }
            }catch(\Exception $e) {
                $data['status'] = 102;
                $data['msg']    = '发送失败,请确认手机号码正确并有效';
                $this->ajaxreturn($data);
            }

        }
    }
    /********************************************************************新代理注册--需要购买产品********************************************************************************/
    /**
    *检查注册验证码是否正确
    *参数：phoneNumber(手机号),acnumber(区号),code(验证码)
    **/
    public function inCode(){
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $acid        =I('post.acid');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($data && $data['code']==$code){
            $sample['status'] = 1;
            $this->ajaxreturn($sample);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }
    /**
    * 保存用户资料
    **/ 
    public function new_registerInfo(){
        if(!IS_POST){
            $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
            foreach ($mape as $key => $value) {
                $data[$key]         = $value;
                if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                    $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
                }else{
                    $data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
                }
            }
            $data['status'] = 200;
            $data['message']= '未提交任何数据';
            $this->ajaxreturn($data);
        }else{
            $data        = I('post.');
            // $data['iuid']= $_SESSION['user']['id'];
            $data['iuid'] = I('post.iuid');
            if(!empty($data['JustIdcard'])){
                $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                $JustIdcard = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
                $data['JustIdcard'] = C('WEB_URL').'/Upload/file/'.$JustIdcard;
            }
            if(!empty($data['BackIdcard'])){
                $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                $BackIdcard = time().'_'.mt_rand().'.jpg';
                $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
                $data['BackIdcard'] = C('WEB_URL').'/Upload/file/'.$BackIdcard;
            }
            $data['EnrollerID'] = strtoupper(I('post.EnrollerID'));
            $data['LastName'] = trimall(I('post.LastName'));
            $data['FirstName'] = trimall(I('post.FirstName'));
            $data['EnLastName'] = trimall(I('post.EnLastName'));
            $data['EnFirstName'] = trimall(I('post.EnFirstName'));
            $add = D('Tempuser')->add($data);
            if($add){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $sample['status'] = 0;
                $this->ajaxreturn($sample);
            }
        }
    }
    /**
    * 确认信息
    **/ 
    public function confirmationMessage(){
        $iuid = I('post.iuid');
        // $iuid = $_SESSION['user']['id'];
        $id = max(D('Tempuser')->where(array('iuid'=>$iuid))->getField('htid',true));
        $data = D('Tempuser')->where(array('htid'=>$id))->find();
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }

    /**
    * 获取首购产品
    **/ 
    public function new_purchase(){
        $data = D('product')->where(array('ip_type'=>1,'is_pull'=>1))->select();
        $this->ajaxreturn($data);

    }
    /**
    * 获取产品详情
    **/ 
    public function new_purchaseInfo(){
        $ipid =I('post.ipid');
        $data = D('product')->where(array('ipid'=>$ipid))->find();
        $this->ajaxreturn($data);
    }
    /**
    * 首购订单
    **/
    public function registerOrder(){
        $iuid = I('post.iuid');
        $ipid = I('post.ipid');
        $htid = D('Tempuser')->where(array('iuid'=>$iuid))->order('htid desc')->getfield('htid');
        //商品信息
        $product = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('User')->where(array('iuid'=>$iuid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        $con = '首购单';
        $order = array(
            //订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'=>time(),
            //订单的状态(0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过  7待注册)
            'ir_status'=>7,
            //下单用户id
            'riuid'=>$iuid,
            //下单用户
            'rCustomerID'=>$userinfo['customerid'],
            //收货人
            'ia_name'=>$userinfo['lastname'].$userinfo['firstname'],
            //收货人电话
            'ia_phone'=>$userinfo['phone'],
            //收货地址
            'ia_address'=>$userinfo['shopaddress1'],
            //订单总商品数量
            'ir_productnum'=>1,
            //订单总金额
            'ir_price'=>$product['ip_price_rmb'],
            //订单总积分
            'ir_point'=>$product['ip_point'],
            //订单待付款总金额
            'ir_unpaid'=>$product['ip_price_rmb'],
            //订单待付款总积分
            'ir_unpoint'=>$product['ip_point'],
            //订单备注
            'ir_desc'=>$con,
            //订单类型
            'ir_ordertype' => $product['ip_type'],
            //产品id
            'ipid'         => $product['ipid'],
            //待注册用户id
            'htid'        => $htid?$htid:0
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
        $content = '您帮代理进行的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        $log = array(
            'iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>0,
            'type'      =>2,
            'date'      =>date('Y-m-d H:i:s')          
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            $order['status'] = 1;
            $this->ajaxreturn($order);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    * 首购产品畅捷支付
    **/
    public function new_cjPayment(){
        //订单号
        $order_num                     = I('post.ir_receiptnum');
        $order = M('Receipt')->where(array('ir_receiptnum'=>$order_num))->find();
        $postData                      = array();   
        // 基本参数
        $postData['Service']           = 'nmg_quick_onekeypay';
        $postData['Version']           = '1.0';
        // $postData['PartnerId']         = '200001280051';//商户号
        $postData['PartnerId']         = '200001380239';//商户号
        $postData['InputCharset']      = 'UTF-8';
        $postData['TradeDate']         = date('Ymd').'';
        $postData['TradeTime']         = date('His').'';
        $postData['ReturnUrl']         = '';// 前台跳转url
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
        $postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Api/HapylifeRegister/new_notifyVerify';//异步通知地址
        $postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
        $postData['Extension']         = '';//扩展字段
        $postData['Sign']              = rsaSign($postData);
        $postData['SignType']          = 'RSA'; //签名类型      
        $query                         = http_build_query($postData);
        $url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
        $data['url']                   = $url;
        $this->ajaxreturn($data);
    }

    //购买产品畅捷返回结果
    public function new_notifyVerify(){
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
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
        $cjPayStatus = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($map);
        //验签
        $return = rsaVerify($map);
        //更改订单状态
        if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
            if($receipt['ir_status']==7 || $receipt['ir_status'] == 0){
                //修改用户最近订单日期/是否通过/等级/数量
                $tmpe['iuid']     =$receipt['riuid'];
                $find             =D('User')->where(array('iuid'=>$receipt['iuid']))->find();
                if($find['number']==0){
                    $tmpe['OrderDate']= date("m/d/Y h:i:s A");
                    $OrderDate        = date("Y-m-d",strtotime("-1 month",time()));
                    if($find['isnew']==0){
                        if($find['number']==0){
                            $tmpe['IsCheck'] = 1;   
                        }
                    }else{
                        $tmpe['IsCheck'] = 2;
                    }
                }else{
                    $OrderDate       = $find['orderdate'];
                    $tmpe['IsCheck'] = 2;
                }
                $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade');
                $tmpe['Number']   =$find['number']+1;
                $status  = array(
                    'ir_status'  =>2,
                    'ir_paytype' =>4,
                    'ir_paytime' =>time(),
                );
                $update  =D('User')->save($tmpe);
                if($upreceipt){
                    $activaDate = D('Activation')->where(array('iuid'=>$receipt['riuid'],'is_tick'=>1))->order('datetime desc')->getfield('datetime');
                    if(empty($activaDate)){
                        $activa = $OrderDate;
                    }else{
                        $activa = $activaDate;
                    }
                    $day = date('d',strtotime($OrderDate));
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
                    // for($i=0;$i<$receipt['ir_productnum'];$i++) {
                        //删除原先未激活，添加激活
                        $time  = date("Y-m",strtotime("+1 month",strtotime($activa)));
                        $year  = date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日';
                        $endday= date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日';
                        $delete= D('Activation')->where(array('iuid'=>$receipt['riuid'],'datetime'=>$time))->delete();
                        $where =array('iuid'=>$receipt['riuid'],'ir_receiptnum'=>$map['outer_trade_no'],'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
                        $save  = D('Activation')->add($where);
                    // }   
                }
                //更新订单信息
                $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($status);
                if($upreceipt){
                    //通知畅捷完成支付
                    $sample['status'] = 1;
                    $this->ajaxreturn($sample);
                }else{
                    $sample['status'] = 0;
                    $this->ajaxreturn($sample);
                }
            }
        }
    }

    //购买产品IPS支付
    public function ipsPayment(){
        //订单号
        $ir_receiptnum  = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(10000, 99999);
        //用户iuid
        $iuid           = I('post.iuid');
        //订单信息查询
        $order          = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();

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
            $url            = createQrcode(urldecode($code_url),'Upload/avatar/'.$ir_receiptnum.'.png');
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

        //记录数据
        if($data['billno'] != ""){
            $add  = M('Log')->add($data);           
        }
        
        //查询订单信息
        $order = M('Receipt')->where(array('ir_receiptnum'=>$data['billno']))->find();

        //支付返回数据验证,是否支付成功验证
        if($data['succ'] == 'Y'){
            //签名验证
            //订单数量&订单金额
            if($data['amount'] == $order['ir_price']){                
                //修改订单状态
                $map = array(
                    'ir_paytype' =>1,
                    'ir_status'  =>2,
                    'ir_paytime'=>time(),
                    'ips_trade_no' => $data['ipsbillno'],
                    'ips_trade_status' => $data['msg']
                );
                $change_orderstatus = M('Receipt')->where(array('ir_receiptnum'=>$data['billno']))->save($map);

                if($change_orderstatus){
                    $OrderDate         = date("Y-m-d",strtotime("-1 month",time()));
                    $activa = $OrderDate;
                    $day    = date('d',strtotime($OrderDate));
                    if($day>=28){
                        $allday = 28;
                    }else{
                        $allday = $day;
                    }
                    $ddd = $allday-1;
                    if($ddd>=10){
                        $oneday = $ddd;
                    }else{
                        $oneday = '0'.$ddd;
                    }
                    //添加激活
                    $time  = date("Y-m",strtotime("+1 month",strtotime($activa)));
                    $year  = date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日';
                    $endday= date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日';
                    $where =array('iuid'=>$order['riuid'],'ir_receiptnum'=>$order['ir_receiptnum'],'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
                    $save  = M('Activation')->add($where);
                    if($save){
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
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
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
        $receipt       = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
            //支付成功
            $receipt['status'] = 1;
            $receipt['msg'] = '支付成功，请跳转...';
            $this->ajaxreturn($receipt);
        }else{
            $data['status'] = 0;
            $data['msg'] = '正在支付，请等待...';
            $this->ajaxreturn($data);
        }
    }

    /**
    * 注册成功显示页面
    **/ 
    public function registerSuccess(){
        $ir_receiptnum = I('post.ir_receiptnum');
        $data = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum,'ir_status'=>2))->find();
        $this->ajaxreturn($data);
    }

    /**
    * 返回推荐人姓名
    **/ 
    public function checkName(){
        $customerid = strtoupper(trim(I('post.EnrollerID')));
        if($customerid){
            if(substr($customerid,0,3) == 'HPL'){
                $data = M('User')->where(array('CustomerID'=>$customerid))->find();
                if(!empty($data)){
                    $this->ajaxreturn($data);     
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);           
                }
            }else{
                $key      = "QACER3H5T6HGYDCCDAZM3";
                $url      = "https://signupapi.wvhservices.com/api/Hpl/Validate?customerId=".$customerid."&"."key=".$key;
                $wv       = file_get_contents($url);
                $data = json_decode($wv,true);
                if(!empty($data)){
                    $data['lastname'] = $data['lastName'];
                    $data['firstname'] = $data['firstName'];
                    $this->ajaxreturn($data);     
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);           
                }
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);           
        }
    }
    /*********************************************************************普通注册********************************************************************************************/  
    /**
    *检查旧注册验证码是否正确
    *参数：phoneNumber(手机号),acnumber(区号),code(验证码)
    **/
    public function oldInCode(){
        $phoneNumber =I('post.phoneNumber');
        $code        =I('post.code');
        $acnumber    =I('post.acnumber');
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $this->success('验证码正确',U('Home/Register/register'));
            }else{
                $this->error('验证码错误');
            }
        }
    }
    // 普通用户注册
    public function oldregister(){
        if(!IS_POST){
            $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
            foreach ($mape as $key => $value) {
                $data[$key]         = $value;
                if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                    $data[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
                }else{
                    $data[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
                }
            }
            // $data['status'] = 200;
            // $data['message']= '未提交任何数据';
            $this->ajaxreturn($data);
        }else{
            $data = I('post.');
            if(!empty($data['JustIdcard'])){
                $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                $JustIdcard = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
                $data['JustIdcard'] = C('WEB_URL').'/Upload/file/'.$JustIdcard;
            }
            if(!empty($data['BackIdcard'])){
                $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                $BackIdcard = time().'_'.mt_rand().'.jpg';
                $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
                $data['BackIdcard'] = C('WEB_URL').'/Upload/file/'.$BackIdcard;
            }
            $data['PassWord'] = I('PassWord','','md5');
            $data['JoinedOn'] = time();
            $data['CustomerID'] = strtoupper(I('post.CustomerID'));
            $data['LastName'] = trimall(I('post.LastName'));
            $data['FirstName'] = trimall(I('post.FirstName'));
            $data['EnLastName'] = trimall(I('post.EnLastName'));
            $data['EnFirstName'] = trimall(I('post.EnFirstName'));
            $keyword= 'HPL';
            $custid = D('User')->where(array('CustomerID'=>array('like','%'.$keyword.'%')))->order('iuid desc')->getfield('CustomerID');
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
            $data['CustomerID'] = $CustomerID;
            $result = D('User')->add($data);
            if($result){
                $sample = array(
                            'status' => 1,
                            'iuid' => $result
                        );
                $this->ajaxreturn($sample);
            }
        }
    }

    // 确认信息页面
    public function oldregisterInfo(){
        $iuid = I('post.iuid');
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $userinfo['status'] = 1;
        $this->ajaxreturn($userinfo);
    }


    // 注册成功显示页面
    public function oldregSuccess(){
        $iuid = I('post.iuid');
        $data = D('User')->where(array('iuid'=>$iuid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $sample = 0;
            $this->ajaxreturn($sample);
        }
    }


    // 有券注册
    public function hadCoupon(){
        $iuid = I('post.iuid');
        $cu_id = I('post.cu_id');
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $data = array(
                    'hu_nickname' => $userinfo['customerid'],
                    'cu_id' => $cu_id,
                );
        $data    = json_encode($data);
        $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/use_coupon";
        // $sendUrl = "http://localhost/nulife/index.php/Api/Couponapi/use_coupon";
        $results  = post_json_data($sendUrl,$data);
        $back_result = json_decode($results['result'],true);
        if($back_result['status']){
            $ipid = $back_result['ipid'];
            //商品信息
            $product = M('Product')->where(array('ipid'=>$ipid))->find();
            //生成唯一订单号
            $order_num = 'CP'.date('YmdHis').rand(10000, 99999);
            $con = $back_result['c_name'].$back_result['coupon_code'];
            $order = array(
                //订单编号
                'ir_receiptnum' =>$order_num,
                //订单创建日期
                'ir_date'=>time(),
                //订单的状态(0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过  7待注册)
                'ir_status'=>2,
                //下单用户id
                'riuid'=>$iuid,
                //下单用户
                'rCustomerID'=>$userinfo['customerid'],
                //收货人
                'ia_name'=>$userinfo['lastname'].$userinfo['firstname'],
                //收货人电话
                'ia_phone'=>$userinfo['phone'],
                //收货地址
                'ia_address'=>$userinfo['shopprovince'].$userinfo['shopcity'].$userinfo['shoparea'].$userinfo['shopaddress1'],
                //订单总商品数量
                'ir_productnum'=>1,
                //订单总金额
                'ir_price'=>$product['ip_price_rmb'],
                //订单总积分
                'ir_point'=>$product['ip_point'],
                //订单待付款总金额
                'ir_unpaid'=>0,
                //订单待付款总积分
                'ir_unpoint'=>0,
                //订单备注
                'ir_desc'=>$con,
                //订单类型
                'ir_ordertype' => $product['ip_type'],
                //产品id
                'ipid'         => $product['ipid'],
                // 订单支付时间
                'ir_paytime' => time(),
                // 通用券标号
                'coucode' => $back_result['coupon_code'],
                
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
            $content = '您帮代理进行的'.$con.'订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
            $log = array(
                'iuid'      =>$back_result['iuid'],
                'content'   =>$content,
                'action'    =>0,
                'type'      =>2,
                'create_time'   =>time(),
                'create_month'   =>date('Y-m'),
            );
            $addlog = M('Log')->add($log);
            if($addlog){
                if($back_result['is_dt'] == 1){
                    $products = 'RBS,DTP,SIGNUP4';
                }else{
                    $products = 'RBS,DTP,SIGNUP5';
                }
                $usa    = new \Common\UsaApi\Usa;
                $result = $usa->createCustomer($userinfo['customerid'],$userinfo['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$products);
                if(!empty($result['result'])){
                    $log = addUsaLog($result['result']);
                    $maps = json_decode($result['result'],true);
                    $wv  = array(
                        'wvCustomerID' => $maps['wvCustomerID'],
                        'wvOrderID'    => $maps['wvOrderID'],
                        'DistributorType' => $product['ip_after_grade'],
                        'Products'      => $products,
                    );
                    $res = M('User')->where(array('iuid'=>$iuid))->save($wv);
                    if($res){
                        // 发送短信提示
                        $templateId ='183054';
                        $params     = array();
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$order_num))->find();
                            $contents = array(
                                        'acnumber' => $userinfo['acnumber'],
                                        'phone' => $userinfo['phone'],
                                        'operator' => '系统',
                                        'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                                        'product_name' => $receiptlist['product_name'],
                                        'date' => time(),
                                        'content' => '恭喜您注册成功。',
                                        'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }
                        $sample['status'] = 1;
                        $this->ajaxreturn($sample);
                    }else{
                        $sample['status'] = 0;
                        $this->ajaxreturn($sample);
                    }
                }
            }
        }else{
            $sample['status'] = 0;
            $this->ajaxreturn($sample);
        }
    }





}



 ?>