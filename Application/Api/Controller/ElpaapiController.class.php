<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* ElpaApi控制器
**/
class ElpaApiController extends HomeBaseController{

	//***************登录**************************
	//使用ibos接口直接登录
	

	//***************广告**************************
	/**
	* 广告列表
	**/
	public function adlist(){
		$data=D('ElpaShow')->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}

	//***************新闻**************************
	/**
	* 推荐新闻
	**/
	public function newstop(){
		$data=D('ElpaNews')->where(array('news_top'=>1))->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}
	/**
	* 新闻列表
	**/
	public function newslist(){
		$data=D('ElpaNews')->where(array('is_show'=>1))->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 新闻详情
	**/
	public function news(){
		$nid=I('post.nid');
		$map=array(
			'nid'=>$nid
		);
		$data=D('ElpaNews')->where($map)->find();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}
	


	//***************课程**************************
	/**
	* 课堂列表
	**/
	public function classlist(){
		$data = D("ElpaLesson")->where(array('pid'=>0))->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['statua'] = 0;
			$this->ajaxreturn($data);
		}
	}

	
	// public function lessonlist(){
	// 	$data=D('ElpaLesson')->where(array('pid'=>0))->select();
	// 	//遍历输出二维数组
	// 	foreach ($data as $key => $value) {
	// 		$tmp[$value['id']]['name']=$value['name'];
	// 		$tmp[$value['id']]['lesson']=D('ElpaLesson')->where(array('pid'=>$value['id']))->select();
	// 	}
	// 	//转换索引数组
	// 	foreach ($tmp as $k => $v) {
	// 		$lessonlist[]=$v;
	// 	}
	// 	if($lessonlist){
	// 		$this->ajaxreturn($lessonlist);
	// 	}else{
	// 		$data['status']=0;
	// 		$this->ajaxreturn($data);
	// 	}
	// }

