<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
class HracapiController extends HomeBaseController{
     
    public function index(){

    }
    /**
    注册新用户
    **/    
    public function register(){
        if(!IS_POST){
            $msg['status'] = 0;
            $this->ajaxreturn($msg);
        }else{
            $data = I('post.');
            $data['iu_registertime'] = date('Y-m-d H:i:s');
            $data['iu_password']     = md5($data['hu_phone']);
            $ibos = D('IbosUsers')->where(array('hu_nickname'=>array('like','%'.'HRAC'.'%')))->order('iuid desc')->select();
            if($ibos){
                $name = substr($ibos[0]['hu_nickname'],4);
                $data['hu_nickname'] = 'HRAC'.($name+1);
            }else{
                $data['hu_nickname'] = 'HRAC10000001';
            }
            //IbosUsers建立账户
            $ibosuser = D('IbosUsers')->add($data);
            if($ibosuser){
                $iuid= D('IbosUsers')->where(array('hu_nickname'=>$data['hu_nickname']))->getfield('iuid');
                $map = array(
                    'iuid'      =>$iuid,
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
    登录 0账号(昵称)或密码错误
    **/
    public function login(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hu_nickname    = strtoupper(trim(I('post.hu_nickname')));
            $iu_password    = md5(trim(I('post.hu_pass')));
            $password       = trim(I('post.hu_pass'));
            
            //验证本服务器是否有该账号
            $data = D('IbosUsers')
                    ->where(array('hu_nickname'=>$hu_nickname))
                    ->find();
            if($data && $data['iu_password'] == $iu_password){
                $Eden = 1;
                $user= D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();
                if(!$user){
                    $tmp = array(
                        'iuid' => $data['iuid']    
                    );
                D('HracUsers')->add($tmp);
                $userarr = D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();                    
                }else{
                    $userarr = $user;
                }     
            }else{
                $Eden = 0;
                //记录最近一次的登录时间
                $date = date('Y-m-d H:i:s');
                $logintime = D('IbosUsers')
                            ->where(array('hu_nickname'=>$hu_nickname))
                            ->setField('iu_logintime',$date);
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
            $data['huid']   = $userarr['huid'];
            $data['is_vip'] = $userarr['is_vip'];
            $data['hu_type']= $userarr['hu_type'];
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
                        $user= D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();
                        if(!$user){
                            $tmp = array(
                                'iuid' => $data['iuid']    
                            );
                        D('HracUsers')->add($tmp);
                        $userarr = D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();                    
                        }else{
                            $userarr = $user;
                        }     
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
                    $uid = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->getfield('iuid');
                    $hrac=D('HracUsers')->add($tmp);
                    if($hrac){
                        $userarr = D('HracUsers')->where(array('iuid'=>$uid))->find();
                    }                    
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
                        $user= D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();
                        if(!$user){
                            $tmp = array(
                                'iuid' => $data['iuid']    
                            );
                        D('HracUsers')->add($tmp);
                        $userarr = D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();                    
                        }else{
                            $userarr = $user;
                        }     
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
                    $uid = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->getfield('iuid');
                    $hrac=D('HracUsers')->add($tmp);
                    if($hrac){
                        $userarr = D('HracUsers')->where(array('iuid'=>$uid))->find();
                    } 
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
                        $user= D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();
                        if(!$user){
                            $tmp = array(
                                'iuid' => $data['iuid']    
                            );
                        D('HracUsers')->add($tmp);
                        $userarr = D('HracUsers')->where(array('iuid'=>$data['iuid']))->find();                    
                        }else{
                            $userarr = $user;
                        }     
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
                    $uid = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->getfield('iuid');
                    $hrac=D('HracUsers')->add($tmp);
                    if($hrac){
                        $userarr = D('HracUsers')->where(array('iuid'=>$uid))->find();
                    } 
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
                        $data = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);  
                    }
                }else{
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==1&&$nulife==0&&$ibos==1){
                //copy至nulife
                $data = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                if($data['iu_nulife'] == 0){
                    $add = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->setField('iu_nulife',1);
                    if($add){
                        $data = D('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }else{
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
            }else if($Eden==1&&$nulife==1&&$ibos==0){
                //copy至ibos360
                $user = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->find();
                if($user['iu_ibos360'] == 0){
                    $add = M('IbosUsers')->where(array('hu_nickname'=>$hu_nickname))->setField('iu_ibos360',1);
                    if($add){
                        $data = D('IbosUsers')
                            ->where(array('hu_nickname'=>$hu_nickname))
                            ->find();
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }
                }else{
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }
                
            }else if($Eden==1&&$nulife==1&&$ibos==1){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取用户信息
    **/
    public function userinfo(){
        if(!IS_POST){
            $tmp['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // $huid = 39;
            $huid = I('post.huid');
            $data = M('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('huid'=>$huid))
                  ->find();
            if($data['hu_hpid']==0){
                $data['parent'] = 0;
            }else{
                $data['parent'] = M('HracUsers')
                                ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                                ->where(array('huid'=>$data['hu_hpid']))
                                ->getfield('hu_nickname');
            }
            $data['huid'] = I('post.huid');           
            // p($data);
            if($data){
                $this->ajaxreturn($data);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    用户信息的编辑
    **/
    public function editUserinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $para         = I('post.para');
            $paravalue    = I('post.paravalue');
            $iuid         = I('post.iuid');
            $user         = D('IbosUsers')
                          ->join('left join nulife_hrac_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                          ->where(array('nulife_ibos_users.iuid'=>$iuid))
                          ->find();
            $arrt['huid'] = $user['huid'];
            $data['iuid'] = $iuid;
            switch ($para) {
                case 'hu_nickname':
                    $users= D('IbosUsers')->where(array('hu_nickname'=>$paravalue))->getfield('hu_nickname');
                    if($users){
                        $status['status'] = 2;
                        $this->ajaxreturn($status); 
                    }else{
                        $data['hu_nickname'] = $paravalue;
                        $tmp['hu_nickname']  = $paravalue;
                    }
                    $edit = D('IbosUsers')->save($data);
                    if($edit){
                        if($user['hu_type']!=0){
                            $tmp['hdid'] = D('HracDocter')->where(array('hd_name'=>$user['hu_nickname']))->getfield('hdid');
                            $hracdocter  = D('HracDocter')->save($tmp);
                        }
                    }     
                    break;
                case 'hu_username':
                    $data['hu_username'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);     
                    break;
                case 'hu_phone':
                    $data['hu_phone'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_age':
                    $data['hu_age'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_sex':
                    $data['hu_sex'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_email':
                    $data['hu_email'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_point':
                    $data['iu_point'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_address':
                    $data['hu_address'] = $paravalue;
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_photo':
                    $img_body = substr(strstr($paravalue,','),1);
                    $hu_photo = time().'_'.mt_rand().'.jpg';
                    $img = file_put_contents('./Public/idcard/'.$hu_photo, base64_decode($img_body));
                    $data['hu_photo'] = C('WEB_URL').'/Public/idcard/'.$hu_photo;
                    if($user['hu_photo']){
                        unlink($user['hu_photo']);    
                    }
                    $edit = D('IbosUsers')->save($data);
                    break;
                case 'hu_comname':
                    $arrt['hu_comname'] = $paravalue;
                    $edit = D('HracUsers')->save($arrt);
                    break;
                case 'hu_comestablish':
                    $arrt['hu_comestablish'] = $paravalue;
                    $edit = D('HracUsers')->save($arrt);
                    break;
                case 'hu_comres':
                    $arrt['hu_comres'] = $paravalue;
                    $edit = D('HracUsers')->save($arrt);
                    break;
                case 'hu_comtype':
                    $arrt['hu_comtype'] = $paravalue;
                    $edit = D('HracUsers')->save($arrt);
                    break;
                case 'hu_comper':
                    $arrt['hu_comper'] = $paravalue;
                    $edit = D('HracUsers')->save($arrt);
                    break;
                case 'hu_comphoto':
                    $img_body1 = substr(strstr($paravalue,','),1);
                    $hu_comphoto = time().'_'.mt_rand().'.jpg';
                    $img1 = file_put_contents('./Public/idcard/'.$hu_comphoto, base64_decode($img_body1));
                    $arrt['hu_comphoto'] = C('WEB_URL').'/Public/idcard/'.$hu_comphoto;
                    if($user['hu_comphoto']){
                        unlink($user['hu_comphoto']);    
                    }
                    $edit = D('HracUsers')->save($arrt);
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
    }
    /**
    通过被预约人所属房间获取门店
    **/
    public function shop(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $id   = I('post.id');
            $hpid = I('post.hpid');
            $hhid = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                  ->where(array('hu_nickname'=>$id))->getfield('hhid');            
            $cid  = D('HracProject')->where(array('hpid'=>$hpid))->getfield('id');
            $aid  = D('HracCategory')->where(array('id'=>$cid))->getfield('aid');
            if($hhid==0){                
                $tmp   = D('HracShop')->where(array('aid'=>$aid))->select();
            }else{
                $sid   = D('HracHouse')->where(array('hhid'=>$hhid))->getfield('sid'); 
                $iaid  = D('HracShop')->where(array('sid'=>$sid))->getfield('aid');
                if($iaid!=$aid){
                    $data['status']=2;
                    $this->ajaxreturn($data);  
                }else{                   
                    $tmp[0]= D('HracShop')->where(array('sid'=>$sid))->find();
                } 
            }
            if($tmp){
                $this->ajaxreturn($tmp); 
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    get获取门店
    **/
     public function getshop(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{           
            $tmp = D('HracShop')->select();
            if($tmp){
                $this->ajaxreturn($tmp); 
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取项目
    **/
    public function project(){
        if(!IS_GETT){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $data = D('HracProject')->order('sort asc')->select();
            if($data){
                $this->ajaxreturn($data); 
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取项目、产品或者优惠券详情
    **/
    public function projectinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hpid  = I('post.hpid');
            $hpcid = I('post.hpcid');
            if($hpcid==0){
                $coup=D('HracCoupon')->where(array('hcid'=>$hpid))->find();
                $data['hpid']    = $coup['hcid'];
                $data['hp_name'] = $coup['hc_name'];
                $data['hp_pic']  = $coup['hc_pic4'];
                $data['hp_money']= $coup['hc_money'];
                $data['hp_point']= $coup['hc_point'];
                $data['hp_desc'] = $coup['hc_desc'];
                $data['hpcid']   = 0;
            }else{
                $data = D('HracProject')->where(array('hpid'=>$hpid))->find();
            }
            if($data){
                $this->ajaxreturn($data); 
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    通过项目、门店获取可预约日期(增加房间)
    **/
    public function bookdate(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $project = I('post.hpid');
            $sid     = I('post.sid');
            $nick    = I('post.id');
            $hgid    = I('post.hgid');
            $id      = D('HracUsers')
                     ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                     ->where(array('hu_nickname'=>$nick))
                     ->getfield('huid');
            $hhid    = D('HracUsers')->where(array('huid'=>$id))->getfield('hhid');
            //项目服务所需时间
            $p       = D('HracProject')->where(array('hpid'=>$project))->getfield('hp_time');
            //查询time时间表
            $timeid  = D('HracTime')->select();
            //今天
            $today   = date('Y-m-d',time());
            // echo $hhid;
            if($p==0.5){
                $number = 6;
            }else if($p==1){
                $number = 13;
            }else if($p==1.5){
                $number = 20;
            }else if($p==2){
                $number = 29;
            }else{
                $number = 59;
            }
            $strarr   = date_time($today,$number);
            //根据等级,门店得医护人员
            $docterid = D('HracDocter')->where(array('hgid'=>$hgid,'sid'=>$sid))->getfield('hdid',true);
            // p($docterid);
            //获取医护人员未预约时间
            foreach($docterid as $key =>$value){
                $booking[$value] = D('HracBook')->where(array('hdid'=>$value,'is_booking'=>0))->order('hb_starttime asc')->select();
            };
            // p($strarr);die;
            if($hhid!=0){
                //查询30天的订单
                foreach ($strarr as $key => $value) {
                    $receipt[$value] = D('HracReceipt')->where(array('hr_stardate'=>$value,'hhid'=>$hhid))->select();
                }
                // p($receipt);die;
                //按时间和房间重构排序、
                foreach ($receipt as $key => $value) {
                    if(empty($value)){
                        $allreceipt=array();
                    }else{
                        $allreceipt[$key][$hhid]=$value;
                    }
                }
                // p($allreceipt);die;
                //生成一天的房间所有时间
                $onedayroom[$hhid] = $timeid;
                // p($onedayroom);
            }else{
                //获取此门店的房间
                $room   = D('HracHouse')->where(array('sid'=>$sid))->getfield('hhid',true);
                //查询30天的订单
                foreach ($strarr as $key => $value) {
                    $receipt[$value] = D('HracReceipt')->where(array('hr_date'=>$value))->select();
                }
                // p($receipt);
                //按时间和房间重构排序、
                foreach ($receipt as $key => $value) {
                    if(empty($value)){
                        $allreceipt=array();
                    }else{
                        foreach ($value as $ke => $va) {
                            foreach ($room as $k => $v) {
                                if($va['hhid']==$v){
                                    $allreceipt[$key][$v][]=$va;
                                }
                            }    
                        }    
                    }
                }
                // p($allreceipt);
                //生成一天的房间所有时间
                foreach ($room as $key => $value) {
                    $onedayroom[$value] = $timeid;
                }
                // p($onedayroom);
            }
            //切割订单中被预约的时间
            foreach ($allreceipt as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $number = (strtotime($v['hr_endservice'])-strtotime($v['hr_statrservice']))/1800;
                        $bookroom[$key][$ke][$k] = cut_apart_time($v['hr_statrservice'], $v['hr_endservice'], $number, $format=true);     
                    }
                }
            }
            // p($bookroom);
            //重构取得所有被预约的房间起始时间
            foreach ($bookroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $ki => $vi) {
                        foreach ($vi as $k => $v) {
                            $allbookroom[$key][$ke][$ki][] = $v[0];
                        }
                    }
                }
            }
            // p($allbookroom);
            //将数组下降一维,得到已被预约的房间时间
            foreach ($allbookroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $ki => $vi) {
                        foreach ($vi as $k => $v) {
                            $roomstart[$key][$ke][] = $v;
                        }
                    }
                }
            }
            // p($roomstart);
            //所有医生未预约时间
            foreach ($strarr as $key => $value) {
                foreach ($booking as $ka => $va) {
                    foreach ($va as $k => $v) {
                        if($value==$v['hb_date']){
                            $allstr[$value][$v['hdid']][] = $v;
                        }
                    }
                }
            }
            // p($allstr);           
            //遍历拿到医护时间htid的所有值
            foreach($allstr as $key => $value){
                foreach($value as $ka => $va){
                    foreach ($va as $k => $v) {
                        $allhtid[$key][$ka][]=$v['htid'];
                    }
                }
            }
            // p($allhtid);
            //生成30天房间所有时间
            foreach ($strarr as $key => $value) {
                $allroom[$value] = $onedayroom;
            }
            // p($allroom);
            //所有房间时间下降一维
            foreach ($allroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $ki => $vi) {
                        $allroomstart[$key][$ke][] = $vi['time'];
                    }
                }
            }
            if(empty($allreceipt)){
                $unroom = $allroomstart;
                // p($unroom);
            }else{
                //取差集，未被预约的房间时间
                foreach ($allroomstart as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($roomstart as $ki => $vi) {
                            if($key==$ki){
                                foreach ($vi as $k => $v) {
                                    if($ke==$k){
                                        $unroom[$key][$ke] = array_diff($va,$v);
                                    }
                                }
                            }else{
                                $unroom[$key] = $value;
                            }
                        }
                    }
                }
                
            }
            // p($unroom);
            // p($unroom);
            //医护人员可以使用的房间时间
            foreach ($unroom as $key => $value) {
                foreach ($docterid as $ke => $va) {
                    $unbookroom[$key][$va]=$value;
                }
            }
            // p($unbookroom);
            //时间转为htid值
            foreach ($timeid as $kv => $vv) {
                foreach ($unbookroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                if($vv['time']==$v){
                                   $unroomhtid[$key][$ke][$ki][]=$vv['htid'];
                                }
                            }
                        }
                    }
                }
            }
            // p($unroomhtid);
            //比较取交集
            foreach ($allhtid as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($unroomhtid as $ki => $vi) {
                        if($key==$ki){
                            foreach ($vi as $kk => $vv) {
                                if($kk==$ke){
                                    foreach ($vv as $k => $v) {
                                        $unroomtime[$ki][$kk][$k] = array_intersect($v,$va);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // p($unroomtime);
            //去除相同的值
            foreach ($unroomtime as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $kk => $vv) {
                        $unlinkbook[$key][$ke][$kk] = array_unique($vv);
                    }
                }
            }
            //重新排序
            foreach ($unlinkbook as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $kk => $vv) {
                        foreach ($vv as $k => $v) {
                            $unlink[$key][$ke][$kk][] = $v;
                        }
                    }
                }
            }
            // p($unlink);die;.
            if($p==0.5){
                $start = $unlink;
                // p($start);
                // die;
            }else{
                //比较相邻的值是否为1
                foreach ($unlink as $key => $value) {
                    foreach ($value as $kk => $vv) {
                        foreach ($vv as $ke => $va) {
                            $count[$ke]= count($va);
                            // p();die;
                            for($i=0;$i<=$count[$ke];$i++){
                                if($va[$i+1]-$va[$i]==1){
                                    $diffnum[$key][$kk][$ke][$i]=$va[$i].','.$va[$i+1];
                                    if($va[$i+2]-$va[$i+1]==1){
                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+2];
                                        if($va[$i+3]-$va[$i+2]==1){
                                            $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+3];
                                            if($va[$i+4]-$va[$i+3]==1){
                                                $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+4];
                                                if($va[$i+5]-$va[$i+4]==1){
                                                    $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+5];
                                                    if($va[$i+6]-$va[$i+5]==1){
                                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+6];
                                                        if($va[$i+7]-$va[$i+6]==1){
                                                            $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+7];
                                                            if($va[$i+8]-$va[$i+7]==1){
                                                                $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+8];
                                                                if($va[$i+9]-$va[$i+8]==1){
                                                                    $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+9];
                                                                     if($va[$i+10]-$va[$i+9]==1){
                                                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+10];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $diffnum[$key][$kk][$ke][$i]=$va[$i];
                                    // array_splice($value, $i); 
                                }
                            }
                        }
                     } 
                }
                // p($diffnum);
                //通过项目得到字符串长度
                $j = $p*2-1;
                //去除字符串长度不够的
                foreach ($diffnum as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                if(strlen($v)>$j){
                                    $timearr[$key][$ke][$ki][$k] = $v;
                                }
                            }
                        }
                    }
                }
                // p($timearr);
                //将字符串分割成数组
                foreach ($timearr as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $extimearr[$key][$ke][$ki][] = explode(',', $v);
                            }
                        }
                    }
                }
                // p($extimearr);
                //去除不符合项目所需时间的
                foreach ($extimearr as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $count[$k]= count($v);
                                for($i=0;$i<=$count[$k];$i++){
                                    if($v[$i+$j]-$v[$i]==$j){
                                        $start[$key][$ke][$ki][] = $v[$i];
                                    }
                                }
                            }
                        }
                    }
                }
                // p($start);die;
            }
            //通过·可用时间得到日期
            foreach ($start as $key => $value) {
                if(!empty($value)){
                    $startdate[] = $key;
                }
            }
            // p($startdate);
            //重构成二维数组
            foreach ($startdate as $key => $value) {
                $startdatetime[$key]['name']=$value;
            }
            // p($startdatetime);
            if($startdatetime){
                $this->ajaxreturn($startdatetime);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);   
            }
        }      
    }
    /**
    通过日期、项目、门店获取可预约时间(增加房间)
    **/
    public function booktime(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $project = I('post.hpid');
            $sid     = I('post.sid');
            $id      = I('post.id');
            $hhid    = D('HracUsers')->where(array('huid'=>$id))->getfield('hhid');
            $hb_date = I('post.hb_date');
            $hgid    = I('post.hgid');
            //项目服务所需时间
            $p       = D('HracProject')->where(array('hpid'=>$project))->getfield('hp_time');
            //查询time时间表
            $timeid = D('HracTime')->select();
            //根据等级,门店得医护人员
            $docterid = D('HracDocter')->where(array('hgid'=>$hgid,'sid'=>$sid))->getfield('hdid',true);
            //获取医护人员未预约时间
            foreach($docterid as $key =>$value){
                $booking[$value] = D('HracBook')->where(array('hdid'=>$value,'is_booking'=>0))->order('hb_starttime asc')->select();
            };
            if($hhid!=0){
                //查询当天的订单
                $receipt[$hb_date] = D('HracReceipt')->where(array('hr_stardate'=>$hb_date,'hhid'=>$hhid))->select();
                // p($receipt);die;
                // 按时间和房间重构排序、
                foreach ($receipt as $key => $value) {
                    $allreceipt[$key][$hhid]=$value;
                }
                // p($allreceipt);die;
                //生成一天的房间所有时间
                $onedayroom[$hhid] = $timeid;
                // p($onedayroom);die;
            }else{             
                //获取此门店的房间
                $room   = D('HracHouse')->where(array('sid'=>$sid))->getfield('hhid',true);
                // p($room);die;
                //查询当天的订单
                $receipt[$hb_date] = D('HracReceipt')->where(array('hr_date'=>$hb_date))->select();          
                //按时间和房间重构排序、
                foreach ($receipt as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($room as $k => $v) {
                            if($va['hhid']==$v){
                                $allreceipt[$key][$v][]=$va;
                            }
                        }    
                    }
                }
                // p($allreceipt);
                //生成一天的房间所有时间
                foreach ($room as $key => $value) {
                    $onedayroom[$value] = $timeid;
                }
                // p($onedayroom);
            }
            //所有医生未预约时间
            foreach ($booking as $ka => $va) {
                foreach ($va as $k => $v) {
                    if($v['hb_date']==$hb_date){
                        $allstr[$hb_date][$v['hdid']][] = $v;
                    }
                }
            }
            // p($allstr);die;           
            //遍历拿到医护时间htid的所有值
            foreach($allstr as $key => $value){
                foreach($value as $ka => $va){
                    foreach ($va as $k => $v) {
                        $allhtid[$key][$ka][]=$v['htid'];
                    }
                }
            }
            // p($allhtid);
            //生成当天房间所有时间
            $allroom[$hb_date] = $onedayroom;
            // p($allroom);
            //所有房间时间下降一维
            foreach ($allroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $ki => $vi) {
                        $allroomstart[$key][$ke][] = $vi['time'];
                    }
                }
            }
            // p($allroomstart);
            if(!$bookroom){
                $unroom = $allroomstart;
            }else{
                //取差集，未被预约的房间时间
                //切割订单中被预约的时间
                foreach ($allreceipt as $key => $value) {
                    foreach ($value as $ke => $va) {
                        if(empty($va)){
                            $bookroom[$key][$ke]=array();
                        }else{
                            foreach ($va as $k => $v) {
                                $number = (strtotime($v['hr_endservice'])-strtotime($v['hr_statrservice']))/1800;
                                $bookroom[$key][$ke][$k] = cut_apart_time($v['hr_statrservice'], $v['hr_endservice'], $number, $format=true);
                            }
                        }         
                    }
                }
                // p($bookroom);
                //重构取得所有被预约的房间起始时间
                foreach ($bookroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $allbookroom[$key][$ke][$ki][] = $v[0];
                            }
                        }
                    }
                }
                // p($allbookroom);
                //将数组下降一维,得到已被预约的房间时间
                foreach ($allbookroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $roomstart[$key][$ke][] = $v;
                            }
                        }
                    }
                }
                // p($roomstart);
                foreach ($allroomstart as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($roomstart as $ki => $vi) {
                            if($key==$ki){
                                foreach ($vi as $k => $v) {
                                    if($ke==$k){
                                        $unroom[$key][$ke] = array_diff($va,$v);
                                    }
                                }
                            }else{
                                $unroom[$key] = $value;
                            }
                        }
                    }
                }
                
            }
            // p($unroom);
            //医护人员可以使用的房间时间
            foreach ($unroom as $key => $value) {
                foreach ($docterid as $ke => $va) {
                    $unbookroom[$key][$va]=$value;
                }
            }
            // p($unbookroom);
            //时间转为htid值
            foreach ($timeid as $kv => $vv) {
                foreach ($unbookroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                if($vv['time']==$v){
                                   $unroomhtid[$key][$ke][$ki][]=$vv['htid'];
                                }
                            }
                        }
                    }
                }
            }
            // p($unroomhtid);
            //比较取交集
            foreach ($allhtid as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($unroomhtid as $ki => $vi) {
                        if($key==$ki){
                            foreach ($vi as $kk => $vv) {
                                if($kk==$ke){
                                    foreach ($vv as $k => $v) {
                                        $unroomtime[$ki][$kk][$k] = array_intersect($v,$va);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // p($unroomtime);die;
             //去除相同的值
            foreach ($unroomtime as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $kk => $vv) {
                        $unlinkbook[$key][$ke][$kk] = array_unique($vv);
                    }
                }
            }
            //重新排序
            foreach ($unlinkbook as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $kk => $vv) {
                        foreach ($vv as $k => $v) {
                            $unlink[$key][$ke][$kk][] = $v;
                        }
                    }
                }
            }
            if($p==0.5){
                //添加起始时间和id
                foreach ($timeid as $key => $value) {
                    foreach ($unlink as $ke => $va) {
                        foreach ($va as $kk => $vv) {
                            foreach ($vv as $ki => $vi) {
                                foreach ($vi as $k => $v) {
                                    if($value['htid']==$v){
                                        $nurooing[$ke][$kk][$ki][$k]['time'] = $value['time'];
                                        $nurooing[$ke][$kk][$ki][$k]['id']   = $kk;
                                    }
                                }
                            }
                        }
                    }
                }
                // p($nurooing);
                //添加房间id
                foreach ($unroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $kk => $vv) {
                            foreach ($nurooing as $ki => $vi) {
                                foreach ($vi as $k => $v) {
                                    foreach ($v as $kj => $vj) {
                                        foreach ($vj as $kp => $vp) {
                                            if($vv==$vp['time']){
                                                $nurooingtime[$ki][$k][$kj][$kp]         = $vp;
                                                $nurooingtime[$ki][$k][$kj][$kp]['hhid'] = $kj;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // p($nurooingtime);
                // die;
            }else{
                //比较相邻的值是否为1
                foreach ($unlink as $key => $value) {
                    foreach ($value as $kk => $vv) {
                        foreach ($vv as $ke => $va) {
                            $count[$ke]= count($va);
                            for($i=0;$i<=$count[$ke];$i++){
                                if($va[$i+1]-$va[$i]==1){
                                    $diffnum[$key][$kk][$ke][$i]=$va[$i].','.$va[$i+1];
                                    if($va[$i+2]-$va[$i+1]==1){
                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+2];
                                        if($va[$i+3]-$va[$i+2]==1){
                                            $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+3];
                                            if($va[$i+4]-$va[$i+3]==1){
                                                $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+4];
                                                if($va[$i+5]-$va[$i+4]==1){
                                                    $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+5];
                                                    if($va[$i+6]-$va[$i+5]==1){
                                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+6];
                                                        if($va[$i+7]-$va[$i+6]==1){
                                                            $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+7];
                                                            if($va[$i+8]-$va[$i+7]==1){
                                                                $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+8];
                                                                if($va[$i+9]-$va[$i+8]==1){
                                                                    $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+9];
                                                                     if($va[$i+10]-$va[$i+9]==1){
                                                                        $diffnum[$key][$kk][$ke][$i]=$diffnum[$key][$kk][$ke][$i].','.$va[$i+10];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $diffnum[$key][$kk][$ke][$i]=$va[$i];
                                    // array_splice($value, $i); 
                                }
                            }
                        }
                     } 
                }
                // p($diffnum);
                //通过项目得到字符串长度
                $j = $p*2-1;
                //去除字符串长度不够的
                foreach ($diffnum as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                if(strlen($v)>$j){
                                    $timearr[$key][$ke][$ki][$k] = $v;
                                }
                            }
                        }
                    }
                }
                //将字符串分割成数组
                // p($timearr);
                foreach ($timearr as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $extimearr[$key][$ke][$ki][] = explode(',', $v);
                            }
                        }
                    }
                }
                // p($extimearr);
                //去除不符合项目所需时间的
                foreach ($extimearr as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $ki => $vi) {
                            foreach ($vi as $k => $v) {
                                $count[$k]= count($v);
                                for($i=0;$i<=$count[$k];$i++){
                                    if($v[$i+$j]-$v[$i]==$j){
                                        $start[$key][$ke][$ki][] = $v[$i];
                                    }
                                }
                            }
                        }
                    }
                }
                foreach ($timeid as $key => $value) {
                    foreach ($start as $ke => $va) {
                        foreach ($va as $kk => $vv) {
                            foreach ($vv as $ki => $vi) {
                                foreach ($vi as $k => $v) {
                                    if($value['htid']==$v){
                                        $nurooing[$ke][$kk][$ki][$k]['time'] = $value['time'];
                                        $nurooing[$ke][$kk][$ki][$k]['id']   = $kk;
                                    }
                                }
                            }
                        }
                    }
                }
                // p($start);die;
                // p($nurooing);
                foreach ($unroom as $key => $value) {
                    foreach ($value as $ke => $va) {
                        foreach ($va as $kk => $vv) {
                            foreach ($nurooing as $ki => $vi) {
                                foreach ($vi as $k => $v) {
                                    foreach ($v as $kj => $vj) {
                                        foreach ($vj as $kp => $vp) {
                                            if($vv==$vp['time']){
                                                $nurooingtime[$ki][$k][$kj][$kp]         = $vp;
                                                $nurooingtime[$ki][$k][$kj][$kp]['hhid'] = $kj;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // p($nurooingtime);die;
            }

            //通过时间为条件得出以id和time的数组
            foreach ($timeid as $key => $value) {
                foreach ($nurooingtime as $ke => $va) {
                    foreach ($va as $ki => $vi) {
                        foreach ($vi as $k => $v) {
                            foreach ($v as $kj => $vj) {
                                if($vj['time']==$value['time']){
                                    $fcas[$vj['time']]['id'].=$vj['id'].',';
                                    $fcas[$vj['time']]['hhid'].=$vj['hhid'].',';
                                    $fcas[$vj['time']]['time']=$vj['time'];
                                }
                            }
                        }
                    }
                }
            }
            // p($fcas);
            //重构数组
            foreach ($fcas as $key => $value) {
                $fcastime[]=$value;
            }
            // p($fcastime);
            //去除逗号
            foreach ($fcastime as $key => $value) {
                $starttime[$key]['id']   = substr($value['id'],0,-1);
                $starttime[$key]['hhid'] = substr($value['hhid'],0,-1);
                $starttime[$key]['time'] = $value['time'];
            }
            // p($starttime);
            if($starttime){
                $this->ajaxreturn($starttime);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);   
            }
        }      
    }
    /**
    获取用户优惠券信息
    **/
    public function couponnum(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid   = I('post.huid');
            // $huid   = 11;
            $hpid   = I('post.hpid');
            // $hpid   = 0;
            if($hpid==0){
                // p($tmparr); 
                $tmpstr =D('HracCoupon')
                        ->join('nulife_hrac_project on nulife_hrac_project.hpid  = nulife_hrac_coupon.hpid')
                        ->select();
                // p($tmpstr);
                //未使用
                $tmparr1= D('HracUsercoupon')
                        ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid  = nulife_hrac_usercoupon.hcid')
                        ->where(array('huid'=>$huid,'huc_vaild'=>0))
                        ->select();
                foreach ($tmparr1 as $key => $value) {                  
                    foreach ($tmpstr as $ke => $va) {
                        if($value['hpid']==$va['hpid']){
                            $tmp1[$key]=$value;
                            $tmp1[$key]['hc_pic']=$value['hc_pic1'];
                            $tmp1[$key]['hpname']=$va['hp_name'];
                            if(!empty($value['hp_abb'])){
                                $tmp1[$key]['number'] = $value['hp_abb'].'-'.$value['huc_number'];
                            }else{
                                $tmp1[$key]['number'] = $value['huc_number'];
                            }

                        }
                    }
                }
                //已使用
                $tmparr2= D('HracUsercoupon')
                        ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid  = nulife_hrac_usercoupon.hcid')
                        ->where(array('huid'=>$huid,'huc_vaild'=>1))
                        ->select();
                foreach ($tmparr2 as $key => $value) {                  
                    foreach ($tmpstr as $ke => $va) {
                        if($value['hpid']==$va['hpid']){
                            $tmp2[$key]=$value;
                            $tmp2[$key]['hc_type']=0;
                            $tmp2[$key]['hc_pic']=$value['hc_pic2'];
                            $tmp2[$key]['hpname']=$va['hp_name'];
                            if(!empty($value['hp_abb'])){
                                $tmp2[$key]['number'] = $value['hp_abb'].'-'.$value['huc_number'];
                            }else{
                                $tmp2[$key]['number'] = $value['huc_number'];
                            }

                        }
                    }
                }
                //已失效
                $tmparr3= D('HracUsercoupon')
                        ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid  = nulife_hrac_usercoupon.hcid')
                        ->where(array('huid'=>$huid,'huc_vaild'=>2))
                        ->select();
                foreach ($tmparr3 as $key => $value) {                  
                    foreach ($tmpstr as $ke => $va) {
                        if($value['hpid']==$va['hpid']){
                            $tmp3[$key]=$value;
                            $tmp3[$key]['hc_type']=0;
                            $tmp3[$key]['hc_pic']=$value['hc_pic3'];
                            $tmp3[$key]['hpname']=$va['hp_name'];
                            if(!empty($value['hp_abb'])){
                                $tmp3[$key]['number'] = $value['hp_abb'].'-'.$value['huc_number'];
                            }else{
                                $tmp3[$key]['number'] = $value['huc_number'];
                            }

                        }
                    }
                }
                $tmpstrarr[0] = $tmp1;
                $tmpstrarr[1] = $tmp2;
                $tmpstrarr[2] = $tmp3;
                foreach ($tmpstrarr as $key => $value) {
                    foreach ($value as $ke => $va) {
                        $tmp[] = $va;
                    }
                }

            }else{
                $tmparr = D('HracUsercoupon')
                        ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid  = nulife_hrac_usercoupon.hcid')
                        ->where(array('huid'=>$huid,'hpid'=>$hpid,'huc_vaild'=>0))
                        ->select();
                $hpname = D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');    
                foreach ($tmparr as $key => $value) {
                    $tmp[$key]=$value;
                    $tmp[$key]['hc_pic']=$value['hc_pic1'];
                    $tmp[$key]['hpname'] = $hpname;
                    $tmp[$key]['number'] = $value['hp_abb'].'-'.$value['huc_number'];
                }
                // p($tmp);
            }
            // p($tmp);
            if($tmp){
                $this->ajaxreturn($tmp); 
            }else{
                $status['status']=0;
                $this->ajaxreturn($status);
            }
        }  
    }
    /**
    获取优惠券详情
    **/
    public function couponinfo(){
        if(!IS_POST){
            $picstatus['status']=0;
            $this->ajaxreturn($picstatus);
        }else{
            $hucid  = I('post.hucid');
            // $hucid  = 5;
            $coupon = D('HracUsercoupon')->where(array('hucid'=>$hucid))->find();
            $hpid   = D('HracCoupon')->where(array('hcid'=>$coupon['hcid']))->getfield('hpid');
            if($coupon['huc_parent']==0){
                $coupon['hu_nickname'] = D('HracUsers')
                                       ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                       ->where(array('huid'=>$coupon['huid']))
                                       ->getfield('hu_nickname');
            }else{
                $coupon['hu_nickname'] = D('HracUsers')
                                       ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                                       ->where(array('huid'=>$coupon['huc_parent']))
                                       ->getfield('hu_nickname');
            }
            $coupon['hpid']   = $hpid;
            $coupon['hpname'] = D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');
            // p($coupon);die;
            if($coupon){
                $this->ajaxreturn($coupon);
            }else{
                $picstatus['status']=0;
                $this->ajaxreturn($picstatus);  
            }
        }
    }
    /**
    生成预约订单
    **/
        public function order(){
        if(!IS_POST){
            $picstatus['status']=0;
            $this->ajaxreturn($picstatus);
        }else{
            // 获取用户huid,门店sid,项目hpid,用户优惠券id
            $huid = I('post.huid');
            //用户券id
            $hucid= I('post.hucid');
            //券
            $vipid= D('HracUsercoupon')->where(array('hucid'=>$hucid))->find();
            //券类型
            $hcid = $vipid['hcid'];
            //拥有者
            $vipname= D('HracUsers')
                    ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                    ->where(array('huid'=>$vipid['huc_parent']))
                    ->getfield('hu_nickname');
            $coupon = D('HracCoupon')->where(array('hcid'=>$hcid))->find();
            //预约人账号
            $users= D('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('huid'=>$huid))
                  ->find();
            $name = $users['hu_nickname'];
            // p($users);die;
            $sid  = I('post.sid');
            $hpid = I('post.hpid');
            // 医护人员
            $var  = I('post.hdid');
            // 转成数组
            $arid = array_unique(explode(',',$var));
            // 拿到键值
            $key  = array_rand($arid,1);
            $hdid = $arid[$key];
            // 房间id
            $varr = I('post.hhid');
            // 转成数组
            $arrid= array_unique(explode(',',$varr));
            // 拿到键值
            $ki   = array_rand($arrid,1);
            $hhid = $arrid[$ki];   
            // 查询项目所需时间
            $hproject = D('HracProject')->where(array('hpid'=>$hpid))->find();
            $hp_time  = $hproject['hp_time'];
            $nick     = I('post.id');
            $user     = D('HracUsers')
                      ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                      ->where(array('hu_nickname'=>$nick))
                      ->find();
            if(!$user){
                $picstatus['status'] = 3;
                $this->ajaxreturn($picstatus);
            }else{
                //营养师昵称
                $hdname   = D('HracDocter')->where(array('hdid'=>$hdid))->getfield('hd_name');
                //就诊时间
                $hr_date  = I('post.hr_date');
                $hr_starttime = I('post.hr_starttime');
                $timeadd      = 3600*$hp_time;
                $time         = strtotime($hr_starttime)+$timeadd;
                $endtime      = date('H:i',$time);
                // 半小时为单位次数
                $number       = (strtotime($endtime)-strtotime($hr_starttime))/1800;
                // echo $number;
                //生成唯一订单号
                $order_num   = date('YmdHis').rand(10000, 99999);
                // 存放的内容
                $content     = array('hr_number'=>$order_num,'codetype'=>2,'hr_status'=>0);
                $pic         = qrcode_arr($content);
                // echo $pic;die;
                //通过分割方法获取二维数组时间段
                $timearr   =cut_apart_time($hr_starttime, $endtime, $number, $format=true);
                //添加hdid,hr_date为条件
                foreach($timearr as $key => $value){
                    $arr[$key]['hr_date']=$hr_date;
                    $arr[$key]['hr_starttime']=$value[0];
                    $arr[$key]['hr_endtime']=$value[1];
                    $arr[$key]['hdid']=$hdid;
                }
                //查询time表
                $altime = D('HracTime')->select();
                // p($arr);die;
                //重构数组得到要预约时间条件
                foreach ($arr as $key => $value) {
                    $map[]      = array(
                        'hb_date'=>$value['hr_date'],
                        'hb_starttime'=>$value['hr_starttime'],
                        'hb_endtime'=>$value['hr_endtime'],
                        'hdid'=>$value['hdid']
                    );                       
                }
                //通过条件得到要修改的时间
                foreach($map as $key => $value){
                    $allhrac[$key]= D('HracBook')
                                  ->where($value)
                                  ->find();
                }
                // p($allhrac);
                //去除hbid
                foreach ($allhrac as $key => $value) {
                    $hractime[$key]= array(
                        'hdid'         => $value['hdid'],
                        'sid'          => $value['sid'],
                        'hb_date'      => $value['hb_date'],
                        'hb_starttime' => $value['hb_starttime'],
                        'hb_endtime'   => $value['hb_endtime'],
                        'htid'         => $value['htid'],
                        'is_booking'   => $value['is_booking']
                    ); 
                }
                // p($hractime);
                //得到要进行对比的数组
                foreach ($arr as $key => $value) {
                    foreach ($altime as $k => $v) {
                        if($value['hr_starttime']==$v['time']){
                            $creteing[$key]= array(
                                'hdid'         => $value['hdid'],
                                'sid'          => $sid,
                                'hb_date'      => $value['hr_date'],
                                'hb_starttime' => $value['hr_starttime'],
                                'hb_endtime'   => $value['hr_endtime'],
                                'htid'         => $v['htid'],
                                'is_booking'   => 0
                            );    
                        }
                    }
                }
                // p($creteing);die;
                //去差集 判断是否已经被选过
                foreach ($hractime as $key => $value) {
                    if(!in_array($value,$creteing)){
                        $cret[$key]=$value;
                    }
                }
                // p($cret);die;
                //重构交集数组
                foreach ($cret as $key => $value) {
                    if($value){
                        $hracret = $value;
                    }
                }
                // p($hracret);die;
                if($hracret){
                    $picstatus['status'] = 2;
                    $this->ajaxreturn($picstatus);
                }else{
                    $order = array(
                        'hr_number'      =>$order_num,//订单编号
                        'hr_cretime'     =>date('Y-m-d H:i:s',time()),//订单创建日期
                        'vipname'        =>$vipname,//谁的券
                        'isvip'          =>$users['is_vip'],//预约人是否金卡
                        'name'           =>$name,//谁的订单
                        'hr_nickname'    =>$nick,//客户账号
                        'identity'       =>'DD',//客户当前身份
                        'isdr'           => 0,//客户当前身份
                        'sid'            =>$sid,//就诊门店
                        'hdid'           =>$hdid,//医护id
                        'hd_name'        =>$hdname,//医护昵称
                        'hpid'           =>$hpid,//项目
                        'hcid'           =>$hcid,//用了什么类型的券
                        'hucid'          =>$hucid,//用了那张券
                        'hhid'           =>$hhid,//房间
                        'hr_money'       =>$hproject['hp_money']-$coupon['hc_money'],//价格
                        'hr_date'        =>$hr_date,//就诊日期
                        'hr_starttime'   =>$hr_starttime,//开始时间
                        'hr_endtime'     =>$endtime,//结束时间
                        'hr_stardate'    =>$hr_date,//真正就诊日期
                        'hr_statrservice'=>$hr_starttime,//真正开
                        'hr_endservice'  =>$endtime,//真正结束时间始时间
                        'hr_codepic'     =>$pic,//二维码
                    );
                    //生成订单
                    $ceipt = D('HracReceipt')->add($order);
                    if($ceipt){
                        //1、服务消息
                        $hicid = D('HracInfoclass')->where(array('hic_type'=>2))->getfield('hicid');
                        //获取医护手机号
                        $phone = D('HracDocter')->where(array('hdid'=>$hdid))->getfield('hd_name');
                        //通过手机号获取医护的huid
                        $dhuid = D('HracUsers')
                               ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                               ->where(array('hu_nickname'=>$phone))
                               ->getfield('huid');
                        //服务名称
                        $hpname= D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');
                        //标题
                        $title = '成功预约服务';
                        //  预约人
                        $con1  = '您已成功为用户'.$user['hu_nickname'].'预约了'.$hpname.'服务,单号为'.$order_num;
                        //被预约人
                        $con2  = $name.'的金卡会员已成功帮您预约了'.$hpname.'服务,单号为'.$order_num; 
                        //医护人员
                        $con3  = $name.'的金卡会员已成功帮'.$user['hu_nickname'].'的用户预约了您的'.$hpname.'服务,单号为'.$order_num;
                        //前台
                        $con4  = $name.'的金卡会员已成功帮'.$user['hu_nickname'].'的用户预约了'.$hpname.'服务,单号为'.$order_num;
                        //时间
                        $time  =date('Y-m-d H:i:s',time());
                        //通过门店获取所有前台
                        $rece  = D('HracDocter')->where(array('sid'=>$sid,'hgid'=>0))->getfield('hd_name',true);
                        //查询所有类型值为1的huid
                        foreach ($rece as $key => $value) {
                            $arrrhuid[] = D('HracUsers')
                                        ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                                        ->where(array('hu_type'=>1,'hu_nickname'=>$value))
                                        ->getfield('huid');  
                        }
                        foreach ($arrrhuid as $key => $value) {
                            if(!empty($value)){
                                $rhuid[] = $value;  
                            }
                        }
                        if(!empty($rhuid[0])){
                            foreach ($rhuid as $key => $value) {
                                $recearr[$key]['hi_content']=$con4;
                                $recearr[$key]['hi_title']  =$title;
                                $recearr[$key]['hi_time']   =$time;
                                $recearr[$key]['hicid']     = 1;
                                $recearr[$key]['huid']      =$value;
                            }
                            // p($recearr);die;
                            $arrarr[0] = $recearr;
                        }
                        // p($recearr);die;
                        $infoarr =array(
                            array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$huid),
                            array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$user['huid']),
                            array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid)
                        );
                        $arrarr[0] = $recearr;
                        $arrarr[1] = $infoarr;
                        foreach($arrarr as $key=>$value){
                            foreach ($value as $ke => $va) {
                                $info[] = $va;
                            }
                        }
                        //2、优惠券消息
                        $hic    = D('HracInfoclass')->where(array('hic_type'=>3))->getfield('hicid');
                        $userco = D('HracUsercoupon')->where(array('hucid'=>$hucid))->find();
                        //标题
                        $title1 = '优惠券已使用';
                        $con    = '您已使用优惠券'.$userco['hp_abb'].'-'.$userco['huc_number'].'号成功预约'.$hpname.'服务';
                        $infoarr1 =array('hi_title'=>$title1,'hi_content'=>$con,'hi_time'=>$time,'hicid'=>$hic,'huid'=>$huid);
                        // p($timearr);
                        $data['is_booking']=1;
                        //修改时间段的is_booking值为1
                        foreach($map as $key => $value){
                            $hracbook = D('HracBook')->where($value)->save($data);
                        }
                        $tmparr = array(
                            'hucid'=>$hucid,
                            'huc_vaild'=>1
                        );
                        $save = D('HracUsercoupon')->save($tmparr);
                        $arrtmp =array(
                            'huid'=>$user['huid'],
                            'hdid'=>$hdid,
                            'hhid'=>$hhid
                        );
                        D('HracUsers')->save($arrtmp);   
                        foreach ($info as $key => $value) {
                            $addinfo = D('HracInformation')->add($value);
                        }
                        $addinfo1 = D('HracInformation')->add($infoarr1);
                        if($hracbook && $save){
                            $picstatus['status'] = 1;
                            $this->ajaxreturn($picstatus);     
                        }else{
                            $picstatus['status'] = 0;
                            $this->ajaxreturn($picstatus); 
                        }
                    }else{
                        $picstatus['status'] = 0;
                        $this->ajaxreturn($picstatus);
                    } 
                } 
            }
        }
    }

    /**
    用户订单
    **/
    public function orderlist(){
        if(!IS_POST){
            $picstatus['status']=0;
            $this->ajaxreturn($picstatus);
        }else{
            // 获取用户huid
            $huid = I('huid');
            $hr_status = I('hr_status');
            // $huid = 19;
            // $hr_status = 1;
            //通过huid获取
            $name = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                  ->where(array('huid'=>$huid))->getfield('hu_nickname');
            switch ($hr_status) {
                case 0:
                    $tmp  = D('HracReceipt')
                          ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                          ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                          ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                          ->where(array('name'=>$name))
                          ->order('hrid desc')
                          ->select();
                    break;
                case 1:
                    $tmp  = D('HracReceipt')
                      ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                      ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                      ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                      ->where(array('name'=>$name,'hr_status'=>0))
                      ->order('hrid desc')
                      ->select(); 
                    break;
                case 2:
                    $tmp  = D('HracReceipt')
                          ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                          ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                          ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                          ->where(array('name'=>$name,'hr_status'=>5))
                          ->order('hrid desc')
                          ->select();
                    break;
                case 3:
                    $tmp  = D('HracReceipt')
                          ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                          ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                          ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                          ->where(array('name'=>$name,'hr_status'=>6))
                          ->order('hrid desc')
                          ->select();
                    break;
            }
            // p($tmp);
            if($tmp){
                $this->ajaxreturn($tmp);
            }else{
                $status['status']=0;
                $this->ajaxreturn($status);
            }
        }   
    }
    /**
    医护人员任务订单
    **/
    public function doctertask(){
        if(!IS_POST){
            $picstatus['status']=0;
            $this->ajaxreturn($picstatus);
        }else{
            // 获取用户huid
            $huid      = I('post.huid');
            $hr_status = I('post.hr_status');
            // $huid      = 4;
            // $hr_status = 0;
            //获取用户信息 
            $user = D('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('huid'=>$huid))
                  ->find();
            // echo $user['hu_type'];
            //通过hu_nickname获取医护人员hdid
            $hdid = D('HracDocter')
                  ->where(array('hd_name'=>$user['hu_nickname']))
                  ->getfield('hdid');
            // echo $hdid;
            //今天
            $today     = date('Y-m-d',time());
            //明天
            $tomorrow  = date('Y-m-d',strtotime('+1 day'));
            //后天
            $will      = date('Y-m-d',strtotime('+2 day'));
            // echo $today;

            switch ($hr_status) {
                //今天（tim()）起30天
                case '0':
                    // echo $today;
                    for($i=0;$i<=28;$i++){
                        $date.=date("Y-m-d",strtotime("+1 day",strtotime($today))).',';
                        $today = date("Y-m-d",strtotime("+1 day",strtotime($today)));
                    }
                    $date   =date('Y-m-d',time()).','.$date;
                    $str    = chop($date,',');
                    $strarr = explode(',', $str);
                    foreach ($strarr as $key => $value) {
                        $mate[$key]['hr_stardate'] = $value;
                        $mate[$key]['name']    = $user['hu_nickname'];
                    }
                    // p($mate);
                    foreach ($mate as $key => $value) {
                        $tmparr[]   = D('HracReceipt')
                                    ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                                    ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                                    ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                                    ->where(array('hr_stardate'=>$value['hr_stardate']))
                                    ->where(array('nulife_hrac_receipt.hd_name'=>$value['name']))
                                    ->order('hrid desc')
                                    ->select(); 
                    }
                    foreach ($tmparr as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmp[] = $v;
                        }
                    }
                    // p($tmp);die;
                    break;
                //今天
                case '1':
                    $where = array(
                        'nulife_hrac_receipt.hd_name'=>$user['hu_nickname'],
                        'hr_stardate'             =>$today
                    );
                    $tmp    = D('HracReceipt')
                            ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                            ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                            ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                            ->where($where)
                            ->order('hrid desc')
                            ->select();
                    // p($tmp);die;
                    break;
                //明天
                case '2':
                    $where = array(
                        'nulife_hrac_receipt.hd_name'=>$user['hu_nickname'],
                        'hr_stardate'             =>$tomorrow
                    );
                    $tmp    = D('HracReceipt')
                            ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                            ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                            ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                            ->where($where)
                            ->order('hrid desc')
                            ->select();
                    break;
                //后天
                case '3':
                    $where = array(
                        'nulife_hrac_receipt.hd_name'=>$user['hu_nickname'],
                        'hr_stardate'             =>$will
                    );
                    $tmp    = D('HracReceipt')
                            ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                            ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                            ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                            ->where($where)
                            ->order('hrid desc')
                            ->select();
                    break;
            }
            $arrtmp    = array_sort($tmp,'hrid',$type='asc');
            // p($arrtmp);
            if($arrtmp){
                $this->ajaxreturn($arrtmp);
            }else{
                $status['status']=0;
                $this->ajaxreturn($status);
            }
        }   
    }
    /**
    订单详细信息
    **/
    public function orderinfo(){
        if(IS_POST){
            $hr_number = I('post.hr_number');
            // $hr_number = '2017111416435789570';
            $receipt   = D('HracReceipt')   
                       ->join('nulife_hrac_shop on nulife_hrac_receipt.sid = nulife_hrac_shop.sid')
                       ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                       ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                       ->join('left join nulife_hrac_house on nulife_hrac_receipt.hhid = nulife_hrac_house.hhid')
                       ->where(array('hr_number'=>$hr_number))
                       ->find();
            if($receipt){
                $this->ajaxreturn($receipt);
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
    新闻列表
    **/
    public function newslist(){
        if(IS_GET){
            $news = D('HracNews')->select();
            if($news){
                $this->ajaxreturn($news); 
           }else{
                $data['status']=0;
                $this->ajaxreturn($data);
           }  
        }
    }
    /**
    新闻详情
    **/
    public function newsinfo(){
        if(!IS_POST){
            $data['status']=0;
            $this->ajaxreturn($data);
        }else{
            $hnid = I('post.hnid');
            $news = D('HracNews')->where(array('hnid'=>$hnid))->find();
            if($news){
                $this->ajaxreturn($news); 
           }else{
                $data['status']=0;
                $this->ajaxreturn($data);
           } 
        }
    }
    /**
    广告图列表
    **/
    public function showlist(){
        if(IS_GET){
            $show = D('HracShow')->order('hsid desc')->limit(4)->select();
            if($show){
                $this->ajaxreturn($show); 
           }else{
                $data['status']=0;
                $this->ajaxreturn($data);
           }  
        }
    }
    /**
    修改订单状态
    **/
    public function orderstatus(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hr_number = I('post.hr_number');
            // $hr_number = '2017110111172842192';
            $huid      = I('post.huid');
            // $huid      = 4;
            $hr_status = I('post.hr_status');
            // $hr_status = 5;
            //获取订单信息
            $info      = D('HracReceipt')->where(array('hr_number'=>$hr_number))->find();
            $name      = $info['name'];
            $nhuid     = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                       ->where(array('hu_nickname'=>$name))
                       ->getfield('huid');
            $nick      = $info['hr_nickname'];
            $khuid     = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                       ->where(array('hu_nickname'=>$nick))
                       ->getfield('huid');
            // 获取分类id
            $hicid = D('HracInfoclass')->where(array('hic_type'=>2))->getfield('hicid');
            //生成新的二维码
            // 存放的内容
            $content     = array('hr_number'=>$hr_number,'codetype'=>2,'hr_status'=>$hr_status);
            $tmp['hr_codepic'] = qrcode_arr($content);
            //服务名称
            $hpname= D('HracProject')->where(array('hpid'=>$info['hpid']))->getfield('hp_name');
            //时间
            $time  =date('Y-m-d H:i:s',time());
            //获取hu_nickname
            $phone = D('HracDocter')->where(array('hdid'=>$info['hdid']))->getfield('hd_name');
            //获取原医护的
            $dhuid = D('HracUsers')
                   ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                   ->where(array('hu_nickname'=>$info['hd_name']))
                   ->getfield('huid');
            //判断状态
            switch ($hr_status) {
                case 1:
                    $tmp['hr_status']     = $hr_status;
                    //标题
                    $title = '服务时间已过';
                    //  预约人
                    $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'时间已过';
                    //被预约人
                    $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'时间已过'; 
                    //医护人员
                    $con3  = $name.'的金卡会员帮'.$nick.'用户预约您的'.$hpname.'服务订单'.$hr_number.'时间已过';
                    //前台
                    $con4  = $name.'的金卡会员帮ID'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'时间已过';
                    $infoarr =array(
                        array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                        array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid),
                        array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid),
                        array('hi_title'=>$title,'hi_content'=>$con4,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$huid)
                    );
                    break;                
                case 2:
                    $tmp['hr_status']     = $hr_status;
                    //标题
                    $title = '该服务被罚款';
                    //  预约人
                    $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'被罚款';
                    //被预约人
                    $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'被罚款'; 
                    //医护人员
                    $con3  = $name.'的金卡会员帮'.$nick.'用户预约您的'.$hpname.'服务订单'.$hr_number.'被罚款';
                    //前台
                    $con4  = '您已罚款'.$name.'的金卡会员帮ID'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number;
                    $infoarr =array(
                        array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                        array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid),
                        array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid),
                        array('hi_title'=>$title,'hi_content'=>$con4,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$huid)
                    );
                    $goin  = array(
                        'huid'   =>$khuid,  
                        'is_fine'=>1 
                    );
                    $save  = D('HracUsers')->save($goin);
                    break;                
                case 3:
                    $tmp['hr_arrivetime'] = date('Y-m-d H:i:s',time());
                    $tmp['hr_status']     = $hr_status;
                    //标题
                    $title = '服务通过审核';
                    //  预约人
                    $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'已通过审核';
                    //被预约人
                    $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'已通过审核'; 
                    //医护人员
                    $con3  = $name.'的金卡会员帮'.$nick.'用户预约您的'.$hpname.'服务订单'.$hr_number.'已通过审核';
                    //前台
                    $con4  = '您已审核并通过'.$name.'的金卡会员帮ID'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number;
                    $infoarr =array(
                        array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                        array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid),
                        array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid),
                        array('hi_title'=>$title,'hi_content'=>$con4,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$huid)
                    );
                    break;
                case 4:
                    if($dhuid==$huid){
                        $tmp['hr_statrservice'] = restructure_time(date('H:i',time()-1800));
                        $tmp['hr_status']       = $hr_status;
                        $tmp['hr_stardate']     = date('Y-m-d',time());
                    }else{
                        $phone  = D('HracUsers')
                                ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                                ->where(array('huid'=>$huid))
                                ->getfield('hu_nickname');
                        $tmp['hr_statrservice'] = restructure_time(date('H:i',time()));
                        $tmp['hr_status']       = $hr_status;
                        $tmp['hr_stardate']     = date('Y-m-d',time());
                        $tmp['hd_name']         = $phone;
                        //标题
                        $title1 = '其他顾问在服务通知';
                        $con1   = '营养师用户'.$phone.'正在帮您进行订单'.$hr_number.'的'.$hpname.'服务';
                        $infoarr=array(
                            array('hi_title'=>$title1,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid)
                        );   
                    }
                    break;
                case 5:
                    $tmp['hr_endservice'] = restructure_time(date('H:i',time()));
                    $tmp['hr_status']     = $hr_status;
                    $tmp['is_product']    = I('post.is_product');                    
                    $title = '服务已完成';//标题                    
                    $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'已完成';//  预约人                    
                    $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'已完成'; //被预约人     
                    $con3  = $name.'的金卡会员帮ID'.$nick.'用户预约您的'.$hpname.'服务订单'.$hr_number.'已完成';//医护人员
                    $infoarr =array(
                        array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                        array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid),
                        array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid)
                    );
                    $stuser = D('HracUsers')->where(array('huid'=>$khuid))->find();
                    if($stuser['hhid']==0){
                        $goin  = array(
                            'huid'=>$khuid,  
                            'hhid'=>$info['hhid'] 
                        );
                        $save    = D('HracUsers')->save($goin);
                    }
                    if($info['hr_date']!=$info['hr_stardate']||$info['hr_starttime']!=$info['hr_statrservice'] || $info['hr_endtime']!=$tmp['hr_endservice']){
                        $number1    = (strtotime($info['hr_endtime'])-strtotime($info['hr_starttime']))/1800;
                        $number2    = (strtotime($tmp['hr_endservice'])-strtotime($info['hr_statrservice']))/1800;
                        //添加hdid,hr_date为条件
                        if($number2==0){
                            $data['status'] = 2;
                            $this->ajaxreturn($data);
                        }else{
                            $timearr1   = cut_apart_time($info['hr_starttime'],$info['hr_endtime'], $number1, $format=true);
                            $timearr2   = cut_apart_time($info['hr_statrservice'], $tmp['hr_endservice'], $number2, $format=true);
                            foreach($timearr1 as $key => $value){
                                $arr1[$key]['hr_date']=$info['hr_date'];
                                $arr1[$key]['hr_starttime']=$value[0];
                                $arr1[$key]['hr_endtime']=$value[1];
                                $arr1[$key]['hdid']=$info['hdid'];
                            }
                            foreach($timearr2 as $key => $value){
                                $arr2[$key]['hr_date']=$info['hr_stardate'];
                                $arr2[$key]['hr_starttime']=$value[0];
                                $arr2[$key]['hr_endtime']=$value[1];
                                $arr2[$key]['hdid']=$inhdid;
                            }
                            $data1['is_booking']=0;
                            $data2['is_booking']=1;
                            //修改时间段的is_booking值为0
                            foreach ($arr1 as $key => $value) {
                                $map1      = array(
                                    'hb_date'=>$value['hr_date'],
                                    'hb_starttime'=>$value['hr_starttime'],
                                    'hb_endtime'=>$value['hr_endtime'],
                                    'hdid'=>$value['hdid']
                                );
                                D('HracBook')->where($map1)->save($data1);
                            }
                            foreach ($arr2 as $key => $value) {
                                $map2      = array(
                                    'hb_date'=>$value['hr_date'],
                                    'hb_starttime'=>$value['hr_starttime'],
                                    'hb_endtime'=>$value['hr_endtime'],
                                    'hdid'=>$value['hdid']
                                );
                                D('HracBook')->where($map2)->save($data2);
                            }
                        }
                        
                    }
                    $content = '用券奖励';
                    $type    = 2;
                    $hucid   = $info['hucid'];
                    $hbitime = date('Y-m-d H:i:s',time());
                    $coupon  = D('HracUsercoupon')->where(array('hucid'=>$info['hucid']))->find();
                    $users   = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                             ->where(array('huid'=>$coupon['huc_parent']))->find();
                    if($coupon['hc_type']==1){
                        $is_num = 1;
                    }else{
                        $is_num = 0;
                    }
                    $mape    = array('name'=>$users['hu_nickname'],'hbi_type'=>2,'hbi_num'=>$users['hu_num'],'is_num'=>0);
                    $altbill = D('HracBills')->where($mape)->select();
                    if($altbill){
                        $count = count($altbill);               
                    }else{
                        $count   = 0;
                    }
                    if($count<6){
                        $bill = array(
                            'name'        => $users['hu_nickname'],
                            'hbi_sum'     => 500,
                            'hbi_content' => '用券奖励',
                            'hbi_type'    => 2,
                            'symbol'      => '+',
                            'hucid'       => $hucid,
                            'is_num'      => $is_num,
                            'hcid'        => $coupon['hcid'],
                            'hbi_num'     => $users['hu_num'],
                            'hbi_time'    => $hbitime
                        );
                        D('HracBills')->add($bill);
                    }else if($count==6){
                        if($start>=$nowtime){
                            $bill = array(
                                'name'        => $users['hu_nickname'],
                                'hbi_sum'     => 1000,
                                'hbi_content' => '用券奖励',
                                'hbi_type'    => 2,
                                'symbol'      => '+',
                                'hucid'       => $hucid,
                                'is_num'      => $is_num,
                                'hcid'        => $coupon['hcid'],
                                'hbi_num'     => $users['hu_num'],
                                'hbi_time'    => $hbitime
                            );
                            $where= array(
                                'name'        => $users['hu_nickname'],
                                'hbi_sum'     => 3000,
                                'hbi_content' => '用券额外奖励',
                                'hbi_type'    => 2,
                                'symbol'      => '+',
                                'hbi_time'    => $hbitime
                            );
                        }else{
                            $bill = array(
                                'name'        => $users['hu_nickname'],
                                'hbi_sum'     => 500,
                                'hbi_content' => '用券奖励',
                                'hbi_type'    => 2,
                                'symbol'      => '+',
                                'hucid'       => $hucid,
                                'is_num'      => $is_num,
                                'hcid'        => $coupon['hcid'],
                                'hbi_num'     => $users['hu_num'],
                                'hbi_time'    => $hbitime
                            );
                        }
                        D('HracBills')->add($bill);
                    }else{
                        if($start>=$nowtime){
                            $bill = array(
                                'name'        => $users['hu_nickname'],
                                'hbi_sum'     => 1000,
                                'hbi_content' => '用券奖励',
                                'hbi_type'    => 2,
                                'symbol'      => '+',
                                'hucid'       => $hucid,
                                'is_num'      => $is_num,
                                'hcid'        => $coupon['hcid'],
                                'hbi_num'     => $users['hu_num'],
                                'hbi_time'    => $hbitime
                            );
                        }else{
                            $bill = array(
                                'name'        => $users['hu_nickname'],
                                'hbi_sum'     => 500,
                                'hbi_content' => '用券奖励',
                                'hbi_type'    => 2,
                                'symbol'      => '+',
                                'hucid'       => $hucid,
                                'is_num'      => $is_num,
                                'hcid'        => $coupon['hcid'],
                                'hbi_num'     => $users['hu_num'],
                                'hbi_time'    => $hbitime
                            );
                        }
                        D('HracBills')->add($bill);
                    }
                    if($where){
                        D('HracBills')->add($where);
                        $balance['iu_point']   = $users['iu_point']+$bill['hbi_sum']+$where['hbi_sum'];
                        $balance['hu_nickname']= $users['hu_nickname'];
                    }else{
                        $balance['iu_point']   = $users['iu_point']+$bill['hbi_sum'];
                        $balance['hu_nickname']= $users['hu_nickname'];
                    }
                    D('IbosUsers')->save($balance);
                    break;
                case 6:
                    $tmp['hr_status']     = $hr_status;
                    //标题
                    $title = '服务已失效';
                    //  预约人
                    $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'已失效';
                    //被预约人
                    $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'已失效'; 
                    $infoarr =array(
                        array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                        array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid)
                    );
                    break;
            }
            // p($tmp);
            $receipt   = D('HracReceipt')->where(array('hr_number'=>$hr_number))->save($tmp);
            if($receipt){
                unlink($info['hr_codepic']);
                foreach ($infoarr as $key => $value) {
                    $addinfo = D('HracInformation')->add($value);
                }
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    删除订单
    **/
    public function orderdelete(){
        if(!IS_POST){
            $tmp['status'] = 0;
            $this->ajaxreturn($tmp);
        }else{
            $hr_number = I('post.hr_number');
            // $hr_number = '2017110616314412517';
            // $hic_type  = 2;
            $receipt   = D('HracReceipt')->where(array('hr_number'=>$hr_number))->find();
            $name      = $receipt['name'];
            $nhuid     = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                       ->where(array('hu_nickname'=>$name))
                       ->getfield('huid');
            $nick      = $receipt['hr_nickname'];
            $khuid     = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                       ->where(array('hu_nickname'=>$nick))
                       ->getfield('huid');
            // p($khuid);die;
            $starttime = $receipt['hr_starttime'];
            $endtime   = $receipt['hr_endtime'];
            $hdid      = D('HracDocter')->where(array('hd_name'=>$receipt['hd_name']))->getfield('hdid');
            $hr_date   = $receipt['hr_date'];
            $hpid      = $receipt['hpid'];
            // echo $starttime;
            // echo $endtime;
            $number    = (strtotime($endtime)-strtotime($starttime))/1800;
            // // echo $number;die;
            $ceipt   = D('HracReceipt')->where(array('hr_number'=>$hr_number,'hr_status'=>0))->delete();
            $time  =date('Y-m-d H:i:s',time());
            //获取门店
            $sid   = D('HracDocter')->where(array('hdid'=>$hdid))->getfield('sid');
            // echo $sid;die; 
            //获取医护hu_nickname
            $phone = D('HracDocter')->where(array('hdid'=>$hdid))->getfield('hd_name');
            //通过hu_nickname获取医护的huid
            //通过手机号获取医护的huid
            $dhuid = D('HracUsers')
                   ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                   ->where(array('hu_nickname'=>$receipt['hd_name']))
                   ->getfield('huid');
            //服务名称
            $hpname= D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');
            // 获取分类id
            $hicid = D('HracInfoclass')->where(array('hic_type'=>2))->getfield('hicid');
            //标题
            $title = '成功取消服务';
            //  预约人
            $con1  = '您为'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'已取消';
            //被预约人
            $con2  = $name.'的金卡会员帮您预约的'.$hpname.'服务订单'.$hr_number.'已取消'; 
            //医护人员
            $con3  = $name.'的金卡会员帮'.$nick.'用户预约您的'.$hpname.'服务订单'.$hr_number.'已取消';
            //前台
            $con4  = $name.'的金卡会员帮'.$nick.'用户预约的'.$hpname.'服务订单'.$hr_number.'已取消';
            //通过门店获取所有前台
            $rece  = D('HracDocter')->where(array('sid'=>$sid,'hgid'=>0))->getfield('hd_name',true);
            //查询所有类型值为1的huid
            // p($rece);die;
            foreach ($rece as $key => $value) {
                $arrrhuid[] = D('HracUsers')
                            ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                            ->where(array('hu_type'=>1,'hu_nickname'=>$value))
                            ->getfield('huid');  
            }
            foreach ($arrrhuid as $key => $value) {
                if(!empty($value)){
                    $rhuid[] = $value;  
                }
            }
            if(!empty($rhuid[0])){
                foreach ($rhuid as $key => $value) {
                    $recearr[$key]['hi_content']=$con4;
                    $recearr[$key]['hi_title']  =$title;
                    $recearr[$key]['hi_time']   =$time;
                    $recearr[$key]['hicid']     = 1;
                    $recearr[$key]['huid']      =$value;
                }
                // p($recearr);die;
                $arr[0] = $recearr;
            }
            // p($recearr);die;
            $infoarr =array(
                array('hi_title'=>$title,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$nhuid),
                array('hi_title'=>$title,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$khuid),
                array('hi_title'=>$title,'hi_content'=>$con3,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$dhuid)
            );
            $arr[1] = $infoarr;
            foreach($arr as $key=>$value){
                foreach ($value as $ke => $va) {
                    $info[] = $va;
                }
            }
            if($ceipt){
                foreach ($info as $key => $value) {
                    $addinfo = D('HracInformation')->add($value);
                }
                unlink($receipt['hr_codepic']);
                //通过分割方法获取二维数组时间段
                $timearr   =cut_apart_time($starttime, $endtime, $number, $format=true);
                // p($timearr);
                //添加hdid,hr_date为条件
                foreach($timearr as $key => $value){
                    $arr[$key]['hr_date']=$hr_date;
                    $arr[$key]['hr_starttime']=$value[0];
                    $arr[$key]['hr_endtime']=$value[1];
                    $arr[$key]['hdid']=$hdid;
                }
                // p($arr);die;
                $data['is_booking']=0;
                //修改时间段的is_booking值为0
                foreach ($arr as $key => $value) {
                    $map      = array(
                        'hb_date'=>$value['hr_date'],
                        'hb_starttime'=>$value['hr_starttime'],
                        'hb_endtime'=>$value['hr_endtime'],
                        'hdid'=>$value['hdid']
                    );
                    $hracbook = D('HracBook')->where($map)->save($data);
                }
                $huccou = D('HracUsercoupon')->where(array('hucid'=>$receipt['hucid']))->find();
                $cou['hucid'] = $receipt['hucid'];
                if($huccou['huc_date']==0){
                    $cou['huc_vaild'] = 0;
                }else{
                    $huctime=time();
                    $hucdate=strtotime($huccou['huc_date']);
                    if($huctime>$hucdate){
                        $cou['huc_vaild'] = 2;
                    }else{
                        $cou['huc_vaild'] = 0;
                    }
                }
                $cousave = D('HracUsercoupon')->save($cou);
                if($hracbook){
                    $tmp['status'] = 1;
                    $this->ajaxreturn($tmp);
                }else{
                    $tmp['status'] = 0;
                    $this->ajaxreturn($tmp);
                }
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    编辑订单
    **/
    public function orderedit(){
        if(!IS_POST){
            $tmp['status'] = 0;
            $this->ajaxreturn($tmp);
        }else{
            //hr_number hr_money hr_expmoney hr_res hr_hra hr_dna givecoupon hr_promoney hr_review hr_minusmoney
            $mape  = I('post.');
            $where = array('hr_number'=>$mape['hr_number']);
            $save  = D('HracReceipt')->where($where)->save($mape);
            if($save){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp); 
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    获取某门店的全部医护的huid
    **/
    public function docter(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);   
        }else{
            // 获取门店id
            $sid    = I('post.sid');
            // $sid    = 1;
            //该门店全部医护信息
            $alldocter = D('HracDocter')->where(array('sid'=>$sid))->select();
            // p($alldocter);
            if($alldocter){
                //去除等级为0的
                foreach ($alldocter as $key => $value) {
                    if($value['hgid']!=0){
                        $docter[] = $value;
                    }
                }
                //全部用户
                $user   = D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')->select();
                //获取医护的用户id
                foreach ($docter as $key => $value) {
                    foreach ($user as $ke => $va) {
                        if($value['hd_name']==$va['hu_nickname']){
                            $docinfo[$key]['hd_name'] = $value['hd_name'];
                            $docinfo[$key]['huid']    = $va['huid'];
                            $docinfo[$key]['username']= $va['hu_username'];
                        }
                    }
                }
                foreach ($docinfo as $key => $value) {
                    $docterinfo[] = $value;
                }
                $count = count($docinfo);
                $docterinfo[$count] = array('hd_name'=>'全部','huid'=>0,'username'=>'');
                $docter=array_sort($docterinfo,'huid',$type='asc');
                foreach ($docter as $key => $value) {
                    $info[] = $value;
                }
            }else{
                $info = array(0=>array('hd_name'=>'全部','huid'=>0,'username'=>''));   
            }
            if($info){
                $this->ajaxreturn($info);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    排班表
    **/
    public function jobtime(){
        header('content-type:application:json;charset=utf8');  
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:POST');  
        header('Access-Control-Allow-Methods:GET');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid       = I('post.huid');
            // $huid       = 4;
            $phone      = D('HracUsers')
                        ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                        ->where(array('huid'=>$huid))
                        ->getfield('hu_nickname');
            $hdid       = D('HracDocter')->where(array('hd_name'=>$phone))->getfield('hdid');
            $albotime   = D('HracBook')->where(array('hdid'=>$hdid))->select();
            foreach ($albotime as $key => $value) {
                $alltime[$key]['hb_date']      = $value['hb_date'];
                $alltime[$key]['hdid']         = $value['hdid'];
                $alltime[$key]['sid']          = $value['sid'];
                $alltime[$key]['hb_starttime'] = $value['hb_starttime'];
                $alltime[$key]['hb_endtime']   = $value['hb_endtime'];
            }
            //订单
            $receipt    = D('HracReceipt')->where(array('hdid'=>$hdid))->select();
            //连表查询订单和服务
            $proceipt   = D('HracReceipt')
                        ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                        ->where(array('hdid'=>$hdid,'hr_status'=>0))
                        ->select();
            // 将hbid作为键值
            foreach ($albotime as $key => $value) {
                $bookarr[$value['hbid']] = $value;
            }
            //未预约时间
            foreach ($bookarr as $key => $value) {
                if($value['is_booking']==0){
                    $allbooknow[$key]      = $value;
                }
            }
            foreach ($allbooknow as $key => $value) {
                $book[0][$key]['hb_date']      = $value['hb_date'];
                $book[0][$key]['hdid']         = $value['hdid'];
                $book[0][$key]['sid']          = $value['sid'];
                $book[0][$key]['hb_starttime'] = $value['hb_starttime'];
                $book[0][$key]['hb_endtime']   = $value['hb_endtime'];
                $book[0][$key]['is_booking']   = $value['is_booking'];  
            }
            // p($book[0]);die;
            //已预约时间
            foreach ($bookarr as $key => $value) {
                if($value['is_booking']==1){
                    $nobook[$key]      = $value;
                }
            }
            // p($nobook);die;
            //已完成时间
            foreach ($receipt as $key => $value) {
                if($value['hr_status']==5){
                    $ceipt1[] = $value; 
                }
            }
            // p($receipt);die;
            //其他状态
            foreach ($receipt as $key => $value) {
                if($value['hr_status']!=0 && $value['hr_status']!=1 && $value['hr_status']!=5){
                    $ceipt2[] = $value; 
                }
            }
            // p($ceipt2);die;
            //已完成数据操作
            foreach ($ceipt1 as $key => $value) {
                $bookroom[$key][0]['hb_date']    = $value['hr_stardate'];
                $bookroom[$key][0]['hdid']       = $value['hdid'];
                $bookroom[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $bookroom[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            foreach ($bookroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $booking[$key][$ke][$k] = $value[0];
                            $booking[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $booking[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }
            // p($booking);die; 
            foreach ($booking as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $bookreceipt[] = $v;
                    }
                }
            }
            //订单已完成时间去除is_booking
            foreach ($bookreceipt as $key => $value) {
                $noreceipt[$key]['hb_date']      = $value['hb_date'];
                $noreceipt[$key]['hdid']         = $value['hdid'];
                $noreceipt[$key]['sid']          = $value['sid'];
                $noreceipt[$key]['hb_starttime'] = $value['hb_starttime'];
                $noreceipt[$key]['hb_endtime']   = $value['hb_endtime'];
            }
            //取差集，订单时间在排班中没有的时间
            foreach ($noreceipt as $key => $value) {
                if(!in_array($value,$alltime)){
                    $noallbook[$key]=$value;
                }
            }
            // p($noallbook); 
            //将已完成不存在时间is_booking变为2
            if($noallbook){
                foreach ($noallbook as $key => $value) {
                    $book[1][$key]              = $value;
                    $book[1][$key]['is_booking']= '2';
                }  
            }
            // p($book[1]);die;
            //查询已完成的数据
            foreach ($noreceipt as $key => $value) {
                $isbook[] = D('HracBook')->where($value)->find();
            }
            if($isbook[0]){
                //已完成将hbid作为键值
                foreach ($isbook as $key => $value) {
                    $isbooking[$value['hbid']] = $value;
                }
                //将已完成时间的is_booking变为2
                foreach ($isbooking as $key => $value) {
                    $book[2][$key]['hb_date']      = $value['hb_date'];
                    $book[2][$key]['hdid']         = $value['hdid'];
                    $book[2][$key]['sid']          = $value['sid'];
                    $book[2][$key]['hb_starttime'] = $value['hb_starttime'];
                    $book[2][$key]['hb_endtime']   = $value['hb_endtime'];
                    $book[2][$key]['is_booking']   = '2';
                }
            }
            // p($isbook);die; 
            //其他状态
            foreach ($ceipt2 as $key => $value) {
                $room[$key][0]['hb_date']    = $value['hr_stardate'];
                $room[$key][0]['hdid']       = $value['hdid'];
                $room[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $room[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            foreach ($room as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $king[$key][$ke][$k] = $value[0];
                            $king[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $king[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }               
            foreach ($king as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $bookceipt[] = $v;
                    }
                }
            }
            //订单其他状态时间去除is_booking
            foreach ($bookceipt as $key => $value) {
                $moreceipt[$key]['hb_date']      = $value['hb_date'];
                $moreceipt[$key]['hdid']         = $value['hdid'];
                $moreceipt[$key]['sid']          = $value['sid'];
                $moreceipt[$key]['hb_starttime'] = $value['hb_starttime'];
                $moreceipt[$key]['hb_endtime']   = $value['hb_endtime'];
            }
            //取差集，订单时间在排班中没有的时间
            foreach ($moreceipt as $key => $value) {
                if(!in_array($value,$alltime)){
                    $moreallbook[$key]=$value;
                }
            }
            // p($noallbook); 
            //将其他状态不存在时间is_booking变为2
            if($moreallbook){
                foreach ($moreallbook as $key => $value) {
                    $book[3][$key]              = $value;
                    $book[3][$key]['is_booking']= '3';
                }
            }
            // p($book[3]);die;
            //查询其他状态的数据
            foreach ($moreceipt as $key => $value) {
                $is_book[] = D('HracBook')->where($value)->find();
            }
            if($is_book[0]){
                //其他状态将hbid作为键值
                foreach ($is_book as $key => $value) {
                    $is_booking[$value['hbid']] = $value;
                }
                //将其他状态其他状态时间的is_booking变为3
                foreach ($is_booking as $key => $value) {
                    $book[4][$key]['hb_date']      = $value['hb_date'];
                    $book[4][$key]['hdid']         = $value['hdid'];
                    $book[4][$key]['sid']          = $value['sid'];
                    $book[4][$key]['hb_starttime'] = $value['hb_starttime'];
                    $book[4][$key]['hb_endtime']   = $value['hb_endtime'];
                    $book[4][$key]['is_booking']   = '3';
                }
            }
            // p($allceipt);
            //未处理订单
            foreach ($proceipt as $key => $value) {
                if(strpos($value['hp_name'],'RES')){
                    $pro[$key][0]['is_booking']='1';
                }else if(strpos($value['hp_name'],'HRA101')){
                    $pro[$key][0]['is_booking']='4';
                }else if(strpos($value['hp_name'],'DNA')){
                    $pro[$key][0]['is_booking']='5';
                }else{
                    $pro[$key][0]['is_booking']='6';
                }
                $pro[$key][0]['hb_date']    = $value['hr_stardate'];
                $pro[$key][0]['hdid']       = $value['hdid'];
                $pro[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $pro[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            //重构
            foreach ($pro as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $prok[$key][$ke][$k] = $value[0];
                            $prok[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $prok[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }           
            //降维
            foreach ($prok as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $proking[] = $v;
                    }
                }
            }
            $book[5] = $proking;
            // p($book[5]);die;
            foreach ($book as $key => $value) {
                foreach ($value as $k => $v) {
                    if(!empty($v['hb_starttime'])){
                        $booktime[] = $v;
                    }
                }
            }
            // p($booktime);
            if($booktime){
                $this->ajaxreturn($booktime);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    添加排班时间
    **/
    public function addjobtime(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);   
        }else{
            //获取用户
            $huid         = I('post.huid');
            // $huid         = 4;
            //日期
            $hb_date      = I('post.hb_date');
            // $hb_date      = '2017-08-24';
            //获取时间
            $time         = I('post.time');
            // $time         = '14:00-14:30';
            $arrtime      = explode('-',$time);
            // p($arrtime);
            //开始时间
            $hb_starttime = $arrtime[0];
            //结束时间
            $hb_endtime   = $arrtime[1];
            //获取hu_nickname
            $htid         = D('HracTime')->where(array('time'=>$hb_starttime))->getfield('htid');
            //获取手机号
            $phone        = D('HracUsers')
                          ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                          ->where(array('huid'=>$huid))
                          ->getfield('hu_nickname');

            //通过手机号查询医护id
            $hdinfo       = D('HracDocter')->where(array('hd_name'=>$phone))->find();
            // p($hdinfo);die;
            if($hdinfo['hdid']){
                // echo $hd_week;  
                //查询该医护这一天有没有排班
                $booktime     = D('HracBook')->where(array('hdid'=>$hdinfo['hdid'],'hb_date'=>$hb_date))->select();
                foreach ($booktime as $key => $value) {
                    $booking[$key]['hb_starttime']=$value['hb_starttime'];
                }
                // p($booking);
                //添加的起始时间
                $starttime = array(
                    'hb_starttime'=>$hb_starttime
                );
                //要添加的数据
                $addbook      =array(
                        'sid'         =>$hdinfo['sid'],
                        'hdid'        =>$hdinfo['hdid'],
                        'hb_date'     =>$hb_date,
                        'hb_starttime'=>$hb_starttime,
                        'hb_endtime'  =>$hb_endtime,
                        'htid'        =>$htid
                );
                //判断是否有相同时间
                foreach($booking as $key => $value){
                    $arr1[] = array_intersect($value,$starttime);
                }
                //重构数组得到时间
                foreach($arr1 as $key => $value){
                    if(!empty($value)){
                        $arrbook = $value;
                    }
                }
                // p($arrbook);
                if(!$arrbook && !empty($htid)){
                    $book = D('HracBook')->add($addbook);
                    if($book){
                        $data['status'] = 1;
                        $this->ajaxreturn($data);
                    }else{
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }    
                }else{
                    $data['status'] = 2;
                    $this->ajaxreturn($data);
                }
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }  
        }
    }
    /**
    删除排班时间
    **/
    public function deletejob(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);   
        }else{
            //获取用户
            $huid         = I('post.huid');
            // $huid         = 12;
            // 日期
            $hb_date      = I('post.hb_date');
            // $hb_date      = '2017-11-04';
            // //获取时间
            $time         = I('post.time');
            // $time         = '14:00-14:30';
            $arrtime      = explode('-',$time);
            // // p($arrtime);
            // //开始时间
            $hb_starttime = $arrtime[0];
            // //结束时间
            $hb_endtime   = $arrtime[1];
            //获取hu_nickname
            $phone        = D('HracUsers')
                          ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                          ->where(array('huid'=>$huid))
                          ->getfield('hu_nickname');
            // p($phone);

            //通过手机号查询医护id
            $hdinfo       = D('HracDocter')->where(array('hd_name'=>$phone))->find();
            // p($hdinfo);die;
            //要删除的数据
            $tmparr      =array(
                    'sid'         =>$hdinfo['sid'],
                    'hdid'        =>$hdinfo['hdid'],
                    'hb_date'     =>$hb_date,
                    'hb_starttime'=>$hb_starttime,
                    'hb_endtime'  =>$hb_endtime,
                    'is_booking'  =>0
            );
            // p($tmparr);die; 
            $book = D('HracBook')->where($tmparr)->delete();
            // p($book);die; 
            if($book){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    复制排班时间
    **/
    public function copyjobtime(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);   
        }else{
            //获取用户
            $huid      = I('post.huid');
            // $huid         = 4;
            //被copy起始日期
            $date      = I('post.date');
            // $date      = '2017-09-15';
            //添加的起始时间
            $newsdate  = I('post.newsdate');
            // $newsdate  = '2017-09-22';
            //数量
            $number    = 6;
            $datetime1 = date_time($date,$number);
            $datetime2 = date_time($newsdate,$number);
            // p($datetime1);
            //获取hu_nickname
            $phone     = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                       ->where(array('huid'=>$huid))
                       ->getfield('hu_nickname');

            //通过手机号查询医护id
            $hdid      = D('HracDocter')->where(array('hd_name'=>$phone))->getfield('hdid');
            // echo $hdid;
            //新日期里原有的book
            foreach ($datetime2 as $key => $value) {
                $book2[$key] = D('HracBook')->where(array('hdid'=>$hdid,'hb_date'=>$value))->select();
            }
            //降维取id
            foreach ($book2 as $key => $value) {
                foreach ($value as $ke => $va) {
                    $bookid[] = $va['hbid'];
                }
            }
            //删除原有的数据
            foreach ($bookid as $key => $value) {
                $delete = D('HracBook')->where(array('hbid'=>$value))->delete();
            }
            //要复制的工作时间
            foreach ($datetime1 as $key => $value) {
                $book1[$key] = D('HracBook')->where(array('hdid'=>$hdid,'hb_date'=>$value))->select();
            }
            //
            // p($book1);die;
            //重构数组
            foreach ($book1 as $key => $value) {
                if(empty($value)){
                    $booktime1[$key]=array();
                }else{
                    foreach ($value as $k => $v) {
                        $booktime1[$key][$k]['hdid']         = $v['hdid'];
                        $booktime1[$key][$k]['sid']          = $v['sid'];
                        $booktime1[$key][$k]['htid']         = $v['htid'];
                        $booktime1[$key][$k]['hb_endtime']   = $v['hb_endtime'];
                        $booktime1[$key][$k]['hb_starttime'] = $v['hb_starttime'];
                    }    
                }
            }
            // p($booktime1);die;
            //将bookingk拷贝到新的日期里
            foreach ($datetime2 as $key => $value) {
                foreach ($booktime1 as $ke => $va) {
                    if($key==$ke){
                        foreach ($va as $k => $v) {
                            $booktime2[$ke][$k]=$v;
                            $booktime2[$ke][$k]['hb_date']=$value;
                        }
                    }
                }
            }
            // p($booktime2);die;
            //降为一维数组
            foreach ($booktime2 as $key => $value) {
                foreach ($value as $ke => $va) {
                    $booktime[]=$va;
                }
            }
            // p($booktime);die;
            foreach ($booktime as $key => $value) {
                $book = D('HracBook')->add($value);
            }
            if($book){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }    
        }
    }
    /**
    获取预约的用户信息
    **/
    public function todayAllbook(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 获取huid
            $huid     = I('post.huid');
            $status   = I('post.hr_status');
            // $status   = 7;
            // $huid     = 1;
            $phone    = D('HracUsers')
                      ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                      ->where(array('huid'=>$huid))
                      ->getfield('hu_nickname');
            $sid      = D('HracDocter')->where(array('hd_name'=>$phone))->getfield('sid');
            // echo $sid;
            if($status==7){
                if($sid==0){
                    $where    = array();                        
                }else{
                    $where    = array(
                           'nulife_hrac_receipt.sid'=>$sid 
                    );
                }
            }else{
                if($sid==0){
                    $where    = array(
                           'nulife_hrac_receipt.hr_status'=>$status
                    );
                }else{
                    $where    = array(
                           'nulife_hrac_receipt.hr_status'=>$status,
                           'nulife_hrac_receipt.sid'=>$sid 
                    );
                }
            }
            // p($tmp);
             $tmp = D('HracReceipt')
                  ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                  ->join('nulife_ibos_users on nulife_hrac_receipt.hr_nickname = nulife_ibos_users.hu_nickname')
                  ->where($where)
                  ->select();
            // p($tmp);
            $tmpe = array_sort($tmp,'hr_stardate','desc');
            if($tmpe){
                $this->ajaxreturn($tmpe);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    转赠用户优惠券
    **/
    public function givecoupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //转赠人
            $huid  = I('post.huid');
            $name  = D('HracUsers')
                   ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                   ->where(array('huid'=>$huid))->getfield('hu_nickname');
            // $huid  = 4;
            //用户优惠券id
            $hucid = I('post.hucid');
            //优惠券id
            $userco= D('HracUsercoupon')->where(array('hucid'=>$hucid))->find();
            //项目id
            $hpid  = D('HracCoupon')->where(array('hcid'=>$userco['hcid']))->getfield('hpid');
            //项目名称
            $hpname= D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');
            // $hucid = 1;
            // 获取分类id
            $hicid = D('HracInfoclass')->where(array('hic_type'=>3))->getfield('hicid');
            // 收到者
            $nick  = I('post.id');
            $id    = D('HracUsers')
                   ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                   ->where(array('hu_nickname'=>$nick))
                   ->getfield('huid');
            if(!$id){
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }else{
                // $id    = 1;
                //原来的codepic
                $code  = D('HracUsercoupon')->where(array('hucid'=>$hucid))->find();
                if($code['huc_more']==0){
                    $parent = $huid;
                }else{
                    $parent = $code['huc_parent'];
                }
                $web_url     = C('WEB_URL');
                // 存放的内容
                $content     = array('huid'=>$id,'hucid'=>$hucid,'huc_number'=>$userco['huc_number'],'codetype'=>1,'huc_vaild'=>0,'huc_parent'=>$parent);
                $pic         = qrcode_arr($content);
                if($pic){
                    unlink($code['huc_codepic']);
                }
                $where = array(
                    'huid'       => $id,
                    'hucid'      => $hucid,
                    'huc_more'   => $code['huc_more']+1,
                    'huc_parent' => $parent,
                    'huc_codepic'=> $pic
                );
                $save  = D('HracUsercoupon')->save($where);
                if($save){
                    //标题
                    $title1 = '成功转出优惠券';
                    $title2 = '收到用户优惠券';
                    $time   = date('Y-m-d H:i:s',time());
                    //转赠人
                    $con1  = '您将用于预约'.$hpname.'服务的'.$userco['hp_abb'].'-'.$userco['huc_number'].'号优惠券转赠给'.$nick.'用户';
                    //收到者
                    $con2  = '收到'.$name.'转赠用于预约'.$hpname.'服务的'.$userco['hp_abb'].'-'.$userco['huc_number'].'号优惠券';
                    $infoarr =array(
                        array('hi_title'=>$title1,'hi_content'=>$con1,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$huid),
                        array('hi_title'=>$title2,'hi_content'=>$con2,'hi_time'=>$time,'hicid'=>$hicid,'huid'=>$id),
                    );
                    foreach ($infoarr as $key => $value) {
                        $addinfo = D('HracInformation')->add($value);
                    }
                    $data['status']=1;
                    $this->ajaxreturn($data);     

                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }   
            }
        }
    }
    /**
    生成个人二维码
    **/
    public function usercode(){
        if(!IS_POST){
            $tmp['status'] = 0;
            $this->ajaxreturn($tmp);
        }else{
            $iuid = I('post.iuid');
            // $huid = 7;
            $user = D('IbosUsers')->where(array('iuid'=>$iuid))->find();
            $hrac = D('HracUsers')->where(array('iuid'=>$iuid))->find();
            // die;
            if($user['hu_codepic']){
                unlink($user['hu_codepic']);
            }
            $web_url     = C('WEB_URL');
            // 存放的内容
            if($hrac){
                $content     = array('iuid'=>$iuid,'huid'=>$hrac['huid'],'codetype'=>3);     
            }else{
                $content     = array('iuid'=>$iuid,'codetype'=>3);
            }
            $qrcode      = qrcode_arr($content);
            $data = array(
                'iuid'      =>$iuid,
                'hu_codepic'=>$qrcode
            );
            $save = D('IbosUsers')->save($data);
            if($qrcode){
                $tmp['status'] = $qrcode;               
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    罚款锁定不能预约或取消
    **/
    public function fine(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $id  = I('post.id');
            $fine= D('HracUsers')->where(array('huid'=>$id))->getfield('is_fine');
            if($fine==0){
                $tmp =array(
                    'huid'    =>$id,
                    'is_fine' =>1
                );
            }else{
                $tmp =array(
                    'huid'    =>$id,
                    'is_fine' =>0
                );
            }
            $save  = D('HracUsers')->save($tmp);
            if($save){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    优惠券列表
    **/
    public function couponlist(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取优惠券列表
            $coupon = D('HracCoupon')->select();
            if($coupon){
                $this->ajaxreturn($coupon);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取过期优惠券
    **/
    public function expirecoupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 时间年月日，如：2017-09-11
            $date  = date('Y-m-d',time());
            // echo $time;
            $number= I('post.number')-1;
            // $number= 2;
            //得到日期数组
            $time  = date_time($date,$number);
            // p($time);die;
            //0优惠券，1服务项目
            $type  = I('post.type');
            // $type    = 0;
            // 所有用户id
            $huid    = D('HracUsers')->getfield('huid',true);
            // p($huid);
            //判断值是0或1
            if($type==0){
                $hcid  = I('post.hcid');
                //优惠券id
                //hcid为0时查询所有优惠券
                // $hcid    = 2;
                if($hcid==0){
                    $cid = 0;
                    $pid = 0;
                    foreach ($time as $key => $value) {
                        $tmp[]   = D('HracUsercoupon')
                                 ->join('nulife_hrac_users on nulife_hrac_users.huid = nulife_hrac_usercoupon.huid')
                                 ->where(array('huc_vaild'=>0,'huc_date'=>$value))
                                 ->select();
                    }
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                }else{
                    $cid = $hcid;
                    $pid = 0;    
                    //所有关于该优惠券的信息
                    foreach ($time as $key => $value) {
                           $tmp[]= D('HracUsercoupon')
                                 ->join('nulife_hrac_users on nulife_hrac_users.huid = nulife_hrac_usercoupon.huid')
                                 ->where(array('hcid'=>$hcid,'huc_vaild'=>0,'huc_date'=>$value))
                                 ->select();
                    }
                    
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                    // p($status);
                }
            }else{
                // 项目id
                $hpid  = I('post.hpid');
                // $hpid    = 0;
                //0为所有项目的优惠券信息 
                if($hpid==0){
                    $cid = 0;
                    $pid = 0;    
                    foreach ($time as $key => $value) {
                        $tmp[] = D('HracUsercoupon')
                               ->join('nulife_hrac_users on nulife_hrac_users.huid = nulife_hrac_usercoupon.huid')
                               ->where(array('huc_vaild'=>0,'huc_date'=>$value))
                               ->select();
                    }                   
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                }else{
                    $cid = 0;
                    $pid = $hpid;
                    //优惠券信息
                    $list   = D('HracCoupon')->where(array('hpid'=>$hpid))->getfield('hcid',true);
                    foreach ($time as $key => $value) {
                        $cou[]  = D('HracUsercoupon')
                                ->join('nulife_hrac_users on nulife_hrac_users.huid = nulife_hrac_usercoupon.huid')
                                ->where(array('huc_vaild'=>0,'huc_date'=>$value))
                                ->select();
                    }
                    foreach ($list as $key => $value) {
                        foreach ($cou as $ke => $va) {
                            foreach ($va as $k => $v) {
                                if($value==$v['hcid']){
                                    $tmp[$value][] = $v;
                                }
                            }
                        }
                    }
                    // p($tmparr);die;
                    //转为一维数组
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                }
            }
            if(!empty($tmparr)){
                foreach ($tmparr as $key => $value) {
                    $status[$key]=$value;
                    $status[$key]['type']=$type;
                    $status[$key]['hcid']=$cid;      
                    $status[$key]['hpid']=$pid;                  
                }
                //重构数组，以用户为键值做区分
                foreach ($huid as $key => $value) {
                    foreach ($status as $ke => $va) {
                        if($va['huid']==$value){
                            $allhuid[$value][]=$va;
                        }
                    }
                }
                // p($allhuid);
                //添加数量字段
                foreach ($allhuid as $key => $value) {
                    $count = count($value);
                    foreach ($value as $k => $v) {
                        $couponnum[$key][$k] = $v;
                        $couponnum[$key][$k]['number'] = $count;
                    }
                }
                // p($couponnum);
                //只取键值为0的
                foreach ($couponnum as $key => $value) {
                        $coupon[] = $value[0];
                }
                // p($coupon);
                $num = $number+1;
                $users = D('HracUsers')
                       ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                       ->select();
                //取姓名,数量,用户地址,用户id,电话
                foreach ($users as $key => $value) {
                    foreach ($coupon as $k => $v) {
                        if($value['huid']==$v['huid']){
                            $usercoupon[$k]['hu_phone']   =$value['hu_phone'];
                            $usercoupon[$k]['number']     =$v['number'];
                            $usercoupon[$k]['huid']       =$v['huid'];
                            $usercoupon[$k]['hu_nickname']=$value['hu_nickname'];
                            $usercoupon[$k]['hu_address'] =$value['hu_address'];
                            $usercoupon[$k]['type']       =$v['type'];      
                            $usercoupon[$k]['hcid']       =$v['hcid'];      
                            $usercoupon[$k]['hpid']       =$v['hpid'];      
                            $usercoupon[$k]['num']        =$num;  
                        }
                    }
                }
                // p($usercoupon);
                if($usercoupon){
                    $this->ajaxreturn($usercoupon);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);  
                } 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }            
        }
    }
    /**
    过期优惠券详情
    **/
    public function expireinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 时间年月日，如：2017-09-11
            $date  = date('Y-m-d',time());
            // echo $time;
            $number= I('post.number')-1;
            // $number= 2;
            //得到日期数组
            $time  = date_time($date,$number);
            //0优惠券，1服务项目
            $type  = I('post.type');
            // $type    = 0;
            //优惠券id
            $hcid  = I('post.hcid');
            // $hcid    = 2;
            //项目id
            $hpid  = I('post.hpid');
            // $hpid    = 1;
            //用户id
            $huid  = I('post.huid');
            // $huid  = 11;
            //用户信息
            $huinfo  = D('HracUsers')
                     ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                     ->where(array('huid'=>$huid))
                     ->find();
            //所有优惠券
            $allcou  = D('HracCoupon')->order('hcid asc')->getfield('hcid',true);
            // p($allcou);exit;
            //判断值是0或1
            if($type==0){
                //hcid为0时查询所有优惠券
                if($hcid==0){
                    foreach ($time as $key => $value) {
                        $cou[]= D('HracUsercoupon')
                              ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid = nulife_hrac_usercoupon.hcid')
                              ->where(array('huc_vaild'=>0,'huc_date'=>$value,'huid'=>$huid))
                              ->select();
                    }
                    //降维
                    foreach ($cou as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                    // p($tmparr);exit;
                    //通过优惠券重构数组
                    foreach ($allcou as $key => $value) {
                        foreach ($tmparr as $k => $v) {
                            if($value==$v['hcid']){
                                $tmp[$value][]=$v;
                            }
                        }
                    }
                    //转为一维数组
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmpnum[] = $v;
                        }
                    }
                    //获取总数量
                    $allnumber = '总数:'.count($tmpnum);
                    // p($allnumber);exit;
                    //增加数量number
                    foreach ($tmp as $key => $value) {
                        $count = count($value);
                        foreach ($value as $k => $v) {
                            $couponnum[$key][$k]               = $v;
                            $couponnum[$key][$k]['number']= $v['hc_name'].':'.$count;
                            $couponnum[$key][$k]['allnumber']  = $allnumber;
                        }
                    }
                    // p($couponnum);
                    //只取键值为0
                    foreach ($couponnum as $key => $value) {
                        $couponzero[$key] = $value[0];
                    }
                    // p($couponzero);die;
                    //将键number 组成数组
                    foreach ($couponzero as $key => $value) {
                        $numarray[$key]['number']  = $value['number'];
                    }
                    foreach ($numarray as $key => $value) {
                        $numarr[] = $value;
                    }
                    // p($numarr);die;
                    //总数,各项目数
                    foreach ($couponzero as $key => $value) {
                        $coupon['allnumber']  = $value['allnumber'];
                        $coupon['list']       = $numarr;
                    }
                    // p($coupon);exit;
                }else{
                    //所有关于该优惠券的信息
                    foreach ($time as $key => $value) {
                        $cou[]  = D('HracUsercoupon')
                                ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid = nulife_hrac_usercoupon.hcid')
                                ->where(array('nulife_hrac_usercoupon.hcid'=>$hcid,'huc_vaild'=>0,'huc_date'=>$value,'huid'=>$huid))
                                ->select();
                    }
                    foreach ($cou as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }
                    //一维数组长度
                    $count = count($tmparr);
                    // echo $count;
                    //遍历需要的值
                    foreach ($tmparr as $key => $value) {
                        $coupon['allnumber']    = '总数:'.$count;
                        $coupon['list'][0]['number'] = $value['hc_name'].':'.$count;
                    }

                }
            }else{
                //0为所有项目的优惠券信息 
                if($hpid==0){
                    //项目
                    $list   = D('HracProject')->select();
                    //个人所有优惠券
                    foreach ($time as $key => $value) {
                        $cou[]   = D('HracUsercoupon')
                                 ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid = nulife_hrac_usercoupon.hcid')
                                 ->where(array('huc_vaild'=>0,'huc_date'=>$value,'huid'=>$huid))
                                 ->select();;
                    }
                    foreach ($cou as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmparr[] = $v;
                        }
                    }  
                    //用项目区分                
                    foreach ($list as $key => $value) {
                        foreach ($tmparr as $k => $v) {
                            if($v['hpid']==$value['hpid']){
                                $tmp[$v['hpid']][]=$v;
                            }
                        }
                    }
                    //转为一维数组
                    foreach ($tmp as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmpnum[] = $v;
                        }
                    }
                    //获取总数量
                    $allnumber = '总数:'.count($tmpnum);
                    //添加总数
                    foreach ($tmp as $key => $value) {
                        $count = count($value);
                        foreach ($value as $k => $v) {
                            $couponnum[$key][$k] = $v;
                        }
                    }
                    //添加总数
                    foreach ($tmp as $key => $value) {
                        $count = count($value);
                        foreach ($value as $k => $v) {
                            $couponnum[$key][$k]               = $v;
                            $couponnum[$key][$k]['number']     = $count;
                            $couponnum[$key][$k]['allnumber']  = $allnumber;
                        }
                    }
                    // p($couponnum);die;
                    //只取第一个
                    foreach ($couponnum as $key => $value) {
                        $couponzero[$key] = $value[0];
                    }
                    //添加项目名称
                    foreach ($couponzero as $key => $value) {
                        foreach ($list as $k => $v) {
                            if($value['hpid']==$v['hpid']){
                                $couponname[$key]           =$value;
                                $couponname[$key]['hp_name']=$v['hp_name'];
                            }
                        }
                    }
                    //将键number 组成数组
                    foreach ($couponname as $key => $value) {
                        $numarray[$key]['number']  = $value['hp_name'].':'.$value['number'];
                    }
                    foreach ($numarray as $key => $value) {
                        $numarr[] = $value;
                    }
                    // p($numarr);die;
                    //每个项目数量拼接项目名
                    foreach ($couponname as $key => $value) {
                        $coupon['allnumber']  = $value['allnumber'];
                        $coupon['list']     = $numarr;
                    }
                    // p($coupon);die;

                }else{
                    //所有
                    foreach ($time as $key => $value) {
                        $cou[]   = D('HracUsercoupon')
                                 ->join('nulife_hrac_coupon on nulife_hrac_coupon.hcid = nulife_hrac_usercoupon.hcid')
                                 ->where(array('huc_vaild'=>0,'huc_date'=>$value,'huid'=>$huid))
                                 ->select();;
                    }
                    //该项目的所有券
                    $list   = D('HracCoupon')->where(array('hpid'=>$hpid))->getfield('hcid',true);
                    $hpname = D('HracProject')->where(array('hpid'=>$hpid))->getfield('hp_name');
                    //优惠券信息
                    foreach ($list as $key => $value) {
                        foreach ($cou as $ke => $va) {
                            foreach ($va as $k => $v) {
                                if($value==$v['hcid']){
                                    $tmparr[$value][] = $v;
                                }
                            }
                        }
                    }
                    // p($tmparr);
                    //转为一维
                    foreach ($tmparr as $key => $value) {
                        foreach ($value as $k => $v) {
                            $tmp[] = $v;
                        }
                    }
                    //总数
                    $count = count($tmp);
                    // echo $count;
                    //遍历需要的值
                    foreach ($tmparr as $key => $value) {
                        $coupon['allnumber']    = '总数:'.$count;
                        $coupon['list'][0]['number'] = $hpname.':'.$count;
                    }
                    // p($tmp); die;
                }
            }
            $vaildnum = $number+1;
            //将用户信息和用户优惠券信息合并为二维数组
            $couponinfo['huinfo']           = $huinfo;
            $couponinfo['couinfo']          = $coupon;
            $couponinfo['couinfo']['vaild'] = '优惠券在'.$vaildnum.'天内过期';
            // p($couponinfo);
            if($couponinfo){
                $this->ajaxreturn($couponinfo);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }            
        }
    }
    /**
    通过id查询是否可以预约
    **/
    public function isfine(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $nick = I('post.id');
            // $nick = 'CN20170012';
            $id   = D('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                  ->where(array('hu_nickname'=>$nick))
                  ->getfield('huid');
            if($id){
                $user = D('HracUsers')->where(array('huid'=>$id,'is_fine'=>0))->find();
                if($user){
                    $data['status'] = 1;
                    $this->ajaxreturn($data); 
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }               
            }else{
                $data['status'] = 2;
                $this->ajaxreturn($data);               
            }
        }
    }
    /**
    消息分类
    **/
    public function infoclass(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid = I('post.huid');
            // $huid = 2;
            //所有消息hiid
            $hiid  = D('HracInformation')->getfield('hiid',true);
            // p($hiid);
            //该用户所有消息
            $info = D('HracInformation')->where(array('huid'=>$huid))->order('hiid desc')->select();
            // p($infoarr);
            foreach ($hiid as $key => $value) {
                foreach ($info as $ke => $va) {
                    if($value==$va['hiid']){
                        $tmp[$value] = $va;
                    }
                }
            }
            rsort($tmp);
            //消息分类
            $class= D('HracInfoclass')->order('order_number desc')->select();
            // p($class);
            //当前时间
            // $time = date('Y-m-d',time());
            //按分类
            foreach ($class as $key => $value) {
                foreach ($tmp as $ke => $va) {
                    if($value['hicid']==$va['hicid']){
                        $infoarr[$key][$ke] = $value;
                        $infoarr[$key][$ke]['hi_content'] = $va['hi_content'];
                        $infoarr[$key][$ke]['hi_time']    = word_time(strtotime($va['hi_time']));
                    }
                }
            }
            // p($infoarr);die;
            //重置索引
            foreach ($infoarr as $key => $value) {
                foreach ($value as $ke => $va) {
                    $content[$key][] = $va;
                }
            }
            // p($content);
            // 只取第一个
            foreach ($content as $key => $value) {
                $con[$key] = $value[0];
            }
            // p($con);
            if($con){
                $data['status'] = 1;
                $this->ajaxreturn($con); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    消息列表
    **/
    public function information(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid  = I('post.huid');
            $hicid = I('post.hicid');
            //该用户所有消息
            $info  = D('HracInformation')->where(array('huid'=>$huid,'hicid'=>$hicid))->order('hiid desc')->select();
            foreach ($info as $key => $value) {
                $status[$key]['hiid']      =$value['hiid'];
                $status[$key]['hi_title']  =$value['hi_title'];
                $status[$key]['hi_content']=$value['hi_content'];
                $status[$key]['hi_time']   =word_time(strtotime($value['hi_time']));
            }
            // p($status);
            if($status){
                $this->ajaxreturn($status); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    消息详情
    **/ 
    public function infodetails(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hiid  = I('post.hiid');
            // $hiid  = 21;
            //消息s
            $info  = D('HracInformation')->where(array('hiid'=>$hiid))->find();
            // p($status);
            if($info){
                $this->ajaxreturn($info); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    查看所有用户
    **/
    public function users(){
       if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 用户
            $nickname     = I('post.nick');
            // $nickname     = 0;
            //转为字符串
            $nick         = (string)$nickname;
            //与字符串0对比
            if($nick==(string)0){
                $usreinfo = M('HracUsers')
                          ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                          ->select();
            }else{
                $usreinfo = M('HracUsers')
                          ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                          ->where(array('hu_nickname'=>array('like','%'.$nick.'%')))
                          ->select();
            }            
            if($usreinfo && !empty($usreinfo[0])){
                $this->ajaxreturn($usreinfo); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        } 
    }
    /**
    vip设置
    **/
     public function isvip(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $id  = I('post.id');
            // $id  = 5;
            $vip = M('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')->where(array('huid'=>$id))->find();
            $time= date('Y-m-d',time());
            $end = date("Y-m-d",strtotime("+1 year",strtotime($time)));
            // echo $time;die; 
            if($vip['is_vip']==1){
                $tmp =array(
                    'huid'    =>$id,
                    'is_vip'  =>0,
                    'vipstart'=>'',
                    'vipend'  =>''                   
                );
            }else{
                if($vip['stack']==0){
                    $data['status'] = 2;
                    $this->ajaxreturn($data);               
                }else{
                    $tmp =array(
                        'huid'    =>$id,
                        'is_vip'  =>1,
                        'vipstart'=>$time,
                        'vipend'  =>$end
                    );             
                }
            }
            $save  = M('HracUsers')->save($tmp);
            if($save){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            } 
        }
    }
    /**
    获取优惠券列表和用户剩余券数量
    **/
    public function coulist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid       = I('post.huid');
            // $huid       = 11;
            $usercoupon = M('HracUsercoupon')->where(array('huid'=>$huid,'huc_vaild'=>0))->select();
            // p($usercoupon);
            //获取优惠券分类
            $coupon = M('HracCoupon')->select();
            if(!$usercoupon){
                foreach ($coupon as $key => $value) {
                    $couarr[$key] = $value;
                    $couarr[$key]['num'] = 0;
                }
            }else{
                //将该用户的券进行分类
                foreach ($usercoupon as $key => $value) {
                    foreach ($coupon as $k => $v) {
                        if($value['hcid']==$v['hcid']){
                            $couponnum[$value['hcid']][]=$value;
                        }
                    }
                }
                //计算数量
                foreach ($couponnum as $key => $value) {
                    $count    = count($value);
                    $counum[$key]['num'] = $count;    
                }
                //取出数量不为零的分类
                foreach ($coupon as $key => $value) {
                    foreach ($counum as $k => $v) {
                        if($value['hcid']==$k){
                            $couno[$key]    = $value;
                        }
                    }
                }
                //将数量计入分类
                foreach ($couno as $key => $value) {
                    foreach ($counum as $k => $v) {
                        if($value['hcid']==$k){
                            $cou[0][$key]        = $value; 
                            $cou[0][$key]['num'] = $v['num']; 
                        }
                    }
                }
                //差集,取出数量为零的分类
                foreach ($coupon as $key => $value) {
                    if(!in_array($value,$couno)){
                        $couzero[]=$value;
                    }
                }
                //将数量计入分类
                foreach ($couzero as $key => $value) {
                    $cou[1][$key]        = $value; 
                    $cou[1][$key]['num'] = 0; 
                }
                foreach ($cou as $key => $value) {
                    foreach ($value as $k => $v) {
                        $couarr[] = $v;
                    }
                }
            }
            $coulist = array_sort($couarr,'hcid');
            foreach ($coulist as $key => $value) {
                $list[] = $value;
            }
            // p($coulist);
            if($list){
                $this->ajaxreturn($list);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    添加用户优惠券
    **/
    public function addusercoupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $huctime = date("Y-m-d",time());
            $hcid    = I('post.hcid');
            $huid    = I('post.huid');
            $lp      = I('post.loop');
            $huciarr = explode('-', $hcid);
            $looparr = explode('-', $lp);
            // p($huciarr);
            // p($looparr);
            foreach ($huciarr as $key => $value) {
                foreach ($looparr as $ke => $va) {
                    if($key==$ke){
                        $coupon[$key]['hcid'] = $value;
                        $coupon[$key]['loop'] = $va;
                        $coupon[$key]['huid'] = $huid;
                    }
                }
            }
            // p($coupon);
            $class = M('HracCoupon')->select();
            // p($coupon);die;
            foreach ($coupon as $key => $value) {
                if($value['loop']!=0){
                    $add[] = $value;
                }
            }
            if($add){
                foreach ($class as $key => $value) {
                    foreach ($add as $k => $v) {
                        if($value['hcid']==$v['hcid']){
                            $couclass[$k] = $v;
                            $couclass[$k]['hc_name'] = $value['hc_name'];
                        }
                    }
                }
                // p($couclass);
                foreach ($couclass as $key => $value) {
                    $couinfo['huid']       = $value['huid'];
                    $couinfo['hi_time']    = date('Y-m-d H:i:s',time());
                    $couinfo['content']   .= $value['hc_name'].'券'.$value['loop'].'张,';
                    $couinfo['hi_title']   = '收到用户优惠券';
                    $couinfo['hicid']      = 2;
                    $couinfo['num']       += $value['loop'];
                }
                $foarr = array(
                    'huid'       =>$couinfo['huid'],
                    'hi_time'    =>$couinfo['hi_time'],
                    'hi_content' =>'总共收到'.$couinfo['num'].'张优惠券,分别有'.rtrim($couinfo['content'], ','),
                    'hi_title'   =>$couinfo['hi_title'],
                    'hicid'      =>$couinfo['hicid']
                );
                // p($foarr);
                foreach ($add as $key => $value) {
                    $hcinfo  = M('HracCoupon')->where(array('hcid'=>$value['hcid']))->find();
                    $hcterm  = $hcinfo['hc_term'];
                    // echo $hcterm;die;
                    $hpname  = M('HracProject')->where(array('hpid'=>$hcinfo['hpid']))->getfield('hp_abb');
                    $numarr  = M('HracUsercoupon')->where(array('hp_abb'=>$hpname))->order('huc_number desc')->select();
                    if($numarr){
                        $number = $numarr[0]['huc_number']+1;
                        $numarray[0]=$number;
                    }else{
                        $number = 50000001;
                        $numarray[0]=$number;
                    }
                    if($hcterm==0){
                        $hucdate=0;
                    }else{
                        $date   = time()+$hcterm*86400;
                        $hucdate= date("Y-m-d",$date);  
                    }
                    // echo $hucdate;die;
                    $loop = $value['loop'];
                    for($i=1;$i<$loop;$i++){
                        $number+=1;
                        $numarray[$i]=$number;
                    }
                    // $numhee[$key] = $numarray;
                    // $numarray = Array(50000003,50000004,50000005,50000006,50000007);
                    // p($numarray);
                    foreach ($numarray as $ke => $va) {
                        $tmp[$ke]['huc_number'] = $va;
                        $tmp[$ke]['hcid']       = $value['hcid'];
                        $tmp[$ke]['huid']       = $value['huid'];
                        $tmp[$ke]['huc_time']   = $huctime;
                        $tmp[$ke]['hp_abb']     = $hpname;
                        $tmp[$ke]['hc_type']    = $hcinfo['hc_type'];
                        $tmp[$ke]['huc_date']   = $hucdate;  
                        $tmp[$ke]['huc_parent'] = $huid;  
                    }
                    foreach ($tmp as $ke => $va) {
                        M('HracUsercoupon')->add($va);
                    }
                    // p($tmp);
                    foreach ($numarray as $ke => $va) {
                        $status[$ke] = M('HracUsercoupon')->where(array('hp_abb'=>$hpname,'huc_number'=>$va))->find();  
                    }
                    foreach ($status as $ke => $va) {
                        // 存放的内容
                        $content     = array('huid'=>$va['huid'],'hucid'=>$va['hucid'],'huc_number'=>$hpname.'-'.$va['huc_number'],'codetype'=>1,'huc_vaild'=>0,'huc_parent'=>0);
                        $pic         = qrcode_arr($content);
                        $where       = array(
                                'hucid'      =>$va['hucid'],
                                'huc_codepic'=>$pic
                        );
                        $code        = M('HracUsercoupon')->save($where);
                    }   
                }
                // p($numhee);
                if($code){
                    D('HracInformation')->add($foarr);
                    $data['status'] = 1;
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($data);
                }
            }else{
                $data['status'] = 2;
                $this->ajaxreturn($data);                
            }
        }    
    }
    /**
    清空消息
    **/
    public function clearinfomation(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hicid = I('post.hicid');
            // $hicid = 1;
            // $huid  = 8;
            $huid  = I('post.huid');
            $infoma= D('HracInformation')->where(array('hicid'=>$hicid,'huid'=>$huid))->select();
            // p($infoma);die;
            foreach ($infoma as $key => $value) {
                $del = D('HracInformation')->where(array('hiid'=>$value['hiid']))->delete();
            }
            if($del){
                $data['status'] = 1;
                $this->ajaxreturn($data); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取被预约时间
    **/
    public function allbook(){
        header('content-type:application:json;charset=utf8');  
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:POST');  
        header('Access-Control-Allow-Methods:GET');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid       = I('post.huid');
            // $huid       = 0;
            $sid        = I('post.sid'); 
            // $sid        = 1; 
            if($huid==0){
                $hdidarr= D('HracDocter')->where(array('sid'=>$sid))->select();
                //去除等级为0
                foreach ($hdidarr as $key => $value) {
                    if($value['hgid']!=0){
                        $hdid[$key] = $value['hdid'];
                    }
                }
                foreach ($hdid as $key => $value) {
                    $booktmp[$key][] = D('HracBook')->where(array('hdid'=>$value))->select();
                }
                //降维
                foreach ($booktmp as $key => $value) {
                    foreach ($value as $ke=> $va) {
                        foreach ($va as $k => $v) {
                            $albotime[] = $v;
                        }
                    }
                }
                foreach ($albotime as $key => $value) {
                    $alltime[$key]['hb_date']      = $value['hb_date'];
                    $alltime[$key]['hdid']         = $value['hdid'];
                    $alltime[$key]['sid']          = $value['sid'];
                    $alltime[$key]['hb_starttime'] = $value['hb_starttime'];
                    $alltime[$key]['hb_endtime']   = $value['hb_endtime'];
                }
                // p($albotime);die;
                //订单
                $receipt    = D('HracReceipt')->where(array('sid'=>$sid))->select();
                //连表查询订单和服务
                $proceipt   = D('HracReceipt')
                            ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                            ->where(array('sid'=>$sid,'hr_status'=>0))
                            ->select();
            }else{
                $phone      = D('HracUsers')
                            ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                            ->where(array('huid'=>$huid))
                            ->getfield('hu_nickname');
                $hdid       = D('HracDocter')->where(array('hd_name'=>$phone))->getfield('hdid');
                $albotime   = D('HracBook')->where(array('hdid'=>$hdid))->select();
                foreach ($albotime as $key => $value) {
                    $alltime[$key]['hb_date']      = $value['hb_date'];
                    $alltime[$key]['hdid']         = $value['hdid'];
                    $alltime[$key]['sid']          = $value['sid'];
                    $alltime[$key]['hb_starttime'] = $value['hb_starttime'];
                    $alltime[$key]['hb_endtime']   = $value['hb_endtime'];
                }
                //订单
                $receipt    = D('HracReceipt')->where(array('hdid'=>$hdid))->select();
                //连表查询订单和服务
                $proceipt   = D('HracReceipt')
                            ->join('nulife_hrac_project on nulife_hrac_receipt.hpid = nulife_hrac_project.hpid')
                            ->where(array('hdid'=>$hdid,'hr_status'=>0))
                            ->select();
            }
            // 将hbid作为键值
            foreach ($albotime as $key => $value) {
                $bookarr[$value['hbid']] = $value;
            }
            //未预约时间
            foreach ($bookarr as $key => $value) {
                if($value['is_booking']==0){
                    $allbooknow[$key]      = $value;
                }
            }
            foreach ($allbooknow as $key => $value) {
                $book[0][$key]['hb_date']      = $value['hb_date'];
                $book[0][$key]['hdid']         = $value['hdid'];
                $book[0][$key]['sid']          = $value['sid'];
                $book[0][$key]['hb_starttime'] = $value['hb_starttime'];
                $book[0][$key]['hb_endtime']   = $value['hb_endtime'];
                $book[0][$key]['is_booking']   = $value['is_booking'];  
            }
            // p($book[0]);die;
            //已预约时间
            foreach ($bookarr as $key => $value) {
                if($value['is_booking']==1){
                    $nobook[$key]      = $value;
                }
            }
            // p($nobook);die;
            //已完成时间
            foreach ($receipt as $key => $value) {
                if($value['hr_status']==5){
                    $ceipt1[] = $value; 
                }
            }
            // p($receipt);die;
            //其他状态
            foreach ($receipt as $key => $value) {
                if($value['hr_status']!=0 && $value['hr_status']!=1 && $value['hr_status']!=5){
                    $ceipt2[] = $value; 
                }
            }
            // p($ceipt2);die;
            //已完成数据操作
            foreach ($ceipt1 as $key => $value) {
                $bookroom[$key][0]['hb_date']    = $value['hr_stardate'];
                $bookroom[$key][0]['hdid']       = $value['hdid'];
                $bookroom[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $bookroom[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            foreach ($bookroom as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $booking[$key][$ke][$k] = $value[0];
                            $booking[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $booking[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }
            // p($booking);die; 
            foreach ($booking as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $bookreceipt[] = $v;
                    }
                }
            }
            //订单已完成时间去除is_booking
            foreach ($bookreceipt as $key => $value) {
                $noreceipt[$key]['hb_date']      = $value['hb_date'];
                $noreceipt[$key]['hdid']         = $value['hdid'];
                $noreceipt[$key]['sid']          = $value['sid'];
                $noreceipt[$key]['hb_starttime'] = $value['hb_starttime'];
                $noreceipt[$key]['hb_endtime']   = $value['hb_endtime'];
            }
            //取差集，订单时间在排班中没有的时间
            foreach ($noreceipt as $key => $value) {
                if(!in_array($value,$alltime)){
                    $noallbook[$key]=$value;
                }
            }
            // p($noallbook); 
            //将已完成不存在时间is_booking变为2
            if($noallbook){
                foreach ($noallbook as $key => $value) {
                    $book[1][$key]              = $value;
                    $book[1][$key]['is_booking']= '2';
                }  
            }
            // p($book[1]);die;
            //查询已完成的数据
            foreach ($noreceipt as $key => $value) {
                $isbook[] = D('HracBook')->where($value)->find();
            }
            if($isbook[0]){
                //已完成将hbid作为键值
                foreach ($isbook as $key => $value) {
                    $isbooking[$value['hbid']] = $value;
                }
                //将已完成时间的is_booking变为2
                foreach ($isbooking as $key => $value) {
                    $book[2][$key]['hb_date']      = $value['hb_date'];
                    $book[2][$key]['hdid']         = $value['hdid'];
                    $book[2][$key]['sid']          = $value['sid'];
                    $book[2][$key]['hb_starttime'] = $value['hb_starttime'];
                    $book[2][$key]['hb_endtime']   = $value['hb_endtime'];
                    $book[2][$key]['is_booking']   = '2';
                }
            }
            // p($isbook);die; 
            //其他状态
            foreach ($ceipt2 as $key => $value) {
                $room[$key][0]['hb_date']    = $value['hr_stardate'];
                $room[$key][0]['hdid']       = $value['hdid'];
                $room[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $room[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            foreach ($room as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $king[$key][$ke][$k] = $value[0];
                            $king[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $king[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }               
            foreach ($king as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $bookceipt[] = $v;
                    }
                }
            }
            //订单其他状态时间去除is_booking
            foreach ($bookceipt as $key => $value) {
                $moreceipt[$key]['hb_date']      = $value['hb_date'];
                $moreceipt[$key]['hdid']         = $value['hdid'];
                $moreceipt[$key]['sid']          = $value['sid'];
                $moreceipt[$key]['hb_starttime'] = $value['hb_starttime'];
                $moreceipt[$key]['hb_endtime']   = $value['hb_endtime'];
            }
            //取差集，订单时间在排班中没有的时间
            foreach ($moreceipt as $key => $value) {
                if(!in_array($value,$alltime)){
                    $moreallbook[$key]=$value;
                }
            }
            // p($noallbook); 
            //将其他状态不存在时间is_booking变为2
            if($moreallbook){
                foreach ($moreallbook as $key => $value) {
                    $book[3][$key]              = $value;
                    $book[3][$key]['is_booking']= '3';
                }
            }
            // p($book[3]);die;
            //查询其他状态的数据
            foreach ($moreceipt as $key => $value) {
                $is_book[] = D('HracBook')->where($value)->find();
            }
            if($is_book[0]){
                //其他状态将hbid作为键值
                foreach ($is_book as $key => $value) {
                    $is_booking[$value['hbid']] = $value;
                }
                //将其他状态其他状态时间的is_booking变为3
                foreach ($is_booking as $key => $value) {
                    $book[4][$key]['hb_date']      = $value['hb_date'];
                    $book[4][$key]['hdid']         = $value['hdid'];
                    $book[4][$key]['sid']          = $value['sid'];
                    $book[4][$key]['hb_starttime'] = $value['hb_starttime'];
                    $book[4][$key]['hb_endtime']   = $value['hb_endtime'];
                    $book[4][$key]['is_booking']   = '3';
                }
            }
            // p($allceipt);
            //未处理订单
            foreach ($proceipt as $key => $value) {
                if(strpos($value['hp_name'],'RES')){
                    $pro[$key][0]['is_booking']='1';
                }else if(strpos($value['hp_name'],'HRA101')){
                    $pro[$key][0]['is_booking']='4';
                }else if(strpos($value['hp_name'],'DNA')){
                    $pro[$key][0]['is_booking']='5';
                }else{
                    $pro[$key][0]['is_booking']='6';
                }
                $pro[$key][0]['hb_date']    = $value['hr_stardate'];
                $pro[$key][0]['hdid']       = $value['hdid'];
                $pro[$key][0]['sid']        = $value['sid'];
                $number = (strtotime($value['hr_endservice'])-strtotime($value['hr_statrservice']))/1800;
                $pro[$key][1] = cut_apart_time($value['hr_statrservice'], $value['hr_endservice'], $number, $format=true);     
            }
            //重构
            foreach ($pro as $key => $value) {
                foreach ($value as $ke => $va) {
                    if($ke==1){
                        foreach ($va as $k => $v) {
                            $prok[$key][$ke][$k] = $value[0];
                            $prok[$key][$ke][$k]['hb_starttime'] = $v[0];
                            $prok[$key][$ke][$k]['hb_endtime']   = $v[1];
                        }
                    }
                }
            }           
            //降维
            foreach ($prok as $key => $value) {
                foreach ($value as $ke => $va) {
                    foreach ($va as $k => $v) {
                        $proking[] = $v;
                    }
                }
            }
            $book[5] = $proking;
            // p($book[5]);die;
            foreach ($book as $key => $value) {
                foreach ($value as $k => $v) {
                    if(!empty($v['hb_starttime'])){
                        $booktime[] = $v;
                    }
                }
            }
            // p($booktime);
            if($booktime){
                $this->ajaxreturn($booktime);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }

    /**
    vip下级
    **/
    public function viptree(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid     = I('post.huid');
            $number   = I('post.number');
            // $huid     = 1;
            // $number   = 3;
            $tree[0]  = M('HracUsers')
                      ->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                      ->where(array('huid'=>$huid))
                      ->select();
            $vip      = M('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')
                      ->select();
            $treearr  = subtree($vip,$huid,$lev=1);
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
                $partner[$key]['key']    = $value['huid'];
                $partner[$key]['parent'] = $value['hu_hpid'];
                $partner[$key]['name']   = $value['hu_username'];
                $partner[$key]['source'] = $value['hu_photo'];
            }
            if($partner){
               $this->ajaxreturn($partner);   
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);               
            }
        }
    }
    /**
    树的最大长度
    **/
    public function length(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid      = I('post.huid');
            // $huid       = 11;
            $vip       = M('HracUsers')->select();
            $treearr   = subtree($vip,$huid,$lev=1);
            if(!$treearr){
                $ev    = 1;
            }else{   
                $count = count($treearr)-1;
                $tree  = array_sort($treearr,'lev','asc');
                foreach ($tree as $key => $value) {
                    $status[] = $value;
                }
                $ev    = $status[$count]['lev']+1;
            }
            $len['length']=$ev;
            // p($len);die;
            if($len){
                $this->ajaxreturn($len);   
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);               
            }
        }
    }
    /**
    查询是否有该账号
    **/
    public function stay(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $nick = I('post.nick');
            // $nick = 'CN20170002';
            $user = M('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('hu_nickname'=>$nick))
                  ->find();
            if($user){
                $data['status'] = 1;
                $this->ajaxreturn($data); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    设置用户上下级
    **/
    public function setlevel(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //上级账号
            $supe = I('post.supe');
            // $supe = 'CN20170011';
            $pid  = M('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('hu_nickname'=>$supe))
                  ->getfield('huid');
            //下级账号
            $subor= I('post.subor');
            // $subor= 'CN20170012';
            $huid = M('HracUsers')
                  ->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')
                  ->where(array('hu_nickname'=>$subor))
                  ->getfield('huid');
            $where=array(
                'huid'    =>$huid,
                'hu_hpid' =>$pid
            );
            $save = M('HracUsers')->save($where);
            if($save){
                $data['status'] = 1;
                $this->ajaxreturn($data);    
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);               
            }
        }
    }  
    /**
    合伙人奖励
    **/
    public function paraward(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 用户id
            $huid = I('post.huid');
            $part = D('HracPartner')->where(array('pid'=>$huid,'is_vaild'=>1))->select();
            $user = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                  ->where(array('huid'=>$huid))->find();
            $count= count($part);
            $bill = D('HracBills')->where(array('name'=>$user['hu_nickname'],'hbi_type'=>1))->select();
            //总金额
            if($bill){
                foreach ($bill as $key => $value) {
                    $totalnum += $value['hbi_sum'];
                }    
            }else{
                $totalnum = 0;
            }
            if($count>=15){
                $addnum = 300000;
            }else{
                $addnum  = 0;
            }
            $addit   = number_format($addnum);
            $total   = number_format($totalnum);
            $pacific = number_format($totalnum-$addnum); 
            $partner = array(
                'count'   => $count,
                'pacific' => $pacific,
                'addit'   => $addit,
                'total'   => $total,
            ); 
            // p($partner);
            if($partner){
                $this->ajaxreturn($partner);
            }else{
                $this->ajaxreturn($partner);
            }   
        }
    }  
    /**
    用券奖励
    **/
    public function couaward(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 用户id
            $huid = I('post.huid');
            // $huid = 1;
            $user = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                  ->where(array('huid'=>$huid))->find();
            $bill = D('HracBills')->where(array('name'=>$user['hu_nickname'],'hbi_type'=>2))->select();
            //取出hucid不為0的
            foreach ($bill as $key => $value) {
                if($value['hucid']!=0){
                    $countlist[] = $value;
                }
            }
            // p($countlist);die;
            $count = count($countlist);
            //总金额
            if($bill){
                foreach ($bill as $key => $value) {
                    $totalnum += $value['hbi_sum'];
                    if($value['hbi_sum']==3000){
                        $t = 3000;
                    }
                }    
            }else{
                $totalnum = 0;
                $t        = 0;
            }
            $additnum = $t;
            $total   = number_format($totalnum);
            $addit   = number_format($additnum);
            $pacific = number_format($totalnum-$additnum); 
            $partner = array(
                'count'   => $count,
                'pacific' => $pacific,
                'addit'   => $addit,
                'total'   => $total,
            );
            // p($partner);
            if($partner){
                $this->ajaxreturn($partner);
            }else{
                $this->ajaxreturn($partner);
            }   
        }
    } 
    /**
    用券奖励详情
    **/
    public function couawardinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            // 用户id
            $huid = I('post.huid');
            $name = D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')->where(array('huid'=>$huid))->getfield('hu_nickname');
            $receipt = D('HracBills')
                     ->join('nulife_hrac_usercoupon on nulife_hrac_bills.hucid  = nulife_hrac_usercoupon.hucid')
                     ->join('nulife_hrac_receipt on nulife_hrac_bills.hucid  = nulife_hrac_receipt.hucid')
                     ->where(array('nulife_hrac_bills.name'=>$name))
                     ->select();
            foreach ($receipt as $key => $value) {
                $status[$key]['hp_abb']     = $value['hp_abb'];
                $status[$key]['huc_number'] = $value['huc_number'];
                $status[$key]['hc_type']    = $value['hc_type'];
                $status[$key]['hu_username']= $value['hr_nickname'];
                $status[$key]['identity']   = $value['identity'];
                $status[$key]['hr_cretime'] = $value['hbi_time'];
                $status[$key]['hbi_sum']    = number_format($value['hbi_sum']);
            }
            $news = array_sort($status,'hr_cretime','desc');
            if($news){
                $this->ajaxreturn($news);
            }else{
                $this->ajaxreturn($news);
            }   
        }
    } 
    /**
    一级文件夹
    **/
    public function filelist(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $data=D('HracFile')->where(array('pid'=>0))->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    获取文件夹/文件
    **/
    public function getfilelist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $id=I('post.id');
            // $id=17;
            $data=D('HracFile')->where(array('pid'=>$id))->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    获取文件详情
    **/ 
    public function fileinfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $id=I('post.id');
            $data=D('HracFile')->where(array('id'=>$id))->find();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    通过项目获取优惠券类
    **/
    public function coupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hpid=I('post.hpid');
            // $hpid=21;
            if($hpid==0){
                $data=D('HracCoupon')->select();
            }else{
                $data=D('HracCoupon')->where(array('hpid'=>$hpid))->select();
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    通过项目获取优惠券并修改字段
    **/
    public function postcoupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hpid= I('post.hpid');
            $coup=D('HracCoupon')->where(array('hpid'=>$hpid))->select();
            foreach ($coup as $key => $value) {
                $data[$key]['hpid']    = $value['hcid'];
                $data[$key]['hpcid']   = 0;
                $data[$key]['hp_name'] = $value['hc_name'];
                $data[$key]['hp_pic']  = $value['hc_pic4'];
                $data[$key]['hp_money']= $value['hc_money'];
                $data[$key]['hp_point']= $value['hc_point'];
                $data[$key]['hp_desc'] = $value['hc_desc'];
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    获取使用券数量
    **/
    public function usecoupon(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hpid=I('post.hpid');
            $hcid=I('post.hcid');
            $page=I('post.page')*12;
            //所有的订单
            $receipt=D('HracReceipt')->where(array('hr_status'=>5))->select();
            //取出所有订单的日期
            foreach ($receipt as $key => $value) {
                $receiptdate[$key] = date('Y年m月',strtotime($value['hr_stardate']));
            }
            //去除相同值
            $ceipt = array_unique($receiptdate);
            //排序
            asort($ceipt);
            foreach ($ceipt as $key => $value) {
                $ceiptarr[] = $value;
            }
            if($hpid==0){
                if($hcid==0){
                    $tmpe=D('HracReceipt')->where(array('hr_status'=>5))->select();
                    foreach ($tmpe as $key => $value) {
                        if($value['hcid']!=0){
                            $data[]=$value;
                        }
                    }
                }else{
                    $data=D('HracReceipt')->where(array('hcid'=>$hcid,'hr_status'=>5))->select();
                }
            }else{
                if($hcid==0){
                    $tmpe=D('HracReceipt')->where(array('hpid'=>$hpid,'hr_status'=>5))->select();
                    foreach ($tmpe as $key => $value) {
                        if($value['hcid']!=0){
                            $data[]=$value;
                         }
                    }
                }else{
                    $data=D('HracReceipt')->where(array('hpid'=>$hpid,'hcid'=>$hcid,'hr_status'=>5))->select();
                }
            }
            //重构订单
            foreach ($data as $key => $value) {
                $hrdate[$key]['hr_stardate'] = date('Y年m月',strtotime($value['hr_stardate']));
                $hrdate[$key]['hrid']        = $value['hrid'];
            }
            // p($hrdate);
            //取总数
            if(!$hrdate){
                $statusarr['total']  = 0;
                $statusarr['content']= array();
                $statusarr['count']  = 0; 
            }else{
                $statusarr['total']  = count($hrdate);     
                foreach ($ceiptarr as $key => $value) {
                    foreach ($hrdate as $ke => $va) {
                        if($value == $va['hr_stardate']){
                            $stardate[$key][] = $va;
                        }
                    }
                }
                foreach ($stardate as $key => $value) {
                    $count = count($value);
                    foreach ($value as $ke => $va) {
                        $statustmp[$key]['num'] = $count;
                        $statustmp[$key]['date']= $va['hr_stardate'];
                    }
                }
                // p($status);
                foreach ($statustmp as $key => $value) {
                    $status[] = $value;
                }
                //取各月份数量
                foreach ($status as $key => $value) {
                    $statusnum[$key] = $value['num'];
                }
                //目前总数量等于上个月加这个月的数量
                $num  = $statusnum[0];
                $leng = count($statusnum);
                for($i=0;$i<$leng;$i++){
                    $level[$i] = $num;
                    $num +=$statusnum[$i+1];
                }
                // p($statusnum);
                foreach ($status as $key => $value) {
                    $statusarray[$key]          = $value;
                    $statusarray[$key]['total'] = $level[$key];
                }
                //取出键值
                foreach ($statusarray as $key => $value) {
                    $keyarr[] = $key;
                }
                rsort($keyarr);
                foreach ($keyarr as $key => $value) {
                    foreach ($statusarray as $k => $v) {
                        if($value == $k){
                            $statusarrtmp[$key] = $v;
                        }
                    }
                }
                $statusarr['count']  = count($statusarrtmp);
                foreach ($statusarrtmp as $key => $value) {
                    if($key<$page){
                        $statusarr['content'][$key] = $value;
                    }
                }
            }
            if($statusarr){
                $this->ajaxreturn($statusarr);
            }else{
                $this->ajaxreturn($statusarr);
            }
        }
    } 
    /**
    获取产品分类
    **/
    public function category(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $status = I('post.status');
            if($status==1){
                $map = array(
                    'pid'       =>0
                );
                $catList = M('HracCategory')
                         ->where($map)
                         ->order('order_number asc')
                         ->select();
                //重构数组
                foreach($catList as $key => $value){
                    $tmp = M('HracCategory')
                         ->where(array('pid'=>$value['id']))
                         ->select();
                    $arr[$value['id']]['hcat_name'] = $value['hcat_name'];
                    $arr[$value['id']]['content']   = $tmp;
                }
            }else{
                $map = array(
                    'pid'       =>0,
                    'is_show'   =>1
                );
                $catList = M('HracCategory')
                         ->where($map)
                         ->order('order_number asc')
                         ->select();
                //重构数组
                foreach($catList as $key => $value){
                    $tmp = M('HracCategory')
                         ->where(array('pid'=>$value['id']))
                         ->where(array('is_show'=>1))
                         ->select();
                    $arr[$value['id']]['hcat_name'] = $value['hcat_name'];
                    $arr[$value['id']]['content']   = $tmp;
                }
            }
            //转索引数组
            foreach ($arr as $key => $value) {
                $data[] = $value;
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    通过产品分类获取产品/服务
    **/
    public function product(){
        if(!IS_POST){
            $data['status']=0;
            $this->ajaxreturn($data);
        }else{
            $id   = I('post.id');
            $type = I('post.type');
            if($type==0){
                $coup =D('HracCoupon')->where(array('id'=>$id))->order('order asc')->select();
                foreach ($coup as $key => $value) {
                    $data[$key]['hpid']    = $value['hcid'];
                    $data[$key]['id']      = $value['id'];
                    $data[$key]['hpcid']   = 0;
                    $data[$key]['hp_name'] = $value['hc_name'];
                    $data[$key]['hp_pic']  = $value['hc_pic4'];
                    $data[$key]['hp_money']= $value['hc_money'];
                    $data[$key]['hp_point']= $value['hc_point'];
                    $data[$key]['hp_desc'] = $value['hc_desc'];
                }
            }else{
                $data =D('HracProject')->where(array('id'=>$id))->order('sort asc')->select();
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    }      
    /**
    获取服务分类
    **/
    public function proclass(){
        if(IS_GET){
            $data=D('HracProjectclass')->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    通过分类获取服务
    **/
    public function postproject(){
        if(!IS_POST){
            $data['status']=0;
            $this->ajaxreturn($data);
        }else{
            // $hpcid=1;
            $hpcid=I('post.hpcid');
            if($hpcid==0){
                $map['hpcid'] = array('NEQ',0);
                $data =D('HracProject')->where($map)->select(); 
            }else{
                $data =D('HracProject')->where(array('hpcid'=>$hpcid))->select();               
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    计算每月服务数量
    **/
    public function useproject(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hpcid=I('post.hpcid');
            $hpid =I('post.hpid');
            $page =I('post.page')*12;
            //所有的订单
            $receipt=D('HracReceipt')->where(array('hr_status'=>5))->select();
            //取出所有订单的日期
            foreach ($receipt as $key => $value) {
                $receiptdate[$key] = date('Y年m月',strtotime($value['hr_stardate']));
            }
            //去除相同值
            $ceipt = array_unique($receiptdate);
            //排序
            asort($ceipt);
            foreach ($ceipt as $key => $value) {
                $ceiptarr[] = $value;
            }
            // p($ceipt);
            if($hpcid==0){
                if($hpid==0){
                    $hparr=D('HracProject')->getfield('hpid',true);
                    foreach ($hparr as $key => $value) {
                        $arr[$key] =D('HracReceipt')->where(array('hpid'=>$value,'hr_status'=>5))->select();
                    }
                    foreach ($arr as $key => $value) {
                        foreach ($value as $k => $v) {
                            $data[] = $v;
                        }
                    }
                }else{
                    $data = D('HracReceipt')->where(array('hpid'=>$hpid,'hr_status'=>5))->select();
                }
            }else{
                if($hpid==0){
                    $hparr=D('HracProject')->where(array('hpcid'=>$hpcid))->getfield('hpid',true);
                    foreach ($hparr as $key => $value) {
                        $arr[$key] =D('HracReceipt')->where(array('hpid'=>$value,'hr_status'=>5))->select();
                    }
                    foreach ($arr as $key => $value) {
                        foreach ($value as $k => $v) {
                            $data[] = $v;
                        }
                    }
                }else{
                    $data = D('HracReceipt')->where(array('hpid'=>$hpid,'hr_status'=>5))->select();
                }
            }
            // p($data);die;
            //重构订单
            foreach ($data as $key => $value) {
                $hrdate[$key]['hr_stardate'] = date('Y年m月',strtotime($value['hr_stardate']));
                $hrdate[$key]['hrid']        = $value['hrid'];
            }
            // p($hrdate);
            //取总数
            if(!$hrdate){
                $statusarr['count']  = count($statusarray);
                $statusarr['total']  = 0;
                $statusarr['content']= array(); 
            }else{
                $statusarr['total']  = count($hrdate);     
                foreach ($ceiptarr as $key => $value) {
                    foreach ($hrdate as $ke => $va) {
                        if($value == $va['hr_stardate']){
                            $stardate[$key][] = $va;
                        }
                    }
                }
                foreach ($stardate as $key => $value) {
                    $count = count($value);
                    foreach ($value as $ke => $va) {
                        $statustmp[$key]['num'] = $count;
                        $statustmp[$key]['date']= $va['hr_stardate'];
                    }
                }
                foreach ($statustmp as $key => $value) {
                    $status[] = $value;
                }
                // p($status);
                //取各月份数量
                foreach ($status as $key => $value) {
                    $statusnum[$key] = $value['num'];
                }
                //目前总数量等于上个月加这个月的数量
                $num  = $statusnum[0];
                $leng = count($statusnum);
                for($i=0;$i<$leng;$i++){
                    $level[$i] = $num;
                    $num +=$statusnum[$i+1];
                }
                // p($level);
                foreach ($status as $key => $value) {
                    $statusarray[$key]          = $value;
                    $statusarray[$key]['total'] = $level[$key];
                }
                //取出键值
                foreach ($statusarray as $key => $value) {
                    $keyarr[] = $key;
                }
                rsort($keyarr);
                foreach ($keyarr as $key => $value) {
                    foreach ($statusarray as $k => $v) {
                        if($value == $k){
                            $statusarrtmp[$key] = $v;
                        }
                    }
                }
                $statusarr['count']  = count($statusarrtmp);
                foreach ($statusarrtmp as $key => $value) {
                    if($key<$page){
                        $statusarr['content'][$key] = $value;
                    }
                }
            }
            if($statusarr){
                $this->ajaxreturn($statusarr);
            }else{
                $this->ajaxreturn($statusarr);
            }
        }
    } 
    /**
    每月合伙人数量
    **/
    public function partner(){
        if(IS_POST){
            $page = I('post.page')*12;
            //所有的成为合伙人时间
            $time = D('HracPartner')->getfield('hpr_time',true);
            foreach ($time as $key => $value) {
                $date[] = date('Y年m月',strtotime($value));
            }
            //去除相同值
            $date1= array_unique($date);
            asort($date1);
            foreach ($date1 as $key => $value) {
                $datetime[] = $value;
            }
            $data = D('HracPartner')->where(array('is_vaild'=>1))->select();
            foreach ($data as $key => $value) {
                $hrdate[$key]['stardate'] = date('Y年m月',strtotime($value['hpr_time']));
                $hrdate[$key]['hprid']        = $value['hprid'];
            }
            // p($hrdate);die;
            if(!$hrdate){
                $statusarr['total']  = 0;
                $statusarr['content']= array(); 
            }else{
                $statusarr['total']  = count($hrdate);     
                foreach ($datetime as $key => $value) {
                    foreach ($hrdate as $ke => $va) {
                        if($value == $va['stardate']){
                            $stardate[$key][] = $va;
                        }
                    }
                }
                // p($stardate);
                foreach ($stardate as $key => $value) {
                    $count = count($value);
                    foreach ($value as $ke => $va) {
                        $statustmp[$key]['num'] = $count;
                        $statustmp[$key]['date']= $va['stardate'];
                    }
                }
                foreach ($statustmp as $key => $value) {
                    $status[] = $value;
                }
                //取各月份数量
                foreach ($status as $key => $value) {
                    $statusnum[$key] = $value['num'];
                }
                //目前总数量等于上个月加这个月的数量
                $num  = $statusnum[0];
                $leng = count($statusnum);
                for($i=0;$i<$leng;$i++){
                    $level[$i] = $num;
                    $num +=$statusnum[$i+1];
                }
                // p($level);
                foreach ($status as $key => $value) {
                    $statusarray[$key]          = $value;
                    $statusarray[$key]['total'] = $level[$key];
                }
                //取出键值
                foreach ($statusarray as $key => $value) {
                    $keyarr[] = $key;
                }
                rsort($keyarr);
                foreach ($keyarr as $key => $value) {
                    foreach ($statusarray as $k => $v) {
                        if($value == $k){
                            $statusarrtmp[$key] = $v;
                        }
                    }
                }
                $statusarr['count']  = count($statusarrtmp);
                foreach ($statusarrtmp as $key => $value) {
                    if($key<$page){
                        $statusarr['content'][$key] = $value;
                    }
                }
                if($statusarr){
                    $this->ajaxreturn($statusarr);
                }else{
                    $this->ajaxreturn($statusarr);
                }  
            }
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }              
    }
    /**
    通过服务获取等级
    **/
    public function postgrade(){
        if(IS_POST){
            $project = I('post.hpid');
            //根据服务项目推荐相应等级的医护人员
            $level   = D('HracProject')->where(array('hpid'=>$project))->getfield('hp_level');
            $hp_level=explode(',',$level);
            foreach ($hp_level as $key => $value) {
                $grade[]=D('HracGrade')->where(array('hgid'=>$value))->find();
            }
            if($grade){
                $this->ajaxreturn($grade);
            }else{
                $data['status']=0;
                $this->ajaxreturn($grade);
            }
        }else{
            $data['status']=0;
            $this->ajaxreturn($data);
        }
    }
    /**
    营养师等级
    **/
    public function grade(){
        if(IS_GET){
            $data=D('HracGrade')->select();
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    所有营养师
    **/
    public function alldocter(){
        if(!IS_POST){
            $data['status']=0;
            $this->ajaxreturn($data);
        }else{
            $hgid    =I('post.hgid');
            $paraFour=(string)I('post.paraFour');
            // $hgid=0;
            // $paraFour='Jancy Chung';
            if($paraFour==(string)0){
                if($hgid==0){
                    $map['hgid'] = array('gt',0);
                    $arry=D('HracDocter')
                         ->join('nulife_ibos_users on nulife_ibos_users.hu_nickname = nulife_hrac_docter.hd_name')
                         ->where($map)
                         ->select();
                }else{
                    $arry=D('HracDocter')
                         ->join('nulife_ibos_users on nulife_ibos_users.hu_nickname = nulife_hrac_docter.hd_name')
                         ->where(array('nulife_hrac_docter.hgid'=>$hgid))
                         ->select();
                }
                // echo 111;
            }else{
                // echo 123;
                if($hgid==0){
                    $map['hgid'] = array('gt',0);
                    $arry=D('HracDocter')
                         ->join('nulife_ibos_users on nulife_ibos_users.hu_nickname = nulife_hrac_docter.hd_name')
                         ->where($map)
                         ->where(array('hu_username'=>$paraFour))
                         ->select();
                }else{
                    $arry=D('HracDocter')
                         ->join('nulife_ibos_users on nulife_ibos_users.hu_nickname = nulife_hrac_docter.hd_name')
                         ->where(array('nulife_hrac_docter.hgid'=>$hgid,'hu_username'=>$paraFour))
                         ->select();
                }
            }
            
            foreach ($arry as $key => $value) {
                $data[$key]['name'] = $value['hu_username'];
                $data[$key]['hdid'] = $value['hdid'];
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status']=0;
                $this->ajaxreturn($data);
            }
        }
    } 
    /**
    套餐/服务=百分比
    **/
    public function comborate(){
        if(!IS_POST){
            $data['status']=0;
            $this->ajaxreturn($data);
        }else{
            $hgid =I('post.hgid');
            // $hgid=0;
            $hdid =I('post.hdid');
            // $hdid=0;
            $page =I('post.page')*12;
            //所有的订单
            $receipt=D('HracReceipt')->where(array('hr_status'=>5))->select();
            //取出所有订单的日期
            foreach ($receipt as $key => $value) {
                $receiptdate[$key] = date('Y年m月',strtotime($value['hr_stardate']));
            }
            //去除相同值
            $ceipt = array_unique($receiptdate);
            //排序
            asort($ceipt);
            foreach ($ceipt as $key => $value) {
                $ceiptarr[] = $value;
            }
            // p($ceipt);
            if($hgid==0){
                if($hdid==0){
                    $data1 = $receipt;
                }else{
                    $data1 = D('HracReceipt')->where(array('hr_status'=>5,'hdid'=>$hdid))->select();
                }
            }else{
                if($hdid==0){
                    $arr   = D('HracDocter')->where(array('hgid'=>$hgid))->getfield('hdid',true);
                    foreach ($arr as $key => $value) {
                        $data[] = D('HracReceipt')->where(array('hr_status'=>5,'hdid'=>$value))->select();
                    }
                    foreach ($data as $key => $value) {
                        foreach ($value as $k => $v) {
                            $data1[] = $v;
                        }
                    }
                }else{
                    $data1 = D('HracReceipt')->where(array('hr_status'=>5,'hdid'=>$hdid))->select();
                }
            }
            // p($data1);
            if(!$data1){
                $statusarr['count']  = 0;
                $statusarr['content']= array();                 
            }else{
                foreach ($data1 as $key => $value) {
                    if($value['is_product']==1){
                        $data2[] = $value;
                    }
                }                
                // p($data2);die;
                //重构所有完成订单
                foreach ($data1 as $key => $value) {
                    $hrdate1[$key]['date'] = date('Y年m月',strtotime($value['hr_stardate']));
                    $hrdate1[$key]['hrid'] = $value['hrid'];
                }
                foreach ($ceiptarr as $key => $value) {
                    foreach ($hrdate1 as $ke => $va) {
                        if($value == $va['date']){
                            $stardate1[$key][] = $va;
                        }
                    }
                }
                //取完成的服务数量
                foreach ($stardate1 as $key => $value) {
                    $count1 = count($value);
                    foreach ($value as $ke => $va) {
                        $statustmp1[$key]['project'] = $count1;
                        $statustmp1[$key]['date']= $va['date'];
                    }
                }
                // p($statustmp1);
                if(!$data2){
                    foreach ($statustmp1 as $key => $value) {
                        $statusarr[$key]['date']    = $value['date'];
                        $statusarr[$key]['project'] = $value['project'];
                        $statusarr[$key]['product'] = 0;
                        $statusarr[$key]['rate']    = (0).'%';
                    }
                }else{
                    //重构所有完成订单(购买产品)
                    foreach ($data2 as $key => $value) {
                        $hrdate2[$key]['date'] = date('Y年m月',strtotime($value['hr_stardate']));
                        $hrdate2[$key]['hrid'] = $value['hrid'];
                    }  
                    foreach ($ceiptarr as $key => $value) {
                        foreach ($hrdate2 as $ke => $va) {
                            if($value == $va['date']){
                                $stardate2[$key][] = $va;
                            }
                        }
                    }
                    //取完成的服务数量(购买产品)
                    foreach ($stardate2 as $key => $value) {
                        $count2 = count($value);
                        foreach ($value as $ke => $va) {
                            $statustmp2[$key]['product'] = $count2;
                            $statustmp2[$key]['date']= $va['date'];
                        }
                    }
                    // p($statustmp2);
                    //有买产品概率
                    foreach ($statustmp1 as $key => $value) {
                        foreach ($statustmp2 as $ke => $va) {
                            if($value['date']==$va['date']){
                                $status[0][$key]['date']    = $value['date'];
                                $status[0][$key]['project'] = $value['project'];
                                $status[0][$key]['product'] = $va['product'];
                                $status[0][$key]['rate']    = (sprintf("%.4f",($va['product']/$value['project']))*100).'%'; 
                            }
                        }
                    }
                    //木有买产品概率
                    //1 差集条件
                    foreach ($stardate1 as $key => $value) {
                        foreach ($value as $ke => $va) {
                            $tmp1[$key]['date']= $va['date'];
                        }
                    }
                    foreach ($stardate2 as $key => $value) {
                        foreach ($value as $ke => $va) {
                            $tmp2[$key]['date']= $va['date'];
                        }
                    }
                    //2 取差集
                    foreach ($tmp1 as $key => $value) {
                        if(!in_array($value,$tmp2)){
                            $noall[$key]=$value;
                        }
                    }
                    if($noall){
                        //3重构
                        foreach ($noall as $key => $value) {
                            $noallarr[] = $value['date']; 
                        }
                        //4取出当月空产品
                        foreach ($noallarr as $key => $value) {
                            foreach ($statustmp1 as $k => $v) {
                                if($value==$v['date']){
                                    $status[1][$key]['date']    = $v['date'];
                                    $status[1][$key]['project'] = $v['project'];
                                    $status[1][$key]['product'] = 0;
                                    $status[1][$key]['rate']    = (0).'%'; 
                                }
                            }
                        }
                    }   
                }
                foreach ($status as $key => $value) {
                    foreach ($value as $k => $v) {
                        $statusarray[] = $v;
                    }
                }
                //取出键值
                foreach ($statusarray as $key => $value) {
                    $keyarr[] = $key;
                }
                rsort($keyarr);
                foreach ($keyarr as $key => $value) {
                    foreach ($statusarray as $k => $v) {
                        if($value == $k){
                            $statusarrtmp[$key] = $v;
                        }
                    }
                }
                $statusarr['count']  = count($statusarrtmp);
                foreach ($statusarrtmp as $key => $value) {
                    if($key<$page){
                        $statusarr['content'][$key] = $value;
                    }
                }
            }
            // p($statusarr);die;
            if($statusarr){
                $this->ajaxreturn($statusarr);
            }else{
                $this->ajaxreturn($statusarr);
            }
        }
    } 
    /**
    服務/套餐/服務+套餐月收入
    **/
    public function income(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $type = I('post.type');
            $page = I('post.page')*12;
            // $type = 4;
            //所有的订单
            if($type==1 || $type==4 || $type==7){
                $receipt=D('HracReceipt')->where(array('hr_status'=>5))->select();     
            }else if($type==2 || $type==5 || $type==8){
                $receipt=D('HracReceipt')->where(array('isvip'=>1,'hr_status'=>5))->select();
            }else if($type==3 || $type==6 || $type==9){
                $receipt=D('HracReceipt')->where(array('isvip'=>0,'hr_status'=>5))->select();
            }
            //取出所有订单的日期
            foreach ($receipt as $key => $value) {
                $receiptdate[$key] = date('Y年m月',strtotime($value['hr_stardate']));
            }
            //去除相同值
            $ceipt = array_unique($receiptdate);
            //排序
            asort($ceipt);
            foreach ($ceipt as $key => $value) {
                $ceiptarr[] = $value;
            }
            // p($ceipt);
            //取总数
            if(!$receipt){
                $statusarr['count']  = 0;
                $statusarr['content']= array(); 
            }else{
                //重构订单
                foreach ($receipt as $key => $value) {
                    $hrdate[$key]['hr_stardate']   = date('Y年m月',strtotime($value['hr_stardate']));
                    $hrdate[$key]['hr_money']      = $value['hr_money'];
                    $hrdate[$key]['hr_promoney']   = $value['hr_promoney'];
                    $hrdate[$key]['hr_expmoney']   = $value['hr_expmoney'];
                    $hrdate[$key]['hr_res']        = $value['hr_res'];
                    $hrdate[$key]['hr_hra']        = $value['hr_hra'];
                    $hrdate[$key]['hr_minusmoney'] = $value['hr_minusmoney'];
                }
                // p($hrdate);die;     
                foreach ($ceiptarr as $key => $value) {
                    foreach ($hrdate as $ke => $va) {
                        if($value == $va['hr_stardate']){
                            $stardate[$key][] = $va;
                        }
                    }
                }
                // p($stardate);
                if($type==1 || $type==2 || $type==3){
                    foreach ($stardate as $key => $value) {
                        foreach ($value as $ke => $va) {
                            $statustmp[$key]['date']   = $va['hr_stardate'];
                            $statustmp[$key]['money'] += $va['hr_money'];
                        }
                    }
                }else if($type==4 || $type==5 || $type==6){
                    foreach ($stardate as $key => $value) {
                        foreach ($value as $ke => $va) {
                            $statustmp[$key]['date']   = $va['hr_stardate'];
                            $statustmp[$key]['money'] += $va['hr_promoney']+$va['hr_expmoney']+$va['hr_res']+$va['hr_hra']-$va['hr_minusmoney'];
                        }
                    }
                }else{
                    foreach ($stardate as $key => $value) {
                        foreach ($value as $ke => $va) {
                            $statustmp[$key]['date']   = $va['hr_stardate'];
                            $statustmp[$key]['money'] += $va['hr_money']+$va['hr_promoney']+$va['hr_expmoney']+$va['hr_res']+$va['hr_hra']-$va['hr_minusmoney'];
                        }
                    }
                }
                //取出键值
                foreach ($statustmp as $key => $value) {
                    $keyarr[] = $key;
                }
                rsort($keyarr);
                foreach ($keyarr as $key => $value) {
                    foreach ($statustmp as $k => $v) {
                        if($value == $k){
                            $statusarrtmp[$key] = $v;
                        }
                    }
                }
                $statusarr['count']  = count($statusarrtmp);
                foreach ($statusarrtmp as $key => $value) {
                    if($key<$page){
                        $statusarr['content'][$key] = $value;
                    }
                }
            }
            if($statusarr){
                $this->ajaxreturn($statusarr);
            }else{
                $this->ajaxreturn($statusarr);
            }
        }
    }
    /**
    获取该门店的医护和房间
    **/
    public function docterroom(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);   
        }else{
            // 获取门店id
            $sid    = I('post.sid');
            // $sid    = 4;
            //该门店全部医护信息
            $alldocter = D('HracDocter')->where(array('sid'=>$sid))->select();
            //该门店的房间
            $allroom   = D('HracHouse')->where(array('sid'=>$sid))->order('hhid asc')->select();
            // p($alldocter);
            if($alldocter){
                //去除等级为0的
                foreach ($alldocter as $key => $value) {
                    if($value['hgid']!=0){
                        $docter[] = $value;
                    }
                }
                //全部用户
                $user   = D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')->where(array('hu_type'=>2))->select();
                //获取医护的用户id
                foreach ($docter as $key => $value) {
                    foreach ($user as $ke => $va) {
                        if($value['hd_name']==$va['hu_nickname']){
                            $docinfo[$key]['huid']    = $va['huid'];
                            $docinfo[$key]['username']= $va['hu_username'];
                        }
                    }
                }
                foreach ($docinfo as $key => $value) {
                    $docterinfo[] = $value;
                }
                $docter=array_sort($docterinfo,'huid',$type='asc');
                foreach ($docter as $key => $value) {
                    $info['docter'][] = $value;
                }
                foreach ($allroom as $key => $value) {
                    $info['room'][$key]['hhid']       = $value['hhid'];
                    $info['room'][$key]['hh_numbeer'] = $value['hh_numbeer'];
                }
            }
            // p($info);
            if($info){
                $this->ajaxreturn($info);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    添加订单
    **/
    public function addorder(){
        if(!IS_POST){
            $date['status'] = 0;
            $this->ajaxreturn($date);
        }else{
            $data = I('post.');
            //顾问hdid
            $name = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                  ->where(array('huid'=>$data['hdid']))->getfield('hu_nickname');
            $hdfo = D('HracDocter')->where(array('hd_name'=>$name))->find();
            //预约人huid
            $hufo = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('hu_nickname'=>$data['huid']))->find();
            if($hufo['is_vip']==0){
                $vipname = 0;
            }else{
                $vipname = $data['huid'];
            }
            //客户
            $info = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('hu_nickname'=>$data['id']))->find();
            //服务信息
            $pro  = D('HracProject')->where(array('hpid'=>$data['hpid']))->find();
            if($hdfo&&$hufo&&$info){
                if($data['ishistory']==1){
                    $money     =$data['money'];
                    $promoney  =$data['promoney'];
                    $isdr      =$data['isdr'];
                    if(empty($data['res'])&& empty($data['hra'])){
                        $res   =0;$hra=0;$dna=0;
                    }else{
                        if(empty($data['res'])){
                            $res=0;
                            $hra=$data['hra']; 
                            $dna=$data['dna']; 
                        }else if(empty($data['hra'])){
                            $hra=0;
                            $res=$data['res']; 
                            $dna=$data['dna']; 
                        }else{
                            $hra=$data['hra'];
                            $res=$data['res']; 
                            $dna=$data['dna'];
                        }
                    }
                    $givecoupon=$data['givecoupon'];
                    $review    =$data['review'];
                    $identity  =$data['identity'];
                    $expmoney  =$data['expmoney'];
                    $minus     =$data['minus'];
                    $hrstatus=5;
                    if($promoney!=0 || $res!=0 || $hra!=0){
                        $product=1;
                    }else{
                        $product=0;
                    }
                    if($data['hcid']!=0){
                        $coupon  = D('HracCoupon')->where(array('hcid'=>$data['hcid']))->find();
                        //numbe[0] hp_abb,[1] huc_number   //date('Y-m-d',strtotime('-1 day')); //date('Y-m-d',time());
                        if($data['hucid']){
                            if(strpos($data['hucid'],'-')){
                            $number=explode('-',$data['hucid']);
                            }else{
                                $number[0]='';
                                $number[1]=$data['hucid'];
                            }
                            // p($number);die;
                            // 存放的内容
                            $content     = array('huid'=>$hufo['huid'],'huc_number'=>$number[1],'codetype'=>1,'huc_vaild'=>1,'huc_parent'=>$hufo['huid']);
                            $pic = qrcode_arr($content);
                            // echo $pic;die;
                            $where=array(
                                'huid'       =>$hufo['huid'],
                                'huc_parent' =>$hufo['huid'],
                                'huc_time'   =>date('Y-m-d',strtotime('-1 day')),
                                'huc_date'   =>date('Y-m-d',time()),
                                'hc_type'    =>$coupon['hc_type'],
                                'hcid'       =>$data['hcid'],
                                'hp_abb'     =>$number[0],
                                'huc_number' =>$number[1],
                                'huc_vaild'  =>1,
                                'huc_codepic'=>$pic
                            );
                            $addcoupon = D('HracUsercoupon')->add($where);
                            $hucid     = D('HracUsercoupon')->where(array('huid'=>$hufo['huid'],'huc_number'=>$number[1],'huc_vaild'=>1,'huc_parent'=>$hufo['huid']))
                                       ->getfield('hucid');
                        }else{
                            $date['status'] = 2;
                            $this->ajaxreturn($date);                            
                        }
                    }else{
                        $hucid=0; 
                    }              
                }else{
                    if($data['hcid']!=0){
                        $coupon  = D('HracCoupon')->where(array('hcid'=>$data['hcid']))->find();
                        $hc_money= $coupon['hc_money'];
                        if($data['hucid']){
                            //numbe[0] hp_abb,[1] huc_number   //date('Y-m-d',strtotime('-1 day')); //date('Y-m-d',time());
                            if(strpos($data['hucid'],'-')){
                                $number=explode('-',$data['hucid']);
                            }else{
                                $number[0]='';
                                $number[1]=$data['hucid'];
                            }
                            //查询是否已经存在
                            $while   = array('huid'=>$hufo['huid'],'huc_number'=>$number[1]);
                            $hcoupon = D('HracUsercoupon')->where($while)->find();
                            if($hcoupon){
                                $hucid = $hcoupon['hucid'];
                            }else{
                                // 存放的内容
                                $content = array('huid'=>$huid,'huc_number'=>$number[1],'codetype'=>1,'huc_vaild'=>1,'huc_parent'=>$huid);
                                $pic = qrcode_arr($content);
                                // echo $pic;die;
                                $where=array(
                                    'huid'       =>$hufo['huid'],
                                    'huc_parent' =>$hufo['huid'],
                                    'huc_time'   =>date('Y-m-d',strtotime('-1 day')),
                                    'huc_date'   =>date('Y-m-d',time()),
                                    'hc_type'    =>$coupon['hc_type'],
                                    'hcid'       =>$data['hcid'],
                                    'hp_abb'     =>$number[0],
                                    'huc_number' =>$number[1],
                                    'huc_vaild'  =>1,
                                    'huc_codepic'=>$pic
                                );
                                $addcoupon = D('HracUsercoupon')->add($where);
                                $hucid     = D('HracUsercoupon')
                                           ->where(array('huid'=>$hufo['huid'],'huc_number'=>$number[1],'huc_vaild'=>1,'huc_parent'=>$hufo['huid']))->getfield('hucid');      
                            }
                            $money     =$pro['hp_money']-$hc_money;
                            $promoney =0;$hrstatus=0;$isdr=0;$res=0;$hra=0;$dna=0;$givecoupon='';$review='';$expmoney=0;$minus=0;$product=0;
                            $identity ='PC';
                        }else{
                            $date['status'] = 2;
                            $this->ajaxreturn($date);                             
                        }
                    }else{
                        $date['status'] = 2;
                        $this->ajaxreturn($date); 
                    }               
                    
                }
                //是否有订单号
                if(!$data['order']){
                    $order = date('YmdHis').rand(10000, 99999);
                }else{
                    $order = $data['order'];
                }
                $contentpic= array('hr_number'=>$order,'codetype'=>2,'hr_status'=>$hrstatus);
                $codepic   = qrcode_arr($content);
                $receipt   = D('HracReceipt')->where(array('hr_number'=>$order))->find();
                if($receipt){
                    $date['status'] = 3;
                    $this->ajaxreturn($date);
                }else{
                    if($data['date']="--"){
                        $date='';
                    }else{
                        $date=$data['date'];
                    }
                    if($data['start']=":"){
                        $start='';
                    }else{
                        $start=$data['start'];
                    }
                    if($data['end']=":"){
                        $end='';
                    }else{
                        $end=$data['end'];
                    }
                    $status    =array(
                        'hr_number'      =>$order,//订单号
                        'hr_cretime'     =>date('Y-m-d H:i:s',time()),//创建时间
                        'vipname'        =>$vipname,//券拥有者
                        'name'           =>$data['huid'],//预约人是否金卡
                        'isvip'          =>$hufo['is_vip'],//预约人是否金卡
                        'hr_nickname'    =>$data['id'],//客户
                        'identity'       =>$identity,//客户身份
                        'isdr'           =>$isdr,//是否dr
                        'sid'            =>$data['sid'],//门店
                        'hdid'           =>$data['hdid'],//医护id
                        'hd_name'        =>$name,//顾问账号
                        'hpid'           =>$data['hpid'],//项目
                        'hcid'           =>$data['hcid'],//券分类
                        'hucid'          =>$hucid,//券
                        'hhid'           =>$data['hhid'],//房间
                        'hr_money'       =>$money,//检测收费
                        'hr_promoney'    =>$promoney,//产品金额
                        'hr_expmoney'    =>$expmoney,//专家金额
                        'hr_res'         =>$res,//RES产品金额
                        'hr_hra'         =>$hra,//HRA产品金额
                        'hr_dna'         =>$dna,//类型
                        'givecoupon'     =>$givecoupon,//赠送的券
                        'hr_minusmoney'  =>$minus,
                        'hr_date'        =>$date,
                        'hr_starttime'   =>$start,
                        'hr_endtime'     =>$end,
                        'hr_stardate'    =>$date,
                        'hr_statrservice'=>$start,
                        'hr_endservice'  =>$end,
                        'hr_status'      =>$hrstatus,
                        'hr_codepic'     =>$codepic,
                        'hr_review'      =>$review,
                        'is_product'     =>$product
                    );
                    // p($status);die;
                    $newsorder = D('HracReceipt')->add($status);
                    if($newsorder){
                        $date['status'] = 1;
                        $this->ajaxreturn($date);
                    }else{
                        $date['status'] = 0;
                        $this->ajaxreturn($date);
                    }
                }
            }else{
                $date['status'] = 4;
                $this->ajaxreturn($date);
            }
        }
    }
    /**
    获取邀约1/5/1/5万奖励(奇数1万，偶数5万)
    **/
    public function parity(){
        if(!IS_POST){
            $date['status'] = 0;
            $this->ajaxreturn($date);
        }else{
            $huid = I('post.huid');
            $name = D('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid  = nulife_ibos_users.iuid')->where(array('huid'=>$huid))->getfield('hu_nickname');
            $mape = D('HracBills')->where(array('name'=>$name,'hbi_type'=>1))->select();
            $bill = array_sort($mape,'hbi_time','desc');
            foreach ($bill as $key => $value) {
                if($value['hbi_sum']!=300000){
                    $hbill[] = $value;
                }
            }
            if($hbill){
                $partner['count'] = count($hbill);
                foreach ($hbill as $key => $value) {
                    $num += $value['hbi_sum'];
                }
                $partner['total'] = number_format($num);
                foreach ($hbill as $key => $value) {
                    $partner['log'][$key]['hbi_sum']     = number_format($value['hbi_sum']); 
                    $partner['log'][$key]['hbi_time']    = $value['hbi_time'];
                    $partner['log'][$key]['hbi_content'] = $value['hbi_content'];
                }
            }else{
                $partner['count'] = 0;
                $partner['total'] = 0;
                $partner['log']   = array();
            }           
            if($partner){
                $this->ajaxreturn($partner);                
            }else{
                $date['status'] = 0;
                $this->ajaxreturn($date);
            }
        }
    }
    /**
    加入购物车
    **/
    public function shopcart(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{      
            $huid   = I('post.huid');//会员huid
            $pid    = I('post.hpid');//产品或券pid
            //获取商品信息
            $product= M('HracCoupon')->where(array('hcid'=>$pid))->find();
            $aid    = M('HracCategory')->where(array('id'=>$product['id']))->getfield('aid');//地区id
            //加入购物车表(存session，判断是否已有商品，判断商品数量)
            $shopcartlist = M('HracShopcart')->where(array('huid'=>$huid))->select();
            //商品可重复购买
            if($shopcartlist){
                //判断是否重复商品
                $is_repeat = false;
                foreach ($shopcartlist as $k => $v) {
                    if($pid == $v['pid']){
                        //商品数量加一
                        $map = array(
                            'huid'=>$huid,
                            'pid' =>$pid
                        );
                        $changenum = M('HracShopcart')->where($map)->setInc('number');
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
                $id   = M('HracCoupon')->where(array('hcid'=>$shopcartlist[0]['pid']))->getfield('id');
                $iaid = M('HracCategory')->where(array('id'=>$id))->getfield('aid');//地区id
                if(!$is_repeat){
                    if($iaid==$aid){
                        $data = array(
                            'huid'      =>$huid,
                            'pid'       =>$pid,
                            'number'    =>1,
                            'aid'       =>$aid,
                            'price'     =>$product['hc_money'],
                            'point'     =>$product['hc_point']
                        );
                        $insertcart = M('HracShopcart')->add($data);
                        if($insertcart){
                            $data['status'] = 1;
                            $this->ajaxreturn($data);
                        }else{
                            $data['status'] = 0;
                            $this->ajaxreturn($data);
                        }
                    }else{
                        $data['status'] = 2;
                        $this->ajaxreturn($data);
                    }
                }    
            }else{
                $data = array(
                    'huid'      =>$huid,
                    'pid'       =>$pid,
                    'number'    =>1,
                    'aid'       =>$aid,
                    'price'     =>$product['hc_money'],
                    'point'     =>$product['hc_point']
                );
                $insertcart = M('HracShopcart')->add($data);
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
    购物车结算界面列表
    **/
    public function cartlistone(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid     = I('post.huid');
            //获取购物车列表信息
            $cartlist = M('HracShopcart')
                      ->where(array('nulife_hrac_shopcart.huid'=>$huid))
                      ->order('hscid desc')
                      ->select();
            if($cartlist){
                foreach($cartlist as $key => $value) {
                    $list    = D('HracCoupon')->where(array('hcid'=>$value['pid']))->find();
                    $shopcart[$key]['hp_name'] = $list['hc_name'];
                    $shopcart[$key]['hp_pic']  = $list['hc_pic4'];
                    $shopcart[$key]['hp_desc'] = $list['hc_desc'];
                    $shopcart[$key]['hp_point']= $value['point'];
                    $shopcart[$key]['hp_money']= $value['price'];
                    $shopcart[$key]['number']  = $value['number'];
                    $shopcart[$key]['hscid']   = $value['hscid'];
                    $shopcart[$key]['hpid']    = $value['pid'];
                    $shopcart[$key]['is_show'] = $value['is_show'];
                    $shopcart[$key]['hpcid']   = "0";
                }
                $cart['content'] = $shopcart;
                $cart['aid']     = $cartlist[0]['aid'];
            }
            if($cart){
                $this->ajaxreturn($cart);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    购物车提交订单界面列表
    **/
    public function cartlist(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid     = I('post.huid');
            //获取购物车列表信息
            $cartlist = M('HracShopcart')
                        ->where(array('huid'=>$huid,'is_show'=>1))
                        ->select();
            if($cartlist){
                foreach($cartlist as $key => $value) {
                    $list    = D('HracCoupon')->where(array('hcid'=>$value['pid']))->find();
                    $shopcart[$key]['hp_name'] = $list['hc_name'];
                    $shopcart[$key]['hp_pic']  = $list['hc_pic4'];
                    $shopcart[$key]['hp_desc'] = $list['hc_desc'];
                    $shopcart[$key]['hp_point']= $value['point'];
                    $shopcart[$key]['hp_money']= $value['price'];
                    $shopcart[$key]['number']  = $value['number'];
                    $shopcart[$key]['hpid']    = $value['pid'];
                    $shopcart[$key]['hscid']   = $value['hscid'];
                    $shopcart[$key]['is_show'] = $value['is_show'];
                    $shopcart[$key]['hpcid']   = "0";
                }
                $cart['content'] = $shopcart;
                $cart['aid']     = $cartlist[0]['aid'];
            }
            if($cart){
                $this->ajaxreturn($cart);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }
    /**
    增加购物车某商品数量
    **/
    public function cartadd(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $huid     = I('post.huid');
            //商品id
            $pid      = I('post.hpid');
            //获取购物车列表信息
            $cart     = M('HracShopcart')
                      ->where(array('huid'=>$huid,'pid'=>$pid))
                      ->find();
            //更新购物车该商品
            $data['number']  = $cart['number']+1;
            $cartpro  = M('HracShopcart')
                      ->where(array('huid'=>$huid,'pid'=>$pid))
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
    减少购物车某商品数量
    **/
    public function cartreduce(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $huid     = I('post.huid');
            //商品id
            $pid      = I('post.hpid');
            //获取购物车列表信息
            $cart     = M('HracShopcart')
                      ->where(array('huid'=>$huid,'pid'=>$pid))
                      ->find();
            //更新购物车该商品
            if($cart['number']>1){
                $data['number']  = $cart['number']-1;
                $cartpro  = M('HracShopcart')
                          ->where(array('huid'=>$huid,'pid'=>$pid))
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
    购物车结算订单界面选购需要提交订单商品
    **/
    public function is_show(){
        $hscid   = I('post.hscid');
        $is_show = I('post.is_show');
        //购物车已添加列表
        $map = array(
                'hscid' =>$hscid,
                'is_show'=>$is_show
            );
        $show= M('HracShopcart')->save($map);
        //购物车确定
        if($show){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    购物车结算订单界面删除商品
    **/
    public function is_delete(){
        $hscid   = I('post.hscid');
        //购物车已添加列表
        $map = array(
                'hscid' =>$hscid,
            );
        $is_delete = M('HracShopcart')->where($map)->delete();
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
    购物车提交订单(生成优惠券订单,计算总价)
    **/
    public function couorder(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            //获取用户huid
            $huid     = I('post.huid');
            $nickname = D('HracUsers')->join('nulife_ibos_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                      ->where(array('huid'=>$huid))->getfield('hu_nickname');
            //遍历购物车所有商品ipid(如果表中存有会话数据，则反序列化)
            $shopcart   = M('HracShopcart')
                        ->join('nulife_hrac_coupon on nulife_hrac_shopcart.pid = nulife_hrac_coupon.hcid')
                        ->where(array('nulife_hrac_shopcart.huid'=>$huid))
                        ->where(array('nulife_hrac_shopcart.is_show'=>1))
                        ->select();
            // p($shopcart);die;
            //生成唯一订单号
            $order_num = date('YmdHis').rand(100000, 999999);
            //计算总价
            foreach ($shopcart as $k => $v){           
                $total_num   += $v['number'];//总数量               
                $total_price += $v['number'] * $v['price'];//总金额                
                $total_point += $v['number'] * $v['point'];//总积分               
                $ir_desc     .= $v['hc_name'].'*'.$v['number'].',';//订单备注
                $hcid        .= $v['hcid'].'-';//券id组合
                $hc_num      .= $v['number'].'-';//券数量组合
            }
            $order = array(               
                'ir_receiptnum' =>$order_num,//订单编号               
                'ir_date'=>time(),//订单创建日期                
                'ir_status'=>0,//订单的状态(0待生成订单，1待支付订单，2已付款订单)                
                'huid'=>$huid,//下单用户id               
                'hu_nickname'=>$nickname,//下单用户                
                'ir_productnum'=>$total_num,//订单总商品数量                
                'ir_price'=>$total_price,//订单总金额              
                'ir_point'=>$total_point,//订单总积分    
                'ir_desc' =>substr($ir_desc, 0, -1),//订单详情    
                'hcid'=>substr($hcid, 0, -1),//券id组合
                'hc_num'=>substr($hc_num, 0, -1)//券数量组合
            );
            $receipt = M('HracCoureceipt')->add($order);
            //订单详情记录商品信息
            if($receipt){
                foreach ($shopcart as $k => $v) {
                    $map = array(
                        'ir_receiptnum'     =>  $order_num,
                        'hpid'              =>  $v['pid'],
                        'product_num'       =>  $v['number'],
                        'product_point'     =>  $v['point']*$v['number'],
                        'product_price'     =>  $v['price']*$v['number'],
                        'product_name'      =>  $v['hc_name'],
                        'product_picture'   =>  $v['hc_pic4']
                    );
                    $receiptlist = M('HracReceiptlist')->add($map);
                }
                //生成消息
                $title= '生成优惠券订单';
                $con  = '您的订单已生成,编号:'.$order_num.',包含:'.$ir_desc.',总价:'.$total_price.'Rmb,所需积分:'.$total_point;
                $hic  = D('HracInfoclass')->where(array('hic_type'=>3))->getfield('hicid');
                $log  = array(
                    'hi_title'  =>$title,
                    'hi_content'=>$con,
                    'hi_time'   =>date('Y-m-d H:i:s'),
                    'hicid'     =>$hic,
                    'huid'      =>$huid
                );
                $addlog = M('HracInformation')->add($log);
                if($receiptlist){
                    //订单提交后清空购物车
                    $rst = M('HracShopcart')->where(array('huid'=>$huid,'is_show'=>1))->delete();
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
    付款 1微信支付 2快钱 3积分 4转账单据
    **/
    public function payment(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $huid    = I('post.huid');//用户iuid  
            $paytype = I('post.paytype');//获取支付方式，1/2/3/4  
            // $ir_receiptnum = '2017122010384140800';//获取订单号
            $ir_receiptnum  = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(100000, 999999);
            $order   = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
            $ir_productnum  = $order['ir_productnum'];
            $ir_desc = $order['ir_desc'];
            $huctime = date("Y-m-d",time());
            switch ($paytype) {
                //快钱支付
                case 1:
                    $kq_target          = "https://sandbox.99bill.com/mobilegateway/recvMerchantInfoAction.htm";
                    $kq_merchantAcctId  = "1001213884201";      //*  商家用户编号     (30)
                    $kq_inputCharset    = "1";  //   1 ->  UTF-8        2 -> GBK        3 -> GB2312   default: 1    (2)
                    $kq_pageUrl         = ""; //   直接跳转页面 (256)
                    $kq_bgUrl           = "http://apps.nulifeshop.com/nulife/index.php/Api/Hracapi/getKqReturn"; //   后台通知页面 (256)
                    $kq_version         = "mobile1.0";  //*  版本  固定值 v2.0   (10)
                    $kq_language        = "1";  //*  默认 1 ， 显示 汉语   (2)
                    $kq_signType        = "4";   //*  固定值 1 表示 MD5 加密方式 , 4 表示 PKI 证书签名方式   (2)
                    $kq_payerName       = ""; //   英文或者中文字符   (32)
                    $kq_payerContactType= "1";  //  支付人联系类型  固定值： 1  代表电子邮件方式 (2)
                    $kq_payerContact    = "";    //   支付人联系方式    (50)
                    $kq_orderId         = $ir_receiptnum; //*  字母数字或者, _ , - ,  并且字母数字开头 并且在自身交易中式唯一  (50)
                    $kq_orderAmount     = "10"; //*   字符金额 以 分为单位 比如 10 元， 应写成 1000 (10)
                    $kq_orderTime       = date(YmdHis);  //*  交易时间  格式: 20110805110533
                    $kq_productName     = $ir_desc;//    商品名称英文或者中文字符串(256)
                    $kq_productNum      = $ir_productnum;   //    商品数量  (8)
                    $kq_productId       = "";   //    商品代码，可以是 字母,数字,-,_   (20) 
                    $kq_productDesc     = $ir_desc; //    商品描述， 英文或者中文字符串  (400)
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
                    $kq_payerId         ="3317";       //付款人标识

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
                    $priv_key = file_get_contents("./pcarduser.pem");
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
                            'ir_receiptnum'=>$ir_receiptnum,
                            'url'          =>$url,
                            'status'       =>1,
                            'msg'          =>'跳转至该url，使用快钱支付'
                        );
                        $this->ajaxreturn($data);
                    }else{
                        $data = array(
                            'ir_receiptnum'=>$ir_receiptnum,
                            'url'          =>'请求失败',
                            'status'       =>0,
                            'msg'          =>'支付请求失败'
                        );
                        $this->ajaxreturn($data);
                    }
                    break;
                //微信支付(IPS)
                case 2:
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
                        $amount         = "0.10";
                        $goodsInfo      = "Nulife Coupon";
                        $strMerchantUrl = "http://apps.nulifeshop.com/nulife/index.php/Api/Hracapi/getResponse";
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
                    break;
                //积分购买
                case 3:
                    //获取用户积分
                    $user       = M('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid=nulife_ibos_users.iuid')
                                ->where(array('huid'=>$huid))->find();
                    $user_point = $user['iu_point'];
                    //获取订单积分
                    $ir_point   = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_point');
                    //扣除所需购买积分
                    $last_point = $user_point-$ir_point;
                    $last_point = sprintf("%.2f",$last_point);
                    if($last_point>0){
                        //修改用户积分
                        $data = array(
                            'iuid'    =>$user['iuid'],
                            'iu_point'=>$last_point
                        );
                        $insertpoint = M('IbosUsers')->save($data);
                        if($insertpoint){
                            //修改订单状态
                            $map = array(
                                'ir_paytype'=>3,
                                'ir_status'=>2
                            );
                            $change_orderstatus = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                            //日志记录
                            $content = '订单:'.$ir_receiptnum.'支付成功,扣除Ep:'.$ir_point.',剩余Ep:'.$last_point;
                            $log     = array(
                                        'iuid'   =>$user['iuid'],
                                        'content'=>$content,
                                        'action' =>3,
                                        'type'   =>1,
                                        'date'   =>date('Y-m-d H:i:s')
                                    );
                            $addlog  = M('IbosLog')->add($log);
                            if($change_orderstatus){
                                $code = addcou($huid,$order['hcid'], $order['hc_num']);
                                if($code){
                                    D('HracInformation')->add($foarr);
                                }
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
                case 4:
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
                            'ir_paytype'        =>4,
                            'ir_bankreceipt'    =>C('WEB_URL').'/Public/idcard/'.$url_bankreceipt,
                            'ir_banknumber'     =>$banknumber
                        );
                        $change_orderstatus = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($data);
                        
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
    微信(IPS)支付成功订单状态修改
    **/
    public function getResponse(){
        //获取ips回调数据
        $data = I('post.');
        //记录数据
        if($data['billno'] != ""){
            $add  = M('IbosLog')->add($data);           
        }       
        //查询订单信息
        $order = M('HracCoureceipt')->where(array('ir_receiptnum'=>$data['billno']))->find();
        // //支付返回数据验证,是否支付成功验证
        if($data['succ'] == 'Y'){
            //签名验证
            //订单数量&订单金额
            if($data['amount'] == 0.10){
                //修改订单状态
                $map = array(
                    'ir_paytype'=>2,
                    'ir_status'=>2
                );
                $change_orderstatus = M('HracCoureceipt')->where(array('ir_receiptnum'=>$data['billno']))->save($map);

                if($change_orderstatus){
                    $code = addcou($order['huid'],$order['hcid'], $order['hc_num']);
                    if($code){
                        D('HracInformation')->add($foarr);
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
    快钱返回结果
    **/
    public function getKqReturn(){
        $kq_check_all_para=kq_ck_null($_GET['merchantAcctId'],'merchantAcctId').kq_ck_null($_GET['version'],'version').kq_ck_null($_GET['language'],'language').kq_ck_null($_GET['signType'],'signType').kq_ck_null($_GET['payType'],'payType').kq_ck_null($_GET['bankId'],'bankId').kq_ck_null($_GET['orderId'],'orderId').kq_ck_null($_GET['orderTime'],'orderTime').kq_ck_null($_GET['orderAmount'],'orderAmount').kq_ck_null($_GET['bindCard'],'bindCard').kq_ck_null($_GET['bindMobile'],'bindMobile').kq_ck_null($_GET['dealId'],'dealId').kq_ck_null($_GET['bankDealId'],'bankDealId').kq_ck_null($_GET['dealTime'],'dealTime').kq_ck_null($_GET['payAmount'],'payAmount').kq_ck_null($_GET['fee'],'fee').kq_ck_null($_GET['ext1'],'ext1').kq_ck_null($_GET['ext2'],'ext2').kq_ck_null($_GET['payResult'],'payResult').kq_ck_null($_GET['errCode'],'errCode');

        $trans_body= substr($kq_check_all_para,0,strlen($kq_check_all_para)-1);
        $MAC       = base64_decode($_GET['signMsg']);
        $cert      = file_get_contents("./99bill[1].cert.rsa.20140803.cer");
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
            $map = array(
                'ir_paytype'=>1,
                'ir_status'=>2
            );
            //查询订单信息
            $order = M('HracCoureceipt')->where(array('ir_receiptnum'=>$data['billno']))->find();
            $receipt = M('HracCoureceipt')->where(array('ir_receiptnum'=>$_GET['orderId']))->save($map);
            if($receipt){
                $code = addcou($order['huid'],$order['hcid'], $order['hc_num']);
                if($code){
                    D('HracInformation')->add($foarr);
                }
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
    订单状态查询 0待付款 1待审核 2已支付 3审核未通过
    **/
    public function checkreceipt(){
        $ir_receiptnum = I('post.ir_receiptnum');
        //订单状态查询
        $receipt_status = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_status');
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
    获取用户订单列表
    **/
    public function couorderlist(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            $huid      = I('post.huid');
            $ir_status = I('post.ir_status')-1;
            if(empty($huid)){
                //订单审核用列表
                if($ir_status == "-1"){
                    $map = array(
                        'is_delete'=>0,
                    );
                }else{
                    $map = array(
                        'is_delete'=>0,
                        'ir_status'=>$ir_status
                    );
                }
                $receiptlist = M('HracCoureceipt')->where($map)->order('ir_date desc')->select();
            }else{
                //个人订单列表
                if($ir_status == "-1"){
                    $map = array(
                        'huid'     =>$huid,
                        'is_delete'=>0,
                    );
                }else{
                    $map = array(
                        'huid'=>$huid,
                        'is_delete'=>0,
                        'ir_status'=>$ir_status
                    );
                }
                $receiptlist = M('HracCoureceipt')->where($map)->order('ir_date desc')->select();
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
    获取优惠券订单详情
    **/
    public function orderdetail(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $ir_receiptnum = trim(I('post.ir_receiptnum'));
            //获取该用户的某个订单详细信息
            $receipt = M('HracReceiptlist')
                     ->join('nulife_hrac_coureceipt on nulife_hrac_receiptlist.ir_receiptnum = nulife_hrac_coureceipt.ir_receiptnum')
                     ->join('nulife_hrac_coupon on nulife_hrac_receiptlist.hpid = nulife_hrac_coupon.hcid')
                     ->where(array('nulife_hrac_receiptlist.ir_receiptnum'=>$ir_receiptnum))
                     ->select();
            $id             = D('HracCoupon')->where(array('hcid'=>$receipt[0]['hcid']))->getfield('id');
            $list['aid']    = D('HracCategory')->where(array('id'=>$id))->getfield('aid');
            $list['time']   = date('Y-m-d H:i:s',$receipt[0]['ir_date']);
            $list['content']=$receipt;
            if($list){
                $this->ajaxreturn($list);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    订单审核 ir_status 0待付款 1待审核 2已完成 3未通过
    **/
    public function check_bankreceipt(){
        $ir_receiptnum = trim(I('ir_receiptnum'));
        $ir_status     = trim(I('ir_status'));
        $map = array(
            'ir_status'    =>$status
        );
        $edit_receipt = M('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
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
    删除优惠券订单
    **/
    public function coudelete(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $ir_receiptnum = trim(I('post.ir_receiptnum'));
            $list = D('HracCoureceipt')->where(array('ir_receiptnum'=>$ir_receiptnum,'ir_status'=>0))->setField('is_delete',1);
            if($list){
                $data['status'] = 1;
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
        /**
    定时调用的接口
    **/
    public function timing(){
        $time = date('Y-m-d',time());
        //修改用户券状态
        $where= array(
            'huc_date' =>$time,
            'huc_vaild'=>0
        );
        $save = array(
            'huc_vaild'=>2
        );
        $usercoupon = D('HracUsercoupon')->where($where)->save($save);
        //变为普通用户
        $null = array(
            'start' =>'',
            'vipend'=>'',
            'is_vip'=>0
        );
        D('HracUser')->where(array('vipend'=>$time))->save($null);
        //修改为合伙人状态并计算奖金
        //1 查询当天所有将成为正式的
        $tmp  = array(
            'hpr_date'=>$time,
            'is_vaild'=>0
        );
        $partner    = D('HracPartner')->where($tmp)->select();
        //循环添加/修改
        foreach ($partner as $key => $value) {
            $arr = array(
                'hprid'   =>$value['hprid'],
                'is_vaild'=>1
            );
            //2 将合伙人修改为正式
            $savepart = D('HracPartner')->save($arr);
            if($savepart){
                $hbitime= date('Y-m-d H:i:s',time());
                $part   = D('HracPartner')->where(array('pid'=>$value['pid'],'is_vaild'=>1))->select();
                $info   = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('huid'=>$value['huid']))->find();
                $data   = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('huid'=>$value['pid']))->find();
                $count  = count($part);
                //3 判断该用的合伙人数量 奇数奖励1万 偶数奖励5万，满15人奖励30万
                if($count==15){
                    $bill    = array(
                        'name'       =>$data['hu_nickname'],
                        'hbi_type'   =>1,
                        'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                        'hbi_num'    =>$data['hu_num'],
                        'symbol'     =>'+',
                        'hbi_sum'    =>10000,
                        'hbi_time'   =>$hbitime
                    );
                    $billss    = array(
                        'name'       =>$data['hu_nickname'],
                        'hbi_type'   =>1,
                        'hbi_content'=>'合夥人滿15人獎勵',
                        'hbi_num'    =>$data['hu_num'],
                        'symbol'     =>'+',
                        'hbi_sum'    =>300000,
                        'hbi_time'   =>$hbitime
                    );
                    $user1    = array(
                        'name'       =>$data['hu_nickname'],
                        'iu_point'   =>$data['iu_point']+10000+300000
                    );
                }else{
                    if($count%2==0){
                        $bill    = array(
                            'name'       =>$data['hu_nickname'],
                            'hbi_type'   =>1,
                            'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                            'hbi_num'    =>$data['hu_num'],
                            'symbol'     =>'+',
                            'hbi_sum'    =>50000,
                            'hbi_time'   =>$hbitime
                        );
                        $user1    = array(
                            'name'       =>$data['hu_nickname'],
                            'iu_point'   =>$data['iu_point']+50000
                        );
                    }else{
                        $bill    = array(
                            'name'       =>$data['hu_nickname'],
                            'hbi_type'   =>1,
                            'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                            'hbi_num'    =>$data['hu_num'],
                            'symbol'     =>'+',
                            'hbi_sum'    =>10000,
                            'hbi_time'   =>$hbitime
                        );
                        $user1    = array(
                            'name'       =>$data['hu_nickname'],
                            'iu_point'   =>$data['iu_point']+10000
                        );
                    }
                }
                $strto    = strtotime($value['hpr_time']);
                $hprtime  = date('Y-m-d',$strto);
                $newsord  = date('Y-m-d',strtotime("+1 year",$strto-86400));
                $user2    = array(
                    'huid'     =>$value['huid'],
                    'hu_hpid'  =>$value['pid'],
                    'hu_hpname'=>$data['hu_nickname'],
                    'vipstart' =>$hprtime,
                    'is_vip'   =>1,
                    'vipend'   =>$newsord,
                    'hu_num'   =>$info['hu_num']+1
                );
                D('IbosUsers')->save($user1);  
                D('HracUsers')->save($user2);  
                D('HracBills')->add($bill);
                if($billss){
                    D('HracBills')->add($billss);
                }
            }
        }
    }
    /**
    临时登记
    */
    public function sign(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $tmp = I('post.');
            $pid = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                 ->where(array('hu_nickname'=>$tmp['name']))->getfield('huid');
            $huid= D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                 ->where(array('hu_nickname'=>$tmp['id']))->getfield('huid');
            $part= D('HracPartner')->where(array('huid'=>$huid))->find();
            if($part){
                $data['status'] = 2;
                $this->ajaxreturn($data);
            }else{
                $mape = array(
                    'huid'     =>$huid,
                    'pid'      =>$pid,
                    'hpr_time' =>date('Y-m-d H:i:s'),
                    'hpr_date' =>date('Y-m-d',strtotime('+6 day')),
                    'is_vaild' =>0
                );
                $where=array(
                    'huid' =>$huid,
                    'stack'=>$pid
                );
                $add = D('HracPartner')->add($mape);
                if($add){
                    D('HracUsers')->save($where);
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
    临时登记列表
    */
    public function parlist(){
        if(!IS_GET){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $partner = D('HracUsers')
                     ->join('nulife_hrac_partner on nulife_hrac_partner.huid = nulife_hrac_users.huid')
                     ->join('nulife_ibos_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')
                     ->order('hprid desc')
                     ->select();
            foreach ($partner as $key => $value) {
                $mape[$key]['name']  = $value['hu_nickname'];
                $mape[$key]['start'] = date('Y-m-d',strtotime($value['hpr_time']));
                $mape[$key]['end']   = $value['hpr_date'];
                $mape[$key]['vaild'] = $value['is_vaild'];
                $mape[$key]['hprid'] = $value['hprid'];
            }
            if($mape){
                $this->ajaxreturn($mape); 
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    跳过7天考虑期，直接成为合伙人
    */
    public function skip(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $hprid = I('post.hprid');
            //修改为合伙人状态并计算奖金
            // 1找到该数据
            $partner    = D('HracPartner')->where(array('hprid'=>$hprid))->find();
            $arr = array(
                'hprid'   =>$hprid,
                'is_vaild'=>1
            );
            //2 将合伙人修改为正式
            $savepart = D('HracPartner')->save($arr);
            if($savepart){
                $hbitime= date('Y-m-d H:i:s',time());
                $part   = D('HracPartner')->where(array('pid'=>$partner['pid'],'is_vaild'=>1))->select();
                $info   = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('huid'=>$partner['huid']))->find();
                $data   = D('IbosUsers')->join('nulife_hrac_users on nulife_ibos_users.iuid = nulife_hrac_users.iuid')->where(array('huid'=>$partner['pid']))->find();
                $count  = count($part);
                //3 判断该用的合伙人数量 奇数奖励1万 偶数奖励5万，满15人奖励30万
                if($count==15){
                    $bill    = array(
                        'name'       =>$data['hu_nickname'],
                        'hbi_type'   =>1,
                        'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                        'hbi_num'    =>$data['hu_num'],
                        'symbol'     =>'+',
                        'hbi_sum'    =>10000,
                        'hbi_time'   =>$hbitime
                    );
                    $billss    = array(
                        'name'       =>$data['hu_nickname'],
                        'hbi_type'   =>1,
                        'hbi_content'=>'合夥人滿15人獎勵',
                        'hbi_num'    =>$data['hu_num'],
                        'symbol'     =>'+',
                        'hbi_sum'    =>300000,
                        'hbi_time'   =>$hbitime
                    );
                    $user1    = array(
                        'name'       =>$data['hu_nickname'],
                        'iu_point'   =>$data['iu_point']+10000+300000
                    );
                }else{
                    if($count%2==0){
                        $bill    = array(
                            'name'       =>$data['hu_nickname'],
                            'hbi_type'   =>1,
                            'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                            'hbi_num'    =>$data['hu_num'],
                            'symbol'     =>'+',
                            'hbi_sum'    =>50000,
                            'hbi_time'   =>$hbitime
                        );
                        $user1    = array(
                            'name'       =>$data['hu_nickname'],
                            'iu_point'   =>$data['iu_point']+50000
                        );
                    }else{
                        $bill    = array(
                            'name'       =>$data['hu_nickname'],
                            'hbi_type'   =>1,
                            'hbi_content'=>'成功邀请'.$info['hu_nickname'].'成为合伙人',
                            'hbi_num'    =>$data['hu_num'],
                            'symbol'     =>'+',
                            'hbi_sum'    =>10000,
                            'hbi_time'   =>$hbitime
                        );
                        $user1    = array(
                            'name'       =>$data['hu_nickname'],
                            'iu_point'   =>$data['iu_point']+10000
                        );
                    }
                }
                $strto    = strtotime($partner['hpr_time']);
                $hprtime  = date('Y-m-d',$strto);
                $newsord  = date('Y-m-d',strtotime("+1 year",$strto-86400));
                $user2    = array(
                    'huid'     =>$partner['huid'],
                    'hu_hpid'  =>$partner['pid'],
                    'hu_hpname'=>$data['hu_nickname'],
                    'vipstart' =>$hprtime,
                    'is_vip'   =>1,
                    'vipend'   =>$newsord,
                    'hu_num'   =>$info['hu_num']+1
                );
                $tmpe1 = D('IbosUsers')->save($user1);  
                $tmpe2 = D('HracUsers')->save($user2);  
                $tmpe3 = D('HracBills')->add($bill);
                if($billss){
                    D('HracBills')->add($billss);
                }
                if($tmpe3){
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
        }
    }
    //获取券拥有者和使用者的关系
    // public function resmove(){
    //     $arr = M('HracUsers')->join('nulife_ibos_users on nulife_hrac_users.iuid = nulife_ibos_users.iuid')->select();
    //     $tree = subtree($arr,5,$lev=1);
    //     $status = subtree_arr($tree,'huid','hu_hpid',6);
    //     p($status);

    // }
}