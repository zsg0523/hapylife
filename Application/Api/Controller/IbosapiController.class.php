<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* IbosApi控制器
**/
class IbosApiController extends HomeBaseController{
    public function index(){
        /**
        * 登录测试user.logon（成功，数据返回）
        * @param $username
        * @param $password
        **/
        // $hu_nickname ="root";
        // $password    ="^ibos&buy$";
        // $hu_nickname = "CN0000000093";
        // $password    = "13242998086";
        // $ibos360_result = ibos360_userlogin($hu_nickname,$password);

        /**
        * 会员注册member.Createmember
        * @param $data
        **/
        // $data = array(
        //         'name'              =>"KR API Test",
        //         'companyCode'       =>"CN",
        //         'gender'            =>"M",
        //         'nationality'       =>"KOREA",
        //         'website'           =>"",
        //         'email'             =>"rpainapq@gmail.com",
        //         'enrollmentGrade'   =>"0",
        //         'pinTitle'          =>"0",
        //         'subtype'           =>"40",
        //         'fax'               =>"010258695",
        //         'telephone'         =>"01085695485",
        //         'mobilePhone'       =>"18956328564",
        //         'createBy'          =>"memberA",
        //         'birthday'          =>"1986-05-15 00:00:00",
        //         'joinDate'          =>"2016-05-05 00:00:00",
        //         'proofOfIdentification'=>"010203040506070809",
        //         'remarks'           =>"This is a short memo",
        //         'status'            =>"10",
        //         'isAgent'           =>"",
        //         'agentNo'           =>"",
        //         'sponsorNo'         =>"CN0000024",
        //         'placementNo'       =>"CN0000024"
        //     ); 
        // $username       ="CN0000024";
        // $password       ="000000";
        // $ibos360_login  = ibos360_userlogin($username,$password);
        // p($ibos360_login);
        // if($ibos360_login){
        //     $data           = json_encode($data);
        //     $ibos360_result = ibos360_Createmember($data,$ibos360_login['sessionId']);
        // }
        // p($ibos360_result);die;

        /**
        * 会员查询 member.GetMembers
        **/
        // $data     = array(
        //                 "companyCode" => "CN"
        //             );
        // $pageModel= array(
        //                 "currentPage"  =>0,
        //                 "pageSize"     =>10
        //             );
        // $data           = json_encode($data);
        // $pageModel      = json_encode($pageModel);
        // $ibos360_result = ibos360_GetMembers($data,$pageModel);

        /**
        * 业绩测试bonus.getOrgMonthlyPfm（没有会员数据测试）
        * @param $memberNo
        * @param $workingStage
        **/
        // $memberNo     = "KR03007190";
        // $workingStage = "2016C04";
        // $ibos360_result = ibos360_Getbonus($memberNo,$workingStage);
        // p($ibos360_result);die;

        /**
        * 商品编码product.GetProducts
        * @param $data
        * @param $pageModel
        **/
        // $data     = array(
        //                 "productNo" => "CNPHJ020202022"
        //             );
        // $pageModel= array(
        //                 "currentPage"  =>1,
        //                 "pageSize"     =>10
        //             );
        // $data           = json_encode($data);
        // $pageModel      = json_encode($pageModel);
        // $ibos360_result = ibos360_GetProducts($data,$pageModel);

        /**
        * 销售商品product.GetProductSales （成功)
        * @param $data
        * @param $pageModel
        **/
        // $data     = array(
        //                 "companyCode"  => "",
        //                 "orderType"    => "",
        //                 "product"      => array(
        //                                     "productNo" => ""
        //                                 )
        //             );
        // $pageModel= array(
        //                 "currentPage"  =>"",
        //                 "pageSize"     =>""
        //             );
        // $data           = json_encode($data);
        // $pageModel      = json_encode($pageModel);
        // $ibos360_result = ibos360_GetProductSales($data,$pageModel);

        // $ibos_product   = M('IbosProduct')->where(array('ip_cnsale'=>1))->select();

        // //单品（productType 10单品 20套装）
        // foreach ($ibos360_result['resultSet'] as $k => $v) {
        //             $product[$k]['bv']          = $v['bv'];
        //             $product[$k]['pv']          = $v['pv'];
        //             $product[$k]['price']       = $v['price'];
        //             $product[$k]['orderType']   = $v['orderType'];
        //             $product[$k]['productNo']   = $v['product']['productNo'];
        //             $product[$k]['productName'] = $v['product']['productName'];
        //             $product[$k]['productType'] = $v['product']['productType'];
        // }

        // foreach ($ibos_product as $key => $value) {
        //         $productList[$key] = $value;
        //     foreach ($product as $k => $v) {
        //         if($v['productNo'] == $productList[$key]['productno']){
        //             $productList[$key]['api'] = 'Get from Ibos360';
        //             $productList[$key]['ip_price_rmb'] = $v['price'];
        //             $productList[$key]['ip_name_zh']   = $v['productName'];
        //             $productList[$key]['productType']  = $v['productType'];
        //             $productList[$key]['bv']           = $v['bv'];
        //         }
        //     }
        // }
        //p($product);
        //P($ibos360_result);
        //p($ibos_product);
        //p($productList);
        //die;
        /**
        * 推荐网network.GetSponsorNetwork(成功)
        * @param $memberNo
        * @param $level
        **/
        // $memberNo ="CN0000000319";
        // $level    ="3";
        // $ibos360_result = ibos360_GetSponsorNetwork($memberNo,$level);
        

        /**
        * 安置网network.GetPlacementNetwork(成功)
        * @param $memberNo
        * @param $level
        **/
        // $memberNo ="CN0000000319";
        // $memberNo ="top";
        // $level    ="1";
        // $ibos360_result = ibos360_GetPlacementNetwork($memberNo,$level);

        /**
        * 订单查询order.GetOrders(成功，有数据返回)
        * @param $data
        * @param $pageModel
        **/
        // $data     = array(
        //                 "companyCode"  => "CN",
        //             );
        // $pageModel= array(
        //                 "currentPage"  =>0,
        //                 "pageSize"     =>10
        //             );
        // $data           = json_encode($data);
        // $pageModel      = json_encode($pageModel);
        // $ibos360_result = ibos360_GetOrders($data,$pageModel);

        /**
        * 订单保存order.SaveOrder()
        * @param $data
        * @param $pageModel
        **/
        // $data     = array(
        //                 "data"=>array(
        //                     "companyCode"       => "CN",
        //                     "docNo"             => "M0201605256665212564",
        //                     "orderDate"         => "2016-05-23 16:55:09",
        //                     "totalPv"           => 100,
        //                     "totalPoint"        => 0,
        //                     "totalBv"           => 50,
        //                     "totalNetAmount"    => 50,
        //                     "subTotal"          => 50,
        //                     "taxTotal"          => 0,
        //                     "shippingTotal"     => 0,
        //                     "bonusDate"         => "2016-05-23 16:55:06",
        //                     "paymentDate"       => "2016-05-23 16:55:06",
        //                     "orderType"         => "10",
        //                     "orderStatus"       => "10",
        //                     "firstname"         => "afaf",
        //                     "lastname"          => "aa",
        //                     "postcode"          => 11,
        //                     "mobile"            => "1111",
        //                     "detailedAddress"   => "china ,Taiwan,市区",
        //                     "createDate"        => "2016-05-23 16:55:09",
        //                     "createBy"          => "root",
        //                     "agentId"           => 111,
        //                     "isSelfPickup"      => "0",
        //                     "member"            => array("memberNo" => "CN191970204"),
        //                     "ordersDetailsList" => array(
        //                             array(
        //                                 "price"         => 50,
        //                                 "pv"            => 100,
        //                                 "bv"            => 50,
        //                                 "counts"        => 1,
        //                                 "status"        => 1,
        //                                 "tax"           => 0,
        //                                 "agioPrice"     => 50,
        //                                 "beforeTaxPrice"=> 50,
        //                                 "taxPrice"      => 0,
        //                                 "total"         => 50,
        //                                 "beforeTaxTotal"=> 50,
        //                                 "taxTotal"      => 0,
        //                                 "productSale"   => array(
        //                                     "product"   => array(
        //                                         "productNo" => "CS00001"
        //                                     )
        //                                 )
        //                             )
        //                         )
        //                     )
        //                 );
        
        // $username       ="root";
        // $password       ="abcdef";
        // $ibos360_login  = ibos360_userlogin($username,$password);
        // if($ibos360_login){
        //     $data           = json_encode($data);
        //     $ibos360_result = ibos360_SaveOrder($data,$ibos360_login['sessionId']);
        // }
        // p($ibos360_login);
            
        
        //验证nulife服务器是否有该账号
        // $hu_nickname    = "HK20141111";
        // $hu_nickname    = "HK123456";
        // $password       = 34686188;
        // $nulife_url     = 'http://202.181.243.35/nulife/Enquiry/index.php?page=Enquiry:Authorization&username=' . $hu_nickname . '&password=' . $password;
        // //返回xml数据
        // $nulife_result  = file_get_contents($nulife_url);
        // if($nulife_result !== "invalid username or password"){
        //     $xml = new \SimpleXMLElement($nulife_result);
        //     if((string)$xml->dist_info == $hu_nickname && (string)$xml->dist_info['elpa_status'] == 1){
        //         $nulife = 1;
        //     }else{
        //         $nulife = 0;
        //     }
        // }else{
        //     $nulife = 0;
        // }
        // p((string)$xml->dist_info);die;

        /**
        * 退货单查询returnOrder.GetReturnOrders(成功，未看真实数据)
        * @param $data
        * @param $pageModel
        **/
        // $data     = array(
        //                 "companyCode"  => "CN",
        //             );
        // $pageModel= array(
        //                 "currentPage"  =>1,
        //                 "pageSize"     =>10
        //             );
        // $data           = json_encode($data);
        // $pageModel      = json_encode($pageModel);
        // $ibos360_result = ibos360_GetReturnOrders($data,$pageModel);
        
        // $num = 100-9.60;
        // p(sprintf(sprintf("%.2f",$num)));
        // p(md5('123456'));
        p($ibos360_result);die;
        
    }

    /**
    * 安置网查询
    **/
    public function getPlacementNetwork(){
        $memberNo = strtoupper(trim(I('post.hu_nickname')));
        $memberNo ="CN0000075";
        $level    ="1";
        $ibos360_result = ibos360_GetPlacementNetwork($memberNo,$level);

        //p($ibos360_result["networks"][0]);die;
        if(!empty($ibos360_result["networks"][0])){
            $data = $ibos360_result["networks"][0];
            $this->ajaxreturn($data);
        }else{
            $data['msg'] = "ibos360无该会员数据";
            $this->ajaxreturn($data);
        }
    }

