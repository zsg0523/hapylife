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
    public function regCode(){
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
                $this->success('验证码正确',U('Home/Register/register'));
            }else{
                $this->error('验证码错误');
            }
        }
    }  
	// 用户注册
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