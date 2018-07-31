<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
/**
* hapylife food控制器
**/
class FeedBackController extends HomeBaseController{
    /**
	* 用户反馈
	* 1ibos 2nlc  3hrac 4elpa 5hapylife 
	**/
	public function feedback(){
		$data = array(
					'iuid'         => trim(I('post.iuid')),
					'whichApp'     => 5,
					'content'      => trim(I('post.content')),
					'type'		   => trim(I('post.type')),	
					'create_month' => date('Y-m',time()),
					'create_time'  => time()
				);
		//最多三张图
		$upload = several_upload();
		if(isset($upload['name'])){
			$data['image1']=C('WEB_URL').$upload['name'][0];
			$data['image2']=C('WEB_URL').$upload['name'][1];
			$data['image3']=C('WEB_URL').$upload['name'][2];
		}
		$addComment = M('feedback')->add($data);

		if($addComment){
			$this->ajaxreturn($data);
		}else{
			$data['status']     = 0;
			$data['message']	= '提交反馈失败';
			$this->ajaxreturn($data);
		}
	}

	/**
    * 用户反馈列表
    * 1ibos 2nlc  3hrac 4elpa 5hapylife
    **/
    public function feedbackList(){
        $iuid     = I('post.iuid');
        $data     = D('feedback')->where(array('iuid'=>$iuid,'whichApp'=>5))->order('create_time desc')->select();
        foreach ($data as $key => $value) {
            $feedback[$key]   = $value;
            switch ($value['type']) {
                case '0':
                    $feedback[$key]['title'] = '软件';
                    break;
                case '1':
                    $feedback[$key]['title'] = '账户';
                        break;
                case '2':
                    $feedback[$key]['title'] = '购物';
                        break;
                case '3':
                    $feedback[$key]['title'] = '银行';
                        break;
                case '4':
                    $feedback[$key]['title'] = '服务';
                        break;
                case '5':
                    $feedback[$key]['title'] = '其他';
                        break;
            } 
            $feedback[$key]['create_time'] = word_time($value['create_time']); 
            
        }
        if($feedback){
            $this->ajaxreturn($feedback);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }

	/**
    * 用户反馈详情及回复
    **/
    public function feedbackInfo(){
        $fbid    = I('post.fbid');
        $content = D('feedback')->join('hapylife_user on hapylife_feedback.iuid = hapylife_user.iuid')->where(array('fbid'=>$fbid))->select();
        foreach ($content as $key => $value) {
            $data['content'][$key]                = $value;
            $data['content'][$key]['create_time'] = word_time($value['create_time']); 
        }
        $reply   = D('feedback')->where(array('id'=>$fbid))->select();
        foreach ($reply as $key => $value) {
            $data['reply'][$key]                  = $value;
            $data['reply'][$key]['create_time']   = word_time($value['create_time']);
        }
        if($data){
            $this->ajaxreturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxreturn($data);
        }
    }
}