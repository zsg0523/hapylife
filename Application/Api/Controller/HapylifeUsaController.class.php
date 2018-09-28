<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 对接USA api
* 生产地址	https://signupapi.wvhservices.com 
* 沙盒地址	https://signupapi-qa.wvhservices.com		
**/
class HapylifeUsaController extends HomeBaseController{


	public function _initialize(){
		// production 生产环境配置
		// $this->key = "KDHE5011CVFO1KJEP1A0S";
		// $this->url = "https://signupapi.wvhservices.com";

		// qa 沙盒环境
		$this->key = "QACER3H5T6HGYDCCDAZM3";
		$this->url = "https://signupapi-qa.wvhservices.com";
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
		$data     = $url."/api/hpl/customer/".$map['CustomerID']."/lineageactivity?key=".$key;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}


	/**
	* PLACEMENT VERIFICATION
	* @param customerId: id to validate
	* @param key: your secret key
	* HPL00000254
	**/
	public function placement(){
		$map      = I('post.');
		$key      = $this->key;
		$url      = $this->url;
		$data     = $url."/api/hpl/customer/".$map['CustomerID']."/placementposition?key=".$key;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}




	/**
	* CREATE CUSTOMER
	* @param   $products[RBS,DTP] [<正常付费注册会员>]
	* @param   $products[RBS,DTP,PROMO_FULL] [<正常优惠券注册会员>]
	* @param   $products[RBS,DTP,PROMO_FREE] [<免费优惠券注册会员>]
	*
	Array
	(
	    [code] => 200
	    [result] => {"happyLifeID":"HPL00001","sponsorID":"HPL00000254","wvCustomerID":"71794533","wvOrderID":"297679739","error":null}
	)
	PROMO_FULL 成功
	Array
	(
	    [code] => 200
	    [result] => {"happyLifeID":"HPL00002","sponsorID":"HPL00000254","wvCustomerID":"71794536","wvOrderID":"297679742","error":null}
	)
	
	PROMO_FREE 成功
	Array
	(
	    [code] => 200
	    [result] => {"happyLifeID":"HPL00003","sponsorID":"HPL00000254","wvCustomerID":"71794539","wvOrderID":"297679745","error":null}
	)

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

	/**
	* Update Customer
	* @param customerId: id to validate
	* @param key: your secret key
	**/ 
	public function updateCustomer(){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'happyLifeID'  =>$map['happyLifeID'],
            'password'     =>$map['password'],
            'emailAddress' =>$map['emailAddress'],
            'phone'        =>$map['phone'],
            'placementpreference' => $map['placementpreference'],
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/UpdateCustomer?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		print_r($result);
	}

	/**
	* Totals
	**/ 
	public function Totals(){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'happyLifeID'  =>$map['happyLifeID'],
            'key'          =>$key
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/Totals?customerId=".$map['happyLifeID']."&key=".$key;
		$result  = post_json_data($sendUrl,$data);
		print_r($result);
	}

	/**
	* Create Payment
	**/ 
	public function createPayment(){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'hapyLifeID'  	=>$map['hapyLifeID'],
            'wvOrderID'   	=>$map['wvOrderID'],
            'paymentDate ' 	=>$map['paymentDate'],
		);
		$sendUrl = $url."/api/Hpl/CreatePayment?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		print_r($result);
	}



	





























































































}