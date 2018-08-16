<?php
namespace Api\Controller;
use Common\Controller\HomeBaseController;
/**
* 购物车
**/
class ShopcartController extends HomeBaseController{
    /**
    *加入购物车
    **/
    public function shopcart(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{      
            $uid   = I('post.uid');//会员uid
            $pid    = I('post.pid');//商品pid
            //获取商品信息
            $product= M('Product')->where(array('pid'=>$pid))->find();
            //加入购物车表(存session，判断是否已有商品，判断商品数量)
            $shopcartlist = M('Shopcart')->where(array('uid'=>$uid))->select();
            //商品可重复购买
            if($shopcartlist){
                //判断是否重复商品
                $is_repeat = false;
                foreach ($shopcartlist as $k => $v) {
                    if($pid == $v['pid']){
                        //商品数量加一
                        $map = array(
                            'uid'=>$uid,
                            'pid' =>$pid
                        );
                        $changenum = M('Shopcart')->where($map)->setInc('quantity');
                        $is_repeat = true;
                        if($changenum){
                            $data['status'] = 1;
                            $data['msg']    = "购物车商品成功增加数量";
                            $this->ajaxreturn($data);
                        }else{
                            $data['status'] = 0;
                            $data['msg']    = "购物车商品增加数量失败";
                            $this->ajaxreturn($data);
                        }
                    }
                }
                if(!$is_repeat){
                    $data = array(
                        'uid'       =>$uid,
	                    'pid'       =>$pid,
	                    'quantity'  =>1,
	                    'price'     =>$product['product_price'],
                    );
                    $insertcart = M('Shopcart')->add($data);
                    if($insertcart){
                        $data['status'] = 1;
                        $data['msg']    = "添加至购物车成功";
                        $this->ajaxreturn($data);
                    }else{
                        $data['status'] = 0;
                        $data['msg']    = "添加至购物车失败";
                        $this->ajaxreturn($data);
                    }
                }    
            }else{
                $data = array(
                    'uid'       =>$uid,
                    'pid'       =>$pid,
                    'quantity'  =>1,
                    'price'     =>$product['product_price'],
                );
                $insertcart = D('Shopcart')->add($data);
                if($insertcart){
                    $data['status'] = 1;
                    $data['msg']    = "添加至购物车成功";
                    $this->ajaxreturn($data);
                }else{
                    $data['status'] = 0;
                    $data['msg']    = "添加至购物车失败";
                    $this->ajaxreturn($data);
                }
            }     
        }
    }
    /**
    *购物车结算界面列表
    **/
    public function cartListOne(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $uid     = I('post.uid');
            //获取购物车列表信息
            $cartlist = M('Shopcart')
                      ->where(array('uid'=>$uid))
                      ->order('scid desc')
                      ->select();
            if($cartlist){
                foreach($cartlist as $key => $value) {
                    $list    = D('Product')->where(array('pid'=>$value['pid']))->find();
                    $data[$key]['name'] = $list['product_name'];
                    $data[$key]['number']  = $list['product_number'];
                    $data[$key]['picture'] = $list['product_picture'];
                    $data[$key]['money']= $value['price'];
                    $data[$key]['quantity']  = $value['quantity'];
                    $data[$key]['pid']    = $value['pid'];
                    $data[$key]['scid']   = $value['scid'];
                    $data[$key]['is_chose'] = $value['is_chose'];
                }
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $data['status'] = 0;
                $data['status'] = "购物车无商品";
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    *购物车提交订单界面列表
    **/
    public function cartList(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            $uid     = I('post.uid');
            //获取购物车列表信息
            $cartlist = M('Shopcart')
                        ->where(array('uid'=>$uid,'is_chose'=>1))
                        ->select();
            if($cartlist){
                foreach($cartlist as $key => $value) {
                    $list    = D('Product')->where(array('pid'=>$value['pid']))->find();
                    $data[$key]['name'] = $list['product_name'];
                    $data[$key]['number']  = $list['product_number'];
                    $data[$key]['picture'] = $list['product_picture'];
                    $data[$key]['money']= $value['price'];
                    $data[$key]['quantity']  = $value['quantity'];
                    $data[$key]['pid']    = $value['pid'];
                    $data[$key]['scid']   = $value['scid'];
                    $data[$key]['is_chose'] = $value['is_chose'];
                }
            }
            if($data){
                $this->ajaxreturn($data);
            }else{
                $status = 0;
                $this->ajaxreturn($status);
            }
        }
    }
    /**
    *增加购物车某商品数量
    **/
    public function cartAdd(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $uid     = I('post.uid');
            //商品id
            $pid      = I('post.pid');
            //获取购物车列表信息
            $cart     = M('Shopcart')
                      ->where(array('uid'=>$uid,'pid'=>$pid))
                      ->find();
            //更新购物车该商品
            $data['quantity']  = $cart['quantity']+1;
            $cartpro  = M('Shopcart')
                      ->where(array('uid'=>$uid,'pid'=>$pid))
                      ->save($data);
            if($cartpro){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    *减少购物车某商品数量
    **/
    public function cartReduce(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
            //用户id
            $uid     = I('post.uid');
            //商品id
            $pid      = I('post.pid');
            //获取购物车列表信息
            $cart     = M('Shopcart')
                      ->where(array('uid'=>$uid,'pid'=>$pid))
                      ->find();
            //更新购物车该商品
            if($cart['quantity']>1){
                $data['quantity']  = $cart['quantity']-1;
                $cartpro  = M('Shopcart')
                          ->where(array('uid'=>$uid,'pid'=>$pid))
                          ->save($data);
            }
            if($cartpro){
                $tmp['status'] = 1;
                $this->ajaxreturn($tmp);
            }else{
                $tmp['status'] = 0;
                $this->ajaxreturn($tmp);
            }
        }
    }
    /**
    *购物车结算订单界面选购需要提交订单商品 scid is_chose
    **/
    public function is_chose(){
        $scid   = I('post.scid');
        $is_chose = I('post.is_chose');
        //购物车已添加列表
        $map = array(
                'scid' =>$scid,
                'is_chose'=>$is_chose
            );
        $show= M('Shopcart')->save($map);
        //购物车确定
        if($show){
            $data['status'] = 1;
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
    /**
    *购物车结算订单界面删除商品
    **/
    public function is_delete(){
        $scid   = I('post.scid');
        //购物车已添加列表
        $map = array(
                'scid' =>$scid,
            );
        $is_delete = M('Shopcart')->where($map)->delete();
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
    *新增收货地址 uid ia_name ia_phone ia_address is_address_show
    **/
    public function addAddress(){
    	$data = I('post.');
    	$add  = D('Address')->add($data);
    	if ($add) {
    		$data['status'] = 1;
    		$data['msg']	= "添加新地址成功";
    		$this->ajaxreturn($data);
    	}else{
            $data['status'] = 0;
            $data['msg']	= "添加地址失败";
            $this->ajaxreturn($data);
        }
    }
    /**
    *删除地址 ia_id 
    **/
    public function deleteAddress(){
    	$ia_id   = I('post.ia_id');
        $map = array(
                'ia_id' =>$ia_id,
            );
        $is_delete = M('Address')->where($map)->delete();
        //购物车确定
        if($is_delete){
            $data['status'] = 1;
            $data['msg']	= "删除成功";
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']	= "删除失败";
            $this->ajaxreturn($data);
        }
    }
    /**
    *修改地址ia_id   para  paravalue
    *paravalue:ia_name ia_phone ia_address is_address_show
    **/
    public function editAddress(){
		$ia_id     = I('post.ia_id');
		$para      = I('post.para');
		$paravalue = I('post.paravalue');
		switch ($para) {
			case 'ia_name':
				$data['ia_name'] = $paravalue;
				break;
			case 'ia_phone':
				$data['ia_phone'] = $paravalue;
				break;
			case 'ia_address':
				$data['ia_address'] = $paravalue;
				break;
		}
		$save = D('Address')->where(array('ia_id'=>$ia_id))->save($data);
		if($save){
            $data['status'] = 1;
            $data['msg']	= "修改成功";
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']	= "修改失败";
            $this->ajaxreturn($data);
        }
    }
    /**
    *设置默认地址
    **/
    public function defaultAddress(){
    	$ia_id = I('post.ia_id');
    	$map = array(
                'ia_id' =>$ia_id,
            );
    	$show = D('Address')->where(array('uid'=>$uid,'is_address_show'=>1))->getfield('ia_id');
    	if ($show == $ia_id) {
    		$data['status'] = 0;
    		$data['msg']    = "已经是默认地址";
            $this->ajaxreturn($data);
    	}else{
	    	$uid  = D('Address')->where($map)->getfield('uid');
	        $oper = D('Address')->where($map)->setfield('is_address_show',1);
	        if ($oper) {
	        	$address = D('Address')->where(array('uid'=>$uid,'is_address_show'=>0))->getfield('ia_id',true);
	        	$count  = count($address);
	        	$num    = 0;
	        	foreach ($address as $key => $value) {
	        		$set = D('Address')->where(array('ia_id'=>$value))->setfield('is_address_show',0);
	        		if ($set) {
	        			$num += 1;
	        		}
	        	}
	        	if ($num == $count) {
	        		$data['status'] = 1;
	        		$data['msg']	= "修改成功";
	            	$this->ajaxreturn($data);
	        	}else{
	        		$data['status'] = 0;
	        		$data['msg']	= "修改失败";
	            	$this->ajaxreturn($data);
	        	}
	        }else{
	            $data['status'] = 0;
	            $data['msg']	= "修改失败";
	            $this->ajaxreturn($data);
	        }
    	}
    }
    // /**
    // *默认地址
    // **/
    // public function addressList(){
    // 	$uid  = I('post.uid');
    // 	$data = D('Address')
    // 		  ->where(array('uid'=>$uid,'is_address_show'=>1))
    // 		  ->find();
    // 	if($data){
    //         $this->ajaxreturn($data);
    //     }else{
    //         $data['status'] = 0;
    //         $data['msg']	= "无收货地址";
    //         $this->ajaxreturn($data);
    //     }
    // }
    /**
    *地址列表
    **/
    public function addressList(){
    	$uid  = I('post.uid');
    	$data = D('Address')
    		  ->where(array('uid'=>$uid))
    		  ->order('is_address_show desc,ia_id desc')
    		  ->select();
    	if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $data['msg']	= "无收货地址";
            $this->ajaxreturn($data);
        }
    }
    /**
    *购物车提交订单 登陆人uid 地址id
    **/
    public function couOrder(){
        if(!IS_POST){
            $status = 0;
            $this->ajaxreturn($status);
        }else{
            //获取用户huid
            $uid    = I('post.uid');
            $ia_id  = I('post.ia_id');//如果选择了收件地址，传递地址id
            if ($ia_id) {
            	$address = D('Address')
                         ->where(array('ia_id'=>$ia_id))
                         ->find();
            }else{
	            $address = D('Address')
	                      ->where(array('uid'=>$uid,'is_address_show'=>1))
	                      ->find();
            }
            //遍历购物车所有商品pid
            $shopcart   = M('Shopcart')
                        ->where(array('uid'=>$uid,'is_chose'=>1))
                        ->select();
            // p($shopcart);die;
            //生成唯一订单号
            $order_num = date('YmdHis').rand(100000, 999999);
            //计算总价
            foreach ($shopcart as $k => $v){       
            	$product      = D('Product') ->where(array('pid'=>$v['pid']))->find();   
                $total_num   += $v['quantity'];//總數量               
                $total_price += $v['quantity'] * $v['price'];//总金额              
                $ir_desc  .= $product['product_name'].'*'.$v['quantity'].',';//订单备注
                // $pidArr      .= $product['pid'].',';
            }
            $order = array(               
                'ir_uid'=>$uid,//下单用户id               
                'ir_receiptnum' =>$order_num,//订单编号               
                'ir_desc' =>substr($ir_desc, 0, -1),//订单详情
                'ir_status'=>0,//订单的状态(0待生成订单，1待支付订单，2已付款订单)
                'ir_productnum'=>$total_num,//订单总商品数量                
                'ir_price'=>$total_price,//订单总金额           
                'ir_name'=>$address['ia_name'],//收件人姓名
                'ir_phone'=>$address['ia_phone'],//收件人手机号码                
                'ir_address'=>$address['ia_address'],//收件人收货地址
                'ir_createTime'=>time(),//订单创建日期                
            );
            $receipt = M('Receipt')->add($order);
            //订单详情记录商品信息
            if($receipt){
                foreach ($shopcart as $k => $v) {
                	$product1      = D('Product') ->where(array('pid'=>$v['pid']))->find();  
                    $map = array(
                        'ir_receiptnum'     =>  $order_num,
                        'pid'              =>  $v['pid'],
                        // 'product_name'		=>  $product1['product_name'],//商品名
                        'product_num'       =>  $v['quantity'],//商品数量
                        'product_price'     =>  $v['price']*$v['quantity'],  //总价
                        'product_picture'   =>  $product1['product_picture']//商品图片
                    );
                    $receiptlist = M('Receiptlist')->add($map);
                }
                // //生成消息
                // $title= '生成订单';
                // $con  = '您的订单已生成,编号:'.$order_num.',包含:'.$order_desc.',总价:'.$total_price.'Rmb'
                // $hic  = D('HracInfoclass')->where(array('hic_type'=>3))->getfield('hicid');
                // $log  = array(
                //     'name'      =>$nickname,
                //     'hi_title'  =>$title,
                //     'hi_content'=>$con,
                //     'hi_time'   =>date('Y-m-d H:i:s'),
                //     'hicid'     =>$hic,
                //     'huid'      =>$huid
                // );
                // $addlog = M('HracInformation')->add($log);
                if($receiptlist){
                    //订单提交后清空购物车
                    $rst = M('Shopcart')->where(array('uid'=>$uid,'is_chose'=>1))->delete();
                    if($rst){
                        $order['status'] = 1;
                        $order['msg'] = "订单提交成功";
                        $this->ajaxreturn($order);
                    }else{
                        $data['status'] = 0;
                        $data['msg'] = "订单提交失败";
                        $this->ajaxreturn($data);
                    }
                }
            }
        }
    }
    /**
    *订单列表  登录人uid,订单状态ir_status:-1全选 0未支付 2已支付
    **/
    public function receiptList(){
	    if(!IS_POST){
	        $status = 0;
	        $this->ajaxreturn($status);
        }else{
            $uid       = I('post.uid');
            $ir_status = I('post.ir_status');
            if ($ir_status == "-1") {
            	$map = array(
	    			'ir_uid' 	=> $uid,
	    			'is_delete' => 0,
    			);
            }else{
	    		$map = array(
	    			'ir_uid' 	=> $uid,
	    			'ir_status' => $ir_status,
	    			'is_delete' => 0,
	    			);
            }
            $receiptlist = M('Receipt')->where($map)->order('ir_createTime')->select();
            if($receiptlist){
                $this->ajaxreturn($receiptlist);
            }else{
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        }
    }
    /**
    *订单详情 ir_receiptnum
    **/
    public function receiptInfo(){
        if(!IS_POST){
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }else{
        	$ir_receiptnum = I('post.ir_receiptnum');
            $receipt       = M('Receipt')->where(array('ir_receiptnum'=>$ir_receiptnum))->find();
            $receipt['receiptlist'] = M('Receiptlist')
                                    ->join('product on receiptlist.pid = product.pid')
                                    ->where(array('ir_receiptnum'=>$ir_receiptnum))
                                    ->select();
            $receipt['ir_date'] = date('Y-m-d H:i:s',$receipt['ir_createTime']);
            if($receipt){
                $this->ajaxreturn($receipt);
            }else{
                $status = 0;
                $this->ajaxreturn($receipt);
            }
        }
    }
}