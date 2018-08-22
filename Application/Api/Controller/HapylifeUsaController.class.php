<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 对接USA api
* 生产地址	https://signupapi.wvhservices.com 
* 沙盒地址	https://signupapi-qa.wvhservices.com		
**/
class HapylifeUsaController extends HomeBaseController{

	public function testUsaClass(){
		$iuid     = I('post.iuid');
		$password = I('post.password');
		$userinfo = M('User')->where(array('iuid'=>$iuid))->find();
		$usa      = new \Common\UsaApi\Usa;
		$result   = $usa->createCustomer($userinfo['customerid'],$password,$userinfo['enrollerid'],$userinfo['enfirstname'],$userinfo['enlastname'],$userinfo['email'],$userinfo['phone']);
		p($userinfo);
		p($result);
        if(!empty($result['result'])){
        	$map = json_decode($result['result'],true);
            $wv  = array(
						'wvCustomerID' => $map['wvCustomerID'],
						'wvOrderID'    => $map['wvOrderID']
                    );
            $res = M('User')->where(array('iuid'=>$userinfo['iuid']))->save($wv);
            if($res){
            	$templateId='164137';
            	$params = array();
				$sms    = D('Smscode')->sms($userinfo['acnumber'],$userinfo['phone'],$params,$templateId);
				p($sms);
            }else{
            	echo 'false';
            }
        }else{
        	echo 'error';
        }
	}


	public function _initialize(){
		// production 生产环境配置
		$this->key = "KDHE5011CVFO1KJEP1A0S";
		$this->url = "https://signupapi.wvhservices.com";

		// qa 沙盒环境
		// $this->key = "QACER3H5T6HGYDCCDAZM3";
		// $this->url = "https://signupapi-qa.wvhservices.com";
	}
	
	/**
	* VALIDATEHPL
	* @param customerId: id to validate
	* @param key: your secret key
	**/
	public function validateHpl(){
		$map      = I('post.');
		$key      = $this->key;
		$url      = $this->url;
		$data     = $url."/api/Hpl/Validate?customerId=".$map['CustomerID']."&"."key=".$key;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}


	/**
	* totals
	* @param customerId: id to validate
	* @param key: your secret key
	**/
	public function total(){
		$map      = I('post.');
		$key      = $this->key;
		$url      = $this->url;
		$data     = $url."/api/Hpl/Totals?customerId=".$map['CustomerID']."&"."key=".$key;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}


	/**
	* REPORT
	* @param customerId: id to validate
	* @param key: your secret key
	* HPL00000254
	**/
	public function activities(){
		$map      = I('post.');
		$key      = $this->key;
		$url      = $this->url;
		 https://signupapi-qa.wvhservices.com/api/hpl/customer/hpl0001/lineageactivity?key=<wv提供給HapyLife的key> -H "accept: application/json"
		$data     = $url."/api/hpl/customer/".$map['CustomerID']."/lineageactivity?key=<QACER3H5T6HGYDCCDAZM3>";
		p($data);die;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}

	/**
	* CREATE CUSTOMER
	**/
	public function createCustomer(){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'happyLifeID'  =>$map['happyLifeID'],
            'password'     =>$map['password'],
            'sponsorID'    =>$map['sponsorID'],
            'firstName_EN' =>$map['firstName_EN'],
            'lastName_EN'  =>$map['lastName_EN'],
            'emailAddress' =>$map['emailAddress'],
            'phone'        =>$map['phone'],
            'products'     =>explode(',',$map['products']),
            'dob'          =>$map['dob'],
            'key'          =>$key
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/CreateCustomer";
		$result  = post_json_data($sendUrl,$data);
		print_r($result);
	}

	





























































































}