    /**
    * 注册 密码是默认电话号码
    * @param iu_bank 开户行
    * @param iu_bankbranch 支行
    * @param iu_bankaccount 银行账号
    * @param iu_logic A->0 B->1
    **/
    public function ibosregister(){
        if(!IS_POST){
            $msg['status'] = 0;
            $this->ajaxreturn($msg);
        }else{
            $hu_iuid        = I('post.hu_iuid');//帮忙注册的人的iuid
            $hu_nickname    = I('post.hu_nickname');//帮忙注册的人的memberNo
            $hu_username    = I('post.hu_username');
            $iu_password    = md5(trim(I('post.hu_phone')));
            $iu_cardid      = I('post.iu_cardid');
            $hu_sex         = I('post.hu_sex');
            $hu_phone       = I('post.hu_phone');
            $hu_address     = I('post.hu_address');
            $iu_upid        = I('post.iu_upid')?I('post.iu_upid'):0;
            $iu_upname      = I('post.iu_upname')?I('post.iu_upname'):0;
            $iu_upphone     = I('post.iu_upphone')?I('post.iu_upphone'):0;
            $iu_bank        = I('post.iu_bank');
            $iu_bankbranch  = I('post.iu_bankbranch');
            $iu_bankaccount = I('post.iu_bankaccount');
            $iu_bankuser    = I('post.hu_nickname');
            $iu_logic       = I('post.iu_logic');
            //首购单订单号
            $ir_receiptnum  = I('post.ir_receiptnum');

            //储存身份证正反面
            $iu_cardrphoto  = I('post.iu_cardrphoto');
            if(!empty($iu_cardrphoto)){
                $img_body1 = substr(strstr($iu_cardrphoto,','),1);
                $url_cardrphoto = time().'_'.mt_rand().'.jpg';
                $img1 = file_put_contents('./Public/idcard/'.$url_cardrphoto, base64_decode($img_body1));
            }
            

            $iu_cardlphoto  = I('post.iu_cardlphoto');
            if(!empty($iu_cardlphoto)){
                $img_body2 = substr(strstr($iu_cardlphoto,','),1);
                $url_cardlphoto = time().'_'.mt_rand().'.jpg';
                $img2 = file_put_contents('./Public/idcard/'.$url_cardlphoto, base64_decode($img_body2));
            }

             //在ibos360注册
            $tmp = array(
                'name'                  =>"KR API Test",
                'password'              =>$hu_phone,
                'companyCode'           =>"CN",
                'gender'                =>"M",
                'nationality'           =>"KOREA",
                'website'               =>"",
                'email'                 =>"rpainapq@gmail.com",
                'enrollmentGrade'       =>"0",
                'pinTitle'              =>"0",
                'subtype'               =>"40",
                'fax'                   =>"010258695",
                'telephone'             =>"01085695485",
                'mobilePhone'           =>$hu_phone,
                'createBy'              =>"memberA",
                'birthday'              =>"1986-05-15 00:00:00",
                'joinDate'              =>"2016-05-05 00:00:00",
                'proofOfIdentification' =>"36072619930523601X",
                'remarks'               =>"This is a short memo",
                'status'                =>"0",
                'isAgent'               =>"",
                'agentNo'               =>"",
                'sponsorNo'             =>$iu_upid,
                'placementNo'           =>$iu_upid
            );
            // $username       = 'CN0000024';
            // $password       = "000000";
            $username = $hu_nickname;
            $password = "123456";
            $ibos360_login  = ibos360_userlogin($username,$password);
            if($ibos360_login){
                $tmp            = json_encode($tmp);
                $ibos360_result = ibos360_Createmember($tmp,$ibos360_login['sessionId']);
            }

            if($ibos360_result['success'] == 1){
                //在Eden系统注册
                if(!empty($iu_cardrphoto) && !empty($iu_cardlphoto)){
                    $data = array(
                        'hu_username'    => $hu_username,
                        'iu_password'    => $iu_password,
                        'iu_cardid'      => $iu_cardid,
                        'hu_nickname'    => $ibos360_result['memberNo'],
                        'hu_sex'         => $hu_sex,
                        'hu_phone'       => $hu_phone,
                        'hu_address'     => $hu_address,
                        'hu_phone'       => $hu_phone,
                        'iu_bp'          => 0,
                        'iu_gbv'         => 0,
                        'iu_point'       => 0,
                        'iu_grade'       => "v0",
                        'iu_registertime'=> date('Y-m-d H:i:s'),
                        'iu_upid'        => $iu_upid,
                        'iu_bank'        => $iu_bank,
                        'iu_bankbranch'  => $iu_bankbranch,
                        'iu_bankaccount' => $iu_bankaccount,
                        'iu_bankuser'    => $ibos360_result['memberNo'],
                        'iu_logic'       => $iu_logic,
                        'iu_cardrphoto'  => C('WEB_URL').'/Public/idcard/'.$url_cardrphoto,
                        'iu_cardlphoto'  => C('WEB_URL').'/Public/idcard/'.$url_cardlphoto
                    );
                }else{
                    $data = array(
                        'hu_username'    => $hu_username,
                        'iu_password'    => $iu_password,
                        'iu_cardid'      => $iu_cardid,
                        'hu_nickname'    => $ibos360_result['memberNo'],
                        'hu_sex'         => $hu_sex,
                        'hu_phone'       => $hu_phone,
                        'hu_address'     => $hu_address,
                        'hu_phone'       => $hu_phone,
                        'iu_bp'          => 0,
                        'iu_gbv'         => 0,
                        'iu_point'       => 1000,
                        'iu_grade'       => 0,
                        'iu_registertime'=> date('Y-m-d H:i:s'),
                        'iu_upid'        => $iu_upid,
                        'iu_bank'        => $iu_bank,
                        'iu_bankbranch'  => $iu_bankbranch,
                        'iu_bankaccount' => $iu_bankaccount,
                        'iu_bankuser'    => $ibos360_result['memberNo'],
                        'iu_logic'       => $iu_logic,
                    );
                }

                //使用昵称检测是否已经有注册的用户
                $user = M('IbosUsers')
                        ->where(array('hu_nickname'=>$ibos360_result['memberNo']))
                        ->getfield('hu_nickname');
                if($user){
                    $msg['status'] = 0;
                    $this->ajaxreturn($msg);
                }else{
                    if(M('IbosUsers')->add($data)){
                        //获取iuid
                        $iuid= M('IbosUsers')->where(array('hu_nickname'=>$ibos360_result['memberNo']))->getfield('iuid');
                        //将该首购单改为改注册用户
                        $map = array(
                            'iuid'          =>$iuid,
                            'hu_nickname'   =>$ibos360_result['memberNo'],
                            'ia_name'       =>$hu_username,
                            'ia_phone'      =>$hu_phone,
                            'ia_address'    =>$hu_address,
                            'ir_change'     =>1
                        );
                        $changeReceipt = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                        if($changeReceipt && $ibos360_result['success'] == 1){
                            //生成日志记录
                            $content = '新代理会员注册成功:memberNo='.$ibos360_result['memberNo'].',memberId='.$ibos360_result['memberId'];
                            $log = array(
                                    'from_iuid' =>$hu_iuid,
                                    'content'   =>$content,
                                    'action'    =>0,
                                    'type'      =>3,
                                    'date'      =>date('Y-m-d H:i:s')          
                                );
                            $addlog = M('IbosLog')->add($log);
                            $msg['status'] = 1;
                            $msg['msg']    = 'Ibos360注册成功，更改订单成功';
                            $msg['ibos360_result'] = $ibos360_result;
                            $this->ajaxreturn($msg);
                        }else{
                            $msg['status'] = 0;
                            $msg['msg']    = 'Ibos360注册成功，更改订单失败';
                            $msg['ibos360_result'] = $ibos360_result;
                            $this->ajaxreturn($msg);
                        }
                    }else{
                        $msg['status'] = 0;
                        $msg['msg']    = '注册失败';
                        $this->ajaxreturn($msg);
                    }
                } 
            }else{
                //生成日志记录
                $content = '新代理会员注册失败:error='.$ibos360_result['error']['message'];
                $log = array(
                        'from_iuid' =>$hu_iuid,
                        'content'   =>$content,
                        'action'    =>0,
                        'type'      =>3,
                        'date'      =>date('Y-m-d H:i:s')          
                    );
                $addlog = M('IbosLog')->add($log);
                $msg['status'] = 0;
                $msg['msg']    = 'Ibos360注册失败';
                $msg['ibos360_result'] = $ibos360_result;
                $this->ajaxreturn($msg);
            }       
        }   
    }


    /**
    注册新用户成为优惠顾客
    **/    
    public function temp_register(){
        if(!IS_POST){
            $msg['status'] = 0;
            $this->ajaxreturn($msg);
        }else{
            $data = I('post.');
            $data['iu_registertime'] = date('Y-m-d H:i:s');
            $data['iu_password']     = md5($data['hu_phone']);
            $ibos = D('IbosUsers')->where(array('hu_nickname'=>array('like','%'.'NlC'.'%')))->order('iuid desc')->find();
            if($ibos){
                $name = substr($ibos['hu_nickname'],3);
                $data['hu_nickname'] = 'NLC'.($name+1);
                $data['iu_permission'] = 2;
            }else{
                $data['hu_nickname'] = 'NLC10000001';
                $data['iu_permission'] = 2;
            }
            //IbosUsers建立账户
            $ibosuser = D('IbosUsers')->add($data);
            if($ibosuser){
                $iuid= D('IbosUsers')->where(array('hu_nickname'=>$data['hu_nickname']))->getfield('iuid');
                $map = array(
                    'iuid' =>$iuid
                );
                $hracuser = D('HracUsers')->add($map);
                if($hracuser){
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }
            } 
        }   
    }


