<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
*  积分管理
*  积分提现列表
*  积分报表
*  积分转账列表
*  会员积分表
*  添加和删除积分
**/
class PointController extends AdminBaseController{
    /**
    * 积分转账列表
    **/
    public function transfer(){
        $type   = '4,5';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname= I('get.hu_nickname');
        $assign = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        if($excel == 'excel'){
            $data         = D('Getpoint')->getAllExcel(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getpoint')->pointInfo_excel($data);
        }else{
            $this->assign($assign);
            $this->assign('starttime',$starttime);
            $this->assign('endtime',$endtime);
            $this->assign('hu_nickname',$hu_nickname);
            $this->display();      
        }
    }

    /**
    * 会员积分表
    **/
    public function index(){
        //账户昵称搜索
        $word = I('get.word');
        $assign=D('User')->getAllPoint(D('User'),$word,"iuid",$limit=50);
        // P($assign);
        $this->assign($assign);
        $this->assign('word',$word);
        $this->display();
    }

    /**
    * 积分报表--每月总额
    **/
    public function table(){
        $status = '2'; 
        $assign = D('Getpoint')->getAllPoint(D('Getpoint'),$hu_nickname,$status);
        $this->assign($assign);
        $this->display();
    }
    /**
    * 积分报表--每日总额
    **/
    public function tableday(){
        $date   = I('get.date');         
        $status = '2';         
        $assign = D('Getpoint')->getPointDay(D('Getpoint'),$hu_nickname,$date,$status);
        $this->assign($assign);
        $this->assign('date',$date);
        $this->display();
    }
    /**
    * 积分报表-详情
    **/
    public function tableinfo(){
        $date       = date('Y-m-d',strtotime(I('get.date')));
        $starttime  = I('get.date');
        $hu_nickname= I('get.hu_nickname');
        $endtime    = I('get.date')?date('Y-m-d',strtotime(I('get.date'))+86400):'';
        $assign = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        $this->assign($assign);
        $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        $this->assign('date',$date);
        $this->assign('hu_nickname',$hu_nickname);
        $this->display();
    }
    /**
    * 积分增加或减少列表
    **/
    public function syspoint(){
        $type      = '1,2';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname = I('get.hu_nickname');
        $assign    = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        if($excel == 'excel'){
            $data         = D('Getpoint')->getAllExcel(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getpoint')->pointInfo_excel($data);
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
    * 积分消费列表
    **/
    public function shoppoint(){
        $type      = '7';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname = I('get.hu_nickname');
        $assign    = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        if($excel == 'excel'){
            $data         = D('Getpoint')->getAllExcel(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getpoint')->pointInfo_excel($data);
        }else{
            $this->assign($assign);
            $this->assign('starttime',$starttime);
            $this->assign('endtime',$endtime);
            $this->assign('hu_nickname',$hu_nickname);
            $this->display();
        }
    }
    /**
    * 积分驳回列表
    **/
    public function rejectpoint(){
        $type      = '8';
        $starttime = I('get.starttime');
        $endtime   = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $excel     = I('get.excel');
        $hu_nickname= I('get.hu_nickname');
        $assign    = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        if($excel == 'excel'){
            $data         = D('Getpoint')->getAllExcel(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,'date desc');
            $export_excel = D('Getpoint')->pointInfo_excel($data);
        }else{
            $this->assign($assign);
            $this->assign('starttime',$starttime);
            $this->assign('endtime',$endtime);
            $this->assign('hu_nickname',$hu_nickname);
            $this->display();
        }
    }
    /**
    * 用户积分报表记录
    **/
    public function userpoint(){
        $hu_nickname= I('get.hu_nickname');
        $iuid       = I('get.iuid');
        $starttime  = I('get.starttime');
        $endtime    = I('get.endtime')?date('Y-m-d',strtotime(I('get.endtime'))+86400):'';
        $assign = D('Getpoint')->getAllPointInfo(D('Getpoint'),$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='','date desc');
        // p($assign);die;
        $this->assign($assign);
        $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        $this->assign('hu_nickname',$hu_nickname);
        $this->assign('iuid',$iuid);
        $this->display();
    }
    /**
    * 操作用户积分(增加或减少)
    * 验证登录后台管理员密码
    **/
    public function opepoint(){
        $session = session();
        $data    = I('post.');
        $user    = D('User')->where(array('CustomerID'=>$data['hu_nickname']))->find();
        $password= md5($data['password']);
        $admin   = D('Admin')->where(array('id'=>$session['user']['id']))->find();
        if($data['pointtype']==1){
            $leftpoint = bcsub($user['iu_point'],$data['realpoint'],4);
            $data['content']   = '系统减少积分'.$data['realpoint'];
            $action    =7;
            $data['send']       = $data['hu_nickname'];
            $data['received']   = '系统';
        }else{
            $leftpoint = bcadd($user['iu_point'],$data['realpoint'],4);
            $data['content']   = '系统增加积分'.$data['realpoint'];
            $action    =5;
            $data['send']       = '系统';
            $data['received']   = $data['hu_nickname'];
        }
        if($admin && $admin['password']==$password){
            $data['iuid']       = $user['iuid'];
            $data['opename']    = $session['user']['username'];
            $data['hu_username']= $user['lastname'].$user['firstname'];
            $data['getpoint']   = $data['realpoint'];
            $data['iu_unpoint'] = $user['iu_unpoint'];
            $data['leftpoint']  = $leftpoint;
            $data['date']       = date('Y-m-d H:i:s');
            $data['handletime'] = date('Y-m-d H:i:s');
            $data['status']     = 2;
            $data['pointNo']    = 'EP'.date('YmdHis').rand(100000, 999999);
            $data['whichApp']   = 5;
            // p($data);;die;
            $add = D('Getpoint')->add($data);
            if($add){
                $save = D('User')->where(array('iuid'=>$user['iuid']))->setField('iu_point',$leftpoint);
                //日志记录
                $add = addLog($user['iuid'],$data['content'],$action,$type=1);
                $this->success('操作成功');
            }else{
                $this->error("操作失败,请确认是否正确"); 
            }
        }else{
            $this->error("操作失败,请核对密码");
        }
    }
    /**
    *添加积分记录
    **/
    public function add_point(){
        $tmpe     = I('post.');
        $session  = session();
        $user     = D('User')->where(array('CustomerID'=>$tmpe['hu_nickname']))->find();
        $data['iuid'] = $user['iuid'];
        $data['hu_username'] = $user['hu_username'];
        $data['hu_nickname'] = $user['hu_nickname'];
        // p($data);die;
        if($tmpe['getpoint']){
            switch ($tmpe['pointtype']) {
                case '0':
                    $data['feepoint']   = $tmpe['getpoint']*0.05;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统冻结';
                    $type               = '提现冻结';
                    $data['status']     = 0;
                    $data['pointtype']  = 6;
                    break;
                case '1':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '系统减少';
                    $data['status']     = 2;
                    $data['pointtype']  = 7;
                    break;
                case '2':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '系统增加';
                    $data['status']     = 2;
                    $data['pointtype']  = 2;
                    break;
                case '3':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'Ibos360';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = 'RD手动转EP';
                    $data['status']     = 2;
                    $data['pointtype']  = 3;
                    break;
                case '4':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '转账出';
                    $data['status']     = 2;
                    $data['pointtype']  = 4;
                    break;
                case '5':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '转账入';
                    $data['status']     = 2;
                    $data['pointtype']  = 5;
                    break;
                case '6':
                    $data['feepoint']   = $tmpe['getpoint']*0.05;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '提现出';
                    $data['status']     = 2;
                    $data['pointtype']  = 6;
                    break;
                case '7':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '消费出';
                    $data['status']     = 2;
                    $data['pointtype']  = 7;
                    break;
                case '8':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统驳回';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '提现驳回';
                    $data['status']     = 3;
                    $data['pointtype']  = 8;
                    break;
            }
            $iu_point          = $tmpe['leftpoint']?$tmpe['leftpoint']:$user['iu_point'];
            $data['getpoint']  = $tmpe['getpoint'];
            $data['realpoint'] = bcsub($tmpe['getpoint'],$data['feepoint'],4);
            $data['date']      = $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['content']   = $tmpe['content']?:$data['send'].'在'.$data['date'].'时,'.$type.$data['getpoint'].'EP到'.$data['received'].',剩EP余额'.$iu_point;
            $data['handletime']= $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['opename']   = $session['user']['username'];
            $data['leftpoint'] = $iu_point;
            $data['iu_unpoint']= $user['iu_unpoint'];
            $data['pointNo']   = $tmpe['pointNo']?$tmpe['pointNo']:'EP'.date('YmdHis').rand(100000, 999999);
            $data['whichApp']  = 5;
            // p($data);die;
            $add = D('Getpoint')->add($data);
            if($add){
                if($tmpe['pointtype']==8){
                    $mape = array(
                       'pointNo'    =>$data['pointNo'],
                       'getpoint'   =>$data['getpoint'],
                       'iuid'       =>$data['iuid'],
                       'hu_nickname'=>$data['hu_nickname'],
                       'hu_username'=>$data['hu_username'],
                       'feepoint'   =>$data['getpoint']*0.05,
                       'realpoint'  =>bcsub($tmpe['getpoint'],$data['getpoint']*0.05,4),
                       'date'       =>$data['date'],
                       'content'    =>$tmpe['content']?:$data['received'].'在'.$data['date'].'时,申请提现'.$data['getpoint'].'EP到系统,剩EP余额'.$iu_point,
                       'handletime' =>$data['handletime'],
                       'opename'    =>$data['opename'],
                       'leftpoint'  =>$data['leftpoint'],
                       'iu_unpoint' =>$data['iu_unpoint'],
                       'send'       =>$data['received'],
                       'received'   =>'系统',
                       'status'     =>$data['status'],
                       'pointtype'  =>6,
                       'whichApp'   =>5
                    );
                    D('Getpoint')->add($mape);
                }
                $this->success('添加成功');
            }else{
                $this->error("添加失败"); 
            }
        }else{
            $this->error("添加失败,请填写正确积分"); 
        }
    }
    /**
    *编辑积分记录
    **/
    public function edit_point(){
        $tmpe     = I('post.');
        $session  = session();
        $user     = D('User')->where(array('CustomerID'=>$tmpe['hu_nickname']))->find();
        $data['iuid'] = $user['iuid'];
        $data['hu_username'] = $user['hu_username'];
        $data['hu_nickname'] = $user['hu_nickname'];
        // p($data);die;
        if($tmpe['getpoint']){
            switch ($tmpe['pointtype']) {
                case '1':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '系统减少';
                    $data['pointtype']  = 7;
                    break;
                case '2':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '系统增加';
                    $data['pointtype']  = 2;
                    break;
                case '3':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'Ibos360';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = 'RD手动转EP';
                    $data['pointtype']  = 3;
                    break;
                case '4':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '转账出';
                    $data['pointtype']  = 4;
                    break;
                case '5':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '转账入';
                    $data['pointtype']  = 5;
                    break;
                case '6':
                    $data['feepoint']   = $tmpe['getpoint']*0.05;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '提现出';
                    $data['pointtype']  = 6;
                    break;
                case '7':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:$user['hu_nickname'];
                    $data['received']   = $tmpe['received']?$tmpe['received']:'系统';
                    $type               = '消费出';
                    $data['pointtype']  = 7;
                    break;
                case '8':
                    $data['feepoint']   = 0;
                    $data['send']       = $tmpe['send']?$tmpe['send']:'系统驳回';
                    $data['received']   = $tmpe['received']?$tmpe['received']:$user['hu_nickname'];
                    $type               = '提现驳回';
                    $data['pointtype']  = 8;
                    break;
            }
            $iu_point          = $tmpe['leftpoint']?$tmpe['leftpoint']:$user['iu_point'];
            $data['getpoint']  = $tmpe['getpoint'];
            $data['realpoint'] = bcsub($tmpe['getpoint'],$data['feepoint'],4);
            $data['date']      = $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['content']   = $tmpe['content']?:$data['send'].'在'.$data['date'].'时,'.$type.$data['getpoint'].'EP到'.$data['received'].',剩EP余额'.$iu_point;
            $data['handletime']= $tmpe['date']?date('Y-m-d H:i:s',strtotime($tmpe['date'])):date('Y-m-d H:i:s');
            $data['opename']   = $session['user']['username'];
            $data['leftpoint'] = $iu_point;
            $data['iu_unpoint']= $user['iu_unpoint'];
            $data['pointNo']   = $tmpe['pointNo']?$tmpe['pointNo']:'EP'.date('YmdHis').rand(100000, 999999);
            $data['whichApp']  = 5;
            // p($data);die;
            $add = D('Getpoint')->where(array('igid'=>$tmpe['igid']))->save($data);
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
    *删除积分记录
    **/
    public function delete_point(){
        $igid   = I('get.id');
        $delete = D('Getpoint')->where(array('igid'=>$igid))->delete();
        if($delete){
            $this->success('删除成功');
        }else{
            $this->error("删除失败"); 
        }
    }
    /**
    *日志记录
    * @param type   1
    * @param action 0积分提现的提交 1给他人转出 2他人转入 3支付 4积分提现的审核 5凭空发放 6rd转ep
    **/
    public function userlog(){
        $word      = trim(I('get.word',''));
        $nickname  = trim(I('get.hu_nickname',''));
        $iuid      = D('User')->where(array('CustomerID'=>$nickname))->getfield('iuid');
        $type      = 1;
        $action    = '0,1,2,3,5,6,7';
        $starttime = strtotime(I('get.starttime'))?strtotime(I('get.starttime')):0;
        $endtime   = strtotime(I('get.endtime'))?strtotime(I('get.endtime'))+86400:time();
        $assign    = D('Log')->getAllLog(D('Log'),$word,$starttime,$endtime,$action,$type,$iuid,$order='create_time desc',$limit=50);
        // p($assign);die;
        $this->assign($assign);
        $this->assign('word',$word);
        $this->assign('nickname',$nickname);
        $this->assign('starttime',I('get.starttime'));
        $this->assign('endtime',I('get.endtime'));
        $this->display();
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