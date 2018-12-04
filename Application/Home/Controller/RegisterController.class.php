<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Api\Controller\HapylifeUsaController;
/**
 * 用户注册Controller
 **/
class RegisterController extends HomeBaseController{

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
        $this->assign('data',$data);
        $this->display();
    }
    public function new_register(){
        $iuid = I('get.iuid');
        $customerid = strtoupper(I('get.hu_nickname'));
        if($customerid){
            $data = array(
                    'customerid' => $customerid,
                );
            $data    = json_encode($data);
            // $sendUrl = "http://10.16.0.153/hapylife/index.php/Api/HapylifeApi/userList";
            $sendUrl = "http://localhost/hapylife/index.php/Api/HapylifeApi/userList";
            $result  = post_json_data($sendUrl,$data);
            $back_msg = json_decode($result['result'],true);
            $hu_nickname = $back_msg['data']['lastname'].$back_msg['data']['firstname'];
        }
        header("Content-type: text/html; charset=gb2312"); 
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $dat[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $dat[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $dat[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $this->assign('dat',$dat);
        $this->assign('customerid',$customerid);
        $this->assign('hu_nickname',$hu_nickname);
        $this->assign('iuid',$iuid);
        $this->display();
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
            $appid = 1400149268; // 1400开头
            // 短信应用SDK AppKey
            $appkey = "010151f33eaec872109b1b507c820bce";
            // 需要发送短信的手机号码
            $phoneNumber = I('post.phoneNumber');
            //手机区号
            $acnumber    = I('post.acnumber');
            // 短信模板ID，需要在短信应用中申请$templateId
            // 签名
            if($acnumber==86){
                $templateId = 209020;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
                $smsSign = "安永中国"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
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
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $this->success('验证码正确',U('Home/Register/new_register',array('phoneNumber'=>$phoneNumber,'acnumber'=>$acnumber,'acid'=>$acid)));
            }else{
                $this->error('验证码错误');
            }
        }
    }

    /**
    * 返回推荐人姓名
    **/ 
    public function checkName(){
        $customerid = strtoupper(trim(I('post.EnrollerID')));
        if($customerid){
            $usa = new \Common\UsaApi\Usa;
            $map = $usa->validateHpl($customerid);
            if(empty($map['errors'])){
                $data['lastname'] = $map['lastName'];
                $data['firstname'] = $map['firstName'];
                $this->ajaxreturn($data);     
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
    * 保存用户资料
    **/ 
    public function new_registerInfo(){
        if(!IS_POST){
            $msg['status'] = 200;
            $msg['message']= '未提交任何数据';
            $this->ajaxreturn($msg);
        }else{
            $iuid = I('get.iuid');
            $customerid = strtoupper(I('get.hu_nickname'));
            if($customerid){
                $data = array(
                        'customerid' => $customerid,
                    );
                $data    = json_encode($data);
                // $sendUrl = "http://10.16.0.153/hapylife/index.php/Api/HapylifeApi/userList";
                $sendUrl = "http://localhost/hapylife/index.php/Api/HapylifeApi/userList";
                $result  = post_json_data($sendUrl,$data);
                $back_msg = json_decode($result['result'],true);
                $hu_nickname = $back_msg['data']['lastname'].$back_msg['data']['firstname'];
            }

            $data = I('post.');
            $upload = several_upload();
            $User = D("User"); // 实例化User对象
            if(!$User->create($data)){
                $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
                foreach ($mape as $key => $value) {
                    $dat[$key]         = $value;
                    if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                        $dat[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
                    }else{
                        $dat[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
                    }
                }
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $error = $User->getError();
                $assign = array(
                    'error' => $error,
                    'data' => $data,
                    'dat' => $dat
                );
                $this->assign($assign);
                $this->assign('customerid',$customerid);
                $this->assign('hu_nickname',$hu_nickname);
                $this->assign('iuid',$iuid);
                $this->display('Register/new_register');
            }else{
                if(isset($upload['name'])){
                    $data['JustIdcard']=C('WEB_URL').$upload['name'][0];
                    $data['BackIdcard']=C('WEB_URL').$upload['name'][1];
                }
                $data['EnrollerID'] = strtoupper(I('post.EnrollerID'));
                $data['LastName'] = trimall(I('post.LastName'));
                $data['FirstName'] = trimall(I('post.FirstName'));
                $data['EnLastName'] = trimall(I('post.EnLastName'));
                $data['EnFirstName'] = trimall(I('post.EnFirstName'));
                $data['WvPass'] = $data['PassWord'];
                $add = D('Tempuser')->add($data);
                if($add){
                    $this->assign('userinfo',$data);
                    $this->assign('iuid',I('get.iuid'));
                    $this->display();
                }
            }
        }
    }
    /**
    * 获取首购产品
    **/ 
    public function new_purchase(){
        $iuid = I('get.iuid');
        $data = D('product')->where(array('ip_type'=>1,'is_pull'=>1))->order('is_sort DESC')->select();
        $this->assign('data',$data);
        $this->assign('iuid',$iuid);
        $this->display();

    }
    /**
    * 获取产品详情
    **/ 
    public function new_purchaseInfo(){
        $iuid = I('get.iuid');
        $ipid =I('get.ipid');
        $data = D('product')->where(array('ipid'=>$ipid))->find();
        $this->assign('data',$data);
        $this->assign('iuid',$iuid);
        $this->display();
    }
    /**
    * 首购订单
    **/
    public function registerOrder(){
        if(I('get.iuid')){
            $iuid = I('get.iuid');
            // 外部链接注册唯一参数
            $isOutside = 1;
        }else{
            $iuid = $_SESSION['user']['id'];
            $isOutside = 0;
        }
        $ipid = I('get.ipid');
        $htid = D('Tempuser')->order('htid desc')->getfield('htid');
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
            'ia_name'=>$userinfo['firstname'],
            //收货人电话
            'ia_phone'=>$userinfo['phone'],
            // 省，州
            'ia_province' => $userinfo['shopprovince'],
            // 市
            'ia_city' => $userinfo['shopcity'],
            // 区
            'ia_area' => $userinfo['shoparea'],
            //详细收货地址
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
            'htid'        => $htid,
            // 是否外部链接接入
            'isOutside' => $isOutside,
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
            'create_time'   =>time(),
            'create_month'   =>date('Y-m'),
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            if($product['ip_type'] == 1){
                $this->redirect('Home/Pay/choosePay1',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num,'status'=>$isOutside));
            }else{
                $this->redirect('Home/Pay/choosePay',array('ir_unpoint'=>$product['ip_point'],'ir_price'=>$product['ip_price_rmb'],'ir_point'=>$product['ip_point'],'ir_unpaid'=>$product['ip_price_rmb'],'ir_receiptnum'=>$order_num));
            }
        }else{
            $this->error('生成订单失败');
        }  
    }
    
    /**
    *代理成功注册页面
    **/
    public function new_regsuccess(){
        $ir_receiptnum = I('get.ir_receiptnum');
        $receipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        if($receipt['ir_status'] == 2){
            $data                  = M('User')->where(array('CustomerID'=>$receipt['rcustomerid']))->find();
            $data['ir_receiptnum'] = $ir_receiptnum;
            $data['isoutside'] = $receipt['isoutside'];
            $this->assign('data',$data);
            $this->display();
        }else{
            $this->error('失败');
        }
    }

    /*********************************************************************普通注册********************************************************************************************/  
    /**
    *注册手机区号 is_show值为1
    **/
    public function oldRegisterCode(){
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
    public function register(){
        header("Content-type: text/html; charset=gb2312"); 
        $cu_id = I('get.cu_id');
        $hu_nickname = I('get.hu_nickname');
        $mape = M('areacode')->where(array('is_show'=>1))->order('order_number desc')->select();
        foreach ($mape as $key => $value) {
            $datas[$key]         = $value;
            if($value['acnumber']==86 || $value['acnumber']==852 || $value['acnumber']==852 || $value['acnumber']==886){
                $datas[$key]['name'] = $value['acname_cn'].'+'.$value['acnumber'];
            }else{
                $datas[$key]['name'] = $value['acname_en'].'+'.$value['acnumber'];
            }
        }
        $data = I('post.');
        $upload = several_upload();
        if(IS_POST){
            $User = D("User"); // 实例化User对象
            if(!$User->create($data)){
                 // 如果创建失败 表示验证没有通过 输出错误提示信息
                $error = $User->getError();
            }else{
                 // 验证通过 可以进行其他数据操作
                if($User->create($data)){
                    if(isset($upload['name'][0])){
                        $data['JustIdcard']=C('WEB_URL').$upload['name'][0];
                    }
                    if(isset($upload['name'][1])){
                        $data['BackIdcard']=C('WEB_URL').$upload['name'][1];
                    }
                    $data['WvPass'] = $data['PassWord'];
                    // $data['PassWord'] = md5($data['PassWord']);
                    $data['JoinedOn'] = time();
                    $data['EnrollerID'] = strtoupper($data['EnrollerID']);
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
                    $result = D('Tempuser')->add($data);
                    if($result){
                        if(!empty($cu_id)){
                            $this->redirect('Home/Register/registerInfo',array('htid'=>$result,'cu_id'=>$cu_id,'hu_nickname'=>$hu_nickname));
                        }else{
                            $this->redirect('Home/Register/registerInfo',array('htid'=>$result));
                        }
                    }
                }
            }
        }

        $assign = array(
            'error' => $error,
            'data' => $data,
            'datas' => $datas,
            'cu_id' => $cu_id,
            'hu_nickname' => $hu_nickname,
        );
        $this->assign($assign);
        $this->display();
    }

    // 确认信息页面
    public function registerInfo(){
        $htid = I('get.htid');
        $hu_nickname = I('get.hu_nickname');
        $cu_id = I('get.cu_id');
        $userinfo = M('Tempuser')->where(array('htid'=>$htid))->find();

        $assign = array(
            'userinfo' => $userinfo,
            'cu_id' => $cu_id,
            'hu_nickname' => $hu_nickname,
        );
        $this->assign($assign);
        $this->display();
    }

    // 注册成功显示页面
    public function regsuccess(){
        $cu_id = I('get.cu_id');
        $hu_nickname = I('get.hu_nickname');
        $htid = I('get.htid');
        $userinfo = M('Tempuser')->where(array('htid'=>$htid))->find();
        $user = M('User')->where(array('htid'=>$htid))->find();
        if(empty($user)){
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

            $array = array(
                'CustomerID' => $CustomerID,
                'Placement' => '',
                'EnrollerID' => $userinfo['enrollerid'],
                'Sex' => $userinfo['sex'],
                'LastName' => $userinfo['lastname'],
                'FirstName' => $userinfo['firstname'],
                'Email' => $userinfo['email'],
                'PassWord' => md5($userinfo['password']),
                'WvPass' => $userinfo['password'],
                'acid' => $userinfo['acid'],
                'acnumber' => $userinfo['acnumber'],
                'Phone' => $userinfo['phone'],
                'ShopAddress1' => $userinfo['shopaddress1'],
                'ShopArea' => $userinfo['shoparea'],
                'ShopCode' => $userinfo['shopcode'],
                'ShopCity' => $userinfo['shopcity'],
                'ShopProvince' => $userinfo['shopprovince'],
                'ShopCountry' => $userinfo['shopcountry'],
                'Idcard' => $userinfo['idcard'],
                'JoinedOn' => time(),
                'JustIdcard' => $userinfo['justidcard'],
                'BackIdcard' => $userinfo['backidcard'],
                'OrderDate' => '',
                'Language' => $userinfo['language'],
                'EnLastName' => $userinfo['enlastname'],
                'EnFirstName' => $userinfo['enfirstname'],
                'BankName' => $userinfo['bankname'],
                'BankAccount' => $userinfo['bankaccount'],
                'BankProvince' => $userinfo['bankprovince'],
                'BankCity' => $userinfo['bankcity'],
                'BankArea' => $userinfo['bankarea'],
                'SubName' => $userinfo['subname'],
                'AccountType' => $userinfo['accounttype'],
                'teamCode' => $userinfo['teamcode'],
                'htid' => $htid,
                'Birthday' => $userinfo['birthday']
            );
            $addResult = M('User')->add($array); 
            $user = M('User')->where(array('iuid'=>$addResult))->find();
        }
        if($addResult){
            // 发送短信提示
            $templateId ='209009';
            $params     = array();
            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
            if($sms['errmsg'] == 'OK'){
                $contents = array(
                            'acnumber' => $userinfo['acnumber'],
                            'phone' => $userinfo['phone'],
                            'operator' => '系统',
                            'addressee' => $userinfo['lastname'].$userinfo['firstname'],
                            'product_name' => '新会员注册',
                            'date' => time(),
                            'content' => '恭喜您注册成功。',
                            'customerid' => $user['customerid']
                );
                $logs = M('SmsLog')->add($contents);
            }
        }
        
        if(!empty($cu_id)){
            $data = array(
                        'hu_nickname' => $hu_nickname,
                        'cu_id' => $cu_id,
                    );
            $data    = json_encode($data);
            $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/use_coupon";
            // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/use_coupon";
            $results  = post_json_data($sendUrl,$data);
            $back_result = json_decode($results['result'],true);   
            if($back_result['status']){
                $iuid = $user['iuid'];
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
                    'rCustomerID'=>$user['customerid'],
                    //收货人
                    'ia_name'=>$userinfo['lastname'].$userinfo['firstname'],
                    //收货人电话
                    'ia_phone'=>$userinfo['phone'],
                    // 省，州
                    'ia_province' => $userinfo['shopprovince'],
                    // 市
                    'ia_city' => $userinfo['shopcity'],
                    // 区
                    'ia_area' => $userinfo['shoparea'],
                    //详细收货地址
                    'ia_address'=>$userinfo['shopaddress1'],
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
                        $products = 2;
                    }else{
                        $products = 3;
                    }
                    $usa    = new \Common\UsaApi\Usa;
                    $result = $usa->createCustomer($user['customerid'],$userinfo['password'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$products,$userinfo['birthday']);
                    if(!empty($result['result'])){
                        $log = addUsaLog($result['result']);
                        $maps = json_decode($result['result'],true);
                        $wv  = array(
                            'wvCustomerID' => $maps['wvCustomerID'],
                            'wvOrderID'    => $maps['wvOrderID'],
                            'DistributorType' => $product['ip_after_grade'],
                            'Products'      => $products,
                            'Number'        => 1,
                            'OrderDate'     => date("m/d/Y h:i:s A")
                        );
                        $res = M('User')->where(array('iuid'=>$iuid))->save($wv);
                        if($res){
                            $addressee = $userinfo['lastname'].$userinfo['firstname'];
                            // 给注册会员发短信
                            $templateId ='223637';
                            $params     = array($addressee,$maps['wvCustomerID'],$product['ip_name_zh']);
                            $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                            if($sms['errmsg'] == 'OK'){
                                $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$order_num))->find();
                                $contents = array(
                                    'acnumber' => $userinfo['acnumber'],
                                    'phone' => $userinfo['phone'],
                                    'operator' => '系统',
                                    'addressee' => $addressee,
                                    'product_name' => $receiptlist['product_name'],
                                    'date' => time(),
                                    'content' => '欢迎来到DT!，亲爱的DT会员您好，欢迎您加入DT成为DT大家庭的一员！在开始使用您的新会员资格前，请确认下列账户信息是否正确:姓名：'.$addressee.'会员号码：'.$maps['wvCustomerID'].'产品：'.$product['ip_name_zh'].'使用上面的会员ID号码以及您在HapyLife帐号注册的时候所创建的密码登录DT官网，开始享受您的会籍。我们很开心您的加入。我们迫不及待地与您分享无数令人兴奋和难忘的体验！',
                                    'customerid' => $user['customerid']
                                );
                                $logs = M('SmsLog')->add($contents);
                            }
                            // 给上线发短信
                            $enrollerinfo = M('User')->where(array('CustomerID'=>$userinfo['enrollerid']))->find(); 
                            $templateId ='220861';
                            $params     = array($enrollerinfo['customerid'],$userinfo['customerid']);
                            $sms        = D('Smscode')->sms($enrollerinfo['acnumber'],$enrollerinfo['phone'],$params,$templateId);
                            if($sms['errmsg'] == 'OK'){
                                $addressee = $enrollerinfo['lastname'].$enrollerinfo['firstname'];
                                $contents = '尊敬的'.$enrollerinfo['customerid'].'会员，您增加一名成员：'.$userinfo['customerid'];
                                $addlog = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$addressee,'上线接收短信',$contents,$enrollerinfo['customerid']);
                            }
                            $createPayment = $usa->createPayment($userinfo['customerid'],$maps['wvOrderID'],date('Y-m-d H:i',time()));
                            $log = addUsaLog($createPayment['result']);

                        }
                    }
                }
            }
        }

        $assign = array(
            'data' => $user,
            'iuid' => $user['iuid'],
            'cu_id' => $cu_id,
        );
        $this->assign($assign);
        $this->display();
    }

    // 有券注册
    public function hadCoupon(){
        $iuid = $_SESSION['user']['id'];
        $cu_id = I('post.cu_id');
        $userinfo = M('User')->where(array('iuid'=>$iuid))->find();
        $data = array(
                    'hu_nickname' => $userinfo['customerid'],
                    'cu_id' => $cu_id,
                );
        $data    = json_encode($data);
        $sendUrl = "http://10.16.0.151/nulife/index.php/Api/Couponapi/use_coupon";
        // $sendUrl = "http://localhost/testnulife/index.php/Api/Couponapi/use_coupon";
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
                // 省，州
                'ia_province' => $userinfo['shopprovince'],
                // 市
                'ia_city' => $userinfo['shopcity'],
                // 区
                'ia_area' => $userinfo['shoparea'],
                //详细收货地址
                'ia_address'=>$userinfo['shopaddress1'],
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
                    $products = 2;
                }else{
                    $products = 3;
                }
                $usa    = new \Common\UsaApi\Usa;
                $result = $usa->createCustomer($userinfo['customerid'],$userinfo['wvpass'],$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone'],$products,$userinfo['birthday']);
                if(!empty($result['result'])){
                    $log = addUsaLog($result['result']);
                    $maps = json_decode($result['result'],true);
                    $wv  = array(
                        'wvCustomerID' => $maps['wvCustomerID'],
                        'wvOrderID'    => $maps['wvOrderID'],
                        'DistributorType' => $product['ip_after_grade'],
                        'Products'      => $products,
                        'Number'        => 1,
                        'OrderDate'     => date("m/d/Y h:i:s A")
                    );
                    $res = M('User')->where(array('iuid'=>$iuid))->save($wv);
                    if($res){
                        $addressee = $userinfo['lastname'].$userinfo['firstname'];
                        // 发送短信提示
                        $templateId ='223637';
                        $params     = array($addressee,$maps['wvCustomerID'],$product['ip_name_zh']);
                        $sms        = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $receiptlist = M('Receiptlist')->where(array('ir_receiptnum'=>$order_num))->find();
                            $contents = array(
                                        'acnumber' => $userinfo['acnumber'],
                                        'phone' => $userinfo['phone'],
                                        'operator' => '系统',
                                        'addressee' => $addressee,
                                        'product_name' => $receiptlist['product_name'],
                                        'date' => time(),
                                        'content' => '欢迎来到DT!，亲爱的DT会员您好，欢迎您加入DT成为DT大家庭的一员！在开始使用您的新会员资格前，请确认下列账户信息是否正确:姓名：'.$addressee.'会员号码：'.$maps['wvCustomerID'].'产品：'.$product['ip_name_zh'].'使用上面的会员ID号码以及您在HapyLife帐号注册的时候所创建的密码登录DT官网，开始享受您的会籍。我们很开心您的加入。我们迫不及待地与您分享无数令人兴奋和难忘的体验！',
                                        'customerid' => $userinfo['customerid']
                            );
                            $logs = M('SmsLog')->add($contents);
                        }

                        // 给上线发短信
                        $enrollerinfo = M('User')->where(array('CustomerID'=>$userinfo['enrollerid']))->find(); 
                        $templateId ='220861';
                        $params     = array($enrollerinfo['customerid'],$userinfo['customerid']);
                        $sms        = D('Smscode')->sms($enrollerinfo['acnumber'],$enrollerinfo['phone'],$params,$templateId);
                        if($sms['errmsg'] == 'OK'){
                            $addressee = $enrollerinfo['lastname'].$enrollerinfo['firstname'];
                            $contents = '尊敬的'.$enrollerinfo['customerid'].'会员，您增加一名成员：'.$userinfo['customerid'];
                            $addlog = D('Smscode')->addLog($enrollerinfo['acnumber'],$enrollerinfo['phone'],'系统',$addressee,'上线接收短信',$contents,$enrollerinfo['customerid']);
                        }

                        $createPayment = $usa->createPayment($userinfo['customerid'],$maps['wvOrderID'],date('Y-m-d H:i',time()));
                        $log = addUsaLog($createPayment['result']);

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

    /**
    * 所在省市区
    **/ 
    public function ShArea(){
        $data = M('ShArea')->where(array('pid'=>0))->select();
        // p($data);
        foreach($data as $key=>$value){
           $province['86'][$value['id']] = $value['name'];
        }
        foreach($province['86'] as $key=>$value){
            $city[$key] = M('ShArea')->where(array('pid'=>$key))->select();    
        }
        foreach($city as $key => $value) {
            foreach ($value as $k => $v) {
                $province[$key][$v['id']] = $v['name'];
            }
        }
        foreach($city as $key=>$value){
            foreach ($value as $k => $v) {
                $area[$v['id']] = M('ShArea')->where(array('pid'=>$v['id']))->select(); 
            }   
        }
        foreach($area as $key => $value) {
            foreach ($value as $k => $v) {
                $province[$key][$v['id']] = $v['name'];
            }
        }
        $this->ajaxreturn($province);
    }

    /**
    * 获取二维码内容，显示在页面
    **/ 
    public function registerIndex(){
        $iuid = I('get.iuid');
        $data = M('User')->where(array('iuid'=>$iuid))->find();
        $this->assign('data',$data);
        $this->display();
    }

}



 ?>