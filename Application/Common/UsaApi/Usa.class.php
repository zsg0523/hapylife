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
	function validateHpl($CustomerId)
	{
		$key    = $this->key;
		$url    = $this->url;
		$data   = $url."/api/Hpl/Validate?customerId=".$CustomerId."&"."key=".$key;
		$wv     = file_get_contents($data);
		$result = json_decode($wv,true);
        return $result;
	}

	/**
	 * [total description]
	 * @param  [type] $CustomerId [description]
	 * @return [type]             [description]
	 */
	function total($CustomerId)
	{
		$key    = $this->key;
		$url    = $this->url;
		$data   = $url."/api/Hpl/Totals?customerId=".$CustomerId."&"."key=".$key;
		$wv     = file_get_contents($data);
		$result = json_decode($wv,true);
		return $result;
	}

	/**
	* CREATE CUSTOMER
	**/
	public function createCustomer($happyLifeID,$password,$sponsorID,$firstName_EN,$lastName_EN,$emailAddress,$phone,$products,$dob='2000-1-1')
	{
		// ='RBS,DTP,SIGNUP4,SIGNUP5'
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





	




























































































}