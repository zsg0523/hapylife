<?php
namespace Common\UsaApi;
class usa
/**
* 对接USA api
* 生产地址	https://signupapi.wvhservices.com 
* 沙盒地址	https://signupapi-qa.wvhservices.com		
**/
{

	private $key;
	private $url;

	public function __construct(){
		// production 生产环境配置
		// $this->key = "Z131MZ8ZV29H5EQ9LGVH";
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
	function validateHpl(){
		$map      = I('post.');
		$key      = $this->key;
		$url      = $this->url;
		$data     = $url."/api/Account/ValidateHpl?customerId=".$map['CustomerID']."&"."key=".$key;
		$wv       = file_get_contents($data);
		$userinfo = json_decode($wv,true);
        $this->ajaxreturn($userinfo);
	}

	/**
	* CREATE CUSTOMER
	**/
	public function createCustomer($happyLifeID,$password,$sponsorID,$firstName_EN,$lastName_EN,$emailAddress,$phone,$products='RBS,DTP',$dob='2000-1-1'){
		
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'happyLifeID'  =>$happyLifeID,
            'password'     =>$password,
            'sponsorID'    =>$sponsorID,
            'firstName_EN' =>$firstName_EN,
            'lastName_EN'  =>$lastName_EN,
            'emailAddress' =>$emailAddress,
            'phone'        =>$phone,
            'products'     =>explode(',',$products),
            'dob'          =>$dob,
            'key'          =>$key
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/CreateCustomer";
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}

	/**
	* UPDATE CUSTOMER
	**/
	function updateCustomer(){
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
            'dob'          =>$map['dob'],
            'key'          =>$key
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/UpdateCustomer";
		$result  = post_json_data($sendUrl,$data);
		print_r($result);
	}



	




























































































}