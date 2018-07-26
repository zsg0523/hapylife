<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
* 提现model
**/
class GetpointModel extends BaseModel{
	/**
	* 获取所有提现列表带分页
	* @param status 0一审 1二审 2审核完毕 3审核不通过
	**/
	public function getAllData($model,$word,$type,$starttime,$endtime,$status,$order='',$limit=50,$field=''){
		if($type==1){
			if($endtime){
				$count=$model
				    ->alias('ig')
		            ->join('__USER__ iu ON ig.iuid=iu.iuid')
		            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->count();
			}else{
				$count=$model
				    ->alias('ig')
		            ->join('__USER__ iu ON ig.iuid=iu.iuid')
		            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->count();
			}
		}else{
			if($starttime){
				if($endtime){
					$count=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime),array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->count();
				}else{
					$count=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->count();
				}
			}else{
				if($endtime){
					$count=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->count();
				}else{
					$count=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6')))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->count();
				}
			}
		}
		$page=new_page($count,$limit);
		//获取分页数据
		if($type==1){
			if($endtime){
				$list=$model
				    ->alias('ig')
		            ->join('__USER__ iu ON ig.iuid=iu.iuid')
		            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();
			}else{
				$list=$model
					->alias('ig')
			        ->join('__USER__ iu ON ig.iuid=iu.iuid')
			        ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->order($order)
					->limit($page->firstRow.','.$page->listRows)
					->select();			
			}
		}else{
			if($starttime){
				if($endtime){
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime),array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();			
				}
			}else{
				if($endtime){
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();
				}else{
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6')))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->limit($page->firstRow.','.$page->listRows)
						->select();			
				}
			}
		}

		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}

	/**
	* 输出Excel是所获取的格式
	**/
	public function getExcelData($model,$word,$type,$starttime,$endtime,$status,$order=''){
		if($type==1){
			if($endtime){
				$list=$model
				    ->alias('ig')
		            ->join('__USER__ iu ON ig.iuid=iu.iuid')
		            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->order($order)
					->select();
			}else{
				$list=$model
					->alias('ig')
			        ->join('__USER__ iu ON ig.iuid=iu.iuid')
			        ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
				    ->where(array('pointtype'=>array('in','6'),'date'=>array(array('egt',$starttime))))
					->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
					->order($order)
					->select();			
			}
		}else{
			if($starttime){
				if($endtime){
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime),array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->select();
				}else{
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('egt',$starttime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->select();			
				}
			}else{
				if($endtime){
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6'),'handletime'=>array(array('elt',$endtime))))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->select();
				}else{
					$list=$model
					    ->alias('ig')
			            ->join('__USER__ iu ON ig.iuid=iu.iuid')
			            ->field('igid,pointno,ig.hu_nickname,ig.hu_username,ig.iu_bank,ig.iu_bankbranch,ig.iu_bankbranch,ig.iu_bankaccount,ig.iu_bankprovince,ig.iu_bankcity,iu.teamCode,ig.iuid,getpoint,feepoint,realpoint,ig.date,opename,handletime,content,ig.status')
					    ->where(array('pointtype'=>array('in','6')))
						->where(array('ig.hu_nickname|pointNo|ig.hu_username|iu.BankName|iu.SubName|iu.BankAccount|teamcode'=>array('like','%'.$word.'%'),'status'=>array('in',$status)))
						->order($order)
						->select();			
				}
			}
		}
		return $list;
	}
	/**
	*原文件excel
	**/
	public function export_excel($data){
		$filename=date('YmdHis',time()).time();
		$getpoint =0;
		$realpoint=0;
		$feepoint =0;
		$title   = array('会员号','提现日期','提现金额','提现净额','手续费','开户银行','开户支行','银行账号','持卡人姓名','银行所在省','银行所在市','状态','处理日期');
		foreach ($data as $k => $v) {
			$content[$k]['hu_nickname']    = $v['hu_nickname'];
			$content[$k]['date']           = $v['date'];
			$content[$k]['getpoint']       = $v['getpoint']*100;
			$content[$k]['realpoint']      = $v['realpoint']*100;
			$content[$k]['feepoint']       = $v['feepoint']*100;
			$content[$k]['iu_bank']        = $v['iu_bank'];
			$content[$k]['iu_bankbranch']  = $v['iu_bankbranch'];
			$content[$k]['iu_bankaccount'] = $v['iu_bankaccount'];
			$content[$k]['iu_bankuser']    = $v['hu_username'];
			$content[$k]['iu_bankprovince']= $v['iu_bankprovince'];
			$content[$k]['iu_bankcity']    = $v['iu_bankcity'];
			$getpoint  = bcadd($getpoint,$v['getpoint']*100,4);
			$realpoint = bcadd($realpoint,$v['realpoint']*100,4);
			$feepoint  = bcadd($feepoint,$v['feepoint']*100,4);
			switch ($v['status']) {
				case '0':
					$content[$k]['ir_status'] = '已申请';
					break;
				case '1':
					$content[$k]['ir_status'] = '处理中';
					break;
				case '2':
					$content[$k]['ir_status'] = '审核通过';
					break;
				case '3':
					$content[$k]['ir_status'] = '提现失败';
					break;
			}
			$content[$k]['handletime']     = $v['handletime'];
		}
		$content[count($data)+1]['hu_nickname']    = '总额:';
		$content[count($data)+1]['date']           = '';
		$content[count($data)+1]['getpoint'] = $getpoint;
		$content[count($data)+1]['realpoint']= $realpoint;
		$content[count($data)+1]['feepoint'] = $feepoint;
		$content[count($data)+1]['iu_bank']        = '';
		$content[count($data)+1]['iu_bankbranch']  = '';
		$content[count($data)+1]['iu_bankaccount'] = '';
		$content[count($data)+1]['iu_bankuser']    = '';
		$content[count($data)+1]['iu_bankprovince']= '';
		$content[count($data)+1]['iu_bankcity']    = '';
    	create_csv($content,$title,$filename);
		return;
    }
	/**
	*快钱excel
	**/
	public function kq_excel($data){
		$filename='快钱'.date('YmdHis',time()).time();
		$title   = array('城市','银行名称','开户行名称','收款方姓名','收款方银行账号','金额','备注','商家订单号');
		foreach ($data as $k => $v) {
			$content[$k]['iu_bankcity']    = $v['iu_bankcity'];
			$content[$k]['iu_bank']        = $v['iu_bank'];
			$content[$k]['iu_bankbranch']  = $v['iu_bankbranch'];
			$content[$k]['iu_bankuser']    = $v['hu_username'];
			$content[$k]['iu_bankaccount'] = $v['iu_bankaccount'];
			$content[$k]['realpoint']      = $v['realpoint']*100;
			$content[$k]['date']           = date('Y-m-d',strtotime($v['date']));
			$content[$k]['number']         ='';
		}
    	create_csv($content,$title,$filename);
		return;
    }
	/**
	* 获取所有成功操作积分--每月总额
	**/
	public function getAllPoint($model,$hu_nickname,$status){
		//获取分页数据
		if($hu_nickname){
			$list=$model
				->where(array('status'=>array('in',$status),'hu_nickname'=>$hu_nickname))
				->select();
		}else{
			$list=$model
				->where(array('status'=>array('in',$status)))
				->select();
		}
		foreach ($list as $key => $value) {
            $listdate[$key] = date('Y-m',strtotime($value['date']));
        }
        //去除相同值
        $ceipt = array_unique($listdate);
        foreach ($ceipt as $key => $value) {
            $ceiptarr[] = $value;
        }
        foreach ($ceiptarr as $key => $value) {
        	foreach ($list as $k => $v) {
	        	if(date('Y-m',strtotime($v['date']))==$value){
	        		$mape[$value][] = $v;
	        	}
	        }
        }
        foreach ($mape as $key => $value) {
        	foreach ($value as $k => $v) {
        		$arr[$key]['date']      = $key;
        		$arr[$key]['feepoint'] += $v['feepoint']*100;
        		switch ($v['pointtype']) {
        			case '1':
        				$arr[$key]['realpoint1'] = bcadd($arr[$key]['realpoint1'],$v['getpoint'],4);
        				$arr[$key]['pointtype1'] = $v['pointtype'];
        				break;
	        		case '2':
	        			$arr[$key]['getpoint2']  = bcadd($arr[$key]['getpoint2'],$v['getpoint'],4);
	        			$arr[$key]['realpoint2'] = bcadd($arr[$key]['realpoint2'],$v['getpoint'],4);
	        			$arr[$key]['pointtype2'] = $v['pointtype'];
	        			break;
	        		case '3':
	        			$arr[$key]['realpoint3'] = bcadd($arr[$key]['realpoint3'],$v['getpoint'],4);
	        			$arr[$key]['pointtype3'] = $v['pointtype'];
	        			break;
	        		case '4':
	        			$arr[$key]['realpoint4'] = bcadd($arr[$key]['realpoint4'],$v['getpoint'],4);
	        			$arr[$key]['pointtype4'] = $v['pointtype'];
	        			break;
	        		case '5':
	        			$arr[$key]['realpoint5'] = bcadd($arr[$key]['realpoint5'],$v['getpoint'],4);
	        			$arr[$key]['pointtype5'] = $v['pointtype'];
	        			break;
	        		case '6':
	        			$arr[$key]['getpoint6']  = bcadd($arr[$key]['getpoint6'],$v['getpoint'],4);
	        			$arr[$key]['realpoint6'] = bcadd($arr[$key]['realpoint6'],$v['getpoint'],4);
	        			$arr[$key]['realpoint']  = bcadd($arr[$key]['realpoint'],$v['realpoint']*100,4);
	        			$arr[$key]['pointtype6'] = $v['pointtype'];
	        			break;
	        		case '7':
	        			$arr[$key]['realpoint7'] = bcadd($arr[$key]['realpoint7'],$v['getpoint'],4);
	        			$arr[$key]['pointtype7'] = $v['pointtype'];
	        			break;
	        		case '8':
	        			$arr[$key]['realpoint8'] = bcadd($arr[$key]['realpoint8'],$v['getpoint'],4);
	        			$arr[$key]['pointtype8'] = $v['pointtype'];
		        		break;
        		}
        	}
        }
        foreach ($arr as $key => $value) {
        	$tmpe[$key] = $value;
        	if(!$value['realpoint1']){
        		$tmpe[$key]['realpoint1'] = 0;
        	}
	        if(!$value['realpoint2']){
	        	$tmpe[$key]['realpoint2'] = 0;
	        }
	        if(!$value['realpoint3']){
	        	$tmpe[$key]['realpoint3'] = 0;
	        }
	        if(!$value['realpoint4']){
	        	$tmpe[$key]['realpoint4'] = 0;
	        }
	        if(!$value['realpoint5']){
	        	$tmpe[$key]['realpoint5'] = 0;
	        }
	        if(!$value['realpoint6']){
	        	$tmpe[$key]['getpoint6']  = 0;
	        	$tmpe[$key]['realpoint']  = 0;
	        	$tmpe[$key]['realpoint6'] = 0;
	        }
	        if(!$value['realpoint7']){
	        	$tmpe[$key]['realpoint7'] = 0;
	        }
	        if(!$value['realpoint8']){
		        $tmpe[$key]['realpoint8'] = 0;
		    }
	    }
        foreach ($tmpe as $key => $value) {
        	$data[] = $value;
        }
        //排序
        $die = array_sort($data,'date','desc');
		$data=array(
			'data'=>$die,
			);
		return $data;
	}
	/**
	* 获取所有成功操作积分--每日总额
	**/
	public function getPointDay($model,$hu_nickname,$date,$status){
		//获取分页数据
		if($hu_nickname){
			$list=$model
				->where(array('status'=>array('in',$status),'hu_nickname'=>$hu_nickname))
				->select();
		}else{
			$list=$model
				->where(array('status'=>array('in',$status)))
				->select();
		}
		foreach ($list as $key => $value) {
			if(date('Y-m',strtotime($value['date']))==$date){
				$listdate[] = $value;
			}
		}
		foreach ($listdate as $key => $value) {
            $listmape[$key] = date('Y-m-d',strtotime($value['date']));
        }
		// p($list);die;
        //去除相同值
        $ceipt = array_unique($listmape);
        foreach ($ceipt as $key => $value) {
            $ceiptarr[] = $value;
        }
        foreach ($ceiptarr as $key => $value) {
        	foreach ($listdate as $k => $v) {
	        	if(date('Y-m-d',strtotime($v['date']))==$value){
	        		$mape[$value][] = $v;
	        	}
	        }
        }
        foreach ($mape as $key => $value) {
        	foreach ($value as $k => $v) {
        		$arr[$key]['date']      = $key;
        		$arr[$key]['feepoint'] += $v['feepoint']*100;
        		switch ($v['pointtype']) {
        			case '1':
        				$arr[$key]['realpoint1'] = bcadd($arr[$key]['realpoint1'],$v['getpoint'],4);
        				$arr[$key]['pointtype1'] = $v['pointtype'];
        				break;
	        		case '2':
	        			$arr[$key]['getpoint2']  = bcadd($arr[$key]['getpoint2'],$v['getpoint'],4);
	        			$arr[$key]['realpoint2'] = bcadd($arr[$key]['realpoint1'],$v['getpoint'],4);
	        			$arr[$key]['pointtype2'] = $v['pointtype'];
	        			break;
	        		case '3':
	        			$arr[$key]['realpoint3'] = bcadd($arr[$key]['realpoint3'],$v['getpoint'],4);
	        			$arr[$key]['pointtype3'] = $v['pointtype'];
	        			break;
	        		case '4':
	        			$arr[$key]['realpoint4'] = bcadd($arr[$key]['realpoint4'],$v['getpoint'],4);
	        			$arr[$key]['pointtype4'] = $v['pointtype'];
	        			break;
	        		case '5':
	        			$arr[$key]['realpoint5'] = bcadd($arr[$key]['realpoint5'],$v['getpoint'],4);
	        			$arr[$key]['pointtype5'] = $v['pointtype'];
	        			break;
	        		case '6':
	        			$arr[$key]['getpoint6']  = bcadd($arr[$key]['getpoint6'],$v['getpoint'],4);
	        			$arr[$key]['realpoint6'] = bcadd($arr[$key]['realpoint6'],$v['getpoint'],4);
	        			$arr[$key]['realpoint']  = bcadd($arr[$key]['realpoint'],$v['realpoint']*100,4);
	        			$arr[$key]['pointtype6'] = $v['pointtype'];
	        			break;
	        		case '7':
	        			$arr[$key]['realpoint7'] = bcadd($arr[$key]['realpoint7'],$v['getpoint'],4);
	        			$arr[$key]['pointtype7'] = $v['pointtype'];
	        			break;
	        		case '8':
	        			$arr[$key]['realpoint8'] = bcadd($arr[$key]['realpoint8'],$v['getpoint'],4);
	        			$arr[$key]['pointtype8'] = $v['pointtype'];
		        		break;
        		}
        	}
        }
        foreach ($arr as $key => $value) {
        	$tmpe[$key] = $value;
        	if(!$value['realpoint1']){
        		$tmpe[$key]['realpoint1'] = 0;
        		$tmpe[$key]['pointtype1'] = 1;
        	}
	        if(!$value['realpoint2']){
	        	$tmpe[$key]['realpoint2'] = 0;
	        	$tmpe[$key]['pointtype2'] = 2;
	        }
	        if(!$value['realpoint3']){
	        	$tmpe[$key]['realpoint3'] = 0;
	        	$tmpe[$key]['pointtype3'] = 3;
	        }
	        if(!$value['realpoint4']){
	        	$tmpe[$key]['realpoint4'] = 0;
	        	$tmpe[$key]['pointtype4'] = 4;
	        }
	        if(!$value['realpoint5']){
	        	$tmpe[$key]['realpoint5'] = 0;
	        	$tmpe[$key]['pointtype5'] = 5;
	        }
	        if(!$value['realpoint6']){
	        	$tmpe[$key]['getpoint6']  = 0;
	        	$tmpe[$key]['realpoint']  = 0;
	        	$tmpe[$key]['realpoint6'] = 0;
	        	$tmpe[$key]['pointtype6'] = 6;
	        }
	        if(!$value['realpoint7']){
	        	$tmpe[$key]['realpoint7'] = 0;
	        	$tmpe[$key]['pointtype7'] = 7;
	        }
	        if(!$value['realpoint8']){
		        $tmpe[$key]['realpoint8'] = 0;
	        }
        }
        foreach ($tmpe as $key => $value) {
        	$data[] = $value;
        }
        //排序
        $die = array_sort($data,'date','desc');
		$data=array(
			'data'=>$die,
			);
		return $data;
	}
	/**
	* 获取所有成功操作积分--总额详情
	**/
	public function getAllPointInfo($model,$type,$date,$hu_nickname,$starttime,$endtime,$limit=50,$field='',$order=''){
		if($hu_nickname){
			if($type){
				if($date){
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();
					}
				}else{
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->count();
					}
				}
			}else{
				if($date){
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();	
					}
				}else{
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->count();		
					}
				}
			}
		}else{
			if($type){
				if($date){
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->count();	
					}
				}else{
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->count();						
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->count();
					}
				}
			}else{
				if($date){
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->count();
					}
				}else{
					if($endtime){
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
						     ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->count();
					}else{
						$count=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
						     ->where(array('date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->count();	
					}
				}
			}
		}
		$page=new_page($count,$limit);		
		//获取分页数据
		if($hu_nickname){
			if($type){
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();					
					}		
				}
			}else{
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();	
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}			
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();		
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();	
					}
				}
			}
		}else{
			if($type){
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();			
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();						
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();	
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();	
					}	
				}
			}else{
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();		
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->limit($page->firstRow.','.$page->listRows)
							 ->select();		
					}
				}
			}
		}
		$data=array(
			'data'=>$list,
			'page'=>$page->show()
			);
		return $data;
	}
	/**
	* 获取所有成功操作积分--总额详情/不分页等待导出excel数据
	**/
	public function getAllExcel($model,$type,$date,$hu_nickname,$starttime,$endtime,$order=''){
		if($hu_nickname){
			if($type){
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('pointtype'=>array('in',$type)))
							 ->order($order)
							 ->select();					
					}		
				}
			}else{
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();	
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();
					}			
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->select();		
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('hapylife_getpoint.hu_nickname'=>$hu_nickname,'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->select();	
					}
				}
			}
		}else{
			if($type){
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();			
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->where(array('date'=>array('like','%'.$date.'%')))
							 ->order($order)
							 ->select();						
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->select();	
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('pointtype'=>array('in',$type),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->select();	
					}	
				}
			}else{
				if($date){
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->select();
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array('like','%'.$date.'%'),'date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->select();
					}
				}else{
					if($endtime){
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array(array('egt',$starttime),array('elt',$endtime))))
							 ->order($order)
							 ->select();		
					}else{
						$list=$model
						     ->join('hapylife_user on hapylife_getpoint.iuid = hapylife_user.iuid')
							 ->where(array('date'=>array(array('egt',$starttime))))
							 ->order($order)
							 ->select();		
					}
				}
			}
		}
		return $list;
	}
	/**
	*导出积分详情数据
	**/
	public function pointInfo_excel($data){
		$filename=date('YmdHis',time()).time();
		$getpoint =0;
		$title   = array('会员号','姓名','团队','类型','积分','日期');
		foreach ($data as $k => $v) {
			$content[$k]['hu_nickname']    = $v['hu_nickname'];
			$content[$k]['hu_username']    = $v['hu_username'];
			$content[$k]['teamcode']       = $v['teamcode'];
			switch ($v['pointtype']) {
				case '1':
					$content[$k]['typepoint'] = '系统减少';
					break;
				case '2':
					$content[$k]['typepoint'] = '系统增加';
					break;
				case '3':
					$content[$k]['typepoint'] = '新增EP';
					break;
				case '4':
					$content[$k]['typepoint'] = '转出';
					break;
				case '5':
					$content[$k]['typepoint'] = '转入';
					break;
				case '6':
					$content[$k]['typepoint'] = '提现';
					break;
				case '7':
					$content[$k]['typepoint'] = '消费';
					break;
				case '8':
					$content[$k]['typepoint'] = '驳回';
					break;
			}
			$content[$k]['getpoint']       = $v['getpoint'];
			$content[$k]['date']           = $v['date'];
			$getpoint = bcadd($getpoint,$v['getpoint'],4);
		}
		$content[count($data)+1]['hu_nickname'] = '总额:';
		$content[count($data)+1]['hu_username'] = '';
		$content[count($data)+1]['teamcode']    = '';
		$content[count($data)+1]['typepoint']        = '';
		$content[count($data)+1]['getpoint']    = $getpoint;
		$content[count($data)+1]['date']        = '';
    	create_csv($content,$title,$filename);
		return;
    }
	//封装curl的调用接口，post请求方式
	function doCurlPostRequest($url,$requestString,$timeout=5){
	    $con = curl_init((string)$url);
	    curl_setopt($con,CURLOPT_HEADER,false);
	    curl_setopt($con,CURLOPT_POSTFIELDS, http_build_query($requestString));
	    curl_setopt($con,CURLOPT_POST,true);
	    curl_setopt($con,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($con,CURLOPT_TIMEOUT,$timeout);
	    $return = curl_exec($con);
	    return $return;
	}
	/**
     * 将json字符串转化成php数组
     * @param  $json_str
     * @return $json_arr
     */
    public function json_to_array($json_str){
        if(is_null(json_decode($json_str))){
            $json_str = $json_str;
        }else{
            $json_str = json_decode($json_str);
        }
        $json_arr=array();

        foreach($json_str as $k=>$w){
            if(is_object($w)){               
                $json_arr[$k]= $this->json_to_array($w); //判断类型是不是object
            }else if(is_array($w)){
                $json_arr[$k]= $this->json_to_array($w);
            }else{
                $json_arr[$k]= $w;
            }
        }
        return $json_arr;
    }
	/**
	* 写入日志文件
	* PHP_EOL 换行常量，windows \r\n; max \r; liunx \n;
	* FILE_APPEND 在文件末尾追加数据
	**/
	function logTest($data){
	    $log = date('Y-m-d H:i:s').$data.'PHP_EOL';
	    $add  = file_put_contents('./log.txt', $log,FILE_APPEND);
	    return;
	} 
	 /**
     * [launch 模拟post发起请求类]
     * @param  [type] $url     [请求地址]
     * @param  [type] $data_po [请求数组]
     * @return [type] result   [请求URL返回参数]
     */
    function launch($url,$data_po){
        $query = http_build_query($data_po); //转换数组aa&&bb

        $options['http'] = array(
             'timeout'=>60,
             'method' => 'POST',
             'header' => 'Content-type:application/x-www-form-urlencoded',
             'content' => $query
            );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        p($result);die;
        return $result;
    }
    /**
     * 获取分页数据
     * Nlc或Iobs综合消费
     */
    public function getPage($model,$word,$starttime,$endtime,$ir_status,$ir_paytype,$ir_area,$order='',$limit=50,$field=''){
        $count=$model
            ->alias('r')
            ->join('hapylife_user u on r.iuid = u.iuid ')
            ->where(
            	array(
            		'u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),
            		'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),
            		'ir_status'=>array('in',$ir_status),
            		'ir_paytype'=>array('in',$ir_paytype),
            		'ir_area'=>$ir_area
            		)
            	)
            ->count();
        // p($count);die;
        $page=new_page($count,$limit);
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('r')
            	->join('hapylife_user u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_paytype'=>array('in',$ir_paytype),'ir_area'=>$ir_area))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        }else{
            $list=$model
            	->alias('r')
            	->join('hapylife_user u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_paytype'=>array('in',$ir_paytype),'ir_area'=>$ir_area))
                ->order($order)
                ->limit($page->firstRow.','.$page->listRows)
                ->select();         
        }
        // p($mape);die;
        $data=array(
            'data'=>$list,
            'page'=>$page->show()
            );
        return $data;
    }
    /**
     * 获取分页数据
     * Nlc或Iobs综合消费数据等待导出excel
     */
    public function getPage_excel($model,$word,$starttime,$endtime,$ir_status,$ir_paytype,$ir_area,$order=''){
        // 获取分页数据
        if (empty($field)) {
            $list=$model
            	->alias('r')
            	->join('hapylife_user u on r.iuid = u.iuid ')
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_paytype'=>array('in',$ir_paytype),'ir_area'=>$ir_area))
                ->order($order)
                ->select();
        }else{
            $list=$model
            	->alias('r')
            	->join('hapylife_user u on r.iuid = u.iuid ')
                ->field($field)
                ->where(array('u.hu_nickname|u.hu_username|ir_receiptnum|ir_360_orderId|ir_360_orderNo|teamCode'=>array('like','%'.$word.'%'),'create_time'=>array(array('egt',$starttime),array('elt',$endtime)),'ir_status'=>array('in',$ir_status),'ir_paytype'=>array('in',$ir_paytype),'ir_area'=>$ir_area))
                ->order($order)
                ->select();         
        }
        // p($mape);die;
        return $list;
    }
    /**
    *积分余额表
    **/
    public function getPoint($model,$word,$starttime,$endtime,$order='',$limit=50){
		if(empty($word)){
			$count=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->count();
		}else{
			$count=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->where(array('hu_nickname|hu_username|iu_upid|hu.hu_hpname|teamCode'=>array('like','%'.$word.'%')))
				->count();
		}
		$page=new_page($count,$limit);
		if(empty($word)){
			$list=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}else{
			$list=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->where(array('hu_nickname|hu_username|iu_upid|hu.hu_hpname|teamCode'=>array('like','%'.$word.'%')))
			    ->limit($page->firstRow.','.$page->listRows)
				->select();
		}
		foreach ($list as $key => $value) {
			$data[$key]=$value;
			if($starttime){
				$data[$key]['openpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('elt',$starttime))))->order('date desc')->select();
				if($data[$key]['openpoint']){
					$data[$key]['open'] = $data[$key]['openpoint'][0]['leftpoint'];
				}else{
					$data[$key]['open'] = 0;
				}
				if($endtime){
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))->order('date desc')->select();
				}else{
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('egt',$starttime))))->order('date desc')->select();
				}
			}else{
				$data[$key]['open'] = 0;
				if($endtime){
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('elt',$endtime))))->order('date desc')->select();
				}else{
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2')))->order('date desc')->select();
				}
			}
			if($data[$key]['allpoint']){
				$data[$key]['end'] = $data[$key]['allpoint'][0]['leftpoint'];
			}else{
				$data[$key]['end'] = $value['iu_point'];
			}
		}
		foreach ($data as $key => $value) {
			$userDatep[$key] = $value;
			foreach ($value['allpoint'] as $k => $v) {
				switch ($v['pointtype']) {
					case '1':
        				$userDatep[$key]['realpoint1'] = bcadd($userDatep[$key]['realpoint1'],$v['getpoint'],4);
        				break;
	        		case '2':
	        			$userDatep[$key]['realpoint2'] = bcadd($userDatep[$key]['realpoint2'],$v['getpoint'],4);
	        			break;
	        		case '3':
	        			$userDatep[$key]['realpoint3'] = bcadd($userDatep[$key]['realpoint3'],$v['getpoint'],4);
	        			break;
	        		case '4':
	        			$userDatep[$key]['realpoint4'] = bcadd($userDatep[$key]['realpoint4'],$v['getpoint'],4);
	        			break;
	        		case '5':
	        			$userDatep[$key]['realpoint5'] = bcadd($userDatep[$key]['realpoint5'],$v['getpoint'],4);
	        			break;
	        		case '6':
        				$userDatep[$key]['realpoint6'] = bcadd($userDatep[$key]['realpoint6'],$v['getpoint'],4);
        				if($v['status']==2){
        					$userDatep[$key]['realpoint9'] = bcadd($userDatep[$key]['realpoint9'],$v['getpoint'],4);
        				}
	        			break;
	        		case '7':
	        			$userDatep[$key]['realpoint7'] = bcadd($userDatep[$key]['realpoint7'],$v['getpoint'],4);
	        			break;
	        		case '8':
	        			$userDatep[$key]['realpoint8'] = bcadd($userDatep[$key]['realpoint8'],$v['getpoint'],4);
	        			break;
        		}
			}
		}
		foreach ($userDatep as $key => $value) {
			$userDate[$key]              = $value;
			$userDate[$key]['deviation'] = bcsub($value['end'],bcadd($value['open'],bcsub(bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4),bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4),4),4),4);
		}
        $data=array(
			'data'=>$userDate,
			'page'=>$page->show()
		);        
		return $data;
	}
	/**
    *积分余额表excel数据
    **/
    public function getPoint_excel($model,$word,$starttime,$endtime,$order=''){
		if(empty($word)){
			$list=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->select();
		}else{
			$list=$model
				->order($order)
				->alias('iu')
				->join('left join __HRAC_USERS__ hu ON iu.iuid=hu.iuid')
				->where(array('hu_nickname|hu_username|iu_upid|hu.hu_hpname|teamCode'=>array('like','%'.$word.'%')))
				->select();
		}
		foreach ($list as $key => $value) {
			$data[$key]=$value;
			if($starttime){
				$data[$key]['openpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('elt',$starttime))))->order('date desc')->select();
				if($data[$key]['openpoint']){
					$data[$key]['open'] = $data[$key]['openpoint'][0]['leftpoint'];
				}else{
					$data[$key]['open'] = 0;
				}
				if($endtime){
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('egt',$starttime),array('elt',$endtime))))->order('date desc')->select();
				}else{
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('egt',$starttime))))->order('date desc')->select();
				}
			}else{
				$data[$key]['open'] = 0;
				if($endtime){
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2'),'date'=>array(array('elt',$endtime))))->order('date desc')->select();
				}else{
					$data[$key]['allpoint'] = D('IbosGetpoint')->where(array('hu_nickname'=>$value['hu_nickname'],'status'=>array('in','0,1,2')))->order('date desc')->select();
				}
			}
			if($data[$key]['allpoint']){
				$data[$key]['end'] = $data[$key]['allpoint'][0]['leftpoint'];
			}else{
				$data[$key]['end'] = $value['iu_point'];
			}
		}
		foreach ($data as $key => $value) {
			$userDatep[$key] = $value;
			foreach ($value['allpoint'] as $k => $v) {
				switch ($v['pointtype']) {
					case '1':
        				$userDatep[$key]['realpoint1'] = bcadd($userDatep[$key]['realpoint1'],$v['getpoint'],4);
        				break;
	        		case '2':
	        			$userDatep[$key]['realpoint2'] = bcadd($userDatep[$key]['realpoint2'],$v['getpoint'],4);
	        			break;
	        		case '3':
	        			$userDatep[$key]['realpoint3'] = bcadd($userDatep[$key]['realpoint3'],$v['getpoint'],4);
	        			break;
	        		case '4':
	        			$userDatep[$key]['realpoint4'] = bcadd($userDatep[$key]['realpoint4'],$v['getpoint'],4);
	        			break;
	        		case '5':
	        			$userDatep[$key]['realpoint5'] = bcadd($userDatep[$key]['realpoint5'],$v['getpoint'],4);
	        			break;
	        		case '6':
        				$userDatep[$key]['realpoint6'] = bcadd($userDatep[$key]['realpoint6'],$v['getpoint'],4);
	        			break;
	        		case '7':
	        			$userDatep[$key]['realpoint7'] = bcadd($userDatep[$key]['realpoint7'],$v['getpoint'],4);
	        			break;
	        		case '8':
	        			$userDatep[$key]['realpoint8'] = bcadd($userDatep[$key]['realpoint8'],$v['getpoint'],4);
	        			break;
        		}
			}
		}
		foreach ($userDatep as $key => $value) {
			$userDate[$key]              = $value;
			$userDate[$key]['deviation'] = bcsub($value['end'],bcadd($value['open'],bcsub(bcadd(bcadd(bcadd($value['realpoint2'],$value['realpoint3'],4),$value['realpoint5'],4),$value['realpoint8'],4),bcadd(bcadd(bcadd($value['realpoint1'],$value['realpoint4'],4),$value['realpoint6'],4),$value['realpoint7'],4),4),4),4);
		}     
		return $userDate;
	}
	/**
	*nlc消费excel
	**/
	public function nlcSynthe_excel($data){
		$filename=date('YmdHis',time()).time();
		$point = 0;
		$price = 0;
		$title   = array('订单编号','360订单ID','3602订单编号','会员','姓名','团队标签','金额(RMB)','EP积分','支付类型','创建时间','支付时间');
		foreach ($data as $k => $v) {
			$content[$k]['ir_receiptnum']  = $v['ir_receiptnum'];
			$content[$k]['ir_360_orderid'] = $v['ir_360_orderid'];
			$content[$k]['ir_360_orderno'] = $v['ir_360_orderno'];
			$content[$k]['hu_nickname']    = $v['hu_nickname'];
			$content[$k]['hu_username']    = $v['hu_username'];
			$content[$k]['teamcode']       = $v['teamcode'];
			$content[$k]['ir_price']       = $v['ir_price'];
			$content[$k]['ir_point']       = $v['ir_point'];
			$price  = bcadd($price,$v['ir_price'],4);
			$point  = sprintf("%.4f",$price/100);;
			switch ($v['ir_paytype']) {
				case '1':
					$content[$k]['ir_paytype'] = '微信支付';
					break;
				case '2':
					$content[$k]['ir_paytype'] = 'EP积分支付';
					break;
				case '3':
					$content[$k]['ir_paytype'] = '单据转账';
					break;
				case '4':
					$content[$k]['ir_paytype'] = '快钱支付';
					break;
			}
			$content[$k]['create_time']     = date('Y-m-d H:i:s',$v['create_time']);
			$content[$k]['update_time']     = date('Y-m-d H:i:s',$v['update_time']);
		}
		$content[count($data)+1]['ir_receiptnum']  = '总额:';
		$content[count($data)+1]['ir_360_orderid'] = '';
		$content[count($data)+1]['ir_360_orderno'] = '';
		$content[count($data)+1]['hu_nickname']    = '';
		$content[count($data)+1]['hu_username']    = '';
		$content[count($data)+1]['teamcode']       = '';
		$content[count($data)+1]['ir_price']       = $price;
		$content[count($data)+1]['ir_point']       = $point;
		$content[count($data)+1]['create_time']    = '';
		$content[count($data)+1]['update_time']    = '';
    	create_csv($content,$title,$filename);
		return;
    }
	/**
	*余额excel
	**/
	public function point_excel($data){
		$filename=date('YmdHis',time()).time();
		$point = 0;
		$price = 0;
		$title   = array('账号','中文姓名','英文姓名','团队标签','Open','现在可用','现在冻结','共提现','共消费','共转入','共转出','增加奖金','系统增加','系统减少','End','余额偏差值');
		foreach ($data as $k => $v) {
			$content[$k]['hu_nickname']    = $v['hu_nickname'];
			$content[$k]['hu_username']    = $v['hu_username'];
			$content[$k]['hu_username_en'] = $v['hu_username_en'];
			$content[$k]['teamcode']       = $v['teamcode'];
			$content[$k]['open']           = $v['open'];
			$content[$k]['teamcode']       = $v['teamcode'];
			$content[$k]['iu_point']       = $v['iu_point']?$v['iu_point']:0;
			$content[$k]['iu_unpoint']     = $v['iu_unpoint']?$v['iu_unpoint']:0;
			$content[$k]['realpoint6']     = $v['realpoint6']?$v['realpoint6']:0;
			$content[$k]['realpoint7']     = $v['realpoint7']?$v['realpoint7']:0;
			$content[$k]['realpoint5']     = $v['realpoint5']?$v['realpoint5']:0;
			$content[$k]['realpoint4']     = $v['realpoint4']?$v['realpoint4']:0;
			$content[$k]['realpoint3']     = $v['realpoint3']?$v['realpoint3']:0;
			$content[$k]['realpoint2']     = $v['realpoint2']?$v['realpoint2']:0;
			$content[$k]['realpoint1']     = $v['realpoint1']?$v['realpoint1']:0;
			$content[$k]['end']            = $v['end'];
			$content[$k]['deviation']      = $v['deviation'];
		}
    	create_csv($content,$title,$filename);
		return;
    }


    /**
    * 全部电子钱包流水查询
    * @param memeberNo 会员账号，不填默认全部会员
    * @param ewalletType 1奖金 2报单币
    * @param auditTimeStart/auditTimeEnd 起始时间/结束时间;格式 Y-m-d H:i:s
    * @param currentPage 当前页码
    **/
    public function getAllWalletdetail($memberNo,$ewalletType,$auditTimeStart,$auditTimeEnd,$currentPage,$pageSize){
        $remark         = 'API消费';
        $pageModel      = array(
                            "currentPage"  =>$currentPage,
                            "pageSize"     =>$pageSize
                        );
        $pageModel      = json_encode($pageModel);
        $ibos360_result = ibos360_getAllWalletdetail($memberNo,$ewalletType,$remark,$auditTimeStart,$auditTimeEnd,$pageModel);
       	return $ibos360_result;
    }

































}             