	/**
	* 课程列表
	**/
	public function lessonlist(){
		$pid  = I('post.pid');
		$data = D('ElpaLesson')->where(array('pid'=>$pid))->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 课程详情
	**/
	public function lesson(){
		$id = I('post.ilid');
		$data = D('ElpaLesson')->where(array('id'=>$id))->find();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}


	/**
	* 课程章节列表
	**/
	public function chapterlist(){
		$ilid=I('post.id');
		$map=array(
			'ilid'=>$ilid
		);
		$data=D('ElpaChapter')->where($map)->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 章节详情
	**/
	public function chapter(){
		$ct_id=I('post.ct_id');
		$map=array(
			'ct_id'=>$ct_id
		);
		$data=D('ElpaChapter')->where(array($map))->find();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}


	/**
	* 加入购物车
	**/
	public function addshopcart(){
		//会员uid
		$iuid=I('post.iuid');
		//购买课程ilid
		$ilid=I('post.id');
		//获取购买的该课程信息
		$lesson=D('ElpaLesson')->where(array('id'=>$ilid))->find();
		//加入购物车表
		$shopcartlist = M('ElpaShopcart')->where(array('iuid'=>$iuid))->select();
		//判断购物车是否已有该课程
		$is_repeat=false;
		if($shopcartlist){
			foreach ($shopcartlist as $k => $v) {
				if($ilid == $v['ilid']){
					$is_repeat = true;
					$data['status'] = 0;
					$this->ajaxreturn($data);
				}
			}
			//课程不重复
			if(!$is_repeat){
				$data=array(
					'iuid'=>$iuid,
					'ilid'=>$ilid,
					'product_number'=>1,
					'product_price'=>$lesson['price'],
					'product_point'=>$lesson['point']
				);
				$insertcart = M('ElpaShopcart')->add($data);
				if($insertcart){
					$data['status'] = 1;
					$this->ajaxreturn($data);
				}else{
					$data['status'] = 0;
					$this->ajaxreturn($data);
				}
			}
		}
	}

	/**
	* 购物车列表显示(is_show=0)
	**/
	public function cartlistone(){
		$iuid = I('post.iuid');
		$cartlist = M('ElpaShopcart')
					->join('nulife_elpa_lesson on nulife_elpa_lesson.id = nulife_elpa_shopcart.ilid')
					->where(array('nulife_elpa_shopcart.iuid'=>$iuid))
					->select();
		if($cartlist){
			$this->ajaxreturn($cartlist);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 结算列表显示(is_show=1)
	**/
	public function cartlist(){
		$iuid = I('post.iuid');
		$cartlist = M('ElpaShopcart')
					->join('nulife_elpa_lesson on nulife_elpa_lesson.id = nulife_elpa_shopcart.ilid')
					->where(array('nulife_elpa_shopcart.iuid'=>$iuid))
					->where(array('nulife_elpa_shopcart.is_show'=>1))
					->select();
		if($cartlist){
			$this->ajaxreturn($cartlist);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 购物车结算订单界面选购需要提交订单商品
	**/
    public function is_show(){

        $iscid   = I('post.iscid');
        $is_show = I('post.is_show');

        //购物车已添加列表
        $map = array(
                'iscid' =>$iscid,
                'is_show'=>$is_show
            );
        $is_show = D('ElpaShopcart')->save($map);
        //购物车确定
        if($is_show){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

    //购物车结算订单界面删除商品
    public function is_delete(){
        $iscid   = I('post.iscid');
        //购物车已添加列表
        $map = array(
                'iscid' =>$iscid,
            );
        $is_delete = M('ElpaShopcart')->where($map)->delete();
        //购物车确定
        if($is_delete){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

	/**
	* 订单提交，生成订单
	**/
	public function order(){
		//获取用户iuid
        $iuid = I('post.iuid');

        //遍历购物车所有商品ilid(如果表中存有会话数据，则反序列化)
        $shopcart   = M('ElpaShopcart')
                    ->join('nulife_elpa_lesson on nulife_elpa_shopcart.ilid = nulife_elpa_lesson.id')
                    ->where(array('nulife_elpa_shopcart.iuid'=>$iuid))
                    ->where(array('nulife_elpa_shopcart.is_show'=>1))
                    ->select();

        //生成唯一订单号
        $order_num = date('YmdHis').rand(10000, 99999);

        //计算总价
        foreach ($shopcart as $k => $v) {
            //总数量
            $total_num += $v['product_number'];
            //总金额
            $total_price += $v['product_number'] * $v['product_price'];
            //总积分
            $total_point += $v['product_number'] * $v['product_point'];
        }

        $order = array(
            //订单编号
            'ir_receiptnum' 		=>$order_num,
            //订单创建日期
            'ir_date'				=>time(),
            //订单的状态(0待生成订单，1待支付订单，2已付款订单)
            'ir_status'				=>0,
            //谁的订单
            'iuid'					=>$iuid,
            //订单总商品数量
            'ir_productnum'			=>$total_num,
            //订单总金额
            'ir_price'				=>$total_price,
            //订单总积分
            'ir_point'				=>$total_point
        );

        $receipt = M('ElpaReceipt')->add($order);

        //订单详情记录商品信息
        if($receipt){
            foreach ($shopcart as $k => $v) {
                $map = array(
                    'ir_receiptnum' 	=>$order_num,
                    'ilid'				=>$v['ilid'],
                    'product_num'		=>$v['product_number'],
                    'product_point'		=>$v['point']*$v['product_number'],
                    'product_price'		=>$v['price']*$v['product_number'],
                    'product_name'		=>$v['name'],
                    'product_picture'	=>$v['picture']
                );
                $receiptlist = M('ElpaReceiptlist')->add($map);
            }
            if($receiptlist){
                //订单提交后清空购物车
                $rst = M('ElpaShopcart')
                    ->where(array('iuid'=>$iuid))
                    ->where(array('is_show'=>1))
                    ->delete();
                if($rst){
                    $data['status'] = 1;
                    $this->ajaxreturn($order);
                }else{
                    $data['status'] = 0;
                    $this->ajaxreturn($order);
                }
            }
        }
	}

	/**
	* 订单支付
	**/
	public function payment(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //获取支付方式，1/2/3
            $paytype = I('post.paytype');

            //获取订单号
            $ir_receiptnum = I('post.ir_receiptnum');

            //用户iuid
            $iuid = I('post.iuid');

            switch ($paytype) {
                //微信支付
                case 1:
                   //订单号
			        $ir_receiptnum  = I('post.ir_receiptnum')?I('post.ir_receiptnum'):date('YmdHis').rand(10000, 99999);
			        //用户iuid
			        $iuid           = I('post.iuid');
			        //订单信息查询
			        $order          = M('ElpaReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();

			        // wsdl模式访问wsdl程序
			        $client = new \SoapClient("https://pay.hkipsec.com/webservice/GetQRCodeWebService.asmx?wsdl",
						array(
							'trace' => true,
							'exceptions' => true,
							'stream_context'=>stream_context_create(array('ssl' => array('verify_peer'=>false,
									'verify_peer_name'  => false,
									'allow_self_signed' => true,
									'cache_wsdl' => WSDL_CACHE_NONE,
									)
								)
							)
						));

			        $merchantcert = "GB30j0XP0jGZPVrJc6G69PCLsmPKNmDiISNvrXc0DB2c7uLLFX9ah1zRYHiXAnbn68rWiW2f4pSXxAoX0eePDCaq3Wx9OeP0Ao6YdPDJ546R813x2k76ilAU8a3m8Sq0";

			        try{
			            $merAccNo       = "E0001904";
			            $orderId        = $ir_receiptnum;
			            $fee_type       = "CNY";
			            $amount         = "0.10";
			            $goodsInfo      = "Nulife Product";
			            $strMerchantUrl = "http://apps.nulifeshop.com/nulifeshop/index.php/Home/api/getResponse";
			            $cert           = $merchantcert;
			            $signMD5        = "merAccNo".$merAccNo."orderId".$orderId."fee_type".$fee_type."amount".$amount."goodsInfo".$goodsInfo."strMerchantUrl".$strMerchantUrl."cert".$cert;
			            $signMD5_lower  = strtolower(md5($signMD5));

			            $para = array(
			                'merAccNo'      => $merAccNo,
			                'orderId'       => $orderId,
			                'fee_type'      => $fee_type,
			                'amount'        => $amount,
			                'goodsInfo'     => $goodsInfo,
			                'strMerchantUrl'=> $strMerchantUrl,
			                'signMD5'       => $signMD5_lower
			            );

			            $result = $client->GetQRCodeXml($para);
			            //对象操作
			            $xmlstr = $result->GetQRCodeXmlResult;
			            //构造SimpleXMLEliement对象
			            $xml = new \SimpleXMLElement($xmlstr);
			            //微信支付链接
			            $code_url = (string)$xml->code_url;
			            //返回数据
			            $para['code_url'] = $code_url;
			            $this->ajaxreturn($para);
			            
			        }catch(SoapFault $f){
			            echo "Error Message:{$f->getMessage()}";
			        }
                    break;
                //积分购买
                case 2:
                    //获取用户积分
                    $user_point = M('IbosUsers')->where(array('iuid'=>$iuid))->getfield('iu_point');
                    //获取订单积分
                    $ir_point   = M('ElpaReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ir_point');
                    //扣除所需购买积分
                    $last_point = $user_point-$ir_point;

                    if($last_point>0){
                        //修改用户积分
                        $data = array(
                            'iuid'		=>$iuid,
                            'iu_point'	=>$last_point
                        );
                        $insertpoint = M('IbosUsers')->save($data);
                        if($insertpoint){
                            //修改订单状态
                            $map = array(
                                'ir_paytype'=>2,
                                'ir_status'=>2
                            );
                            $change_orderstatus = M('ElpaReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($map);
                           
                            //支付完成后查询该订单列表，有ilid则将课程写入我的课程
                            $lesson = M('ElpaReceiptlist')->where(array('ir_receiptnum'=>$ir_receiptnum))->getfield('ilid',true);
                            foreach ($lesson as $k => $v) {
                                if($v != 0){
                                    $data = array(
                                            'ir_receiptnum'=>$ir_receiptnum,
                                            'iuid'=>$iuid,
                                            'ilid'=>$v,
                                            //课程进度
                                            'iul_schedule'=>'',
                                            //课程是否完成
                                            'iul_complete'=>0,
                                            //课程购买时间
                                            'iul_buytime'=>time(),
                                            //截止日期 一周后
                                            'iul_deadline'=>time()+604800,
                                            //奖励积分
                                            'iul_rewardrecord'=>200
                                        );
                                    $insert = M('ElpaUserlesson')->add($data);
                                }
                            }
                            $data['status'] = 1;
                            $this->ajaxreturn($data);
                        }
                    }else{
                        //积分不足，请充值
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }
                    break;
                //转账购买
                case 3:
                    //获取图片
                    $bankreceipt = I('post.ir_bankreceipt');

                    //银行单据账号
                    $banknumber  = I('post.banknumber');

                    //收据凭证解码
                    $ir_bankreceipt 	= substr(strstr($bankreceipt,','),1);
                    $url_bankreceipt 	= time().'_'.mt_rand().'.jpg';
                    $img 				= file_put_contents('./Upload/bank/'.$url_bankreceipt, base64_decode($ir_bankreceipt));


                    if($url_bankreceipt){
                        //添加图片并修改订单状态
                        $data = array(
                            'ir_paytype'		=>3,
                            'ir_status'			=>1,
                            'ir_bankreceipt'  	=>C('WEB_URL').'/Upload/bank/'.$url_bankreceipt,
                            'ir_banknumber'		=>$banknumber
                        );
                        $change_orderstatus = M('ElpaReceipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->save($data);
                        
                        if($change_orderstatus){
                            //修改成功1
                            $data['status'] = 1;
                            $this->ajaxreturn($data);
                        }else{
                            //修改失败0
                            $data['status'] = 0;
                            $this->ajaxreturn($data);
                        }
                    }else{
                        //没有上传凭证
                        $data['status'] = 0;
                        $this->ajaxreturn($data);
                    }                   
                    break;
            }
        }
    }


	/**
	* 用户-我的课程列表
	**/
	public function userlesson(){
		$iuid = I('post.iuid');
		$userlesson = M('ElpaUserlesson')
					->join('nulife_elpa_lesson on nulife_elpa_userlesson.ilid = nulife_elpa_lesson.id')
					->where(array('nulife_elpa_userlesson.iuid'=>$iuid))
					->select();
		if($userlesson){
			$this->ajaxreturn($userlesson);
		}else{
			$data['status'] = 0;
			$this->ajaxreturn($data);
		}
	}


	
	//***************文件夹************************
	/**
	* 一级文件夹
	**/
	public function filelist(){
		$data=D('ElpaFile')->where(array('pid'=>0))->select();
		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}

	/**
	* 获取文件夹/文件
	**/
	public function getfilelist(){
		$id=I('post.id');
		$data=D('ElpaFile')->where(array('pid'=>$id))->select();

		if($data){
			$this->ajaxreturn($data);
		}else{
			$data['status']=0;
			$this->ajaxreturn($data);
		}
	}

	




























































































}
