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

	/**
	* Update Customer
	**/ 
	public function updateCustomer($happyLifeID,$emailAddress,$placementPreference){
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'HappyLifeID'  =>$happyLifeID,
            'EMailAddress' =>$emailAddress,
            'BinaryPlacementPreference' => $placementPreference,
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/UpdateCustomer?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}

	/**
	* Change PassWord
	**/
	public function changePassWord($happyLifeID,$Password){
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'HappyLifeID'  =>$happyLifeID,
			'Password' => $Password,
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/UpdateCustomer?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}

	/**
	* Change Phone
	**/ 
	public function updateCustomer($happyLifeID,$phone){
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'HappyLifeID'  =>$happyLifeID,
            'Phone'        =>$phone,
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/UpdateCustomer?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}

	/**
	* Create Payment
	**/ 
	public function createPayment($hapyLifeID,$wvOrderID,$paymentDate){
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'hapyLifeID'  	=>$hapyLifeID,
            'wvOrderID'   	=>$wvOrderID,
            'paymentDate' 	=>$paymentDate,
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/CreatePayment?key=".$key;
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}

	/**
	* get virtual CURRENCY (DT Points) balance
	**/ 
	public function dtPoint($hapyLifeID){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$sendUrl = $url."/api/Hpl/Customer/".$hapyLifeID."/VirtualCurrency?key=".$key;
		// p($sendUrl);die;
		$wv       = file_get_contents($sendUrl);
		$userinfo = json_decode($wv,true);
		return $userinfo;
	}

	/**
	* redeem virtual CURRENCY (DT Points and training dollors)
	**/ 
	public function redeemVirtual($hapyLifeID,$amount,$category,$comment){
		$map  = I('post.');
		$key  = $this->key;
		$url  = $this->url;
		$data = array(
			'hapyLifeID'  	=>$hapyLifeID,
            'amount'   		=>$amount,
            'category' 		=>$category,
            'comment' 		=>$comment,
		);
		$data    = json_encode($data);
		$sendUrl = $url."/api/Hpl/Customer/".$hapyLifeID.'/VirtualCurrency?key='.$key;
		$result  = post_json_data($sendUrl,$data);
		return $result;
	}




	




























































































}