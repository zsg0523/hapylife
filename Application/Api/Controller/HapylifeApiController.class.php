<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife控制器
**/
class HapylifeApiController extends HomeBaseController{

    /**
     * [receivePhoto description]
     * @return [type] [description]
     */
    public function receivePhoto(){
        $photo        = I('post.photo');
        $img_body     = substr(strstr($photo,','),1);
        $photo_name   = time().'_'.mt_rand().'.jpg';
        $img          = file_put_contents('./Upload/file/'.$photo_name, base64_decode($img_body));
        $map['photo'] = C('WEB_URL').'/Upload/file/'.$photo_name;
        $save_image   = M('photo')->add($map);
        if($save_image){
            // $customerId   = trim(I('post.customerId'));
            // $content      = trim(I('post.content'));
            $notification = new \Common\PushEvent\Notification;
            $notification->setUser()->setContent($map['photo'])->push();
        }
    }

    /**
     * [push description]
     * @return [type] [description]
     */
    public function push(){
        $customerId   = trim(I('post.customerId'));
        $content      = trim(I('post.content'));
        $notification = new \Common\PushEvent\Notification;
        $notification->setUser($customerId)->setContent($content)->push();
    }

    public function index(){
        //检查WV api用户信息
        $HappyLifeID  = trim(I('post.happyLifeID'));
        $Password     = trim(I('post.password'));
        $SponsorID    = trim(I('post.sponsorID'));
        $FirstName_EN = trim(I('post.firstName_EN'));
        $LastName_EN  = trim(I('post.lastName_EN'));
        $EMailAddress = trim(I('post.emailAddress'));
        $Phone        = trim(I('post.phone'));
        $Products     = explode(',',trim(I('post.products')));
        $key          = trim(I('post.key'));
        $dob          = trim(I('post.dob'));
        $data         = array(
                'happyLifeID'  =>$HappyLifeID,
                'password'     =>$Password,
                'sponsorID'    =>$SponsorID,
                'firstName_EN' =>$FirstName_EN,
                'lastName_EN'  =>$LastName_EN,
                'emailAddress' =>$EMailAddress,
                'phone'        =>$Phone,
                'products'     =>$Products,
                'key'          =>$key,
                'dob'          =>$dob
        );

        $data   = json_encode($data);
        $url    = "https://signupapi-qa.wvhservices.com/api/Hpl/CreateCustomer";
        $result = post_json_data($url,$data);
        p($result);
    }

    /**
    * 用户注册LastName FirstName EnrollerID Email PassWord Phone JustIdcard BackIdcard Sex
    * IsNew/1新 0旧
    **/
    public function newregister(){
        if(IS_POST){
            $data['status'] = 0;
            $data['message']= 'app暂不允许注册，请前往web端';
            $this->ajaxreturn($data);
        }else{
            $data  = I('post.');
            if($data['IsNew']==1){
                // $find  = D('User')->where(array('CustomerID'=>$data['EnrollerID']))->find();
                // if(!$find){
                //     $tmpe['status'] = 2;
                //     $this->ajaxreturn($tmpe);           
                // }else{
                    if(!empty($data['JustIdcard'])){
                        $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                        $JustIdcard = time().'_'.mt_rand().'.jpg';
                        $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
                        $where['JustIdcard'] = C('WEB_URL').'/Upload/file/'.$JustIdcard;
                    }
                    if(!empty($data['BackIdcard'])){
                        $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                        $BackIdcard = time().'_'.mt_rand().'.jpg';
                        $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
                        $where['BackIdcard'] = C('WEB_URL').'/Upload/file/'.$BackIdcard;
                    }
                    //查询原先最大CustomerID，新添+1
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
                    $where  = array(
                        'CustomerID'         => $CustomerID,
                        'IsNew'              => 1,
                        'Placement'          => 'Right',
                        'CustomerStatus'     => 'Active',
                        'LastName'           => $data['LastName'],
                        'FirstName'          => $data['FirstName'],
                        'EnrollerID'         => $data['EnrollerID'],
                        'JoinedOn'           => date("m/d/Y h:i:s A"),
                        'HighestAchievedRank'=> 'Director',
                        'Email'              => $data['Email'],
                        'WeeklyVolume'       => 0,
                        'PassWord'           => md5($data['PassWord']),
                        'Phone'              => $data['Phone'],
                        'Sex'                => $data['Sex'],
                        'IsCheck'            => 0,
                        'ShopAddress1'       => $data['ShopAddress1'],
                        'ShopAddress2'       => $data['ShopAddress2'],
                        'Idcard'             => $data['Idcard']
                    );
                    $add   = D('User')->add($where);
                    if($add){
                        $tmpe  = D('User')->where(array('CustomerID'=>$CustomerID))->find();
                        $tmpe['CustomerID'] = $CustomerID;
                        $tmpe['pass']       = $data['PassWord'];
                        $tmpe['status']     = 1;
                        $this->ajaxreturn($tmpe);               
                    }else{
                        $tmpe['status'] = 0;
                        $this->ajaxreturn($tmpe);
                    }
                // }
            }else{
                // //正反面身份证
                // if(!empty($data['JustIdcard'])){
                //     $img_body1 = substr(strstr($data['JustIdcard'],','),1);
                //     $JustIdcard = time().'_'.mt_rand().'.jpg';
                //     $img1 = file_put_contents('./Upload/file/'.$JustIdcard, base64_decode($img_body1));
                //     $tmpe['JustIdcard'] = C('WEB_URL').'/Upload/file/'.$JustIdcard;
                // }
                // if(!empty($data['BackIdcard'])){
                //     $img_body2 = substr(strstr($data['BackIdcard'],','),1);
                //     $BackIdcard = time().'_'.mt_rand().'.jpg';
                //     $img2 = file_put_contents('./Upload/file/'.$BackIdcard, base64_decode($img_body2));
                //     $tmpe['BackIdcard'] = C('WEB_URL').'/Upload/file/'.$BackIdcard;
                // }
                // $tmpe  = array(
                //     'Email'              => $data['Email'],
                //     'PassWord'           => md5($data['PassWord']),
                //     'Phone'              => $data['Phone'],
                //     'Sex'                => $data['Sex'],
                //     'ShopAddress1'       => $data['ShopAddress1'],
                //     'ShopAddress2'       => $data['ShopAddress2'],
                //     'Idcard'             => $data['Idcard'],
                //     'OrderDate'          => '3/16/2018 12:00:00 AM',
                //     'Number'             => '1',
                //     'DistributorType'    => 'Platinum'
                // );
                // $data['CustomerID']= $data['EnrollerID'];
                // $data['pass']      = $data['PassWord'];
                // $where= array(
                //     'CustomerID'   =>$data['EnrollerID']
                // );
                // $find = D('User')->where($where)->find();
                // if($find){
                //     if($find['password']){
                //         $data['status'] = 3;
                //         $this->ajaxreturn($data);
                //     }else{
                //         // echo 2;die;
                //         $arr= array(
                //             'LastName'     =>$data['LastName'],
                //             'FirstName'    =>$data['FirstName'],
                //             'CustomerID'   =>$data['EnrollerID']
                //         );
                //         $user = D('User')->where($arr)->find();
                //         if($user){
                //             $mape  = array('CustomerID'=>$data['EnrollerID']);
                //             $data['iuid'] = $find['iuid'];
                //             $save = D('User')->where($mape)->save($tmpe);
                //         }
                //     }
                // }else{
                //     $strlen = strlen($data['EnrollerID']);
                //     if($strlen==8){
                //         $tmpe['CustomerID']=$data['EnrollerID'];
                //         $tmpe['LastName']  =$data['LastName'];
                //         $tmpe['FirstName'] =$data['FirstName'];
                //         $tmpe['Placement']      ='Right';
                //         $save = D('User')->add($tmpe);
                //         $data['iuid'] = D('User')->where(array('CustomerID'=>$data['EnrollerID']))->getfield('iuid');
                //     }
                // }
                // // $array= array(
                // //     array('iuid'=>$find['iuid'],'datetime'=>'2018-03','hatime'=>'2018年03月23日','endtime'=>'2018年04月15日','is_tick'=>1),
                // //     array('iuid'=>$find['iuid'],'datetime'=>'2018-04','hatime'=>'2018年04月16日','endtime'=>'2018年05月15日','is_tick'=>1),
                // //     array('iuid'=>$find['iuid'],'datetime'=>'2018-05','hatime'=>'2018年05月16日','endtime'=>'2018年06月15日','is_tick'=>1)
                // // );
                // if($save){
                //     // foreach ($array as $key => $value) {
                //     //     D('Activation')->add($value);
                //     // }
                //     $data['status'] = 1;
                //     $this->ajaxreturn($data);               
                // }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                // }
            }
        }
    }

