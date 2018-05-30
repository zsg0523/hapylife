<?php 
namespace Home\Controller;
use Common\Controller\HomeBaseController;
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
        $data        =D('Smscode')->where(array('phone'=>$phoneNumber,'acnumber'=>$acnumber))->order('nsid desc')->find();
        $time        =time()-strtotime($data['date']);
        if($time>60){
            $this->error('验证码失效,请重新发送');
        }else{
            if($data && $data['code']==$code){
                $this->success('验证码正确',U('Home/Register/new_register'));
            }else{
                $this->error('验证码错误');
            }
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
        	$data        = I('post.');
            $data['iuid']= $_SESSION['user']['id'];
			if(isset($upload['name'])){
				$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
				$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
			}
            $add = D('Tempuser')->add($data);
            if($add){
		        $this->assign('data',$data);
		        $this->display();
            }else{
				$this->error('数据有误，请确认信息');
            }
        }
    }
    /**
    * 获取首购产品
    **/ 
    public function new_purchase(){
    	$data = D('product')->where(array('ip_type'=>1,'is_pull'=>1))->select();
    	$this->assign('data',$data);
        $this->display();

    }
    /**
    * 获取产品详情
    **/ 
    public function new_purchaseInfo(){
    	$ipid =I('post.ipid');
    	$data = D('product')->where(array('ipid'=>$ipid))->find();
    	$this->assign('data',$data);
        $this->display();
    }
   	/**
	* 首购订单
	**/
	public function registerOrder(){
		$iuid = $_SESSION['user']['id'];
        $ipid = I('post.ipid');
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
            //订单的状态(0待生成订单，1待支付订单，2已付款订单,3待注册)
            'ir_status'=>3,
            //下单用户id
            'riuid'=>$iuid,
            //下单用户
            'CustomerID'=>$userinfo['customerid'],
            //收货人
            'ia_name'=>$userinfo['firstname'],
            //收货人电话
            'ia_phone'=>$userinfo['phone'],
            //收货地址
            'ia_address'=>$userinfo['shopaddress1'].' '.$userinfo['shopaddress2'],
            //订单总商品数量
            'ir_productnum'=>1,
            //订单总金额
            'ir_price'=>$product['ip_price_rmb'],
            //订单总积分
            'ir_point'=>$product['ip_point'],
            //订单备注
            'ir_desc'=>$con,
            //订单类型
            'ir_ordertype' => $product['ip_type'],
            //产品id
            'ipid'         => $product['ipid'],
            //待注册用户id
            '$htid'        => $htid
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
	public function register(){
		$data = I('post.');

		$upload = several_upload();
		if(IS_POST){
			if($data['Sex']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if($User->create($data)){
						if(isset($upload['name'])){
							$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
							$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						}
						$data['PassWord'] = md5($data['PassWord']);
						$data['JoinedOn'] = time();
						$data['CustomerID'] = strtoupper($data['CustomerID']);
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
						$result	= D('User')->add($data);
						if($result){
							$this->redirect('Home/Register/regsuccess');
						}
					}
				}
			}else{
				$error['Sex'] = '请选择性别';
			}

			if($data['AccountType']){
				$User = D("User1"); // 实例化User对象
				if (!$User->create($data)){
				     // 如果创建失败 表示验证没有通过 输出错误提示信息
				    $error = $User->getError();
				}else{
				     // 验证通过 可以进行其他数据操作
					if($User->create($data)){
						if(isset($upload['name'])){
							$data['JustIdcard']=C('WEB_URL').$upload['name'][0];
							$data['BackIdcard']=C('WEB_URL').$upload['name'][1];
						}
						$data['PassWord'] = md5($data['PassWord']);
						$data['JoinedOn'] = time();
						$data['CustomerID'] = strtoupper($data['CustomerID']);
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
						$result	= D('User')->add($data);
						if($result){
							$this->redirect('Home/Register/regsuccess');
						}
					}
				}
			}else{
				$error['AccountType'] = '请选择销售类型';
			}
		}

		$assign = array(
            		'error' => $error,
            		'data' => $data
            		);
        $this->assign($assign);
		$this->display();
	}

	// 注册成功显示页面
	public function regsuccess(){
		$data = max(D('User')->select());
		$data = D('User')->where(array('iuid'=>$data['iuid']))->find();
		
		$assign = array(
						'data' => $data,
						);
        $this->assign($assign);
		$this->display();
	}





}



 ?>