<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*  DT管理
**/
class DtpointController extends AdminBaseController{
    /**
    * 会员DT表
    **/
    public function index(){
        //账户昵称搜索
        $word = I('get.word');
        $excel     = I('get.excel');
        $assign = D('User')->getDtPoint(D('User'),$word,"iuid",$limit=50);
        // P($assign);
        $array = '测,试,测试,测试测试,test,测试点,testtest';
        if($excel == 'excel'){
            $data = D('User')->getDtPoints(D('User'),$word,"iuid",$array);
            foreach($data['data'] as $key=>$value){
                if(strlen($value['hu_nickname']) != 8){
                    if($value['distributortype'] == 'Platinum'){
                        $message[] = $value;
                    }
                }
            }
            $export_excel = D('User')->export_excelDt($message);
        }else{
            $this->assign($assign);
            $this->assign('word',$word);
            $this->display();
        }
    }

    /**
    * DT报表--每月总额
    **/
    public function table(){
        $status = '2'; 
        $assign = D('Getdt')->getAllDt(D('Getdt'),$hu_nickname,$status);
        $this->assign($assign);
        $this->display();
    }
    /**
    * DT报表--每日总额
    **/
    public function tableday(){
        $date   = I('get.date');         
        $status = '2';         
        $assign = D('Getdt')->getDtDay(D('Getdt'),$hu_nickname,$date,$status);
        $this->assign($assign);
        $this->assign('date',$date);
        $this->display();
    }
    /**
    * DT报表-详情
    **/
    public function tableinfo(){
        $date       = date('Y-m-d',strtotime(I('get.date')));
        $starttime  = I('get.date');
        $hu_nickname= I('get.hu_nickname');
        $endtime    = I('get.date')?date('Y-m-d',strtotime(I('get.date'))+86400):'';
        $assign = D('Getdt')->getAllDtInfo(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        $this->assign($assign);
        $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        $this->assign('date',$date);
        $this->assign('hu_nickname',$hu_nickname);
        $this->display();
    }
    /**
    * DT增加或减少列表
    **/
    public function syspoint(){
        $type      = '1,2';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname = I('get.hu_nickname');
        $assign    = D('Getdt')->getAllDtInfo(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        if($excel == 'excel'){
            $data         = D('Getdt')->getAllExcel(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getdt')->pointInfo_excel($data);
        }else{
            // p($assign);die;
            $this->assign($assign);
            $this->assign('starttime',$starttime);
            $this->assign('endtime',$endtime);
            $this->assign('hu_nickname',$hu_nickname);
            $this->display();
        }
    }
    /**
    * DT消费列表
    **/
    public function shoppoint(){
        $type      = '4';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname = I('get.hu_nickname');
        $assign    = D('Getdt')->getAllDtInfo(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        if($excel == 'excel'){
            $data         = D('Getdt')->getAllExcel(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getdt')->pointInfo_excel($data);
        }else{
            $this->assign($assign);
            $this->assign('starttime',$starttime);
            $this->assign('endtime',$endtime);
            $this->assign('hu_nickname',$hu_nickname);
            $this->display();
        }
    }
    /**
    * 用户DT报表记录
    **/
    public function userpoint(){
        $hu_nickname= I('get.hu_nickname');
        $iuid       = I('get.iuid');
        $starttime  = I('get.starttime');
        $endtime    = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $assign = D('Getdt')->getAllDtInfo(D('Getdt'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        $this->assign($assign);
        $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        $this->assign('hu_nickname',$hu_nickname);
        $this->assign('iuid',$iuid);
        $this->display();
    }
    /**
    * 操作用户DT(增加或减少)
    * 验证登录后台管理员密码
    **/
    public function opepoint(){
        $session = session();
        $data    = I('post.');
        $user    = D('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
        $password= md5($data['password']);
        $admin   = D('Admin')->where(array('id'=>$session['user']['id']))->find();
        if($admin && $admin['password']==$password){
            if($data['dttype']==1){
                $leftdt = bcsub($user['iu_dt'],$data['realpoint'],4);
                $data['content']    = $session['user']['username'].'在'.date('Y-m-d H:i:s').',通过系统减少DT'.$data['realpoint'].',DT余额:'.$leftdt;
                $data['send']       = $data['hu_nickname'];
                $data['received']   = '系统';
            }else{
                $leftdt = bcadd($user['iu_dt'],$data['realpoint'],4);
                $data['content']    = $session['user']['username'].'在'.date('Y-m-d H:i:s').',通过系统增加DT'.$data['realpoint'].',DT余额:'.$leftdt;
                $data['send']       = '系统';
                $data['received']   = $data['hu_nickname'];
            }
            $data['iuid']       = $user['iuid'];
            $data['opename']    = $session['user']['username'];
            $data['hu_username']= $user['lastname'].$user['firstname'];
            $data['getdt']      = $data['realpoint'];
            $data['leftdt']     = $leftdt;
            $data['date']       = date('Y-m-d H:i:s');
            $data['handletime'] = date('Y-m-d H:i:s');
            $data['status']     = 2;
            $data['pointNo']    = 'DT'.date('YmdHis').rand(100000, 999999);
            // p($data);;die;
            $add = D('Getdt')->add($data);
            if($add){
                $save = D('User')->where(array('iuid'=>$user['iuid']))->setField('iu_dt',$leftdt);
                $this->success('操作成功');
            }else{
                $this->error("操作失败,请确认是否正确"); 
            }
        }else{
            $this->error("操作失败,请核对密码");
        }
    }
    /**
    *添加DT记录
    **/
    public function add_point(){
        $tmpe     = I('post.');
        $session  = session();
        $user     = D('User')->where(array('CustomerID'=>$tmpe['hu_nickname']))->find();
        $data['iuid'] = $user['iuid'];
        $data['hu_username'] = $user['lastname'].$user['firstname'];
        $data['hu_nickname'] = $user['customerid'];
        // p($data);die;
        if($tmpe['getdt']){
            switch ($tmpe['dttype'])    {
                case '1':
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['customerid'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '系统减少';
                    $data['status']     = 2;
                    $data['dttype']     = 1;
                    break;
                case '2':
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['customerid'];
                    $type               = '系统增加';
                    $data['status']     = 2;
                    $data['dttype']     = 2;
                    break;
                case '3':
                    $data['send']       = $tmpe['send']?$tmpe['send']:'WV';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['customerid'];
                    $type               = '新增入';
                    $data['status']     = 2;
                    $data['dttype']     = 3;
                    break;
                case '4':
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['customerid'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '消费出';
                    $data['status']     = 2;
                    $data['dttype']     = 4;
                    break;
            }
            $iu_dt             = $tmpe['leftdt']?$tmpe['leftdt']:$user['iu_dt'];
            $data['getdt']     = $tmpe['getdt'];
            $data['date']      = $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['content']   = $tmpe['content']?:$data['send'].'在'.$data['date'].'时,'.$type.$data['getdt'].'EP到'.$data['received'].',剩EP余额'.$iu_dt;
            $data['opename']   = $session['user']['username'];
            $data['leftdt']    = $iu_dt;
            $data['pointNo']   = $tmpe['pointNo']?$tmpe['pointNo']:'DT'.date('YmdHis').rand(100000, 999999);
            // p($data);die;
            $add = D('Getdt')->add($data);
            if($add){
                $this->success('添加成功');
            }else{
                $this->error("添加失败"); 
            }
        }else{
            $this->error("添加失败,请填写正确DT"); 
        }
    }
    /**
    *编辑DT记录
    **/
    public function edit_point(){
        $tmpe     = I('post.');
        $session  = session();
        $user     = D('User')->where(array('CustomerID'=>$tmpe['hu_nickname']))->find();
        $data['iuid'] = $user['iuid'];
        $data['hu_username'] = $user['lastname'].$user['firstname'];
        $data['hu_nickname'] = $user['customerid'];
        // p($data);die;
        if($tmpe['getdt']){
            switch ($tmpe['dttype'])    {
                case '1':
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['customerid'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '系统减少';
                    $data['status']     = 2;
                    $data['dttype']     = 1;
                    break;
                case '2':
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['customerid'];
                    $type               = '系统增加';
                    $data['status']     = 2;
                    $data['dttype']     = 2;
                    break;
                case '3':
                    $data['send']       = $tmpe['send']?$tmpe['send']:'WV';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['customerid'];
                    $type               = '新增入';
                    $data['status']     = 2;
                    $data['dttype']     = 3;
                    break;
                case '4':
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['customerid'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '消费出';
                    $data['status']     = 2;
                    $data['dttype']     = 4;
                    break;
            }
            $iu_dt             = $tmpe['leftdt']?$tmpe['leftdt']:$user['iu_dt'];
            $data['getdt']     = $tmpe['getdt'];
            $data['date']      = $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['content']   = $tmpe['content']?:$data['send'].'在'.$data['date'].'时,'.$type.$data['getdt'].'DT到'.$data['received'].',剩EP余额'.$iu_dt;
            $data['opename']   = $session['user']['username'];
            $data['leftdt'] = $iu_dt;
            $data['pointNo']   = $tmpe['pointNo']?$tmpe['pointNo']:'EP'.date('YmdHis').rand(100000, 999999);
            // p($data);die;
            $add = D('Getdt')->where(array('igid'=>$tmpe['igid']))->save($data);
            if($add){
                $this->success('编辑成功');
            }else{
                $this->error("编辑失败"); 
            }
        }else{
            $this->error("编辑失败"); 
        }
    }
    /**
    *删除DT记录
    **/
    public function delete_point(){
        $igid   = I('get.id');
        $delete = D('Getdt')->where(array('igid'=>$igid))->delete();
        if($delete){
            $this->success('删除成功');
        }else{
            $this->error("删除失败"); 
        }
    }
    /*****************************************会计*****************************************************/
    /**
    * 360明细
    **/
    public function getAllWalletdetail(){
        $memberNo       = I('get.hu_nickname');
        $remark         = 'API消费';
        $ewalletType    = I('post.ewalletType');
        $auditTimeStart = I('post.auditTimeStart');
        $auditTimeEnd   = I('post.auditTimeEnd');
        $currentPage    = I('post.currentPage');
        $pageSize       = I('post.pageSize');
        $pageModel      = array(
                            "currentPage"  =>$currentPage,
                            "pageSize"     =>$pageSize
                        );
        $pageModel      = json_encode($pageModel);
        $ibos360_result = ibos360_getAllWalletdetail($memberNo,$ewalletType,$remark,$auditTimeStart,$auditTimeEnd,$pageModel);
        // p($ibos360_result);die;
        if($ibos360_result['success'] == 1){
            $assign['data'] = $ibos360_result['resultSet'];
            // $assign['page'] = $ibos360_result['pageModel'];
            $this->assign('hu_nickname',$memberNo);
            $this->assign($assign);
            $this->display();
        }else{
            $this->error($ibos360_result['error']['message']);
        }
    }
}