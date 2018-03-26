<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 畅捷支付
**/
class HapylifePayController extends HomeBaseController{
	/**
	* 直接支付请求接口（畅捷前台）   nmg_quick_onekeypay
	**/
	public function nmg_quick_onekeypay(){
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
		$postData['ReturnUrl']         = 'http://dev.chanpay.com/receive.php';// 前台跳转url
		$postData['Memo']              = '备注';
		// 4.4.2.8. 直接支付请求接口（畅捷前台） 业务参数++++++++++++++++++
		$postData['TrxId']             = $order_num; //外部流水号
		$postData['SellerId']          = '200001380239'; //商户编号，调用畅捷子账户开通接口获取的子账户编号;该字段可以传入平台id或者平台id下的子账户号;作为收款方使用；与鉴权请求接口中MerchantNo保持一致
		$postData['SubMerchantNo']     = '200001380239'; //子商户，在畅捷商户自助平台申请开通的子商户，用于自动结算
		$postData['ExpiredTime']       = '48h'; //订单有效期，取值范围：1m～48h。单位为分，如1.5h，可转换为90m。用来标识本次鉴权订单有效时间，超过该期限则该笔订单作废
		$postData['MerUserId']         = $order['iuid']; //用户标识
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
		$postData['NotifyUrl']         = 'http://apps.hapy-life.com/hapylife/index.php/Api/HapylifePay/notifyVerify';//异步通知地址
		$postData['AccessChannel']     = 'wap';//用户终端类型；web,wap
		$postData['Extension']         = '';//扩展字段
		$postData['Sign']              = rsaSign($postData);
		$postData['SignType']          = 'RSA'; //签名类型		
		$query                         = http_build_query($postData);
		$url                           = 'https://pay.chanpay.com/mag-unify/gateway/receiveOrder.do?'. $query; //该url为生产环境url
		$data['url']                   = $url;
		$this->ajaxreturn($data);
	}

	/**
	* 获取畅捷回调数据，验签更新订单状态
	**/
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
		$receipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->find();
		$cjPayStatus = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($map);
		//验签
		$return = rsaVerify($map);
		//更改订单状态
		if($return == "true" && $map['trade_status'] == 'TRADE_SUCCESS'){
			//修改用户最近订单日期/是否通过/等级/数量
            $tmpe['iuid']     =$receipt['iuid'];
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
				'ir_paytype' =>1,
			);
			$activaDate = D('Activation')->where(array('iuid'=>$receipt['iuid'],'is_tick'=>1))->order('datetime desc')->getfield('datetime');
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
				$delete= D('Activation')->where(array('iuid'=>$receipt['iuid'],'datetime'=>$time))->delete();
				$where =array('iuid'=>$receipt['iuid'],'ir_receiptnum'=>$map['outer_trade_no'],'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
				$save  = D('Activation')->add($where);
            // }   
        	$upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$map['outer_trade_no']))->save($status);
            $update  =D('User')->save($tmpe);
        	if($upreceipt){
        		//通知畅捷完成支付
				echo "success";
        	}
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

   //  public function freeay(){
   //  	$ir_receiptnum = I('post.ir_receiptnum');
   //  	$receipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
   //  	if($receipt){
   //  		//修改用户最近订单日期/是否通过/等级/数量
   //          $tmpe['iuid']     =$receipt['iuid'];
   //          $find             =D('User')->where(array('iuid'=>$receipt['iuid']))->find();
   //          if($find['number']==0){
   //          	$tmpe['OrderDate']= date("m/d/Y h:i:s A");
   //          	$OrderDate        = date("Y-m-d",strtotime("-1 month",time()));
   //              if($find['isnew']==0){
   //                  if($find['number']==0){
   //                      $tmpe['IsCheck'] = 1;   
   //                  }
   //              }else{
   //                  $tmpe['IsCheck'] = 2;
   //              }
   //          }else{
   //          	$OrderDate       = $find['orderdate'];
   //              $tmpe['IsCheck'] = 2;
   //          }
   //          $tmpe['DistributorType'] = D('Product')->where(array('ipid'=>$receipt['ipid']))->getfield('ip_after_grade');
   //          $tmpe['Number']   =$find['number']+1;
			// $status  = array(
			// 	'ir_status'  =>2,
			// 	'ir_paytype' =>1
			// );
			// $activaDate = D('Activation')->where(array('iuid'=>$receipt['iuid'],'is_tick'=>1))->order('datetime desc')->getfield('datetime');
			// if(empty($activaDate)){
			// 	$activa = $OrderDate;
			// }else{
			// 	$activa = $activaDate;
			// }
			// $day = date('d',strtotime($OrderDate));
   //          if($day>=28){
   //              $allday = 28;
   //          }else{
   //              $allday = $day;
   //          }
   //          $ddd    = $allday-1;
   //          if($ddd>=10){
   //              $oneday = $ddd;
   //          }else{
   //              $oneday = '0'.$ddd;
   //          }
   //          //删除原先未激活，添加激活
   //      	$time  = date("Y-m",strtotime("+1 month",strtotime($activa)));
   //      	$year  = date("Y年m月",strtotime("+1 month",strtotime($activa))).$allday.'日';
			// $endday= date("Y年m月",strtotime("+2 month",strtotime($activa))).$oneday.'日';
			// $delete= D('Activation')->where(array('iuid'=>$receipt['iuid'],'datetime'=>$time))->delete();
			// $where =array('iuid'=>$receipt['iuid'],'ir_receiptnum'=>$ir_receiptnum,'is_tick'=>1,'datetime'=>$time,'hatime'=>$year,'endtime'=>$endday);
			// $save  = D('Activation')->add($where);
			// $upreceipt = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($status);
			// if($save && $upreceipt){
			// 	$tmpe['DistributorType'] = 'Platinum';
			// 	$update=D('User')->save($tmpe);
			// 	$data['ir_price'] = $receipt['ir_price'];
	  //           $data['status'] = 1;
	  //           $data['msg'] = '支付成功，请跳转...';
	  //           $this->ajaxreturn($data);
			// }else{
			// 	$data['status'] = 0;
	  //           $data['msg'] = '正在支付，请等待...';
	  //           $this->ajaxreturn($data);
			// }
   //  	}
   //  }



















































}