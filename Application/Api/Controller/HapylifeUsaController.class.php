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
		$usa = new \Common\UsaApi\Usa;
		$usa->createCustomer();
	}


	public function _initialize(){
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
	public function validateHpl(){
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
	* UPDATE CUSTOMER
	**/
	public function updateCustomer(){
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