    /**
    * 校验用户名密码
    **/
    public function check_user(){
        $hu_nickname = trim(I('post.hu_nickname'));
        $password    = trim(I('post.password'));

        $user = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
        if($user && $user['password'] = md5($password)){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 登录
    * @param hu_username 账号昵称
    * @param iu_password 账号密码
    **/
    public function iboslogin(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hu_nickname    = strtoupper(trim(I('post.hu_username')));
            $iu_password    = md5(trim(I('post.iu_password')));
            $password       = trim(I('post.iu_password'));
            
            //验证本服务器是否有该账号
            $data = D('IbosUsers')
                    ->where(array('hu_nickname'=>$hu_nickname))
                    ->find();
            if($data && $data['iu_password'] == $iu_password){
                $Eden = 1;     
            }else{
                $Eden = 0;
            }

            //验证nulife服务器是否有该账号
            $nulife_url     = 'http://202.181.243.35/nulife/Enquiry/index.php?page=Enquiry:Authorization&username=' .$hu_nickname. '&password=' . $password;
            //返回xml数据
            $nulife_result  = file_get_contents($nulife_url);
            if($nulife_result !== "invalid username or password"){
                $xml = new \SimpleXMLElement($nulife_result);
                if((string)$xml->dist_info == $hu_nickname && (string)$xml->dist_info['elpa_status'] == 1){
                    $nulife = 1;
                }else{
                    $nulife = 0;
                }
            }else{
                $nulife = 0;
            }

            //验证ibos360是否有该账号
            $ibos360_result = ibos360_userlogin($hu_nickname,$password);
            if($ibos360_result['success'] == 1){
                $ibos = 1;
            }else{
                $ibos = 0;
            }

            //根据不同组合验证情景执行，人手copy数据至nulife服务器，本服务器创建数据

            $data['Eden']   = $Eden;
            $data['nulife'] = $nulife;
            $data['ibos']   = $ibos;
            
            if($Eden==0&&$nulife==0&&$ibos==0){
                //不允许登录
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }else if($Eden==0&&$nulife==1&&$ibos==0){
                //判断是否有该账号
                if($data['hu_nickname'] == $hu_nickname){
                    $map = array(
                        'iuid' => $data['iuid'],
                        'iu_password'=>md5($password)
                    );
                    $save = M('IbosUsers')->save($map);
                    if($save){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }
                //在ibosuser表创建该账户
                $data = array(
                        'hu_nickname'   => $hu_nickname,
                        'hu_username'   => "GMS会员",
                        'iu_password'   => md5($password),
                        'iu_ibos360'    => 1
                    );
                $add = M('IbosUsers')->add($data);
                if($add){
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                } 
            }else if($Eden==0&&$nulife==1&&$ibos==1){
                //判断是否有该账号
                if($data['hu_nickname'] == $hu_nickname){
                    $map = array(
                        'iuid' => $data['iuid'],
                        'iu_password'=>md5($password)
                    );
                    $save = M('IbosUsers')->save($map);
                    if($save){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }
                //Eden系统无此账号直接创建
                $data = array(
                        'hu_nickname'   => $hu_nickname,
                        'hu_username'   => "GMS,360会员",
                        'iu_password'   => md5($password)
                    );
                $add = M('IbosUsers')->add($data);
                if($add){
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==0&&$nulife==0&&$ibos==1){
                //判断是否有该账号
                if($data['hu_nickname'] == $hu_nickname){
                    $map = array(
                        'iuid' => $data['iuid'],
                        'iu_password'=>md5($password)
                    );
                    $save = M('IbosUsers')->save($map);
                    if($save){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }
                //在IbosUsers创建账号的同时，copy至nulife服务器
                $data = array(
                        'hu_nickname'   => $hu_nickname,
                        'hu_username'   => "360会员",
                        'iu_password'   => md5($password),
                        'iu_nulife'     => 1
                    );
                $add = M('IbosUsers')->add($data);
                if($add){
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==1&&$nulife==0&&$ibos==0){
                //copy至nulife服务器和ibos360
                $user = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                if($user['iu_nulife'] == 0 || $user['iu_ibos360'] == 0){
                    $map = array(
                        'iu_nulife' => 1,
                        'iu_ibos360'=> 1
                    );
                    $add = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->save($map);
                    if($add){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);  
                    }
                }else{
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==1&&$nulife==0&&$ibos==1){
                //copy至nulife
                $data = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                if($data['iu_nulife'] == 0){
                    $add = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->setField('iu_nulife',1);
                    if($add){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }else{
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==1&&$nulife==1&&$ibos==0){
                //copy至ibos360
                $user = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                if($user['iu_ibos360'] == 0){
                    $add = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->setField('iu_ibos360',1);
                    if($add){
                        //记录最近一次的登录时间
                        $date = date('Y-m-d H:i:s');
                        $logintime = D('IbosUsers')
                                    ->where(array('hu_nickname'=>$hu_nickname))
                                    ->setField('iu_logintime',$date);
                        $data = D('IbosUsers')
                            ->where(array('hu_nickname'=>$hu_nickname))
                            ->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }else{
                    //记录最近一次的登录时间
                    $date = date('Y-m-d H:i:s');
                    $logintime = D('IbosUsers')
                                ->where(array('hu_nickname'=>$hu_nickname))
                                ->setField('iu_logintime',$date);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
                
            }else if($Eden==1&&$nulife==1&&$ibos==1){
                //记录最近一次的登录时间
                $date = date('Y-m-d H:i:s');
                $logintime = D('IbosUsers')
                            ->where(array('hu_nickname'=>$hu_nickname))
                            ->setField('iu_logintime',$date);
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 调取用户信息
    * @param hu_nickname 昵称
    **/
    public function userid(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hu_nickname = I('post.hu_nickname');
            $data = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 调取用户信息
    * @param iuid 账号id
    **/
    public function userinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid             = I('post.iuid');
            $data             = M('IbosUsers')->where(array('iuid'=>$iuid))->find();
            $memberNo         = strtoupper($data['hu_nickname']);
            $level            = "1";
            $ibos360_result   = ibos360_GetPlacementNetwork($memberNo,$level);
            if($ibos360_result["networks"][0] == ""){
                $data['networks'] = array(
                    'SPONSOR_NO'    => '',
                    'PLACEMENT_NO'  => ''
                );
            }else{
                $data['networks'] = $ibos360_result["networks"][0];
            }
            
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }

    }



    /**
    * 商品分类（一级分类）
    **/
    public function category(){ 
        if(!IS_GET){
            $$data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //通过parent_id获取分类
            $pid  = 0;
            $data = M('IbosCategory')->where(array('pid'=>$pid))->order('sort_id')->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 获取商品分类（完整分类树）大陆
    **/
    public function classify(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取一级分类·
            $map     = array(
                        'pid'        =>0,
                        'is_show'    =>1,
                        'icat_cnsale'=>1
                    );
            $catList = M('IbosCategory')
                     ->where($map)
                     ->order('sort_id asc')
                     ->select();
            //重构数组
            foreach($catList as $key => $value){
                $tmp = M('IbosCategory')
                     ->where(array('pid'=>$value['id']))
                     ->where(array('is_show'=>1))
                     ->where(array('icat_cnsale'=>1))
                     ->select();
                $arr[$value['id']]['icat_name'] = $value['icat_name_zh'];
                $arr[$value['id']]['content']   = $tmp;
            }
            //转索引数组
            foreach ($arr as $key => $value) {
                $row[] = $value;
            }
            if($row){
                $this->ajaxreturn($row);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    * 获取商品分类（完整分类树）香港
    **/
    public function hk_classify(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取一级分类·
            $map     = array(
                        'pid'        =>0,
                        'is_show'    =>1,
                        'icat_hksale'=>1
                    );
            $catList = M('IbosCategory')
                     ->where($map)
                     ->order('sort_id asc')
                     ->select();
            //重构数组
            foreach($catList as $key => $value){
                $tmp = M('IbosCategory')
                     ->where(array('pid'=>$value['id']))
                     ->where(array('is_show'=>1))
                     ->select();
                $arr[$value['id']]['icat_name'] = $value['icat_name_zh'];
                $arr[$value['id']]['content']   = $tmp;
            }
            //转索引数组
            foreach ($arr as $key => $value) {
                $row[] = $value;
            }
            if($row){
                $this->ajaxreturn($row);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    

    /**
    * 获取商品列表
    * id  分类id
    **/
    public function productlist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $id         = trim(I('post.id'));
            $ip_cnsale  = trim(I('post.ip_cnsale'));   
            if(empty($ip_cnsale)){
                $map     = array(
                    'nulife_ibos_category.id'      =>$id,
                    'nulife_ibos_category.is_show' =>1,
                    'nulife_ibos_product.is_pull'  =>1,
                    'nulife_ibos_product.ip_hksale'=>1,
                    'ip_type'                      =>0
                );
            }else{
                $map     = array(
                    'nulife_ibos_category.id'      =>$id,
                    'nulife_ibos_category.is_show' =>1,
                    'nulife_ibos_product.is_pull'  =>1,
                    'nulife_ibos_product.ip_cnsale'=>1,
                    'ip_type'                      =>0
                );
            }

            $data    = M('IbosProduct')
                    ->join('nulife_ibos_category on nulife_ibos_category.id = nulife_ibos_product.icid')
                    ->where($map)
                    ->order('sort_id desc')
                    ->select();
            
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    

    /**
    * 首购单商品列表
    **/
    public function productlist_regist(){
        $map = array(
            'ip_type'=>1
        );
        $data= M('IbosProduct')->where($map)->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 商品详情
    **/
    public function product(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $ipid = I('post.ipid');
            $ibos_product = M('IbosProduct')->where(array('ipid'=>$ipid))->select();
            // $data = M('IbosProduct')->where(array('ipid'=>$ipid))->find();
            $data     = array(
                        "companyCode"  => "",
                        "orderType"    => "",
                        "product"      => array(
                                            "productNo" => ""
                                        )
                    );
            $pageModel= array(
                            "currentPage"  =>"",
                            "pageSize"     =>""
                        );
            $data           = json_encode($data);
            $pageModel      = json_encode($pageModel);
            $ibos360_result = ibos360_GetProductSales($data,$pageModel);


            //单品（productType 10单品 20套装）
            foreach ($ibos360_result['resultSet'] as $k => $v) {
                        $product[$k]['bv']          = $v['bv'];
                        $product[$k]['pv']          = $v['pv'];
                        $product[$k]['price']       = $v['price'];
                        $product[$k]['product']     = $v['product'];
                        $product[$k]['productNo']   = $v['product']['productNo'];
                        $product[$k]['productName'] = $v['product']['productName'];
                        $product[$k]['productType'] = $v['product']['productType'];
            }
            foreach ($ibos_product as $key => $value) {
                    $productList[$key] = $value;
                foreach ($product as $k => $v) {
                    if($v['productNo'] == $productList[$key]['productno']){
                        $productList[$key]['api'] = 'Get from Ibos360';
                        // $productList[$key]['ip_price_rmb'] = $v['price'];
                        $productList[$key]['ip_name_zh']   = $v['productName'];
                        $productList[$key]['productType']  = $v['productType'];
                        $productList[$key]['bv']           = $v['bv'];
                    }
                }
            }
            foreach ($productList as $key => $value) {
                $map = $value;
            }

            if($map){
                $this->ajaxreturn($map);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }


    /**
    * 加入购物车
    * @param iuid 用户id
    * @param ipid 商品id
    * @param product_type 大陆0，香港1
    **/
    public function shopcart(){
        //接受商品ipid
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //会员iuid
            $iuid = I('post.iuid');
            //商品ipid
            $ipid = I('post.ipid');
            //获取商品所属地区
            $product_type = I('post.product_type');

            $product = M('IbosProduct')->where(array('ipid'=>$ipid))->find();
            $userinfo= M('IbosUsers')->where(array('iuid'=>$iuid))->find();
            //判断是否属于优惠商品，以及是否属于会员
            if($product['icid'] == 27 && substr($userinfo['hu_nickname'],0,3) == "NLC"){
                $data['status'] = 0;
                $data['msg']    = "成为优惠会员即可购买此商品";
                $this->ajaxreturn($data);
            }
            //加入购物车表(存session，判断是否已有商品，判断商品数量)
            $shopcartlist = M('IbosShopcart')->where(array('iuid'=>$iuid))->select();

            //商品可重复购买
            if($shopcartlist){
                //判断是否重复商品
                $is_repeat = false;
                foreach ($shopcartlist as $k => $v) {
                    if($ipid == $v['ipid']){
                        //商品数量加一
                        $map = array(
                            'iuid'=>$iuid,
                            'ipid'=>$ipid
                        );
                        $changenum = M('IbosShopcart')->where($map)->setInc('product_number');
                        $is_repeat = true;
                        if($changenum){
                            $data['status'] = 1;
                            $this->ajaxreturn($data);
                        }else{
                            $data['status'] = 0;
                            $this->ajaxreturn($data);
                        }
                    }
                }
                if(!$is_repeat){
                    $data = array(
                        'iuid'              =>$iuid,
                        'ipid'              =>$ipid,
                        'product_number'    =>1,
                        'product_price'     =>$product['ip_price_rmb'],
                        'product_point'     =>$product['ip_point'],
                        'product_type'      =>$product_type
                    );
                    $insertcart = M('IbosShopcart')->add($data);

                    if($insertcart){
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }else{
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }
                }    
            }else{
                $data = array(
                        'iuid'              =>$iuid,
                        'ipid'              =>$ipid,
                        'product_number'    =>1,
                        'product_price'     =>$product['ip_price_rmb'],
                        'product_point'     =>$product['ip_point'],
                        'product_type'      =>$product_type
                    );
                $insertcart = M('IbosShopcart')->add($data);
                if($insertcart){
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }
            }     
        }
    }

    /**
    * 购物车结算界面列表 hk
    * @param iuid 用户id
    **/
    public function hk_cartlistone(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid = I('post.iuid');
            //获取购物车列表信息
            $cartlist = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_product.ipid = nulife_ibos_shopcart.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('product_type'=>1))
                        ->order('iscid desc')
                        ->select();
            if($cartlist){
                $this->ajaxreturn($cartlist);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }

    /**
    * 购物车结算界面列表 cn
    * @param iuid 用户id
    **/
    public function cn_cartlistone(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid = I('post.iuid');
            //获取购物车列表信息
            $cartlist = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_product.ipid = nulife_ibos_shopcart.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('product_type'=>0))
                        ->order('iscid desc')
                        ->select();
            if($cartlist){
                $this->ajaxreturn($cartlist);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }

    /**
    * 购物车提交订单界面列表 hk
    **/
    public function hk_cartlist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid = I('post.iuid');
            //获取购物车列表信息
            $cartlist = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_product.ipid = nulife_ibos_shopcart.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('nulife_ibos_shopcart.is_show'=>1))
                        ->where(array('product_type'=>1))
                        ->select();

            if($cartlist){
                $this->ajaxreturn($cartlist);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }


    /**
    * 购物车提交订单界面列表 cn
    **/
    public function cn_cartlist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $iuid = I('post.iuid');
            //获取购物车列表信息
            $cartlist = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_product.ipid = nulife_ibos_shopcart.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('nulife_ibos_shopcart.is_show'=>1))
                        ->where(array('product_type'=>0))
                        ->select();

            if($cartlist){
                $this->ajaxreturn($cartlist);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }



    

    /**
    *  增加购物车某商品数量
    **/
    public function cartadd(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $iuid     = I('post.iuid');
            //商品id
            $ipid     = I('post.ipid');
            //获取商品信息
            $product  = M('IbosProduct')
                      ->where(array('ipid'=>$ipid))
                      ->find();
            //商品价格
            $price    = $product['ip_price_rmb'];
            //商品分数
            $point    = $product['ip_point'];
            //获取购物车列表信息
            $cart     = M('IbosShopcart')
                      ->where(array('iuid'=>$iuid,'ipid'=>$ipid))
                      ->find();
            //更新购物车该商品
            $data['product_number']  = $cart['product_number']+1;
            $cartpro  = M('IbosShopcart')
                      ->where(array('iuid'=>$iuid,'ipid'=>$ipid))
                      ->save($data);
            if($cartpro){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }

    /**
    * 减少购物车某商品数量
    **/
    public function cartreduce(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $iuid     = I('post.iuid');
            //商品id
            $ipid     = I('post.ipid');
            //获取商品信息
            $product  = M('IbosProduct')
                      ->where(array('ipid'=>$ipid))
                      ->find();
            //商品价格
            $price    = $product['ip_price_rmb'];
            //商品分数
            $point    = $product['ip_point'];
            //获取购物车列表信息
            $cart     = M('IbosShopcart')
                      ->where(array('iuid'=>$iuid,'ipid'=>$ipid))
                      ->find();
            //更新购物车该商品
            if($cart['product_number']>1){
                $data['product_number']  = $cart['product_number']-1;
                $cartpro  = M('IbosShopcart')
                          ->where(array('iuid'=>$iuid,'ipid'=>$ipid))
                          ->save($data);
            }
            if($cartpro){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }


    /**
    *  购物车结算订单界面选购需要提交订单商品
    **/
    public function is_show(){

        $iscid   = I('post.iscid');
        $is_show = I('post.is_show');

        //购物车已添加列表
        $map = array(
                'iscid' =>$iscid,
                'is_show'=>$is_show
            );
        $is_show = M('IbosShopcart')->save($map);
        //购物车确定
        if($is_show){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    *   购物车结算订单界面删除商品
    **/
    public function is_delete(){
        $iscid   = I('post.iscid');
        //购物车已添加列表
        $map = array(
                'iscid' =>$iscid,
            );
        $is_delete = M('IbosShopcart')->where($map)->delete();
        //购物车确定
        if($is_delete){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    *   购物车提交订单(生成订单,计算总价) hk
    * @param iuid    用户id
    * @param ir_area 订单所属地区
    **/
    public function hk_order(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            //获取用户iuid
            $iuid     = I('post.iuid');
            $ir_area  = trim(I('post.ir_area'));
            $nickname = D('IbosUsers')->where(array('iuid'=>$iuid))->getfield('hu_nickname');
            //获取地址id
            $iaid = I('post.iaid');
            

            //遍历购物车所有商品ipid(如果表中存有会话数据，则反序列化)
            $shopcart   = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_shopcart.ipid = nulife_ibos_product.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('nulife_ibos_shopcart.is_show'=>1))
                        ->where(array('product_type'=>1))
                        ->select();
            // p($shopcart);die;
            $address    = M('IbosAddress')
                        ->where(array('iaid'=>$iaid))
                        ->find();
            //生成唯一订单号
            $order_num = date('YmdHis').rand(10000, 99999);

            //计算总价
            foreach ($shopcart as $k => $v) {
                //总数量
                $total_num   += $v['product_number'];
                //总金额
                $total_price += $v['product_number'] * $v['product_price'];
                //总积分
                $total_point += $v['product_number'] * $v['product_point'];
                //订单备注
                $ir_desc .= $v['ip_name_zh'].'*'.$v['product_number'].'&nbsp;';
            }

            $order = array(
                //订单编号
                'ir_receiptnum' =>$order_num,
                //订单创建日期
                'ir_date'=>time(),
                //订单的状态(0待生成订单，1待支付订单，2已付款订单)
                'ir_status'=>0,
                //下单用户id
                'iuid'=>$iuid,
                //下单用户
                'hu_nickname'=>$nickname,
                //收货人
                'ia_name'=>$address['ia_name'],
                //收货人电话
                'ia_phone'=>$address['ia_phone'],
                //收货地址
                'ia_address'=>$address['ia_address'],
                //订单总商品数量
                'ir_productnum'=>$total_num,
                //订单总金额
                'ir_price'=>$total_price,
                //订单总积分
                'ir_point'=>$total_point,
                //订单备注
                'ir_desc'=>$ir_desc,
                //订单所属地区
                'ir_area'=>$ir_area
            );

            $receipt = M('IbosReceipt')->add($order);

            //订单详情记录商品信息
            if($receipt){
                foreach ($shopcart as $k => $v) {
                    $map = array(
                        'ir_receiptnum'     =>  $order_num,
                        'ipid'              =>  $v['ipid'],
                        'product_num'       =>  $v['product_number'],
                        'product_point'     =>  $v['ip_point']*$v['product_number'],
                        'product_price'     =>  $v['ip_price_rmb']*$v['product_number'],
                        'product_name'      =>  $v['ip_name_zh'],
                        'product_picture'   =>  $v['ip_picture_zh']
                    );
                    $receiptlist = M('IbosReceiptlist')->add($map);
                }
                //生成日志记录
                $content = '您的订单已生成,编号:'.$order_num.',包含:'.$ir_desc.',总价:'.$total_price.'Rmb,所需积分:'.$total_point;
                $log = array(
                        'from_iuid' =>$iuid,
                        'content'   =>$content,
                        'action'    =>0,
                        'type'      =>2,
                        'date'      =>date('Y-m-d H:i:s')          
                    );
                $addlog = M('IbosLog')->add($log);
                if($receiptlist){
                    //订单提交后清空购物车
                    $rst = M('IbosShopcart')
                        ->where(array('iuid'=>$iuid))
                        ->where(array('is_show'=>1))
                        ->where(array('product_type'=>1))
                        ->delete();
                    if($rst){
                        $data['status'] = 1;
                        $this->ajaxreturn($order);
                    }else{
                        $data['status'] = 0;
                        $this->ajaxreturn($order);
                    }
                }
            }
        }
    }


    /**
    *   购物车提交订单(生成订单,计算总价) 大陆
    * @param iuid    用户id
    * @param ir_area 订单所属地区
    **/
    public function cn_order(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            //获取用户iuid
            $iuid     = I('post.iuid');
            $ir_area  = trim(I('post.ir_area'));
            $nickname = D('IbosUsers')->where(array('iuid'=>$iuid))->getfield('hu_nickname');
            //获取地址id
            $iaid = I('post.iaid');
            

            //遍历购物车所有商品ipid(如果表中存有会话数据，则反序列化)
            $shopcart   = M('IbosShopcart')
                        ->join('nulife_ibos_product on nulife_ibos_shopcart.ipid = nulife_ibos_product.ipid')
                        ->where(array('nulife_ibos_shopcart.iuid'=>$iuid))
                        ->where(array('nulife_ibos_shopcart.is_show'=>1))
                        ->where(array('product_type'=>0))
                        ->select();
            // p($shopcart);die;
            $address    = M('IbosAddress')
                        ->where(array('iaid'=>$iaid))
                        ->find();
            //生成唯一订单号
            $order_num = date('YmdHis').rand(10000, 99999);

            //计算总价
            foreach ($shopcart as $k => $v) {
                //总数量
                $total_num   += $v['product_number'];
                //总金额
                $total_price += $v['product_number'] * $v['product_price'];
                //总积分
                $total_point += $v['product_number'] * $v['product_point'];
                //订单备注
                $ir_desc .= $v['ip_name_zh'].'*'.$v['product_number'].'&nbsp;';
            }

            $order = array(
                //订单编号
                'ir_receiptnum' =>$order_num,
                //订单创建日期
                'ir_date'=>time(),
                //订单的状态(0待生成订单，1待支付订单，2已付款订单)
                'ir_status'=>0,
                //下单用户id
                'iuid'=>$iuid,
                //下单用户
                'hu_nickname'=>$nickname,
                //收货人
                'ia_name'=>$address['ia_name'],
                //收货人电话
                'ia_phone'=>$address['ia_phone'],
                //收货地址
                'ia_address'=>$address['ia_address'],
                //订单总商品数量
                'ir_productnum'=>$total_num,
                //订单总金额
                'ir_price'=>$total_price,
                //订单总积分
                'ir_point'=>$total_point,
                //订单备注
                'ir_desc'=>$ir_desc,
                //订单所属地区
                'ir_area'=>$ir_area
            );

            $receipt = M('IbosReceipt')->add($order);

            //订单详情记录商品信息
            if($receipt){
                foreach ($shopcart as $k => $v) {
                    $map = array(
                        'ir_receiptnum'     =>  $order_num,
                        'ipid'              =>  $v['ipid'],
                        'product_num'       =>  $v['product_number'],
                        'product_point'     =>  $v['ip_point']*$v['product_number'],
                        'product_price'     =>  $v['ip_price_rmb']*$v['product_number'],
                        'product_name'      =>  $v['ip_name_zh'],
                        'product_picture'   =>  $v['ip_picture_zh']
                    );
                    $receiptlist = M('IbosReceiptlist')->add($map);
                }
                //生成日志记录
                $content = '您的订单已生成,编号:'.$order_num.',包含:'.$ir_desc.',总价:'.$total_price.'Rmb,所需积分:'.$total_point;
                $log = array(
                        'from_iuid' =>$iuid,
                        'content'   =>$content,
                        'action'    =>0,
                        'type'      =>2,
                        'date'      =>date('Y-m-d H:i:s')          
                    );
                $addlog = M('IbosLog')->add($log);
                if($receiptlist){
                    //订单提交后清空购物车
                    $rst = M('IbosShopcart')
                        ->where(array('iuid'=>$iuid))
                        ->where(array('is_show'=>1))
                        ->where(array('product_type'=>0))
                        ->delete();
                    if($rst){
                        $data['status'] = 1;
                        $this->ajaxreturn($order);
                    }else{
                        $data['status'] = 0;
                        $this->ajaxreturn($order);
                    }
                }
            }
        }
    }

    /**
    * 获取用户订单列表 hk
    * @param ir_status 订单状态筛选
    * @param ir_area   订单地区 0hk 1cn
    **/
    public function hk_orderlist(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            $iuid      = I('post.iuid');
            $ir_status = I('post.ir_status');
            if(empty($iuid)){
                //订单审核用列表
                if($ir_status == "-1"){
                    $map = array(
                        'is_delete'=>0,
                        'ir_area'  =>0
                    );
                }else{
                    $map = array(
                        'is_delete'=>0,
                        'ir_status'=>$ir_status,
                        'ir_area'  =>0
                    );
                }
                $receiptlist = M('IbosReceipt')->where($map)->order('ir_date desc')->select();
            }else{
                //个人订单列表
                if($ir_status == "-1"){
                    $map = array(
                        'iuid'     =>$iuid,
                        'is_delete'=>0,
                        'ir_area'  =>0
                    );
                }else{
                    $map = array(
                        'iuid'=>$iuid,
                        'is_delete'=>0,
                        'ir_status'=>$ir_status,
                        'ir_area'  =>0
                    );
                }
                $receiptlist = M('IbosReceipt')->where($map)->order('ir_date desc')->select();
            }
            
            if($receiptlist){
                $this->ajaxreturn($receiptlist);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }


    /**
    * 获取用户订单列表 cn
    * @param ir_status 订单状态筛选
    * @param ir_area   订单地区 0hk 1cn
    **/
    public function cn_orderlist(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            $iuid      = I('post.iuid');
            $ir_status = I('post.ir_status');
            if(empty($iuid)){
                //订单审核用列表
                if($ir_status == "-1"){
                    $map = array(
                        'is_delete'=>0,
                        'ir_area'  =>1
                    );
                }else{
                    $map = array(
                        'is_delete'=>0,
                        'ir_status'=>$ir_status,
                        'ir_area'  =>1
                    );
                }
                $receiptlist = M('IbosReceipt')->where($map)->order('ir_date desc')->select();
            }else{
                //个人订单列表
                if($ir_status == "-1"){
                    $map = array(
                        'iuid'     =>$iuid,
                        'is_delete'=>0,
                        'ir_area'  =>1
                    );
                }else{
                    $map = array(
                        'iuid'=>$iuid,
                        'is_delete'=>0,
                        'ir_status'=>$ir_status,
                        'ir_area'  =>1
                    );
                }
                $receiptlist = M('IbosReceipt')->where($map)->order('ir_date desc')->select();
            }
            
            if($receiptlist){
                $this->ajaxreturn($receiptlist);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 获取订单详情
    **/
    public function orderdetail(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $ir_receiptnum = trim(I('post.ir_receiptnum'));
            //获取该用户的某个订单详细信息
            // $receiptlist = M('IbosReceiptlist')
            //              ->join('nulife_ibos_receipt on nulife_ibos_receiptlist.ir_receiptnum = nulife_ibos_receipt.ir_receiptnum')
            //              ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
            //              ->where(array('nulife_ibos_receiptlist.ir_receiptnum'=>$ir_receiptnum))
            //              ->find();
            $receipt = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
            $receipt['receiptlist'] = M('IbosReceiptlist')
                                    ->join('nulife_ibos_product on nulife_ibos_receiptlist.ipid = nulife_ibos_product.ipid')
                                    ->where(array('ir_receiptnum'=>$ir_receiptnum))
                                    ->select();
            $receipt['ir_date'] = date('Y-m-d H:i:s',$receipt['ir_date']);
            if($receipt){
                $this->ajaxreturn($receipt);
            }else{
                $status = 0;
                $this->ajaxreturn($receipt);
            }
        }
    }


    /**
    * 选择不发货
    * @param ir_receiptnum  订单号
    * @param is_delivery    是否选择送货 默认1送货，0不送
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    **/
    public function is_delivery(){
        $ir_receiptnum = trim(I('post.ir_receiptnum'));
        $is_delivery   = trim(I('post.is_delivery'))?0:1;
        $map = array(
                'is_delivery'  =>$is_delivery,
                'ir_status'    =>4
            );
        $receipt  = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
        $delivery = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
        $userinfo = M('IbosUsers')->where(array('iuid'=>$receipt['iuid']))->find();
        $endvp    = $userinfo['iu_vp']+$receipt['ir_price'];
        if($delivery){
            //将货品暂时转换成vp
            $temp = array(
                'iuid' =>$receipt['iuid'],
                'iu_vp'=>$endvp
            );
            $addvp = M('IbosUsers')->save($temp);
            if($temp){
                //生成日志记录
                $content = '您的订单,编号为:'.$ir_receiptnum.',已申请改单，临时兑换成VP:'.$receipt['ir_price'].',VP可以换取任意等价货品，但不会有奖励';
                $log = array(
                        'from_iuid' =>$receipt['iuid'],
                        'content'   =>$content,
                        'action'    =>0,
                        'type'      =>1,
                        'date'      =>date('Y-m-d H:i:s')          
                    );
                $addlog = M('IbosLog')->add($log);
            }
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 获取订单收货人地址
    **/
    public function orderaddress(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $ir_receiptnum = I('post.ir_receiptnum');
            //获取收货人信息
            $address = M('IbosReceipt')
                     ->where(array('ir_receiptnum'=>$ir_receiptnum))
                     ->find();         
            if($address){
                $this->ajaxreturn($address);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($address);
            }
        }
    }


    /**
    * 付款
    * @param paytype 1微信支付 2积分购买 3转账单据
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单号
    **/
    public function payment(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取支付方式，1/2/3
            $paytype = I('post.paytype');

            //获取订单号
            $ir_receiptnum = I('post.ir_receiptnum');

            //用户iuid
            $iuid = I('post.iuid');


            switch ($paytype) {
                //微信支付
                case 1:
                    //订单号
                    $ir_receiptnum = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(10000, 99999);
                    //用户iuid
                    $iuid = I('post.iuid');
                    //订单信息查询
                    $order = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();

                    $client = new \SoapClient("https://pay.hkipsec.com/webservice/GetQRCodeWebService.asmx?wsdl");

                    try{
                        $merAccNo       = "T0000603";
                        $orderId        = $ir_receiptnum;
                        $fee_type       = "CNY";
                        $amount         = "0.02";
                        $goodsInfo      = "Nulife Product";
                        $strMerchantUrl = "http://apps.nulifeshop.com/nulifeshop/index.php/Home/api/getResponse";
                        $cert           = "Yv1IqGRl5rfLOmUILIjdjrh1BlYjpXrFxo1So0BvcQXy5zB1aCiRDndGnZhfHHW7azyALb4ugNastIWlA6iCO0bkkLEos6DuU0yfbaGYiOKQmKw5dW5l9tIAxozZxpb9";
                        $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
                        $signMD5_lower  = strtolower(md5($signMD5));

                        $para = array(
                            'merAccNo'      => $merAccNo,
                            'orderId'       => $orderId,
                            'fee_type'      => $fee_type,
                            'amount'        => $amount,
                            'goodsInfo'     => $goodsInfo,
                            'strMerchantUrl'=> $strMerchantUrl,
                            'signMD5'       => $signMD5_lower
                        );

                        $result = $client->GetQRCodeXml($para);
                        //对象操作
                        $xmlstr = $result->GetQRCodeXmlResult;
                        //构造SimpleXMLEliement对象
                        $xml = new \SimpleXMLElement($xmlstr);
                        //微信支付链接
                        $code_url = (string)$xml->code_url;
                        //返回数据
                        $para['code_url'] = $code_url;
                        $this->ajaxreturn($para);
                        
                    }catch(SoapFault $f){
                        echo "Error Message:{$f->getMessage()}";
                    }
                    break;
                //积分购买
                case 2:
                    //获取用户积分
                    $user_point = M('IbosUsers')->where(array('iuid'=>$iuid))->getfield('iu_point');
                    //获取订单积分
                    $ir_point   = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_point');
                    //扣除所需购买积分
                    $last_point = $user_point-$ir_point;
                    $last_point = sprintf("%.2f",$last_point);

                    if($last_point>0){
                        //修改用户积分
                        $data = array(
                            'iuid'    =>$iuid,
                            'iu_point'=>$last_point
                        );
                        $insertpoint = M('IbosUsers')->save($data);
                        if($insertpoint){
                            //修改订单状态
                            $map = array(
                                'ir_paytype'=>2,
                                'ir_status'=>2
                            );
                            $change_orderstatus = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                            //日志记录
                            $content = '订单:'.$ir_receiptnum.'支付成功,扣除EP:'.$ir_point.',剩余EP:'.$last_point;
                            $log     = array(
                                        'from_iuid'=>$iuid,
                                        'content'=>$content,
                                        'action' =>3,
                                        'type'   =>1,
                                        'date'   =>date('Y-m-d H:i:s')
                                    );
                            $addlog  = M('IbosLog')->add($log);
                            if($change_orderstatus){
                                $data['status'] = 1;
                                $this->ajaxreturn($data);
                            }else{
                                $data['status'] = 0;
                                $this->ajaxreturn($data);
                            }
                        }
                    }else{
                        //积分不足，请充值
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }
                    break;
                //转账购买
                case 3:
                    //获取图片
                    $bankreceipt = I('post.ir_bankreceipt');

                    //银行单据账号
                    $banknumber  = I('post.banknumber');

                    //收据凭证解码
                    $ir_bankreceipt = substr(strstr($bankreceipt,','),1);
                    $url_bankreceipt = time().'_'.mt_rand().'.jpg';
                    $img = file_put_contents('./Public/idcard/'.$url_bankreceipt, base64_decode($ir_bankreceipt));


                    if($url_bankreceipt){
                        //添加图片并修改订单状态
                        $data = array(
                            'ir_status'         =>1,
                            'ir_paytype'        =>3,
                            'ir_bankreceipt'    =>C('WEB_URL').'/Public/idcard/'.$url_bankreceipt,
                            'ir_banknumber'     =>$banknumber
                        );
                        $change_orderstatus = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($data);
                        
                        if($change_orderstatus){
                            $data['status'] = 1;
                            $this->ajaxreturn($data);
                        }else{
                            $data['status'] = 0;
                            $this->ajaxreturn($data);
                        }
                    }else{
                        //没有上传凭证
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }                   
                    break;
            }
        }
    }

    /**
    * 二维码获取
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    **/
    public function getQrcode(){
        //订单号
        $ir_receiptnum  = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(10000, 99999);
        //用户iuid
        $iuid           = I('post.iuid');
        //订单信息查询
        $order          = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();

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

        $merchantcert = "GB30j0XP0jGZPVrJc6G69PCLsmPKNmDiISNvrXc0DB2c7uLLFX9ah1zRYHiXAnbn68rWiW2f4pSXxAoX0eePDCaq3Wx9OeP0Ao6YdPDJ546R813x2k76ilAU8a3m8Sq0";

        try{
            $merAccNo       = "E0001904";
            $orderId        = $ir_receiptnum;
            $fee_type       = "CNY";
            $amount         = $order['ir_price'];
            $goodsInfo      = "Nulife Product";
            $strMerchantUrl = "http://apps.nulifeshop.com/nulifeshop/index.php/Home/api/getResponse";
            $cert           = $merchantcert;
            $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
            $signMD5_lower  = strtolower(md5($signMD5));

            $para = array(
                'merAccNo'      => $merAccNo,
                'orderId'       => $orderId,
                'fee_type'      => $fee_type,
                'amount'        => $amount,
                'goodsInfo'     => $goodsInfo,
                'strMerchantUrl'=> $strMerchantUrl,
                'signMD5'       => $signMD5_lower
            );

            $result = $client->GetQRCodeXml($para);
            //对象操作
            $xmlstr = $result->GetQRCodeXmlResult;
            //构造SimpleXMLEliement对象
            $xml = new \SimpleXMLElement($xmlstr);
            //微信支付链接
            $code_url = (string)$xml->code_url;
            //返回数据
            $para['code_url'] = $code_url;
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
            $add  = M('IbosLog')->add($data);           
        }
        
        //查询订单信息
        $order = M('IbosReceipt')->where(array('ir_receiptnum'=>$data['billno']))->find();

        // //支付返回数据验证,是否支付成功验证
        if($data['succ'] == 'Y'){
            //签名验证
            //订单数量&订单金额
            if($data['amount'] == $order['ir_price']){
                //修改订单状态
                $map = array(
                    'ir_paytype'=>1,
                    'ir_status'=>2
                );
                $change_orderstatus = M('IbosReceipt')->where(array('ir_receiptnum'=>$data['billno']))->save($map);

                if($change_orderstatus){
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
    * 订单状态查询
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单编号
    **/
    public function checkreceipt(){
        $ir_receiptnum = I('post.ir_receiptnum');
        //订单状态查询
        $receipt_status = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_status');
        if($receipt_status == 2){
            //支付成功
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
    * 首购单下单
    **/
    public function registOrder(){
        $iuid = trim(I('post.iuid'));
        $ipid = trim(I('post.ipid'));
        //商品信息
        $product = M('IbosProduct')->where(array('ipid'=>$ipid))->find();
        //用户信息
        $userinfo= M('IbosUsers')->where(array('iuid'=>$iuid))->find();
        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);
        $order = array(
                //订单编号
                'ir_receiptnum' =>$order_num,
                //订单创建日期
                'ir_date'=>time(),
                //订单的状态(0待生成订单，1待支付订单，2已付款订单)
                'ir_status'=>0,
                //下单用户id
                'iuid'=>$iuid,
                //下单用户
                'hu_nickname'=>$userinfo['hu_username'],
                //收货人
                'ia_name'=>$userinfo['hu_username'],
                //收货人电话
                'ia_phone'=>$userinfo['hu_phone'],
                //收货地址
                'ia_address'=>$userinfo['hu_address'],
                //订单总商品数量
                'ir_productnum'=>1,
                //订单总金额
                'ir_price'=>$product['ip_price_rmb'],
                //订单总积分
                'ir_point'=>$product['ip_point'],
                //订单备注
                'ir_desc'=>'首购单',
                //订单类型（10首购 20升级 30重消 40零售）
                'ir_ordertype' => 10
            );

        $receipt = M('IbosReceipt')->add($order);
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
            $addReceiptlist = M('IbosReceiptlist')->add($map);
        }
         //生成日志记录
        $content = '您的首购订单已生成,编号:'.$order_num.',包含:'.$product['ip_name_zh'].',总价:'.$product['ip_price_rmb'].'Rmb,所需积分:'.$product['ip_point'];
        $log = array(
                'from_iuid' =>$iuid,
                'content'   =>$content,
                'action'    =>0,
                'type'      =>2,
                'date'      =>date('Y-m-d H:i:s')          
            );
        $addlog = M('IbosLog')->add($log);
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
    * 快钱支付
    **/
    public function kqPayment(){
        //$order_num = trim(I('post.ir_receiptnum'))?trim(I('post.ir_receiptnum')):date(YmdHis);
        $order_num = trim(I('post.ir_receiptnum'));
        //订单信息
        $order     = M('IbosReceipt')->where(array('ir_receiptnum'=>$order_num))->find();
        $kq_target          = "https://www.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
        $kq_merchantAcctId  = "1020997278101";      //*  商家用户编号     (30)
        $kq_inputCharset    = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
        $kq_pageUrl         = ""; //   直接跳转页面 (256)
        $kq_bgUrl           = "http://apps.nulifeshop.com/nulife/index.php/Api/Ibosapi/getKqReturn"; //   后台通知页面 (256)
        $kq_version         = "mobile1.0";  //*  版本  固定值 v2.0   (10)
        $kq_language        = "1";  //*  默认 1 ， 显示 汉语   (2)
        $kq_signType        = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
        $kq_payerName       = $order['hu_nickname']; //   英文或者中文字符   (32)
        $kq_payerContactType= "1";    //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
        $kq_payerContact    = "";     //   支付人联系方式    (50)
        $kq_orderId         = $order_num; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
        $kq_orderAmount     = $order['ir_price']*100; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
        $kq_orderTime       = date(YmdHis);  //*  交易时间  格式: 20110805110533
        $kq_productName     = "Nulife";//    商品名称英文或者中文字符串(256)
        $kq_productNum      = $order['ir_productnum'];   //    商品数量  (8)
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
        $kq_payerId         =date('YmdHis').rand(10000, 99999);       //付款人标识

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

    //快钱返回结果
    public function getKqReturn(){
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
            $add = M('IbosLog')->add($map);
            //做订单的处理
            $receipt = M('IbosReceipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->setField('ir_status',2);
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
    * 日志管理
    * @param type 0默认全部记录 1积分(0提现 1转出 2转入 3支付 4兑换 5所有记录 ) 2订单(生成，支付，审核) 3系统通知 4管理员操作(修改信息，审核信息)
    * @param action 
    **/
    public function log(){
        $iuid = trim(I('post.iuid'));
        $type = trim(I('post.type'));
        $date = date('Y-m-d',time()-60*24*60*60);
        if(empty($iuid)){
            if(!empty($type)){
                $map = array(
                    'type'=>$type,
                    'date'=>array('EGT',$date)
                );
            }else{
                $map = array(
                    'date'=>array('EGT',$date)
                );
            }
        }else{
            if(!empty($type)){
                $map = array(
                    'from_iuid'=>$iuid,
                    'type'=>$type,
                    'date'=>array('EGT',$date)
                );
            }else{
                $map = array(
                    'from_iuid'=>$iuid,
                    'date'=>array('EGT',$date)
                );
            }
        }
        
        $data = M('IbosLog')->where($map)->order('date desc')->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '暂无记录';
            $this->ajaxreturn($data);
        }
    }

    /**
    * 积分转账
    * @param iuid   转赠账户id
    * @param toiuid 被转赠账户id
    * @param point  转赠积分
    **/
    public function transfer(){
        $hu_nickname    = strtolower(trim(I('post.hu_nickname')));
        $tohu_nickname  = strtolower(trim(I('post.tohu_nickname')));
        $point          = trim(I('post.point'));

        $userinfo   = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
        $touserinfo = M('IbosUsers')->where(array('hu_nickname'=>$tohu_nickname))->find();

        if($tohu_nickname === $hu_nickname){
            //提现
            $iuid       = $userinfo['iuid'];
            $unpoint    = $point;
            if(!$userinfo){
                $data['status'] = -1;
                $data['msg']    = '账号有误';
                $this->ajaxreturn($data);
            }
            $iu_point   = $userinfo['iu_point'];
            $iu_unpoint = $userinfo['iu_unpoint'];
            //更新用户积分
            $point      = $iu_point-$unpoint;
            $newunpoint = $iu_unpoint+$unpoint;
            if($point<0){
                $data['status'] = 0;
                $data['msg']    = "提现积分超过余额";
                $this->ajaxreturn($data);
            }
            $map     = array(
                        'iuid'      =>$userinfo['iuid'],
                        'iu_point'  =>$point,
                        'iu_unpoint'=>$newunpoint
                    );
            $tmp     = array(
                        'iuid'              =>$userinfo['iuid'],
                        'getpoint'          =>$unpoint,
                        'unpoint'           =>$newunpoint,
                        'leftpoint'         =>$point,
                        'date'              =>date('Y-m-d H:i:s'),
                        'status'            =>0
                    );
            //更新用户积分
            $save   = M('IbosUsers')->save($map);
            $addtmp = M('IbosGetpoint')->add($tmp);
            if($save && $addtmp){
                $content = '提取积分:'.$unpoint.',剩余积分:'.$point;
                $log = array(
                        'from_iuid' =>$userinfo['iuid'],
                        'content'   =>$content,
                        'action'    =>0,
                        'date'      =>date('Y-m-d H:i:s'),
                        'type'      =>1
                    );
                $add = M('IbosLog')->add($log);
                if($add){
                    $data['status'] = 1;
                    $data['msg']    = '提现成功';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $data['msg']    = '提现失败';
                    $this->ajaxreturn($data);
                }
            }
        }

        
        if(!$userinfo || !$touserinfo){
            $data['status'] = -1;
            $data['msg']    = '账号有误';
            $this->ajaxreturn($data);
        }

        $leftpoint_user  = $userinfo['iu_point']-$point;
        if($leftpoint_user<0){
            $data['status'] = -2;
            $data['msg']    = '积分不足';
            $this->ajaxreturn($data);
        }
        $leftpoint_touser= $touserinfo['iu_point']+$point;
        $user = array(
                'iuid'      =>$userinfo['iuid'],
                'iu_point'  =>$leftpoint_user
            );
        $touser = array(
                'iuid'      =>$touserinfo['iuid'],
                'iu_point'  =>$leftpoint_touser
            );
        $save_userpoint  = M('IbosUsers')->save($user);
        $save_touserpoint= M('IbosUsers')->save($touser);
        if($save_userpoint && $save_touserpoint){
            //写入日志记录
            $user_content = '你给'.$touserinfo['hu_nickname'].'转赠积分'.$point; 
            $map = array(
                    'from_iuid'=>$userinfo['iuid'],
                    'to_iuid'=>$touserinfo['iuid'],
                    'content'=>$user_content,
                    'action' =>1,
                    'date'   =>date('Y-m-d H:i:s'),
                    'type'   =>1
                );
            $add_userlog = M('IbosLog')->add($map);
            $touser_content = $userinfo['hu_nickname'].'给你转赠积分'.$point; 
            $tmp = array(
                    'from_iuid'=>$touserinfo['iuid'],
                    'to_iuid'=>$userinfo['iuid'],
                    'content'=>$touser_content,
                    'action' =>2,
                    'date'   =>date('Y-m-d H:i:s'),
                    'type'   =>1
                );
            $add_userlog = M('IbosLog')->add($tmp);
            if($user_content && $add_userlog){
                $data['status'] = 1;
                $data['msg']    = '转账成功';
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['msg']    = '转账失败';
                $this->ajaxreturn($data);
            }
        }
    }


    /**
    * vp转账
    * @param type 0系统 1积分(0提现 1转出 2转入 3支付 4兑换 5所有记录 6vp ) 2订单(生成，支付，审核) 3管理员操作(修改信息，审核信息)
    **/
    public function vp_transfer(){
        $hu_nickname    = strtolower(trim(I('post.hu_nickname')));
        $tohu_nickname  = strtolower(trim(I('post.tohu_nickname')));
        $vp          = trim(I('post.vp'));

        $userinfo   = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
        $touserinfo = M('IbosUsers')->where(array('hu_nickname'=>$tohu_nickname))->find();

        if(!$userinfo || !$touserinfo){
            $data['status'] = -1;
            $data['msg']    = '账号有误';
            $this->ajaxreturn($data);
        }

        $leftvp_user  = $userinfo['iu_vp']-$vp;
        if($leftvp_user<0){
            $data['status'] = -2;
            $data['msg']    = 'VP不足';
            $this->ajaxreturn($data);
        }
        $leftvp_touser= $touserinfo['iu_vp']+$vp;
        $user = array(
                'iuid'      =>$userinfo['iuid'],
                'iu_vp'     =>$leftvp_user
            );
        $touser = array(
                'iuid'      =>$touserinfo['iuid'],
                'iu_vp'     =>$leftvp_touser
            );
        $save_userpoint  = M('IbosUsers')->save($user);
        $save_touserpoint= M('IbosUsers')->save($touser);
        if($save_userpoint && $save_touserpoint){
            //写入日志记录
            $user_content = '你给'.$touserinfo['hu_nickname'].'转赠VP'.$vp; 
            $map = array(
                    'from_iuid'=>$userinfo['iuid'],
                    'to_iuid'=>$touserinfo['iuid'],
                    'content'=>$user_content,
                    'action' =>6,
                    'date'   =>date('Y-m-d H:i:s'),
                    'type'   =>1
                );
            $add_userlog = M('IbosLog')->add($map);
            $touser_content = $userinfo['hu_nickname'].'给你转赠VP'.$vp; 
            $tmp = array(
                    'from_iuid'=>$touserinfo['iuid'],
                    'to_iuid'=>$userinfo['iuid'],
                    'content'=>$touser_content,
                    'action' =>6,
                    'date'   =>date('Y-m-d H:i:s'),
                    'type'   =>1
                );
            $add_userlog = M('IbosLog')->add($tmp);
            if($user_content && $add_userlog){
                $data['status'] = 1;
                $data['msg']    = '转账成功';
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['msg']    = '转账失败';
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 转账记录
    * @param type 0系统 1积分(0提现 1转出 2转入 3支付 4兑换 5所有记录 6vp ) 2订单(生成，支付，审核) 3管理员操作(修改信息，审核信息)
    **/
    public function transferList(){
        $iuid  = trim(I('post.iuid'));
        $page  = trim(I('post.page'));
        $action= trim(I('post.action'));

        switch ($action) {
            case '0':
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','0')
                ); 
                break;
            case '1':
                //积分的转入，转出
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','1,2')
                ); 
                break;
            case '2':
                //包含积分的提取，转入，转出
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','0,1,2')
                ); 
                break;
            case '3':
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','3')
                ); 
                break;
            case '4':
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','4')
                ); 
                break;        
            case '5':
                $map = array(
                    'from_iuid'=>$iuid,
                    'type'  =>1,
                    'action'=>array('in','0,1,2,3,4')
                ); 
                break;
            case '6':
                $map = array(
                    'from_iuid'  =>$iuid,
                    'type'  =>1,
                    'action'=>array('in','6')
                ); 
                break;
        }               
        $data['count']= M('IbosLog')->where($map)->count();
        $data['log']  = M('IbosLog')
                        ->join('left join nulife_ibos_users on nulife_ibos_log.to_iuid=nulife_ibos_users.iuid')
                        ->where($map)
                        ->order('date desc')
                        ->limit($page)
                        ->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 积分提现
    * @param iuid 用户id
    * @param unpoint 提现金额
    * @param account 银行账号
    * @param name 账户名称
    **/
    public function getPoint(){
        $iuid       = trim(I('post.iuid'));
        $unpoint    = trim(I('post.unpoint'));
        
        $userinfo   = M('IbosUsers')->where(array('iuid'=>$iuid))->find();
        if(!$userinfo){
            $data['status'] = -1;
            $data['msg']    = '账号有误';
            $this->ajaxreturn($data);
        }
        $iu_point   = $userinfo['iu_point'];
        $iu_unpoint = $userinfo['iu_unpoint'];
        //更新用户积分
        $point      = $iu_point-$unpoint;
        $newunpoint = $iu_unpoint+$unpoint;
        if($point<0){
            $data['status'] = 0;
            $data['msg']    = "提现积分超过余额";
            $this->ajaxreturn($data);
        }
        $map     = array(
                    'iuid'      =>$userinfo['iuid'],
                    'iu_point'  =>$point,
                    'iu_unpoint'=>$newunpoint
                );
        $tmp     = array(
                    'iuid'              =>$userinfo['iuid'],
                    'getpoint'          =>$unpoint,
                    'unpoint'           =>$newunpoint,
                    'leftpoint'         =>$point,
                    'date'              =>date('Y-m-d H:i:s'),
                    'status'            =>0
                );
        //更新用户积分
        $save   = M('IbosUsers')->save($map);
        $addtmp = M('IbosGetpoint')->add($tmp);
        if($save && $addtmp){
            $content = '提取积分:'.$unpoint.',剩余积分:'.$point;
            $log = array(
                    'from_iuid' =>$userinfo['iuid'],
                    'content'   =>$content,
                    'action'    =>0,
                    'date'      =>date('Y-m-d H:i:s'),
                    'type'      =>1
                );
            $add = M('IbosLog')->add($log);
            if($add){
                $data['status'] = 1;
                $data['msg']    = '提现成功';
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['msg']    = '提现失败';
                $this->ajaxreturn($data);
            }
        }
    }


    /**
    * 申请提现列表
    * @param status 0待审核 1待一审 2审核完毕 3审核不通过
    **/
    public function getPointList(){
        $iuid   = trim(I('post.iuid'));
        $status = trim(I('post.status'));
        if(!empty($iuid)){
            if($status == 4){
                $map = array(
                    'nulife_ibos_users.iuid'=>$iuid,
                );
            }else{
                $map = array(
                    'nulife_ibos_users.iuid'=>$iuid,
                    'status' => $status
                );
            } 
        }else{
            if($status ==4){
                $map = array();
            }else{
                $map = array(
                    'status'=>$status
                );
            }
        }
        $list = M('IbosGetpoint')
                ->join('nulife_ibos_users on nulife_ibos_getpoint.iuid = nulife_ibos_users.iuid')
                ->where($map)
                ->order('date desc')
                ->select();
        if($list){
            $this->ajaxreturn($list);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 提现记录详情
    * @param igid 提现申请记录id
    **/
    public function getPointDetail(){
        $igid = trim(I('igid'));
        $map  = array(
                'igid' =>$igid
            );
        $select = M('IbosGetpoint')
                ->join('nulife_ibos_users on nulife_ibos_getpoint.iuid = nulife_ibos_users.iuid')
                ->where($map)
                ->find();
        if($select){
            $this->ajaxreturn($select);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }


    /**
    * 提现审核
    * @param igid 提现记录id
    * @param status 0待审核 1一审 2二审 3审核完毕 4审核不通过
    **/
    public function check_point(){
        $igid   = trim(I('post.igid'));
        $status = trim(I('post.status'));
        switch ($status) {
            case '0':
                $status = 1;
                break;
            case '1':
                $status = 2;
                break;
            case '2':
                $status = 3;
                break;
        }
        $map  = array(
                'igid'  =>$igid,
                'status'=>$status
            );
        $save = M('IbosGetpoint')->save($map);
        if($save){
            $data['status'] = 1;
            $data['msg']    = '审核成功';
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '审核失败';
            $this->ajaxreturn($data);
        }
    }

    

    /**
    * 订单审核
    * @param ir_status 0待付款 1待审核 2已支付待发货 3已发货待收货 4已收货待评价 5已评价完成 6审核未通过
    * @param ir_receiptnum 订单编号
    **/
    public function check_bankreceipt(){
        $ir_receiptnum = trim(I('ir_receiptnum'));
        $ir_status     = trim(I('ir_status'));
        switch ($ir_status) {
            case '0':
                $status = 6;
                break;
            case '1':
                $status = 2;
                break;
            case '2':
                $status = 3;
                break;
            case '3':
                $status = 4;
                break;
        }
        $map = array(
                    'ir_status'    =>$status
                );
        $edit_receipt = M('IbosReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
        if($edit_receipt){
            $data['status'] = 1;
            $data['msg']    = '订单状态修改成功';
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']    = '订单状态修改失败';
            $this->ajaxreturn($data);
        }
    }


    /**
    * 获取广告图(实际上是链接推广位,编辑的时候记得加上链接推广位这选项)
    **/
    public function advertise(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $data = M('IbosShow')
                    ->where(array('typeer'=>0))
                    ->order('order_number desc')
                    ->limit(3)
                    ->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 首页四格推广位
    **/
    public function anypush(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $data = M('IbosShow')
                    ->where(array('is_show'=>1,'typeer'=>1))
                    ->order('order_number')
                    ->limit(4)
                    ->select();
            if($data){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }


    /**
    * 首页新闻
    **/
    public function news(){
        $data = M('IbosNews')
                ->where(array('news_top'=>1))
                ->where(array('is_show'=>1))
                ->order('addtime desc')->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 新闻详情
    **/
    public function newscontent(){
        $nid  = I('post.nid');
        $data = M('IbosNews')->where(array('nid'=>$nid))->find();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    /**
    * 获取新闻列表
    **/
    public function newslist(){
        $data = M('IbosNews')->where(array('is_show'=>1))->order('addtime desc')->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }


    /**
    * 推荐商品列表,大陆
    **/
    public function productpush(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //通过显示、推荐字段显示商品
            $map  = array(
                'is_push'=>1,
                'is_pull'=>1,
                'ip_cnsale'=>1,
                'ip_type'=>0
            );
            $data = M('IbosProduct')
                  ->where($map)
                  ->order('is_sort desc')
                  ->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 推荐商品列表,香港
    **/
    public function hk_productpush(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //通过显示、推荐字段显示商品
            $map  = array(
                'is_push'=>1,
                'is_pull'=>1,
                'ip_hksale'=>1,
                'ip_type'=>0
            );
            $data = M('IbosProduct')
                  ->where($map)
                  ->order('is_sort desc')
                  ->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

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
            $ia_address     = I('post.ia_address');
            $ia_road        = I('post.ia_road');
            $ia_phone       = I('post.ia_phone');
            $arr  = M('IbosAddress')
                  ->where(array('iuid'=>$iuid))
                  ->select();
            //判断是否存在地址。有则is_show为0，没有则为1
            if($arr){
                $tmp        = array(
                'iuid'      =>$iuid,
                'ia_name'   =>$ia_name,
                'ia_address'=>$ia_address,
                'ia_road'   =>$ia_road,
                'ia_phone'  =>$ia_phone,
                'is_address_show'   =>0
                );
            }else{
                $tmp        = array(
                'iuid'      =>$iuid,
                'ia_name'   =>$ia_name,
                'ia_address'=>$ia_address,
                'ia_road'   =>$ia_road,
                'ia_phone'  =>$ia_phone,
                'is_address_show'   =>1
                );
            }
            //添加
            $data       = M('IbosAddress')
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
            $data   = M('IbosAddress')
                    ->where(array('iuid'=>$iuid))
                    ->select(); 
            if($data){
                $this->ajaxreturn($data);
            }
            else{
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
            $data   = M('IbosAddress')
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
            $tmp    = M('IbosAddress')
                    ->where(array('iaid'=>$iaid))
                    ->setField('is_address_show',1);
            if($tmp){
                //1变为0
                $map    = array(
                        'iaid'=>array('NEQ',$iaid),
                        'iuid'=>$iuid
                    );
                $arr    = M('IbosAddress')
                        ->where($map)
                        ->setField('is_address_show',0);
            }
            
            if($tmp && $arr){
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
            $data   = M('IbosAddress')
                    ->where(array('iaid'=>$iaid))
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
            $ia_address     = I('post.ia_address');
            $ia_phone       = I('post.ia_phone');
            $ia_road        = I('post.ia_road');
            $tmp            = array(
                'ia_name'   =>$ia_name,
                'ia_address'=>$ia_address,
                'ia_phone'  =>$ia_phone,
                'ia_road'   =>$ia_road,
                'iaid'      =>$iaid
                );
            //修改
            $data = M('IbosAddress')
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
            $data = M('IbosAddress')
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

    /**
    * 评论列表
    **/
    public function commentlist(){
        //产品ipid
        $ipid = I('post.ipid');
        //评论信息
        $data = D('IbosComment')
                ->join('nulife_ibos_users on nulife_ibos_users.iuid = nulife_ibos_comment.iuid')
                ->where(array('ipid'=>$ipid))
                ->select();
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }



    /**
    * 会员申请成为V4
    * @param iuid 会员id
    * @param is_v 1申请，0取消申请
    **/
    public function is_v(){
        $iuid = I('post.iuid');
        $is_v = I('post.is_v');
        if($is_v == 1){
            //核验是否符合申请资格
            $iu_grade = M('IbosUsers')->where(array('iuid'=>$iuid))->getfield('iu_grade');
            if($iu_grade == 'v3'){
                $changeV = D('IbosUsers')->where(array('iuid'=>$iuid))->setField('is_v',$is_v);
                if($changeV){
                    $data['status'] = 1;
                    $data['msg']    = '申请成功';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $data['msg']    = '申请失败';
                    $this->ajaxreturn($data);
                }
            }else{
                $data['status'] = -1;
                $data['msg']    = '不符合申请资格';
                $this->ajaxreturn($data);
            }
        }else{
            $changeV = D('IbosUsers')->where(array('iuid'=>$iuid))->setField('is_v',$is_v);
            if($changeV){
                $data['status'] = 1;
                $data['msg']    = '撤销成功';
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['msg']    = '撤销失败';
                $this->ajaxreturn($data);
            }
        } 
    }

    /**
    * 会员成为V4
    **/
    public function bev4(){
        $iuid = trim(I('post.iuid'));
        $map  = array(
                'iuid'      =>$iuid,
                'iu_grade'  =>'v4'
            );
        $save = M('IbosUsers')->save($map);
        if($save){
            $set_status = M('IbosUsers')->where(array('iuid'=>$iuid))->setField('is_v',0);
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }


    /**
    * 会员申请成为优惠会员(接入ibos360系统)
    * @param iuid 会员id
    * @param is_ib 1申请，0取消申请
    **/
    public function is_ib(){
        $iuid  = trim(I('post.iuid'));
        $is_ib = trim(I('post.is_ib'));
        if($is_ib == 1){
            $iu_grade = M('IbosUsers')->where(array('iuid'=>$iuid))->getfield('iu_grade');
            $changeib = D('IbosUsers')->where(array('iuid'=>$iuid))->setField('is_ib',$is_ib);
            if($changeib){
                    $data['status'] = 1;
                    $data['msg']    = '申请成功';
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $data['msg']    = '申请失败';
                    $this->ajaxreturn($data);
                }
        }else{
            $changeib = D('IbosUsers')->where(array('iuid'=>$iuid))->setField('is_ib',$is_ib);
            if($changeib){
                $data['status'] = 1;
                $data['msg']    = '撤销成功';
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['msg']    = '撤销失败';
                $this->ajaxreturn($data);
            }
        } 
    }


    /**
    * 查看所有用户
    * @param category 1is_vip 2hu_type 3is_v 4 is_ib
    * @param is_vip 金卡合伙人 0非金卡合伙人 1金卡合伙人
    * @param hu_type 0普通用户 1前台 2顾问 3主管
    * @param is_v   申请金卡会员列表 0未申请 1申请
    * @param nick   搜索词
    **/
    public function category_users(){
       // if(!IS_POST){
       //      $data['status'] = 0;
       //      $this->ajaxreturn($data);
       //  }else{
            $category= trim(I('post.category'));
            $nick    = trim(I('post.nick'));
            $is_v    = trim(I('post.is_v'));
            $hu_type = trim(I('post.hu_type'));
            $is_vip  = trim(I('post.is_vip'));
            $is_ib   = trim(I('post.is_ib'));

            switch ($category) {
                case '1':
                    //金卡合伙人或非金卡合伙人
                    if(!empty($nick)){
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_hrac_users.is_vip'=>$is_vip))
                                 ->where(array('nulife_ibos_users.hu_nickname'=>array('like','%'.$nick.'%')))
                                 ->select();
                    }else{
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_hrac_users.is_vip'=>$is_vip))
                                 ->select();
                    }                   
                    break;
                case '2':
                    //普通用户或职员
                    if(!empty($nick)){
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_hrac_users.hu_type'=>$hu_type))
                                 ->where(array('nulife_ibos_users.hu_nickname'=>array('like','%'.$nick.'%')))
                                 ->select();
                    }else{
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_hrac_users.hu_type'=>$hu_type))
                                 ->select();
                    }
                    break;
                case '3':
                    //申请V4列表
                    if(!empty($nick)){
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_ibos_users.is_v'=>$is_v))
                                 ->where(array('nulife_ibos_users.hu_nickname'=>array('like','%'.$nick.'%')))
                                 ->select();
                    }else{
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_ibos_users.is_v'=>$is_v))
                                 ->select();
                    }
                    break;
                case '4':
                    //申请V4列表
                    if(!empty($nick)){
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_ibos_users.is_ib'=>$is_ib))
                                 ->where(array('nulife_ibos_users.hu_nickname'=>array('like','%'.$nick.'%')))
                                 ->select();
                    }else{
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_ibos_users.is_ib'=>$is_ib))
                                 ->select();
                    }
                    break;
                default:
                    //默认显示全部
                    if(!empty($nick)){
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->where(array('nulife_ibos_users.hu_nickname'=>array('like','%'.$nick.'%')))
                                 ->select();
                    }else{
                        $usreinfo    = M('HracUsers')
                                 ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                 ->select();
                    }
                    
                    break;
            }               
            if($usreinfo && !empty($usreinfo[0])){
                $this->ajaxreturn($usreinfo); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        // } 
    }


    /**
    * 高管添加用户
    * @param hu_nickname 昵称
    * @param hu_username_en 英文名
    * @param hu_username 中文名
    * @param hu_phone 手机号码
    * @param hu_type  Hrac员工身份 1,2,3
    * @param sid      Hrac员工所属门店 1,2,3
    * @param hgid     Hrac营养师等级 1,2
    **/
    public function add_users(){
        $data = I('post.');
        $data['iu_registertime'] = date('Y-m-d');
        $data['iu_password']     = md5('123456');
        //IbosUsers建立账户
        $ibosuser = D('IbosUsers')->add($data);
        $user     = D('IbosUsers')->where(array('hu_nickname'=>$data['hu_nickname']))->find();
        if($user){
            //HracUsers建立账户
            $map = array(
                'iuid'      =>$user['iuid'],
                'hu_type'   =>$data['hu_type'],
            );
            $hracuser = D('HracUsers')->add($map);
            if($hracuser && !empty($data['hu_type'])){
                //HracDocter建立职员信息
                $tmp = array(
                    'sid'       =>$data['sid'],
                    'hd_name'   =>$data['hu_nickname'],
                    'hd_phone'  =>$data['hu_phone'],
                    'hd_type'   =>$data['hu_type'],
                    'hgid'      =>$data['hgid']
                );
                $hracdocter = D('HracDocter')->add($tmp);
                if($hracdocter){
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }
            }else{
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    * 门店，营养师等级数据
    **/
    public function info_user(){
        //门店
        $HracShop   =D('HracShop')->select();
        //顾问等级
        $HracGrade  =D('HracGrade')->select();
        //
        $data = array(
            'shop'  =>$HracShop,
            'grade' =>$HracGrade
        );
        $this->ajaxreturn($data);
    }

    /**
    * 我的代理
    *
    **/
    public function viptree(){
        // $iuid     = I('post.huid');
        // $number   = I('post.number');
        $iuid     = 1;
        $number   = 3;
        $tree[0]  = M('IbosUsers')
                  ->where(array('iuid'=>$iuid))
                  ->select();

        $vip      = M('IbosUsers')
                  ->where(array('iu_upid'=>$iuid))
                  ->select();
        $treearr  = IbosSubtree($vip,$iuid,$lev=1);
        if($number==0){
            $tree[1] = $treearr;
        }else{
            foreach ($treearr as $key => $value) {
                if($value['lev']<$number){
                    $tree[1][] = $value;
                }
            }
        }
        foreach ($tree as $key => $value) {
            foreach ($value as $k => $v) {
                $viparr[] = $v; 
            }
        }

        foreach ($viparr as $key => $value) {
            $partner[$key]['key']    = $value['iuid'];
            $partner[$key]['parent'] = $value['iu_upid'];
            $partner[$key]['name']   = $value['hu_username'];
            $partner[$key]['source'] = $value['hu_photo'];
        }
        // p($vip);die;
        if($partner){
           $this->ajaxreturn($partner);   
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);               
        }
    }

































}