    /**
    * 登录
    **/
    public function login(){
        if(IS_POST){
            $tmpe = I('post.');
            $data = D('User')->where(array('CustomerID'=>$tmpe['CustomerID']))->find();
            if($data){
                if($data['password']==md5($tmpe['PassWord'])){
                    $data['status'] =1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] =0;
                    $this->ajaxreturn($data);
                }
            }else{
                //检查WV api用户信息
                $usa      = new \Common\UsaApi\Usa;
                $userinfo = $usa->validateHpl($tmpe['CustomerID']);
                //检查wv是否存在该账号 Y创建该账号  N登录失败
                switch ($userinfo['isActive']) {
                    case true:
                        //创建该新账号在本系统
                        $map = array(
                            'CustomerID'  =>$tmpe['CustomerID'],
                            'PassWord'    =>md5($tmpe['PassWord']),
                            'WvPass'      =>$tmpe['PassWord'],
                            'LastName'    =>$userinfo['lastName'],
                            'FirstName'   =>$userinfo['firstName'],
                            'isActive'    =>$userinfo['isActive'],
                        );
                        $createUser = D('User')->add($map);
                        if($createUser){
                            $data = D('User')->where(array('CustomerID'=>trim($tmpe['CustomerID'])))->find();
                            $data['status'] =1;
                        }else{
                            $data['status'] =0;
                        }
                        break;
                    default:
                        $data['status'] = 0;
                        break;
                }
                $this->ajaxreturn($data); 
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data); 
        }
    }

    /**
    * 获取用户信息
    **/
    public function userinfo(){
        $iuid = I('post.iuid');
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
        if($data){
            $data['status'] = 1;
            $this->ajaxreturn($data);   
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);           
        }
    }

    /**
    * 编辑用户信息
    **/
    public function edituserinfo(){
        $iuid         = I('post.iuid');
        $para         = I('post.para');
        $paravalue    = I('post.paravalue');
        $user         = D('User')->where(array('iuid'=>$iuid))->find();
        $data['iuid'] = $iuid;
        switch ($para) {
            case 'LastName':
                $data['LastName']  = $paravalue;
                $edit = D('User')->save($data);    
            case 'FirstName':
                $data['FirstName'] = $paravalue;
                $edit = D('User')->save($data);
                //同时修改评论里的名字
                if($edit){
                    $map = array('username2'=>$paravalue);
                    $save  = D('Comment')->where(array('uid2'=>$iuid))->save($map); 
                }    
                break;
            case 'City':
                $data['City']      = $paravalue;
                $edit = D('User')->save($data);    
                break;
            case 'State':
                $data['State']     = $paravalue;
                $edit = D('User')->save($data);    
                break;
             case 'Country':
                $data['Country']   = $paravalue; 
                $edit = D('User')->save($data);                  
                break;
            case 'Phone':
                $data['Phone']     = $paravalue;
                $edit = D('User')->save($data);
                break;
            case 'Sex':
                $data['Sex']       = $paravalue;
                $edit = D('User')->save($data); 
                break;
            case 'Email':
                $data['Email']    = $paravalue;
                $edit = D('User')->save($data); 
                break;
            case 'Children':
                $data['Children']  = $paravalue;
                $edit = D('User')->save($data); 
                break;
            case 'ShopAddress1':
                $data['ShopAddress1']  = $paravalue;
                $edit = D('User')->save($data); 
                break;
            case 'ShopAddress2':
                $data['ShopAddress2']  = $paravalue;
                $edit = D('User')->save($data); 
                break;
            case 'Photo':
                $img_body1 = substr(strstr($paravalue,','),1);
                $Photo = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Upload/file/'.$Photo, base64_decode($img_body1));
                $data['Photo'] = C('WEB_URL').'/Upload/file/'.$Photo;
                if($user['Photo']){
                    unlink($user['Photo']);    
                }
                $edit = D('User')->save($data); 
                break;
        }          
        if($edit){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    *会籍记录激活
    **/
    public function activation(){
        $iuid     = I('post.iuid');
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
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    * 新闻列表
    **/ 
    public function newslist(){
        $map  = array(
            'is_show' =>1
        );
        $data = M('News')
                ->where($map)
                ->order('addtime desc')
                ->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'无法获取新闻列表'
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 新闻详情
    **/
    public function newscontent(){
        $nid  = I('post.nid');
        $data = M('News')->where(array('nid'=>$nid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'获取新闻详情失败'
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 商品列表
    **/
    public function productlist(){
        $map = array(
            'ip_type'    =>2,
            'ip_name_zh '=>array('NEQ','Rbs'),
        );
        $data= M('Product')->where($map)->order('is_sort desc')->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'获取商品列表失败'
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 商品详情
    **/
    public function product(){
        $ipid = I('post.ipid');
        $data = M('Product')->where(array('ipid'=>$ipid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'获取商品详情失败'
            );
            $this->ajaxreturn($data);
        }
    }
    /**
    * 产品订单
    **/
    public function order(){
        $iuid = trim(I('post.iuid'));
        $ipid = trim(I('post.ipid'));
        //商品信息
        $product   = M('Product')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo  = M('User')->where(array('iuid'=>$iuid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        //查看地址
        $address   = M('Address')->where(array('iuid'=>$iuid,'is_address_show'=>1))->find(); 
        switch ($product['ip_type']) {
            case '1':
                $list= D('Receipt')->where(array('ir_ordertype'=>$product['ip_type'],'riuid'=>$iuid))->setField('is_delete',1);
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
        if($address){
            $ia_name     = $address['ia_name'];    
            $phone       = $address['ia_phone'];    
            $shopaddress = $address['ia_province'].$address['ia_town'].$address['ia_region'].$address['ia_road'];    
        }else if($userinfo['shopaddress1']){
            $ia_name     = $userinfo['lastname'].$userinfo['firstname'];
            $phone       = $userinfo['phone'];
            $shopaddress = $userinfo['shopaddress1'];
        }else{
            $order['status'] = 3;
            $order['msg']    = '请确保有个人资料详细地址或收货地址';
            $this->ajaxreturn($order);
        }
        $order = array(
            //订单编号
            'ir_receiptnum' =>$order_num,
            //订单创建日期
            'ir_date'=>time(),
            //订单的状态(0待生成订单，1待支付订单，2已付款订单)
            'ir_status'=>0,
            //下单用户id
            'riuid'=>$iuid,
            //下单用户
            'rCustomerID'=>$userinfo['customerid'],
            //收货人
            'ia_name'=>$ia_name,
            //收货人电话
            'ia_phone'=>$phone,
            //收货地址
            'ia_address'=>$shopaddress,
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
            'ipid'         => $product['ipid']
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
        $log = array(
            'from_iuid' =>$iuid,
            'content'   =>$content,
            'action'    =>0,
            'type'      =>2,
            'date'      =>date('Y-m-d H:i:s')          
        );
        $addlog = M('Log')->add($log);
        if($addlog){
            $order['status'] = 1;
            $order['msg']    = '订单已生成';
            $this->ajaxreturn($order);
        }else{
            $order['status'] = 0;
            $order['msg']    = '订单生成失败';
            $this->ajaxreturn($order);
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

    /**
    * 旅游列表
    **/ 
    public function travellist(){
        $map    = array(
            'is_show' =>1
        );
        $travel = M('Travel')
                ->where($map)
                ->order('addtime desc')
                ->select();
        foreach ($travel as $key => $value) {
            $data[$key]                 = $value;
            $data[$key]['travel_price'] = sprintf("%.2f",$value['travel_price']);
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'无法获取旅游列表'
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 旅游详情
    **/
    public function travelcontent(){
        $tid  = I('post.tid');
        $iuid = I('post.iuid');
        $data = M('Travel')->where(array('tid'=>$tid))->find();
        $bann = array($data['travel_picture'],$data['travel_picture1'],$data['travel_picture2'],$data['travel_picture3'],$data['travel_picture4'],$data['travel_picture5']);
        foreach ($bann as $key => $value) {
            if(!empty($value)){
                $data['banner'][]['pic'] = $value;
            }
        }
        //点赞列表 like--1、该用户已点赞 0、已点赞
        $like = D('Like')->where(array('pid'=>$tid,'type'=>1))->select();
        if($like){
            $data['likenum'] = count($like); 
            foreach ($like as $key => $value){
                if($value['uid']==$iuid){
                    $tmpe = 1;
                }
            }
            if($tmpe==1){
                $data['like'] = 1;
            }else{
                $data['like'] = 0;
            }
        }else{
            $data['likenum']  = 0;
            $data['like']     = 0;
        }
        $data['whattime']     = $data['whattime']-1;
        $data['travel_price'] = sprintf("%.2f",$data['travel_price']);
        $comm = D('Comment')->join('hapylife_user on hapylife_comment.uid = hapylife_user.iuid')->where(array('pid'=>$tid,'type'=>1))->select();
        if($comm){
            $comment          = subtree($comm,0,$lev=1);
            //show--1、评论可删除 0、评论不可删
            foreach ($comment as $key => $value) {
                if($value['uid']==$iuid){
                    $comm[$key]['show'] = 1;
                }else{
                    $comm[$key]['show'] = 0;
                }
                $comm[$key]   = $value;
                $comm[$key]['time'] = formattime(strtotime($value['time']));
            }
            $data['comm']     = $comm;  
            $data['commnum']  = count($comm);
        }else{
            $data['comm']     = array();
            $data['commnum']  =0;
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'status'=>0,
                'msg'   =>'获取旅游详情失败'
            );
            $this->ajaxreturn($data);
        }
    }

    /**
    * 点赞
    **/
    public function like(){
        $tid  = I('post.tid');
        $iuid = I('post.iuid');
        //类型：1、旅游 2、动态 3、...
        $type = I('post.type');
        $where= array(
            'pid'  => $tid,
            'uid'  => $iuid,
            'type' => $type
        );
        //查看是否已经点赞
        $find = D('Like')->where($where)->find();
        if($find){
            $save = D('Like')->where(array('id'=>$find['id']))->delete();
            //取消点赞
            if($save){
                $data['status']=2;
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }else{
            //点赞
            $where['time']= date("m/d/Y h:i:s A");
            $save = D('Like')->add($where);
            if($save){
                $data['status']=1;
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    
    /**
    *添加评论
    **/
    public function comment(){
        $tid  = I('post.tid');
        $iuid = I('post.iuid');
        $iuid2= I('post.iuid2')?I('post.iuid2'):0;
        $cid  = I('post.id')?I('post.id'):0;
        //类型：1、旅游 2、动态 3、...
        $type = I('post.type');
        $comm = I('post.comm');
        $temp= array(
            'pid'     => $tid,
            'uid'     => $iuid,
            'uid2'    => $iuid2,
            'cid'     => $cid,
            'type'    => $type,
            'content' => $comm,
            'time'    => date("m/d/Y h:i:s A")
        );
        if($iuid2!=0){
            $temp['username2'] = D('User')->where(array('iuid'=>$iuid2))->getfield('firstname');
        }
        $add = D('comment')->add($temp);
        if($add){
            $data['status']=1;
            $this->ajaxreturn($data);
        }else{
            $data['status']=0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 产品 1首购 2升级 3月费
    **/
    public function upgrade(){
        $iuid  = I('post.iuid');
        $find  = M('User')->where(array('iuid'=>$iuid))->find();
        $type  = trim($find['distributortype']);
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
                $data['grade'][$key]         = $value; 
                $data['grade'][$key]['show'] = 1; 
            }
        // }else if(in_array($find['customerid'],$arrayTo)){
        //     $an_pro = M('Product')->where(array('ipid'=>48,'is_pull'=>0))->select();
        //     $product = array_merge($products,$an_pro);
        //     foreach ($product as $key => $value) {
        //         $data[$key]         = $value; 
        //         $data[$key]['show'] = 1; 
        //     }
        }else{
            $data['grade'] = $products;
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '获取失败';
            $this->ajaxreturn($data);
        }  
    }

    /**
    * 获取房间
    **/
    public function roomtype(){
        $tid = I('post.tid');
        $data= D('Room')->where(array('tid'=>$tid))->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '没有获取到内容';
            $this->ajaxreturn($data);
        }
    }
    /**
    * 获取房间详情
    **/
    public function roominfo(){
        $tid = I('post.tid');
        $rid = I('post.rid');
        $find          = D('Travel')->where(array('tid'=>$tid))->find();
        $data['start'] = $find['starttime'];
        $data['end']   = $find['endtime'];
        //rid默认为0,获取其中一个房间
        if($rid==0){
            $room= D('Room')->where(array('tid'=>$tid))->find();
        }else{
            $room= D('Room')->where(array('rid'=>$rid))->find();
        }
        $rooms['name']     = $room['name'];
        $rooms['rid']      = $room['rid'];
        $rooms['adult0']   = sprintf("%.2f",$room['adult0']);
        $data['type']      = $room['rtype'];
        $data['room']      = $rooms;
        //房间数量参数
        $data['number']['lpnum'] = 0;
        $data['number']['lpid']  = 0;
        $data['number']['lptype']= 4;
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '没有获取到内容';
            $this->ajaxreturn($data);
        }
    }

    /**
    * 选择房间数获取people层数
    **/
    public function people(){
        $tid   = I('post.tid');
        $rid   = I('post.rid');
        $lpnum = I('post.lpnum');//房间数
        if($rid==0){ 
            $room= D('Room')->where(array('tid'=>$tid))->find();        
        }else{
            $room= D('Room')->where(array('rid'=>$rid))->find();           
        }
        $isadult = D('Travel')->where(array('tid'=>$tid))->getfield('isadult');
        //adult数量值
        $where   = array('lptype'=>1,'lpnum'=>array('ELT',$room['adultnum']));
        $adultnum= D('Number')->where($where)->order('lpnum asc')->find();
        //child的数量值
        $temp    = array('lptype'=>2,'lpnum'=>array('ELT',$room['childnum']));
        $childnum= D('Number')->where($temp)->order('lpnum asc')->find();
        $number  = $isadult-1;
        //判断是否能带小孩
        if($room['childnum']>0){
            for($i=0;$i<$lpnum;$i++){
                $data[$i]['index']     = $i;
                $data[$i]['room']      = 'Room'.($i+1);
                $data[$i]['adultnum']  = $adultnum['lpnum'];
                $data[$i]['adulttype'] = $adultnum['lptype'];
                $data[$i]['aged']      = $isadult;
                $data[$i]['childnum']  = $childnum['lpnum'];
                $data[$i]['childtype'] = $childnum['lptype'];
                $data[$i]['stage']     = '0'.'-'.$number;
                $data[$i]['show']      = 1;
            }
        }else{
            for($i=0;$i<$lpnum;$i++){
                $data[$i]['index']     = $i;
                $data[$i]['room']      = 'Room'.($i+1);
                $data[$i]['adultnum']  = $adultnum['lpnum'];
                $data[$i]['adulttype'] = $adultnum['lptype'];
                $data[$i]['aged']      = $isadult;
                $data[$i]['show']      = 0;
            }
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '没有获取到内容';
            $this->ajaxreturn($data);
        }
    }
    /**
    * 获取child层数
    **/
    public function child(){
        $tid    = I('post.tid');
        //child数量
        $lpnum  = I('post.lpnum');
        $isadult= D('Travel')->where(array('tid'=>$tid))->getfield('isadult');
        $temp   = array('lptype'=>3,'lpnum'=>array('ELT',$isadult));
        $child  = D('Number')->where($temp)->order('lpnum asc')->select();
        for($i=0;$i<$lpnum;$i++){
            $data[$i]          = $child[0];
            $data[$i]['index'] = $i;
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '没有获取到内容';
            $this->ajaxreturn($data);
        }
    }
    /**
    * 获取adult/child/年龄/房间列表
    **/
    public function quantity(){
        $tid    = I('post.tid');
        //lptype--1、adult 2、child 3、年龄 4、房间列表
        $lptype = I('post.lptype');
        $rid    = I('post.rid');
        $room   = D('Room')->where(array('rid'=>$rid))->find();
        switch ($lptype) {
            case '1':
                $where   = array('lptype'=>$lptype,'lpnum'=>array('ELT',$room['adultnum']));
                $data = D('Number')->where($where)->order('lpnum asc')->select();
                break;
            case '2':
                $where   = array('lptype'=>$lptype,'lpnum'=>array('ELT',$room['childnum']));
                $data = D('Number')->where($where)->order('lpnum asc')->select();
                break;
            case '3':
                $isadult = D('Travel')->where(array('tid'=>$tid))->getfield('isadult');
                $number  = $isadult-1;
                $where   = array('lptype'=>$lptype,'lpnum'=>array('ELT',$number));
                $data = D('Number')->where($where)->order('lpnum asc')->select();
                break;
            case '4':
                $where   = array('lptype'=>$lptype);
                $data    = D('Number')->where($where)->order('lpnum asc')->select();
                break;
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '没有获取到内容';
            $this->ajaxreturn($data);
        }
    }
    /**
    * 计算金额
    **/
    public function total(){
        $iuid  = I('post.iuid');  
        $rid   = I('post.rid');  
        $tid   = I('post.tid');  
        $adult = I('post.adult');  
        $child = I('post.child');  
        $age   = I('post.age');
        //根据逗号转为数组
        $aduarr= explode(',',$adult); 
        $chiarr= explode(',',$child); 
        $agearr= explode(',',$age);
        $room  = D('Room')->where(array('rid'=>$rid))->find();
        $travel= D('Travel')->where(array('tid'=>$tid))->find();
        $user  = D('User')->where(array('iuid'=>$iuid))->find();
        $temp  = explode('/',$travel['starttime']);
        $data['address']  = $travel['address'];
        $data['starttime']= $travel['starttime'];
        $data['endtime']  = $travel['endtime'];
        $data['day']      = $travel['whattime'];
        $data['night']    = $travel['whattime']-1;
        $data['rid']      = $rid;
        $data['tid']      = $tid;
        //根据成年数组得循环次数
        foreach ($aduarr as $key => $value) {
            $keyarr[] = $key;
            $data['adultnum'] += $value;
        }
        foreach ($chiarr as $key => $value) {
            $data['chiidnum'] += $value;
        }
        $data['room']     = count($aduarr);
        //循环得到adult价格和child价格
        foreach ($keyarr as $key => $value) {
            foreach ($aduarr as $k => $v) {
                if($k==$value){
                    switch ($v) {
                        case '1':
                            $adultmony += $room['adult1'];
                            break;
                        case '2':
                            $adultmony += $room['adult2'];
                            break;
                        case '3':
                            $adultmony += $room['adult3'];
                            break;
                        case '4':
                            $adultmony += $room['adult4'];
                            break;
                        case '5':
                            $adultmony += $room['adult5'];
                            break;
                        case '6':
                            $adultmony += $room['adult6'];
                            break;
                        case '7':
                            $adultmony += $room['adult7'];
                            break;
                        case '8':
                            $adultmony += $room['adult8'];
                            break;
                        case '9':
                            $adultmony += $room['adult9'];
                            break;
                        case '10':
                            $adultmony += $room['adult10'];
                            break;
                        case '11':
                            $adultmony += $room['adult11'];
                            break;
                        case '12':
                            $adultmony += $room['adult12'];
                            break;
                    }
                }
            }
        }
        foreach ($agearr as $key => $value) {
            if($value>$room['age']){
                $childmony += $room['child'];
            }
        }
        if($user['point']>=$travel['dispoint']){
            $maximum  = $travel['dispoint']; 
        }else{
            if($user['point']!=0){
                $maximum  = $user['point'];
            }else{
                $maximum  = 0;
            }
        }
        $data['average']  = sprintf("%.2f",($adultmony+$childmony)/($data['adultnum']+$data['chiidnum']));
        $data['adultmony']= sprintf("%.2f",$adultmony);
        $data['childmony']= sprintf("%.2f",$childmony);
        $data['allpoint'] = sprintf("%.2f",($adultmony+$childmony));
        $data['maximum']  = sprintf("%.2f",$maximum);
        $data['maxicount']= sprintf("%.2f",$travel['discount']);
        $data['total']    = sprintf("%.2f",($adultmony+$childmony-$travel['discount']-$maximum));
        $data['ltine']    = 'Day '.$temp[1].' to'.$temp[0];
        $data['hotel']    = $room['hotel'].'-'.$room['name'];
        if($data['adultnum']){
            $this->ajaxreturn($data);
        }else{
            $mape['status']   = 0;
            $this->ajaxreturn($mape);
        }
    }
    /*
    **填写游客信息
    */
    public function fillinfo(){
        $rid   = I('post.rid');  
        $tid   = I('post.tid');
        $iuid  = I('post.iuid');
        $adult = I('post.adult');
        $child = I('post.child');
        $user  = D('User')->where(array('iuid'=>$iuid))->find();
        $room  = D('Room')->where(array('rid'=>$rid))->find();
        $travel= D('Travel')->where(array('tid'=>$tid))->find();
        $prefix= D('Around')->where(array('atype'=>1))->find();
        $suffix= D('Around')->where(array('atype'=>2))->find();
        $ptype = D('Around')->where(array('atype'=>3))->find();
        $data['total']    = I('post.total');
        $data['address']  = $travel['address'];
        $data['starttime']= $travel['starttime'];
        $data['endtime']  = $travel['endtime'];
        $data['address1'] = '';
        $data['address2'] = '';
        $data['city']     = $user['city'];
        $data['state']    = $user['state'];
        $data['zip']      = '';
        $data['country']  = $user['country'];
        $data['day']      = $travel['whattime'];
        $data['hotel']    = $room['hotel'];
        $data['night']    = $travel['whattime']-1;
        $aduarr= explode(',',$adult); 
        $chiarr= explode(',',$child);
        foreach ($aduarr as $key => $value){
            $keyarr[] = $key;
            $data['adultnum'] += $value;
        }
        foreach ($chiarr as $key => $value){
            $data['chiidnum'] += $value;
        }
        foreach ($keyarr as $key => $value) {
            foreach ($aduarr as $ke => $va) {
                if($ke==$value){
                    $status[$value][] = $va;
                }
            }
            foreach ($chiarr as $k => $v) {
                if($k==$value){
                    $status[$value][] = $v;
                }
            }
        }
        foreach ($status as $key => $value) {
            foreach ($value as $k => $v) {
                for($i=0;$i<$v;$i++){
                    if($k==0){
                        $temp[$key][$k][$i]['legal']   = 'Room'.($key+1).':Adult '.($i+1).' Legal Name';
                        $temp[$key][$k][$i]['room']    = ($key+1);
                    }else{
                        $temp[$key][$k][$i]['legal']   = 'Room'.($key+1).':Child '.($i+1).' Legal Name';
                        $temp[$key][$k][$i]['room']    = ($key+1);
                    }
                    $temp[$key][$k][$i]['prefix']   = $prefix['aname'];
                    $temp[$key][$k][$i]['prefid']   = $prefix['aid'];
                    $temp[$key][$k][$i]['preftype'] = $prefix['atype'];
                    $temp[$key][$k][$i]['suffix']   = $suffix['aname'];
                    $temp[$key][$k][$i]['suffid']   = $suffix['aid'];
                    $temp[$key][$k][$i]['sufftype'] = $suffix['atype'];
                    $temp[$key][$k][$i]['phonename']= $ptype['aname'];
                    $temp[$key][$k][$i]['phonetyid']= $ptype['aid'];
                    $temp[$key][$k][$i]['phonetype'] = $ptype['atype'];
                    $temp[$key][$k][$i]['firstname']= '';
                    $temp[$key][$k][$i]['lastname'] = '';
                    $temp[$key][$k][$i]['middle']   = '';
                    $temp[$key][$k][$i]['gender']   = 0;
                    $temp[$key][$k][$i]['email']    = '';
                    $temp[$key][$k][$i]['phone']    = '';
                }
            }
        }
        foreach ($temp as $key => $value) {
            foreach ($value as $key => $val) {
                foreach ($val as $ke => $va) {
                    $mapear[] = $va;
                }
            }
        }
        foreach ($mapear as $key => $value) {
            $data['info'][$key]          = $value;
            $data['info'][$key]['index'] = $key;
            if($key==0){
                $data['info'][$key]['firstname'] = $user['firstname'];
                $data['info'][$key]['lastname']  = $user['lastname'];
                $data['info'][$key]['email']     = $user['email'];
                $data['info'][$key]['phone']     = $user['phone'];
            }
        }
        if($data['adultnum']){
            $this->ajaxreturn($data);
        }else{
            $mape['status']   = 0;
            $this->ajaxreturn($mape);
        }
    }

    /*
    **获取前后缀和联系方式
    */
    public function around(){
        $atype = I('post.atype');
        switch ($atype) {
            case '1':
                $data = D('Around')->where(array('atype'=>1))->select();
                break;
            case '2':
                $data = D('Around')->where(array('atype'=>2))->select();
                    break;
            case '3':
                $data = D('Around')->where(array('atype'=>3))->select();
                    break;
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $mape['status']   = 0;
            $this->ajaxreturn($mape);
        }
    }
    /*
    **生成booking订单
    */
    public function bookingOrder(){
        $mape   = I('post.');
        $prefix = explode(',',$mape['prefix']);
        $suffix = explode(',',$mape['suffix']);
        $first  = explode(',',$mape['firstname']);
        $last   = explode(',',$mape['lastname']);
        $middle = explode(',',$mape['middle']);
        $birth  = explode(',',$mape['birth']);
        $gender = explode(',',$mape['gender']);
        $email  = explode(',',$mape['email']);
        $phone  = explode(',',$mape['phone']);
        $user   = D('User')->where(array('iuid'=>$mape['iuid']))->find();
        foreach ($prefix as $pey => $palue) {
            $info[$pey]['prefix'] = $palue;
            foreach ($suffix as $sey => $salue) {
                if($sey==$pey){
                    $info[$pey]['suffix'] = $salue;
                }
            }
            foreach ($first as $fey => $falue) {
                if($fey==$pey){
                    $info[$pey]['firstname'] = $falue;
                }
            }
            foreach ($last as $ley => $lalue) {
                if($ley==$pey){
                    $info[$pey]['lastname'] = $lalue;
                }
            }
            foreach ($middle as $mey => $malue) {
                if($mey==$pey){
                    $info[$pey]['middle'] = $malue;
                }
            }
            foreach ($birth as $bey => $balue) {
                if($bey==$pey){
                    $info[$pey]['birth'] = $balue;
                }
            }
            foreach ($gender as $gey => $galue) {
                if($gey==$pey){
                    $info[$pey]['gender'] = $galue;
                }
            }
            foreach ($email as $eey => $ealue) {
                if($eey==$pey){
                    $info[$pey]['email'] = $ealue;
                }
            }
            foreach ($phone as $hey => $halue) {
                if($hey==$pey){
                    $info[$pey]['phone'] = $halue;
                }
            }
        }
        // p($info);
        $trid   = D('Room')->where(array('hotel'=>$mape['hotel']))->find();
        $travel = D('Travel')->where(array('tid'=>$trid['tid']))->find();
        $ordnum = date('YmdHis').rand(100000, 999999);
        $booking=array(
            'iuid'       =>$mape['iuid'],
            'customerid' =>$user['customerid'],
            'breceiptnum'=>$ordnum,
            'bstatus'    =>0,
            'adultnum'   =>$mape['adultnum'],
            'childnum'   =>$mape['childnum'],
            'total'      =>$mape['total'],
            'bname'      =>$user['firstname'].' '.$user['lastname'],
            'bphone'     =>$user['phone'],
            'address1'   =>$mape['address1'],
            'address2'   =>$mape['address2'],
            'city'       =>$mape['city'],
            'state'      =>$mape['state'],
            'country'    =>$mape['country'],
            'zip'        =>$mape['zip'],
            'hotel'      =>$mape['hotel'],
            'room'       =>$trid['name'],
            'tid'        =>$trid['tid'],
            'rid'        =>$trid['rid'],
            'starttime'  =>$travel['starttime'],
            'endtime'    =>$travel['endtime'],
            'bpaytype'   =>0,
            'bdate'      =>time(),
        );
        // p($booking);
        $addbooking = D('booking')->add($booking);
        if($addbooking){
            //生成日志记录
            $content = '您的旅游行程已生成,编号:'.$ordnum.',总价:'.$mape['total'];
            $log = array(
                'from_iuid' =>$mape['iuid'],
                'content'   =>$content,
                'action'    =>0,
                'type'      =>2,
                'date'      =>date('Y-m-d H:i:s')          
            );
            $addlog = M('Log')->add($log);
            foreach ($info as $key => $value) {
                $value['breceiptnum'] = $ordnum;
                $addinfo = D('Visitor')->add($value);
            } 
        }
        if($addinfo){
            $data['status'] = 1;
            $data['ordnum'] = $ordnum;
            $data['message']= "订单已成功生成";
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['message']= "订单生成失败";
            $this->ajaxreturn($data); 
        }
    }

    /**
    * booking快钱支付
    **/
    public function bookingPay(){
        //$order_num = trim(I('post.ir_receiptnum'))?trim(I('post.ir_receiptnum')):date(YmdHis);
        $order_num = trim(I('post.breceiptnum'));
        //订单信息
        $order     = M('Booking')->where(array('breceiptnum'=>$order_num))->find();
        $peoplenum = $order['adultnum']+$order['childnum'];
        $kq_target          = "https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
        $kq_merchantAcctId  = "1020997278101";      //*  商家用户编号     (30)
        $kq_inputCharset    = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
        $kq_pageUrl         = ""; //   直接跳转页面 (256)
        $kq_bgUrl           = "http://apps.hapy-life.com/hapylife/index.php/Api/HapylifeApi/bookingReturn"; //   后台通知页面 (256)
        $kq_version         = "mobile1.0";  //*  版本  固定值 v2.0   (10)
        $kq_language        = "1";  //*  默认 1 ， 显示 汉语   (2)
        $kq_signType        = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
        $kq_payerName       = $order['customerid']; //   英文或者中文字符   (32)
        $kq_payerContactType= "1";    //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
        $kq_payerContact    = "";     //   支付人联系方式    (50)
        $kq_orderId         = $order_num; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
        $kq_orderAmount     = $order['total']*100; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
        $kq_orderTime       = date(YmdHis);  //*  交易时间  格式: 20110805110533
        $kq_productName     = "hapylife";//    商品名称英文或者中文字符串(256)
        $kq_productNum      = $peoplenum;   //    商品数量  (8)
        $kq_productId       = "";   //    商品代码，可以是 字母,数字,-,_   (20) 
        $kq_productDesc     = ""; //    商品描述， 英文或者中文字符串  (400)
        $kq_ext1            = "";   //    扩展字段， 英文或者中文字符串，支付完成后，按照原样返回给商户。 (128)
        $kq_ext2            = "";
        $kq_payType         = "21"; //*  固定选择值：00、15、21、21-1、21-2
        //00代表显示快钱各支付方式列表；
        //15信用卡无卡支付
        //21 快捷支付
        //21-1 代表储蓄卡快捷；21-2 代表信用卡快捷
        //*其中”-”只允许在半角状态下输入。
        $kq_bankId          = "";   //银行代码 银行代码 要在开通银行时 使用， 默认不开通 (8)
        $kq_redoFlag        = "0";  //同一订单禁止重复提交标志  固定值 1 、 0      
                                    //1 表示同一订单只允许提交一次 ； 0 表示在订单没有支付成功状态下 可以重复提交； 默认 0 
        $kq_pid             = "";       //合作伙伴在快钱的用户编号 (30)
        $kq_payerIdType     ="3";        //指定付款人
        $kq_payerId         =date('YmdHis').rand(100000, 999999);       //付款人标识

        $map = array(
            'inputCharset'      =>$kq_inputCharset,
            'pageUrl'           =>$kq_pageUrl,
            'bgUrl'             =>$kq_bgUrl,
            'version'           =>$kq_version,
            'language'          =>$kq_language,
            'signType'          =>$kq_signType,
            'merchantAcctId'    =>$kq_merchantAcctId,
            'payerName'         =>$kq_payerName,
            'payerContactType'  =>$kq_payerContactType,
            'payerContact'      =>$kq_payerContact,
            'payerIdType'       =>$kq_payerIdType,
            'payerId'           =>$kq_payerId,
            'orderId'           =>$kq_orderId,
            'orderAmount'       =>$kq_orderAmount,
            'orderTime'         =>$kq_orderTime,
            'productName'       =>$kq_productName,
            'productNum'        =>$kq_productNum,
            'productId'         =>$kq_productId,
            'productDesc'       =>$kq_productDesc,
            'ext1'              =>$kq_ext1,
            'ext2'              =>$kq_ext2,
            'payType'           =>$kq_payType,
            'bankId'            =>$kq_bankId,
            'redoFlag'          =>$kq_redoFlag,
            'pid'               =>$kq_pid
        );
        foreach ($map as $k => $v) {
            if(!empty($v)){
                $k.='='.$v.'&';
                $kq_all_para .= $k;
            }
        }
        $kq_all_para = rtrim($kq_all_para,'&');
        //生成证书
        $priv_key = file_get_contents("./99bill-rsa.pem");
        $pkeyid   = openssl_get_privatekey($priv_key);
        // compute signature
        openssl_sign($kq_all_para, $signMsg, $pkeyid);
        // free the key from memory
        openssl_free_key($pkeyid);
        $kq_sign_msg = urlencode(base64_encode($signMsg));
        $url = $kq_target.'?'.$kq_all_para.'&signMsg='.$kq_sign_msg;
        //header("Location:".$url);
        if($url){
            $data = array(
                'ir_receiptnum'=>$order_num,
                'url'          =>$url,
                'status'       =>1,
                'msg'          =>'跳转至该url，使用快钱支付'
            );
            $this->ajaxreturn($data);
        }else{
            $data = array(
                'ir_receiptnum'=>$order_num,
                'url'          =>'请求失败',
                'status'       =>0,
                'msg'          =>'支付请求失败'
            );
            $this->ajaxreturn($data);
        }
    }

    /*
    **旅游快钱返回结果
    */
    public function bookingReturn(){
        $kq_check_all_para=kq_ck_null($_GET['merchantAcctId'],'merchantAcctId').kq_ck_null($_GET['version'],'version').kq_ck_null($_GET['language'],'language').kq_ck_null($_GET['signType'],'signType').kq_ck_null($_GET['payType'],'payType').kq_ck_null($_GET['bankId'],'bankId').kq_ck_null($_GET['orderId'],'orderId').kq_ck_null($_GET['orderTime'],'orderTime').kq_ck_null($_GET['orderAmount'],'orderAmount').kq_ck_null($_GET['bindCard'],'bindCard').kq_ck_null($_GET['bindMobile'],'bindMobile').kq_ck_null($_GET['dealId'],'dealId').kq_ck_null($_GET['bankDealId'],'bankDealId').kq_ck_null($_GET['dealTime'],'dealTime').kq_ck_null($_GET['payAmount'],'payAmount').kq_ck_null($_GET['fee'],'fee').kq_ck_null($_GET['ext1'],'ext1').kq_ck_null($_GET['ext2'],'ext2').kq_ck_null($_GET['payResult'],'payResult').kq_ck_null($_GET['errCode'],'errCode');

        $trans_body= substr($kq_check_all_para,0,strlen($kq_check_all_para)-1);
        $MAC       = base64_decode($_GET['signMsg']);
        $cert      = file_get_contents("./99bill.cert.rsa.20340630.cer");
        $pubkeyid  = openssl_get_publickey($cert); 
        $ok        = openssl_verify($trans_body, $MAC, $pubkeyid); 
        if ($ok == 1) {
            //写入日志记录
            $map = array(
                'content'=>'<result>1</result><redirecturl>http://success.html</redirecturl>',
                'date'   =>date('Y-m-d H:i:s'),
                'billno' =>$_GET['orderId'],
                'amount' =>$_GET['orderAmount'],
                'action' =>1,
                'status' =>1
            ); 
            $add = M('Log')->add($map);
            //做订单的处理
            $receipt = M('Booking')->where(array('breceiptnum'=>$_GET['orderId']))->setField('bstatus',2);
            if($receipt){
                //通知快钱商户收到的结果
                echo '<result>1</result><redirecturl>http://success.html</redirecturl>';
            }
        }else{
            $map = array(
                'content'=>'<result>1</result><redirecturl>http://false.html</redirecturl>',
                'date'   =>date('Y-m-d H:i:s'),
                'action' =>1,
                'status' =>0
            ); 
            //通知快钱商户收到的结果
            echo '<result>1</result><redirecturl>http://false.html</redirecturl>';
            $this->ajaxreturn($map);
        }
    }


    /**
    * 旅游订单状态查询
    * @param bstatus 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param breceiptnum 订单编号
    **/
    public function bookingCheck(){
        $ir_receiptnum = I('post.breceiptnum');
        //订单状态查询
        $data = M('Booking')->where(array('breceiptnum'=>$ir_receiptnum))->find();
        $data['info']  = D('Visitor')->where(array('breceiptnum'=>$ir_receiptnum))->select();
        if($data['bstatus'] == 2){
            //支付成功
            $data['status'] = 1;
            $data['msg'] = '支付成功，请跳转...';
            $this->ajaxreturn($data);
        }else{
            $mape['status'] = 0;
            $mape['msg'] = '正在支付，请等待...';
            $this->ajaxreturn($mape);
        }
    }

    /**
    * 订单信息查询
    **/
    public function getOrderInfo(){
        //订单信息查询
        $ir_receiptnum = I('post.breceiptnum');
        $data          = M('Booking')->where(array('breceiptnum'=>$ir_receiptnum))->find();
        $data['info']  = D('Visitor')->where(array('breceiptnum'=>$ir_receiptnum))->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['message']= '查询不到该订单';
            $this->ajaxreturn($data);
        }
    }

}
