<?php

header("Content-type:text/html;charset=utf-8");

//传递数据以易于阅读的样式格式化后输出
function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/**
 * app 图片上传
 * @return string 上传后的图片名
 */
function app_import_excelimage($path,$maxSize=52428800){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'.');
    $path=trim($path,'/');
    $config=array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',   
        'exts'      => array('jpg', 'gif', 'png', 'jpeg','bmp'),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
        );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    if($info) {
        foreach ($info as $k => $v) {
            $data[]=trim($v['savepath'],'.').$v['savename'];
        }
        return $data;
    }
}

/**
 * 实例化阿里云oos
 * @return object 实例化得到的对象
 */
function new_oss(){
    vendor('Alioss.autoload');
    $config=C('ALIOSS_CONFIG');
    $oss=new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
    return $oss;
}

/**
* 写入日志文件
* PHP_EOL 换行常量，windows \r\n; max \r; liunx \n;
* FILE_APPEND 在文件末尾追加数据
**/
function logTest($data){
    $log  = date('Y-m-d H:i:s').'**********'.$data.'**********'.PHP_EOL;
    $add  = file_put_contents('./log.txt', $log,FILE_APPEND);
    return;
}

/**
 * 上传文件到oss并删除本地文件
 * @param  string $path 文件路径
 * @return bollear      是否上传
 */
function oss_upload($path){
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    // 先统一去除左侧的.或者/ 再添加./
    $oss_path=ltrim($path,'./');
    $path='./'.$oss_path;
    if (file_exists($path)) {
        // 实例化oss类
        $oss=new_oss();
        // 上传到oss    
        $oss->uploadFile($bucket,$oss_path,$path);
        // 如需上传到oss后 自动删除本地的文件 则删除下面的注释 
        // unlink($path);
        return true;
    }
    return false;
}

/**
 * 删除oss上指定文件
 * @param  string $object 文件路径 例如删除 /Public/README.md文件  传Public/README.md 即可
 */
function oss_delet_object($object){
    // 实例化oss类
    $oss=new_oss();
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    $test=$oss->deleteObject($bucket,$object);
}

/**
 * app 视频上传
 * @return string 上传后的视频名
 */
function app_upload_video($path,$maxSize=52428800){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'.');
    $path=trim($path,'/');
    $config=array(
        'rootPath'  =>'./',         //文件上传保存的根路径
        'savePath'  =>'./'.$path.'/',   
        'exts'      => array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack'),
        'maxSize'   => $maxSize,
        'autoSub'   => true,
        );
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    if($info) {
        foreach ($info as $k => $v) {
            $data[]=trim($v['savepath'],'.').$v['savename'];
        }
        return $data;
    }
}


/**
 * 返回文件格式
 * @param  string $str 文件名
 * @return string      文件格式
 */
function file_format($str){
    // 取文件后缀名
    $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $image=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
    // 视频格式
    $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
    // 压缩格式
    $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
    // 文档格式
    $text=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $image):
            return 'image';
            break;
        case in_array($str, $video):
            return 'video';
            break;
        case in_array($str, $zip):
            return 'zip';
            break;
        case in_array($str, $text):
            return 'text';
            break;
        default:
            return 'image';
            break;
    }
}
 
/**
 * 发送友盟推送消息
 * @param  integer  $uid   用户id
 * @param  string   $title 推送的标题
 * @return boolear         是否成功
 */
function umeng_push($uid,$title){
    // 获取token
    $device_tokens=D('OauthUser')->getToken($uid,2);
    // 如果没有token说明移动端没有登录；则不发送通知
    if (empty($device_tokens)) {
        return false;
    }
    // 导入友盟
    Vendor('Umeng.Umeng');
    // 自定义字段   根据实际环境分配；如果不用可以忽略
    $status=1;
    // 消息未读总数统计  根据实际环境获取未读的消息总数 此数量会显示在app图标右上角
    $count_number=1;
    $data=array(
        'key'=>'status',
        'value'=>"$status",
        'count_number'=>$count_number
        );
    // 判断device_token  64位表示为苹果 否则为安卓
    if(strlen($device_tokens)==64){
        $key=C('UMENG_IOS_APP_KEY');
        $timestamp=C('UMENG_IOS_SECRET');
        $umeng=new \Umeng($key, $timestamp);
        $umeng->sendIOSUnicast($data,$title,$device_tokens);
    }else{
        $key=C('UMENG_ANDROID_APP_KEY');
        $timestamp=C('UMENG_ANDROID_SECRET');
        $umeng=new \Umeng($key, $timestamp);
        $umeng->sendAndroidUnicast($data,$title,$device_tokens);
    }
    return true;
}
 

/**
 * 返回用户id
 * @return integer 用户id
 */
function get_uid(){
    return $_SESSION['user']['id'];
}

/**
 * 返回iso、Android、ajax的json格式数据
 * @param  array  $data           需要发送到前端的数据
 * @param  string  $error_message 成功或者错误的提示语
 * @param  integer $error_code    状态码： 0：成功  1：失败
 * @return string                 json格式的数据
 */
function ajax_return($data='',$error_message='成功',$error_code=1){
    $all_data=array(
        'error_code'=>$error_code,
        'error_message'=>$error_message,
        );
    if ($data!=='') {
        $all_data['data']=$data;
        // app 禁止使用和为了统一字段做的判断
        $reserved_words=array('id','title','price','product_title','product_id','product_category','product_number');
        foreach ($reserved_words as $k => $v) {
            if (array_key_exists($v, $data)) {
                echo 'app不允许使用【'.$v.'】这个键名 —— 此提示是function.php 中的ajax_return函数返回的';
                die;
            }
        }
    }
    // 如果是ajax或者app访问；则返回json数据 pc访问直接p出来
    echo json_encode($all_data);
    exit(0);
}

/**
 * 获取完整网络连接
 * @param  string $path 文件路径
 * @return string       http连接
 */
function get_url($path){
    // 如果是空；返回空
    if (empty($path)) {
        return '';
    }
    // 如果已经有http直接返回
    if (strpos($path, 'http://')!==false) {
        return $path;
    }
    // 判断是否使用了oss
    $alioss=C('ALIOSS_CONFIG');
    if (empty($alioss['KEY_ID'])) {
        return 'http://'.$_SERVER['HTTP_HOST'].$path;
    }else{
        return 'http://'.$alioss['BUCKET'].'.'.$alioss['END_POINT'].$path;
    }
    
}

/**
 * 检测是否登录
 * @return boolean 是否登录
 */
function check_login(){
    if (!empty($_SESSION['user']['id'])){
        return true;
    }else{
        return false;
    }
}

/**
 * 根据配置项获取对应的key和secret
 * @return array key和secret
 */
function get_rong_key_secret(){
    // 判断是需要开发环境还是生产环境的key
    if (C('RONG_IS_DEV')) {
        $key=C('RONG_DEV_APP_KEY');
        $secret=C('RONG_DEV_APP_SECRET');
    }else{
        $key=C('RONG_PRO_APP_KEY');
        $secret=C('RONG_PRO_APP_SECRET');
    }
    $data=array(
        'key'=>$key,
        'secret'=>$secret
        );
    return $data;
}

/**
 * 获取融云token
 * @param  integer $uid 用户id
 * @return integer      token
 */
function get_rongcloud_token($uid){
    // 从数据库中获取token
    $token=D('OauthUser')->getToken($uid,1);
    // 如果有token就返回
    if ($token) {
        return $token;
    }
    // 获取用户昵称和头像
    $user_data=M('Users')->field('username,avatar')->getById($uid);
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    // 获取头像url格式
    $avatar=get_url($user_data['avatar']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new \Org\Xb\RongCloud($key_secret['key'],$key_secret['secret']);
    // 获取token
    $token_json=$rong_cloud->getToken($uid,$user_data['username'],$avatar);
    $token_array=json_decode($token_json,true);
    // 获取token失败
    if ($token_array['code']!=200) {
        return false;
    }
    $token=$token_array['token'];
    $data=array(
        'uid'=>$uid,
        'type'=>1,
        'nickname'=>$user_data['username'],
        'head_img'=>$avatar,
        'access_token'=>$token
        );
    // 插入数据库
    $result=D('OauthUser')->addData($data);
    if ($result) {
        return $token;
    }else{
        return false;
    }
}

/**
 * 更新融云头像
 * @param  integer $uid 用户id
 * @return boolear      操作是否成功
 */
function refresh_rongcloud_token($uid){
    // 获取用户昵称和头像
    $user_data=M('Users')->field('username,avatar')->getById($uid);
    // 用户不存在
    if (empty($user_data)) {
        return false;
    }
    $avatar=get_url($user_data['avatar']);
    // 获取key和secret
    $key_secret=get_rong_key_secret();
    // 实例化融云
    $rong_cloud=new \Org\Xb\RongCloud($key_secret['key'],$key_secret['secret']);
    // 更新融云用户头像
    $result_json=$rong_cloud->userRefresh($uid,$user_data['username'],$avatar);
    $result_array=json_decode($result_json,true);
    if ($result_array['code']==200) {
        return true;
    }else{
        return false;
    }
}

/**
 * 删除指定的标签和内容
 * @param array $tags 需要删除的标签数组
 * @param string $str 数据源
 * @param string $content 是否删除标签内的内容 0保留内容 1不保留内容
 * @return string
 */
function strip_html_tags($tags,$str,$content=0){
    if($content){
        $html=array();
        foreach ($tags as $tag) {
            $html[]='/(<'.$tag.'.*?>[\s|\S]*?<\/'.$tag.'>)/';
        }
        $data=preg_replace($html,'',$str);
    }else{
        $html=array();
        foreach ($tags as $tag) {
            $html[]="/(<(?:\/".$tag."|".$tag.")[^>]*>)/i";
        }
        $data=preg_replace($html, '', $str);
    }
    return $data;
}

/**
 * 传递ueditor生成的内容获取其中图片的路径
 * @param  string $str 含有图片链接的字符串
 * @return array       匹配的图片数组
 */
function get_ueditor_image_path($str){
    $preg='/\/Upload\/image\/u(m)?editor\/\d*\/\d*\.[jpg|jpeg|png|bmp]*/i';
    preg_match_all($preg, $str,$data);
    return current($data);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function re_substr($str, $start=0, $length, $suffix=true, $charset="utf-8") {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $omit=mb_strlen($str) >=$length ? '...' : '';
    return $suffix ? $slice.$omit : $slice;
}

// 设置验证码
function show_verify($config=''){
    if($config==''){
        $config=array(
            'codeSet'=>'1234567890',
            'fontSize'=>30,
            'useCurve'=>false,
            'imageH'=>60,
            'imageW'=>240,
            'length'=>4,
            'fontttf'=>'4.ttf',
            );
    }
    $verify=new \Think\Verify($config);
    return $verify->entry();
}

// 检测验证码
function check_verify($code){
    $verify=new \Think\Verify();
    return $verify->check($code);
}

/**
 * 取得根域名
 * @param type $domain 域名
 * @return string 返回根域名
 */
function get_url_to_domain($domain) {
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}

/**
 * 按符号截取字符串的指定部分
 * @param string $str 需要截取的字符串
 * @param string $sign 需要截取的符号
 * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
 * @return string 返回截取的内容
 */
/*  示例
    $str='123/456/789';
    cut_str($str,'/',0);  返回 123
    cut_str($str,'/',-1);  返回 789
    cut_str($str,'/',-2);  返回 456
    具体参考 http://www.baijunyao.com/index.php/Home/Index/article/aid/18
*/
function cut_str($str,$sign,$number){
    $array=explode($sign, $str);
    $length=count($array);
    if($number<0){
        $new_array=array_reverse($array);
        $abs_number=abs($number);
        if($abs_number>$length){
            return 'error';
        }else{
            return $new_array[$abs_number-1];
        }
    }else{
        if($number>=$length){
            return 'error';
        }else{
            return $array[$number];
        }
    }
}

/**
 * 发送邮件
 * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return boolean       是否成功
 */
function send_email($address,$subject,$content){
    $email_smtp=C('EMAIL_SMTP');
    $email_username=C('EMAIL_USERNAME');
    $email_password=C('EMAIL_PASSWORD');
    $email_from_name=C('EMAIL_FROM_NAME');
    $email_smtp_secure=C('EMAIL_SMTP_SECURE');
    $email_port=C('EMAIL_PORT');
    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
        return array("error"=>1,"message"=>'邮箱配置不完整');
    }
    require_once './ThinkPHP/Library/Org/Nx/class.phpmailer.php';
    require_once './ThinkPHP/Library/Org/Nx/class.smtp.php';
    $phpmailer=new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置设置smtp_secure
    $phpmailer->SMTPSecure=$email_smtp_secure;
    // 设置port
    $phpmailer->Port=$email_port;
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet='UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host=$email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth=true;
    // 设置用户名
    $phpmailer->Username=$email_username;
    // 设置密码
    $phpmailer->Password=$email_password;
    // 设置邮件头的From字段。
    $phpmailer->From=$email_username;
    // 设置发件人名字
    $phpmailer->FromName=$email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    if(is_array($address)){
        foreach($address as $addressv){
            $phpmailer->AddAddress($addressv);
        }
    }else{
        $phpmailer->AddAddress($address);
    }
    // 设置邮件标题
    $phpmailer->Subject=$subject;
    // 设置邮件正文
    $phpmailer->Body=$content;
    // 发送邮件。
    if(!$phpmailer->Send()) {
        $phpmailererror=$phpmailer->ErrorInfo;
        return array("error"=>1,"message"=>$phpmailererror);
    }else{
        return array("error"=>0);
    }
}

/**
 * 获取一定范围内的随机数字
 * 跟rand()函数的区别是 位数不足补零 例如
 * rand(1,9999)可能会得到 465
 * rand_number(1,9999)可能会得到 0465  保证是4位的
 * @param integer $min 最小值
 * @param integer $max 最大值
 * @return string
 */
function rand_number ($min=1, $max=9999) {
    return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}

/**
 * 生成一定数量的随机数，并且不重复
 * @param integer $number 数量
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @return string
 */
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] = rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] = rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}

/**
 * 生成不重复的随机数
 * @param  int $start  需要生成的数字开始范围
 * @param  int $end 结束范围
 * @param  int $length 需要生成的随机数个数
 * @return array       生成的随机数
 */
function get_rand_number($start=1,$end=10,$length=4){
    $connt=0;
    $temp=array();
    while($connt<$length){
        $temp[]=rand($start,$end);
        $data=array_unique($temp);
        $connt=count($data);
    }
    sort($data);
    return $data;
}

/**
 * 实例化page类
 * @param  integer  $count 总数
 * @param  integer  $limit 每页数量
 * @return subject       page类
 */
function new_page($count,$limit=10){
    return new \Org\Nx\Page($count,$limit);
}

/**
 * 获取分页数据
 * @param  subject  $model  model对象
 * @param  array    $map    where条件
 * @param  string   $order  排序规则
 * @param  integer  $limit  每页数量
 * @return array            分页数据
 */
function get_page_data($model,$map,$order='',$limit=10){
    $count=$model
        ->where($map)
        ->count();
    $page=new_page($count,$limit);
    // 获取分页数据
    $list=$model
            ->where($map)
            ->order($order)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
    $data=array(
        'data'=>$list,
        'page'=>$page->show()
        );
    return $data;
}

/**
 * 处理post上传的文件；并返回路径
 * @param  string $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string $format  文件格式限制
 * @param  string $maxSize 允许的上传文件最大值 52428800
 * @return array           返回ajax的json格式数据
 */
function post_upload($path='file',$format='empty',$maxSize='3145728000'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='Upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               //上传文件最大为50M
                'rootPath'  =>  './',                   //文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                  //自动使用子目录保存上传文件 默认为true
                'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            return $data;
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
            }               
            return $data;
        }
    }
}


/**
 * 处理post上传的文件；并返回路径
 * @param  string $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string $format  文件格式限制
 * @param  string $maxSize 允许的上传文件最大值 52428800
 * @return array           返回ajax的json格式数据
 */
function several_upload($path='file',$format='empty',$maxSize='3145728000'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp','mp4','avi'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               //上传文件最大为50M
                'rootPath'  =>  './',                   //文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   //自动使用子目录保存上传文件 默认为true
                'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            return $data;
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name'][]=trim($file['savepath'].$file['savename'],'.');
            }               
            return $data;
        }
    }
}

/**
 * 上传文件类型控制   此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler       返回ajax的json格式数据
 */
function upload($path='file',$format='empty',$maxSize='52428800'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,       //   上传文件最大为50M
                'rootPath'  =>  './',           //文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   //  自动使用子目录保存上传文件 默认为true
                'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                echo json_encode($data);
            }               
        }
    }
}

/**
 * 使用curl获取远程数据
 * @param  string $url url连接
 * @return string      获取到的数据
 */
function curl_get_contents($url){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    // curl_setopt($ch,CURLOPT_HEADER,1);               //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r=curl_exec($ch);
    curl_close($ch);
    return $r;
}

/*
* 计算星座的函数 string get_zodiac_sign(string month, string day)
* 输入：月份，日期
* 输出：星座名称或者错误信息
*/

function get_zodiac_sign($month, $day)
{
    // 检查参数有效性
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
        return (false);
        // 星座名称以及开始日期
        $signs = array(
        array( "20" => "水瓶座"),
        array( "19" => "双鱼座"),
        array( "21" => "白羊座"),
        array( "20" => "金牛座"),
        array( "21" => "双子座"),
        array( "22" => "巨蟹座"),
        array( "23" => "狮子座"),
        array( "23" => "处女座"),
        array( "23" => "天秤座"),
        array( "24" => "天蝎座"),
        array( "22" => "射手座"),
        array( "22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int)$month-1]);
    if ($day < $sign_start)
    list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]);
    return $sign_name;
}

/**
 * 发送 容联云通讯 验证码
 * @param  int $phone 手机号
 * @param  int $code  验证码
 * @return boole      是否发送成功
 */
function send_sms_code($phone,$code){
    //请求地址，格式如下，不需要写https://
    $serverIP='app.cloopen.com';
    //请求端口
    $serverPort='8883';
    //REST版本号
    $softVersion='2013-12-26';
    //主帐号
    $accountSid=C('RONGLIAN_ACCOUNT_SID');
    //主帐号Token
    $accountToken=C('RONGLIAN_ACCOUNT_TOKEN');
    //应用Id
    $appId=C('RONGLIAN_APPID');
    //应用Id
    $templateId=C('RONGLIAN_TEMPLATE_ID');
    $rest = new \Org\Xb\Rest($serverIP,$serverPort,$softVersion);
    $rest->setAccount($accountSid,$accountToken);
    $rest->setAppId($appId);
    // 发送模板短信
    $result=$rest->sendTemplateSMS($phone,array($code,5),$templateId);
    if($result==NULL) {
        return false;
    }
    if($result->statusCode!=0) {
        return  false;
    }else{
        return true;
    }
}

/**
 * 将路径转换加密
 * @param  string $file_path 路径
 * @return string            转换后的路径
 */
function path_encode($file_path){
    return rawurlencode(base64_encode($file_path));
}

/**
 * 将路径解密
 * @param  string $file_path 加密后的字符串
 * @return string            解密后的路径
 */
function path_decode($file_path){
    return base64_decode(rawurldecode($file_path));
}

/**
 * 根据文件后缀的不同返回不同的结果
 * @param  string $str 需要判断的文件名或者文件的id
 * @return integer     1:图片  2：视频  3：压缩文件  4：文档  5：其他
 */
function file_category($str){
    // 取文件后缀名
    $str=strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $images=array('webp','jpg','png','ico','bmp','gif','tif','pcx','tga','bmp','pxc','tiff','jpeg','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','hdri');
    // 视频格式
    $video=array('mp4','avi','3gp','rmvb','gif','wmv','mkv','mpg','vob','mov','flv','swf','mp3','ape','wma','aac','mmf','amr','m4a','m4r','ogg','wav','wavpack');
    // 压缩格式
    $zip=array('rar','zip','tar','cab','uue','jar','iso','z','7-zip','ace','lzh','arj','gzip','bz2','tz');
    // 文档格式
    $document=array('exe','doc','ppt','xls','wps','txt','lrc','wfs','torrent','html','htm','java','js','css','less','php','pdf','pps','host','box','docx','word','perfect','dot','dsf','efe','ini','json','lnk','log','msi','ost','pcs','tmp','xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $images):
            return 1;
            break;
        case in_array($str, $video):
            return 2;
            break;
        case in_array($str, $zip):
            return 3;
            break;
        case in_array($str, $document):
            return 4;
            break;
        default:
            return 5;
            break;
    }
}

/**
 * 组合缩略图
 * @param  string  $file_path  原图path
 * @param  integer $size       比例
 * @return string              缩略图
 */
function get_min_image_path($file_path,$width=170,$height=170){
    $min_path=str_replace('.', '_'.$width.'_'.$height.'.', trim($file_path,'.'));
    $min_path=OSS_URL.$min_path;
    return $min_path;
} 

/**
 * 不区分大小写的in_array()
 * @param  string $str   检测的字符
 * @param  array  $array 数组
 * @return boolear       是否in_array
 */
function in_iarray($str,$array){
    $str=strtolower($str);
    $array=array_map('strtolower', $array);
    if (in_array($str, $array)) {
        return true;
    }
    return false;
}

/**
 * 传入时间戳,计算距离现在的时间
 * @param  number $time 时间戳
 * @return string     返回多少以前
 */
function word_time($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2){
        $str = sprintf('刚刚', $int);
    }elseif ($int < 60){
        $str = sprintf('%d秒前', $int);
    }elseif ($int < 3600){
        $str = sprintf('%d分钟前', floor($int / 60));
    }elseif ($int < 86400){
        $str = sprintf('%d小时前', floor($int / 3600));
    }elseif ($int < 1728000){
        $str = sprintf('%d天前', floor($int / 86400));
    }else{
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}
/**
 * 传入时间戳,计算距离现在的时间(英文版)
 * @param  number $time 时间戳
 * @return string 返回多少以前
 */
function formattime($time){
    if (is_int($time)) {
        $time = intval($time);
    }elseif ($time instanceof Carbon) {
        $time = intval(strtotime($time));
    }else {
        return '';
    }
    $ctime = time();
    $t = $ctime - $time; //时间差 （秒）
    if ($t < 0) {
        return date('Y-m-d', $time);
    }
    $y = intval(date('Y', $ctime) - date('Y', $time));//是否跨年
    if($t == 0){
        $text = 'a moment ago';
    }elseif ($t < 60) {//一分钟内
        $text = $t . 'seconds ago';
    }elseif ($t < 3600) {//一小时内
        $text = floor($t / 60) . 'minutes ago';
    }elseif ($t < 86400) {//一天内
        $text = floor($t / 3600) . 'hours ago'; // 一天内
    }elseif ($t < 2592000) {//30天内
        if ($time > strtotime(date('Ymd', strtotime("-1 day")))) {
            $text = 'yesterday';
        } elseif ($time > strtotime(date('Ymd', strtotime("-2 days")))) {
            $text = 'before yesterday';
        } else {
            $text = floor($t / 86400) . 'days ago';
        }
    }elseif ($t < 31536000 && $y == 0) {//一年内 不跨年
        $m = date('m', $ctime) - date('m', $time) - 1;
        if ($m == 0) {
            $text = floor($t / 86400) . 'days ago';
        } else {
            $text = $m . 'months ago';
        }
    }elseif ($t < 31536000 && $y > 0) {//一年内 跨年
        $text = (12 - date('m', $time) + date('m', $ctime)) . 'months ago';
    }else {
        $text = (date('Y', $ctime) - date('Y', $time)) . 'years ago';
    }
    return $text;
}

/**
 * 生成缩略图
 * @param  string  $image_path 原图path
 * @param  integer $width      缩略图的宽
 * @param  integer $height     缩略图的高
 * @return string             缩略图path
 */
function crop_image($image_path,$width=170,$height=170){
    $image_path=trim($image_path,'.');
    $min_path='.'.str_replace('.', '_'.$width.'_'.$height.'.', $image_path);
    $image = new \Think\Image();
    $image->open($image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height,\Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/**
 * 上传文件类型控制 此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler   返回ajax的json格式数据
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               // 上传文件最大为50M
                'rootPath'  =>  './',                   // 文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         // 文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     // 上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   // 自动使用子目录保存上传文件 默认为true
                'exts'      =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // p($_FILES);
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        // p($info);
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            echo json_encode($data);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $data['name']=trim($file['savepath'].$file['savename'],'.');
                // p($data);
                echo json_encode($data);
            }               
        }
    }
}

/**
 * 检测webuploader上传是否成功
 * @param  string $file_path post中的字段
 * @return boolear           是否成功
 */
function upload_success($file_path){
    // 为兼容传进来的有数组；先转成json
    $file_path=json_encode($file_path);
    // 如果有undefined说明上传失败
    if (strpos($file_path, 'undefined') !== false) {
        return false;
    }
    // 如果没有.符号说明上传失败
    if (strpos($file_path, '.') === false) {
        return false;
    }
    // 否则上传成功则返回true
    return true;
}



/**
 * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
 */
function emoji_encode($str){
    if(!is_string($str))return $str;
    if(!$str || $str=='undefined')return '';

    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
        return addslashes($str[0]);
    },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}

/**
 * 检测是否是手机访问
 */
function is_mobile(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    function _is_mobile($substrs,$text){
        foreach($substrs as $substr)
            if(false!==strpos($text,$substr)){
                return true;
            }
            return false;
    }
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

    $found_mobile=_is_mobile($mobile_os_list,$useragent_commentsblock) ||
              _is_mobile($mobile_token_list,$useragent);
    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}

/**
 * 将utf-16的emoji表情转为utf8文字形
 * @param  string $str 需要转的字符串
 * @return string      转完成后的字符串
 */
function escape_sequence_decode($str) {
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function($matches) {
        if (isset($matches[3])) {
            $cp = hexdec($matches[3]);
        } else {
            $lead = hexdec($matches[1]);
            $trail = hexdec($matches[2]);
            $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
        }

        if ($cp > 0xD7FF && 0xE000 > $cp) {
            $cp = 0xFFFD;
        }
        if ($cp < 0x80) {
            return chr($cp);
        } else if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
        }
        $result =  html_entity_decode('&#'.$cp.';');
        return $result;
    }, $str);
}

/**
 * 获取当前访问的设备类型
 * @return integer 1：其他  2：iOS  3：Android
 */
function get_device_type(){
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 1;
    //分别进行判断
    if(strpos($agent, 'iphone')!==false || strpos($agent, 'ipad')!==false){
        $type = 2;
    } 
    if(strpos($agent, 'android')!==false){
        $type = 3;
    }
    return $type;
}

/**
 * 生成pdf
 * @param  string $html      需要生成的内容
 */
function pdf($html='<h1 style="color:red">hello word</h1>'){
    vendor('Tcpdf.tcpdf');
    $pdf = new \Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // 设置打印模式
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nicola Asuni');
    $pdf->SetTitle('TCPDF Example 001');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
    // 是否显示页眉
    $pdf->setPrintHeader(false);
    // 设置页眉显示的内容
    $pdf->SetHeaderData('logo.png', 60, 'baijunyao.com', '白俊遥博客', array(0,64,255), array(0,64,128));
    // 设置页眉字体
    $pdf->setHeaderFont(Array('dejavusans', '', '12'));
    // 页眉距离顶部的距离
    $pdf->SetHeaderMargin('5');
    // 是否显示页脚
    $pdf->setPrintFooter(true);
    // 设置页脚显示的内容
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // 设置页脚的字体
    $pdf->setFooterFont(Array('dejavusans', '', '10'));
    // 设置页脚距离底部的距离
    $pdf->SetFooterMargin('10');
    // 设置默认等宽字体
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // 设置行高
    $pdf->setCellHeightRatio(1);
    // 设置左、上、右的间距
    $pdf->SetMargins('10', '10', '10');
    // 设置是否自动分页  距离底部多少距离时分页
    $pdf->SetAutoPageBreak(TRUE, '15');
    // 设置图像比例因子
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->setFontSubsetting(true);
    $pdf->AddPage();
    // 设置字体
    $pdf->SetFont('stsongstdlight', '', 14, '', true);
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Output('example_001.pdf', 'I');
}

/**
 * 生成二维码
 * @param  string  $url  url连接
 * @param  integer $size 尺寸 纯数字
 */
function qrcode($url,$size=4){
    Vendor('Phpqrcode.phpqrcode');
    QRcode::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
}

/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
        $data = array(
            array(NULL, 2010, 2011, 2012),
            array('Q1',   12,   15,   21),
            array('Q2',   56,   73,   86),
            array('Q3',   52,   61,   69),
            array('Q4',   30,   32,    0),
           );
 */
function create_xls($data,$filename='simple.xls'){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}

/**
 * 数据转csv格式的excle
 * @param  array $data      需要转的数组
 * @param  string $header   要生成的excel表头
 * @param  string $filename 生成的excel文件名
 *      示例数组：
        $data = array(
            '1,2,3,4,5',
            '6,7,8,9,0',
            '1,3,5,6,7'
            );
        $header='用户名,密码,头像,性别,手机号';
 */
function create_csv($data,$header=null,$filename='simple.csv'){
    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }
    // 防止没有添加文件后缀
    $filename=str_replace('.csv', '', $filename).'.csv';
    ob_clean();
    Header( "Content-type:  application/octet-stream ");
    Header( "Accept-Ranges:  bytes ");
    Header( "Content-Disposition:  attachment;  filename=".$filename);
    foreach( $data as $k => $v){
        // 如果是二维数组；转成一维
        if (is_array($v)) {
            $v=implode(',', $v);
        }
        // 替换掉换行
        $v=preg_replace('/\s*/', '', $v); 
        // 解决导出的数字会显示成科学计数法的问题
        $v=str_replace(',', "\t,", $v); 
        // 转成gbk以兼容office乱码的问题
        echo iconv('UTF-8','GBK',$v)."\t\r\n";
    }
}

/**
 * 导入excel文件
 * @param  string $file excel文件路径
 * @return array        excel文件内容数组
 */
function import_excel($file){
    // 判断文件是什么格式
    $type = pathinfo($file); 
    $type = strtolower($type["extension"]);
    if ($type=='xlsx') { 
        $type='Excel2007'; 
    }elseif($type=='xls') { 
        $type = 'Excel5'; 
    } 
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file); 
    $sheet = $objPHPExcel->getSheet(0); 
    // 取得总行数 
    $highestRow = $sheet->getHighestRow();     
    // 取得总列数      
    $highestColumn = $sheet->getHighestColumn(); 
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='A';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        } 
    }  
    return $data;
}

/**
 * 跳向支付宝付款
 * @param  array $order 订单数据 必须包含 out_trade_no(订单号)、price(订单金额)、subject(商品名称标题)
 */
function alipay($order){
    vendor('Alipay.AlipaySubmit','','.class.php');
    // 获取配置
    $config=C('ALIPAY_CONFIG');
    $data=array(
        "_input_charset" => $config['input_charset'], // 编码格式
        "logistics_fee" => "0.00", // 物流费用
        "logistics_payment" => "SELLER_PAY", // 物流支付方式SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        "logistics_type" => "EXPRESS", // 物流类型EXPRESS（快递）、POST（平邮）、EMS（EMS）
        "notify_url" => $config['notify_url'], // 异步接收支付状态通知的链接
        "out_trade_no" => $order['out_trade_no'], // 订单号
        "partner" => $config['partner'], // partner 从支付宝商户版个人中心获取
        "payment_type" => "1", // 支付类型对应请求时的 payment_type 参数,原样返回。固定设置为1即可
        "price" => $order['price'], // 订单价格单位为元
        // "price" => 0.01, // // 调价用于测试
        "quantity" => "1", // price、quantity 能代替 total_fee。 即存在 total_fee,就不能存在 price 和 quantity;存在 price、quantity, 就不能存在 total_fee。 （没绕明白；好吧；那无视这个参数即可）
        "receive_address" => '1', // 收货人地址 即时到账方式无视此参数即可
        "receive_mobile" => '1', // 收货人手机号码 即时到账方式无视即可
        "receive_name" => '1', // 收货人姓名 即时到账方式无视即可
        "receive_zip" => '1', // 收货人邮编 即时到账方式无视即可
        "return_url" => $config['return_url'], // 页面跳转 同步通知 页面路径 支付宝处理完请求后,当前页面自 动跳转到商户网站里指定页面的 http 路径。
        "seller_email" => $config['seller_email'], // email 从支付宝商户版个人中心获取
        "service" => "create_direct_pay_by_user", // 接口名称 固定设置为create_direct_pay_by_user
        "show_url" => $config['show_url'], // 商品展示网址,收银台页面上,商品展示的超链接。
        "subject" => $order['subject'] // 商品名称商品的标题/交易标题/订单标 题/订单关键字等
    );
    $alipay=new \AlipaySubmit($config);
    $new=$alipay->buildRequestPara($data);
    $go_pay=$alipay->buildRequestForm($new, 'get','支付');
    echo $go_pay;
}

/**
 * 微信扫码支付
 * @param  array $order 订单 必须包含支付所需要的参数 body(产品描述)、total_fee(订单金额)、out_trade_no(订单号)、product_id(产品id)
 */
function weixinpay($order){
    $order['trade_type']='NATIVE';
    Vendor('Weixinpay.Weixinpay');
    $weixinpay=new \Weixinpay();
    $weixinpay->pay($order);
}

/**
 * 验证AppStore内付
 * @param  string $receipt_data 付款后凭证
 * @return array                验证是否成功
 */
function validate_apple_pay($receipt_data){
    /**
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     */
    function acurl($receipt_data, $sandbox=0){
        //小票信息
        $POSTFIELDS = array("receipt-data" => $receipt_data);
        $POSTFIELDS = json_encode($POSTFIELDS);
 
        //正式购买地址 沙盒购买地址
        $url_buy     = "https://buy.itunes.apple.com/verifyReceipt";
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $url = $sandbox ? $url_sandbox : $url_buy;
 
        //简单的curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    // 验证参数
    if (strlen($receipt_data)<20){
        $result=array(
            'status'=>false,
            'message'=>'非法参数'
            );
        return $result;
    }
    // 请求验证
    $html = acurl($receipt_data);
    $data = json_decode($html,true);
 
    // 如果是沙盒数据 则验证沙盒模式
    if($data['status']=='21007'){
        // 请求验证
        $html = acurl($receipt_data, 1);
        $data = json_decode($html,true);
        $data['sandbox'] = '1';
    }
 
    if (isset($_GET['debug'])) {
        exit(json_encode($data));
    }
     
    // 判断是否购买成功
    if(intval($data['status'])===0){
        $result=array(
            'status'=>true,
            'message'=>'购买成功'
            );
    }else{
        $result=array(
            'status'=>false,
            'message'=>'购买失败 status:'.$data['status']
            );
    }
    return $result;
}

/**
 * geetest检测验证码
 */
function geetest_chcek_verify($data){
    $geetest_id=C('GEETEST_ID');
    $geetest_key=C('GEETEST_KEY');
    $geetest=new \Org\Xb\Geetest($geetest_id,$geetest_key);
    $user_id=$_SESSION['geetest']['user_id'];
    if ($_SESSION['geetest']['gtserver']==1) {
        $result=$geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $user_id);
        if ($result) {
            return true;
        } else{
            return false;
        }
    }else{
        if ($geetest->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode'])) {
            return true;
        }else{
            return false;
        }
    }
    
}
// 写入时间
function addtime($starttime,$endtime){
    //半小时
    $length = ($endtime-$starttime)/1800;
    for($i=0;$i<$length;$i++){
        $data[] = date('H:i',$starttime+$i*1800);
    }
    return $data;
}

/**
 *无限极分类函数
 * @param  [type]  $arr      [原数组]
 * @param  integer $parentid [父id]
 * @return [type]  $list     [重构数组]
 */
function arraySort($arr,$parentid=0){
    $list=array();
    foreach($arr as $key => $v){
        if($v['parent_pid']==$parentid){
            $tmp = arraySort($arr,$v['parent_id']);
            if($tmp){
                $v['submenu'] = $tmp;
            }
            $list[]=$v;
        }
    }
    return $list;
}


//签名生成算法
function getSignature($params,$secret){
    //对所有请求参数按ASCII码升序排列
    ksort($params); 
    //将排序好的参数名和参数值拼装在一起
    foreach ($params as $key => $value) {
        $str .= $key.$value; 
    }
    //对拼接好的字符流MD5()摘要,如果是sha1加密，需要再字符流前后加secret
    $sign = strtoupper(md5($secret.$str.$secret));
    return $sign;
}

//快钱参数过滤
function kq_ck_null($kq_va,$kq_na){
        if($kq_va == ""){
            return $kq_va="";
        }else{
            return $kq_va=$kq_na.'='.$kq_va.'&';
        }
    }


//封装curl的调用接口，get的请求方式
function doCurlGetRequest($url,$data,$timeout=5){
    $url = $url.'?'.http_build_query($data);
    $con = curl_init((string)$url);
    curl_setopt($con, CURLOPT_HEADER, false);
    curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($con, CURLOPT_TIMEOUT,$timeout);
    return curl_exec($con);
}


//封装curl的调用接口，post请求方式
function doCurlPostRequest($url,$requestString,$timeout=5){
    $con = curl_init((string)$url);
    curl_setopt($con,CURLOPT_HEADER,false);
    curl_setopt($con,CURLOPT_POSTFIELDS, http_build_query($requestString));
    curl_setopt($con,CURLOPT_POST,true);
    curl_setopt($con,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($con,CURLOPT_TIMEOUT,$timeout);
    return curl_exec($con);
}

//访问IBOS360 会员登录user.logon
function ibos360_userlogin($username,$password){
    //生成数字签名
    $params = array(
        'method'    => 'user.logon',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'username'  => $username,
        'password'  => $password
    );
    $secret = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    // $ibos360_url = "http://victor.ngrok.xiaomiqiu.cn/router/rest";
    $ibos360_userdata = array(
            'method'    =>'user.logon',
            'appKey'    =>'00001',
            'v'         =>"2.0",
            'format'    => 'json',
            'username'  => $username,
            'password'  => $password,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问ibos360 会员注册member.Createmember
function ibos360_Createmember($data,$sessionId){
    //生成数字签名
    $params = array(
        'method'    => 'member.CreateMember',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'sessionId' => $sessionId,
        'data'      =>  $data,
    );
    $secret = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    // $ibos360_url = "http://victor.ngrok.xiaomiqiu.cn/router/rest";
    $ibos360_userdata = array(
            'method'    =>'member.CreateMember',
            'appKey'    =>'00001',
            'v'         =>'2.0',
            'format'    =>'json',
            'sessionId' => $sessionId,
            'data'      => $data,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问IBOS360 会员查询member.GetMembers
function ibos360_GetMembers($data,$pageModel){
    //生成数字签名
    $params = array(
        'method'    => 'member.GetMembers',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
        'pageModel' => $pageModel
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    $ibos360_userdata = array(
            'method'    => 'member.GetMembers',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'pageModel' => $pageModel,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}


//访问IBOS360 业绩获取Getbonus
function ibos360_Getbonus($memberNo,$workingStage){
    //生成数字签名
    $params = array(
        'method'    => 'bonus.getOrgMonthlyPfm',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'memberNo'      => $memberNo,
        'workingStage'  => $workingStage
    );
    $secret = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_userdata = array(
            'method'    => 'bonus.getOrgMonthlyPfm',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'memberNo'      => $memberNo,
            'workingStage'  => $workingStage,
            'sign'          => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问IBOS360 商品编码查询product.GetProducts
function ibos360_GetProducts($data,$pageModel){
    //生成数字签名
    $params = array(
        'method'    => 'product.GetProducts',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
        'pageModel' => $pageModel
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_userdata = array(
            'method'    => 'product.GetProducts',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'pageModel' => $pageModel,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问IBOS360 销售商品查询product.GetProductSales
function ibos360_GetProductSales($data,$pageModel){
    //生成数字签名
    $params = array(
        'method'    => 'product.GetProductSales',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
        'pageModel' => $pageModel
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    $ibos360_userdata = array(
            'method'    => 'product.GetProductSales',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'pageModel' => $pageModel,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//推荐网network.GetSponsorNetwork
function ibos360_GetSponsorNetwork($memberNo,$level){
    //生成数字签名
    $params = array(
        'method'    => 'network.GetSponsorNetwork',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'memberNo'  => $memberNo,
        'level'     => $level
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    $ibos360_userdata = array(
            'method'    => 'network.GetSponsorNetwork',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'memberNo'  => $memberNo,
            'level'     => $level,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}


//安置网network.GetSponsorNetwork
function ibos360_GetPlacementNetwork($memberNo,$level){
    //生成数字签名
    $params = array(
        'method'    => 'network.GetPlacementNetwork',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'memberNo'  => $memberNo,
        'level'     => $level
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    $ibos360_userdata = array(
            'method'    => 'network.GetPlacementNetwork',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'memberNo'  => $memberNo,
            'level'     => $level,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}


//访问IBOS360 订单查询order.GetOrders
function ibos360_GetOrders($data,$pageModel){
    //生成数字签名
    $params = array(
        'method'    => 'order.GetOrders',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
        'pageModel' => $pageModel
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://oss.ibos360.com/router/rest";
    $ibos360_userdata = array(
            'method'    => 'order.GetOrders',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'pageModel' => $pageModel,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问IBOS360 订单保存order.SaveOrder
function ibos360_SaveOrder($data,$sessionId){
    //生成数字签名
    $params = array(
        'method'    => 'order.SaveOrder',
        'appKey'    => '00001',
        'sessionId' => $sessionId,
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    // $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_url = "http://victor.ngrok.xiaomiqiu.cn/router/rest";
    $ibos360_userdata = array(
            'method'    => 'order.SaveOrder',
            'appKey'    => '00001',
            'sessionId' => $sessionId,
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

//访问IBOS360 退货单查询returnOrder.GetReturnOrders
function ibos360_GetReturnOrders($data,$pageModel){
    //生成数字签名
    $params = array(
        'method'    => 'returnOrder.GetReturnOrders',
        'appKey'    => '00001',
        'v'         => '2.0',
        'format'    => 'json',
        'data'      => $data,
        'pageModel' => $pageModel
    );
    $secret    = 'abcdeabcdeabcdeabcdeabcde';
    $signature = getSignature($params,$secret);

    //访问接口
    $ibos360_url      = "http://ibos-sandbox-oss.isoftlife.cc/router/rest";
    $ibos360_userdata = array(
            'method'    => 'returnOrder.GetReturnOrders',
            'appKey'    => '00001',
            'v'         => "2.0",
            'format'    => 'json',
            'data'      => $data,
            'pageModel' => $pageModel,
            'sign'      => $signature
        );
    $ibos360_result = doCurlPostRequest($ibos360_url,$ibos360_userdata);
    $ibos360_result = json_decode($ibos360_result,true);
    return $ibos360_result;
}

 /**
 * 功能：生成二维码
 * @param string $qr_data   手机扫描后要跳转的网址
 * @param string $qr_level  默认纠错比例 分为L、M、Q、H四个等级，H代表最高纠错能力
 * @param string $qr_size   二维码图大小，1－10可选，数字越大图片尺寸越大
 * @param string $save_path 图片存储路径
 * @param string $save_prefix 图片名称前缀
 */
function createQRcode($save_path,$qr_data,$qr_level='L',$qr_size=4,$save_prefix='qrcode'){
    if(!isset($save_path)) return '';
    //设置生成png图片的路径
    $PNG_TEMP_DIR = & $save_path;
    //导入二维码核心程序

    vendor('Phpqrcode.phpqrcode');  //注意这里的大小写哦，不然会出现找不到类，Phpqrcode是文件夹名字，class#phpqrcode就代表class.phpqrcode.php文件名

    //检测并创建生成文件夹
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR);
    }
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
        $errorCorrectionLevel = & $qr_level;
    }
    $matrixPointSize = 4;
    if (isset($qr_size)){
        $matrixPointSize = & min(max((int)$qr_size, 1), 10);
    }
    if (isset($qr_data)) {
        if (trim($qr_data) == ''){
            die('data cannot be empty!');
        }
        //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
        $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        //开始生成
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    } else {
        //默认生成
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    if(file_exists($PNG_TEMP_DIR.basename($filename)))
        return basename($filename);
    else
        return FALSE;
}

/**
* 把指定时间段切份,以半小时为单位
* -----------------------------------
* @param string $start 开始时间
* @param string $end 结束时间
* @param int $nums 切分数目
* @param boolean 是否格式化
* @return array 时间段数组
*/
function cut_apart_time($start, $end="", $nums, $format=true) {
    $start = strtotime($start);
    $end   = strtotime($end);
    $parts = ($end - $start)/$nums;
    $last  = ($end - $start)%$nums;
    if ( $last > 0) {
        $parts = ($end - $start - $last)/$nums;
    }
    for ($i=1; $i <= $nums; $i++) { 
        $_end  = $start + $parts * $i;
        $arr[] = array($start + $parts * ($i-1), $_end);
    }
    $len = count($arr)-1;
    $arr[$len][1] = $arr[$len][1] + $last;
    if ($format) {
        foreach ($arr as $key => $value) {
            $arr[$key][0] = date("H:i", $value[0]);
            $arr[$key][1] = date("H:i", $value[1]);
        }
    }
    return $arr;
}


/**
* 某一日期开始的日期数组
* -----------------------------------
* @param string $today 开始日
* @param string $number 次数
* @param boolean 是否格式化
* @return array 时间段数组
*/ 
function date_time($today,$number){
    $date = $today.',';
    for($i=0;$i<$number;$i++){
        $date.=date("Y-m-d",strtotime("+1 day",strtotime($today))).',';
        $today = date("Y-m-d",strtotime("+1 day",strtotime($today)));
    }
    $str = chop($date,',');
    $strarr = explode(',', $str);
    return $strarr;
} 


/**
 * 把返回的数据集转换成Tree
 * @param array $list   要转换的数据集
 * @param string $pk    自增字段huid
 * @param string $pid   hpid字段
 * @param string $child 标记字段
 * @param string $root  起始huid值
 * @return array
 */
function make_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
  $tree=array();
  $packData=array();
  foreach ($list as $data) {
    $packData[$data[$pk]] = $data;
  }
  foreach ($packData as $key =>$val){
    if($val[$pid]==$root){
      $tree[]=& $packData[$key];
    }else{
      //找到其上级
      $packData[$val[$pid]][$child][]=& $packData[$key];
    }
  }
  return $tree;
}


//记录交易流水号
function debugPrint($log){
    $file = 'apps.nulifeshop.com/nulife/Public/people.txt';
    $d = date();
    file_put_contents($file, $d.':'.$log,FILE_APPEND | LOCK_EX);
}

/**
 *评论无限极分类函数
 * @param  [type]  $arr   [原数组]
 * @param  integer $id    [父id]
 * @param  integer id     [自增id]
 * @return [type]  $lev   [标记第几级]
 * @return [type]  $subs  [重构数组]
 */
function subtree($arr,$id=0,$lev=1) {
    static $subs = array(); //子孙数组
    static $num  = 1; //子孙数组
    foreach ($arr as $v) {
        if ($v['cid'] == $id) {
            $v['lev']   = $lev;
            $subs[] = $v;
            subtree($arr,$v['id'],$lev+1);
        }       
    }
    return $subs;
}

/**
* weekbonus 左大区
**/
function leftTree($arr,$sponsorid,$lev=1) {
    static $subs = array(); //子孙数组
    static $num  = 1; //子孙数组
    foreach ($arr as $v) {
        if ($v['sponsorid'] == $sponsorid) {
            $v['lev']   = $lev;
            $subs[] = $v;
            leftTree($arr,$v['userid'],$lev+1);
        }       
    }
    return $subs;
}
/**
* weekbonus 右大区
**/
function rightTree($arr,$sponsorid,$lev=1) {
    static $subs = array(); //子孙数组
    static $num  = 1; //子孙数组
    foreach ($arr as $v) {
        if ($v['sponsorid'] == $sponsorid) {
            $v['lev']   = $lev;
            $subs[] = $v;
            rightTree($arr,$v['userid'],$lev+1);
        }       
    }
    return $subs;
}
/**
* 获取父级元素
**/
function getSponsor($arr,$sponsor,$lev=0){
    static $subs = array();
    foreach ($arr as $key => $value) {
        if($value['account'] == $sponsor){
            $value['lev'] = $lev;
            $subs[]   = $value;
            getSponsor($arr,$value['sponsor'],$lev-1);
        }
    }
    return $subs;
}

function hapysubtree($arr,$id=0,$lev=1) {
    static $subs = array(); //子孙数组
    static $num  = 1; //子孙数组
    foreach ($arr as $v) {
        if ($v['pid'] == $id) {
            $v['lev']   = $lev;
            $subs[] = $v;
            hapysubtree($arr,$v['uid'],$lev+1);
        }       
    }
    return $subs;
}
/**
* binary 二叉树
**/
function getBinary($arr,$sponsor,$lev=1){
    static $subs = array();
    foreach ($arr as $k => $v) {
        if($v['sponsor'] == $sponsor){
            $v['lev'] = $lev;
            $subs[]   = $v;
            getBinary($arr,$v['account'],$lev+1);
        }
    }
    return $subs;
}

/**
* binary 二叉树
**/
function getAllBinary($arr,$SponsorID,$lev=1){
    static $subs = array();
    foreach ($arr as $k => $v) {
        if($v['sponsorid'] == $SponsorID){
            $v['lev'] = $lev;
            $subs[]   = $v;
            getAllBinary($arr,$v['customerid'],$lev+1);
        }
    }
    return $subs;
}
/**
 * 把返回的数据集根据字段排序
 * @param array $arr   要转换的数据集
 * @param string $keys 排序字段
 * @param string $type 排序方式
 * @return array
 */
function array_sort($arr,$keys,$type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[] = $arr[$k];
    }
    return $new_array;
} 
/**
 * 把时间进行重写 如:08:20变成08:30/08:31变成09:00/08:00不变
 * @param array  $tmp   要转换的数据集
 * @return string
 */
function restructure_time($tmp){
    $arr = explode(':', $tmp);
    // p($arr);
    foreach ($arr as $key => $value) {
        $str[$key]=str_split($value);
    }
    foreach ($str as $key => $value) {
        foreach ($value as $k => $v) {
            $timedate[] = $v;
        }
    }
    if($timedate[2]==0){
        if($timedate[3]==0){
            $date = $tmp;
        }else{
            $date = $timedate[0].$timedate[1].':30';
        }
    }else if($timedate[2]==1 || $timedate[2]==2){
        $date = $timedate[0].$timedate[1].':30';
    }else if($timedate[2]==3){
        if($timedate[3]==0){
            $date = $tmp;
        }else{
            $timekey = $timedate[1]+1;
            $date = $timedate[0].$timekey.':00';
        }
    }else{
        $timekey = $timedate[1]+1;
        $date = $timedate[0].$timekey.':00';
    }
    return $date;
} 

/**
 * 将二维数组中相同的一维数组重复删除剩一个
 * @param array  $array  要转换的数据集
 * @return string
 */
function unique_arr($array,$stkeep=false,$ndformat=true){
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if($stkeep) $stArr = array_keys($array);
    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if($ndformat) $ndArr = array_keys(end($array));
    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array as $v){
        $v = join(",",$v);
        $temp[] = $v;
    }
    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);
    //再将拆开的数组重新组装
    foreach ($temp as $k => $v){
        if($stkeep) $k = $stArr[$k];
        if($ndformat){
            $tempArr = explode(",",$v);
            foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
        }
        else $output[$k] = explode(",",$v);
    }
    return $output;
}

/**
 * 处理post上传的文件；并返回路径
 * @param  string $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string $format  文件格式限制
 * @param  string $maxSize 允许的上传文件最大值 52428800
 * @return array           返回ajax的json格式数据
 */
function several_upload_arr($path='file',$format='empty',$maxSize='3145728000'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp','mp4','avi'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               //上传文件最大为50M
                'rootPath'  =>  './',                   //文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         //文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     //上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   //自动使用子目录保存上传文件 默认为true
                'exts'    =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            $data['error_info']=$error;
            return $data;
        }else{
            // 返回成功信息
            foreach($info as $key => $file){
                $data['name'][$key]=trim($file['savepath'].$file['savename'],'.');
            }               
            return $data;
        }
    }
}

/**
 * 将信息存进二维码
 * @param  string $content 要存放的json数据
 * @return array  $pic     返回图片路径
 */
function qrcode_arr($content){
    $web_url     = C('WEB_URL');
    $qrcodecon   = json_encode($content);
    // echo $hr_number;die;
    //图片存储的绝对路径
    $save_path   = isset($_GET['save_path'])?$_GET['save_path']:'Public/codepic/';
    // 图片在网页上显示的路径
    $web_path    = isset($_GET['save_path'])?$_GET['web_path']:'Public/codepic/';
    $qr_data     = $qrcodecon;
    $qr_level    = isset($_GET['qr_level'])?$_GET['qr_level']:'H';
    $qr_size     = isset($_GET['qr_size'])?$_GET['qr_size']:'5';
    $save_prefix = isset($_GET['save_prefix'])?$_GET['save_prefix']:'ZETA';
    if($filename = createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix)){
        $pic     = $web_url.'/Public/codepic/'.$filename;
    }
    return $pic;
}
/**
 *生成随机字符串
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
 * @return string
 */
function randCode($length = 5, $type = 0){
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    }elseif ($type == "-1") {
        $string = implode("", $arr);
    }else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for($i = 0; $i < $length; $i++) {
        $code .= $string[rand(0, $count)];
    }
    return $code;
}

/**
 *截取无限极(最高和指定位置的中间)
 * @param  [type]  $arr   [原数组]
 * @param  integer $id    [主键id]
 * @param  integer $pid   [父id]
 * @return [type]  $level [循环次数]
 * @return [type]  $idnum [结束id]
 * @return [type]  $tmp   [重构数组]
 */
function subtree_arr($arr,$id,$pid,$idnum) {
    foreach ($arr as $key => $value) {
        if($value[$id]==$idnum){
            $level = $value['lev'];
            $ids   = $value[$pid];
        }
    }
    // echo $level;
    foreach ($arr as $key => $value) {
        if($value['lev']<$level){
            $treearr[] = $value;
        }
    }
    for($i==0;$i<$level;$i++){
        $j=$ids;
        foreach ($treearr as $key => $value){
            if($value[$id]==$j){
                $tmp[$key] = $value;
                $ids       = $value[$pid];
            }
        }
    }
    return $tmp;
}
/** 
 * 二维数组分页 
 * @param  [type]  $arr        [二维数组] 
 * @param  integer $page       [当前页数]
 * @param  integer $indexinpage[每页显示的条数] 
 * @return [type]  $newarr     [结果]
 */  
function arrPage($arr, $page, $indexinpage){  
    $page = is_int($page) != 0 ? $page : 1; //当前页数  
    $indexinpage = is_int($indexinpage) != 0 ? $indexinpage : 5; //每页显示几条  
    $newarr = array_slice($arr, ($page - 1) * $indexinpage, $indexinpage);  
    return $newarr;  
}

/** 
 * 添加用户优惠券 
 * @param  integer $huid  [用户id] 
 * @param  integer $hcid  [券组合1-2-3]
 * @param  integer $lp    [数量1-2-3]
 */  
function addcou($huid, $hcid, $lp){
    $huctime = date("Y-m-d",time());  
    if(strpos($hcid,'-')){
        $huciarr = explode('-', $hcid);
        $looparr = explode('-', $lp);
        foreach ($huciarr as $key => $value) {
            foreach ($looparr as $ke => $va) {
                if($key==$ke){
                    $coupon[$key]['hcid'] = $value;
                    $coupon[$key]['loop'] = $va;
                    $coupon[$key]['huid'] = $huid;
                }
            }
        }
    }else{
        $coupon[0]['hcid'] = $hcid;
        $coupon[0]['loop'] = $lp;
        $coupon[0]['huid'] = $huid;
    }
    // p($coupon);
    $class = M('HracCoupon')->select();
    // p($coupon);die;
    foreach ($coupon as $key => $value) {
        if($value['loop']!=0){
            $add[] = $value;
        }
    }
    foreach ($class as $key => $value) {
        foreach ($add as $k => $v) {
            if($value['hcid']==$v['hcid']){
                $couclass[$k] = $v;
                $couclass[$k]['hc_name'] = $value['hc_name'];
            }
        }
    }
    // p($couclass);
    foreach ($couclass as $key => $value) {
        $couinfo['huid']       = $value['huid'];
        $couinfo['hi_time']    = date('Y-m-d H:i:s',time());
        $couinfo['content']   .= $value['hc_name'].'券'.$value['loop'].'张,';
        $couinfo['hi_title']   = '收到用户优惠券';
        $couinfo['hicid']      = 2;
        $couinfo['num']       += $value['loop'];
    }
    $foarr = array(
        'huid'       =>$couinfo['huid'],
        'hi_time'    =>$couinfo['hi_time'],
        'hi_content' =>'总共收到'.$couinfo['num'].'张优惠券,分别有'.rtrim($couinfo['content'], ','),
        'hi_title'   =>$couinfo['hi_title'],
        'hicid'      =>$couinfo['hicid']
    );
    // p($foarr);
    foreach ($add as $key => $value) {
        $hcinfo  = M('HracCoupon')->where(array('hcid'=>$value['hcid']))->find();
        $hcterm  = $hcinfo['hc_term'];
        // echo $hcterm;die;
        $hpname  = M('HracProject')->where(array('hpid'=>$hcinfo['hpid']))->getfield('hp_abb');
        $numarr  = M('HracUsercoupon')->where(array('hp_abb'=>$hpname))->order('huc_number desc')->select();
        if($numarr){
            $number = $numarr[0]['huc_number']+1;
            $numarray[0]=$number;
        }else{
            $number = 50000001;
            $numarray[0]=$number;
        }
        if($hcterm==0){
            $hucdate=0;
        }else{
            $date   = time()+$hcterm*86400;
            $hucdate= date("Y-m-d",$date);  
        }
        // echo $hucdate;die;
        $loop = $value['loop'];
        for($i=1;$i<$loop;$i++){
            $number+=1;
            $numarray[$i]=$number;
        }
        // $numhee[$key] = $numarray;
        // $numarray = Array(50000003,50000004,50000005,50000006,50000007);
        // p($numarray);
        foreach ($numarray as $ke => $va) {
            $tmp[$ke]['huc_number'] = $va;
            $tmp[$ke]['hcid']       = $value['hcid'];
            $tmp[$ke]['huid']       = $value['huid'];
            $tmp[$ke]['huc_time']   = $huctime;
            $tmp[$ke]['hp_abb']     = $hpname;
            $tmp[$ke]['hc_type']    = $hcinfo['hc_type'];
            $tmp[$ke]['huc_date']   = $hucdate;  
            $tmp[$ke]['huc_parent'] = $huid;  
        }
        foreach ($tmp as $ke => $va) {
            M('HracUsercoupon')->add($va);
        }
        // p($tmp);
        foreach ($numarray as $ke => $va) {
            $status[$ke] = M('HracUsercoupon')->where(array('hp_abb'=>$hpname,'huc_number'=>$va))->find();  
        }
        foreach ($status as $ke => $va) {
            // 存放的内容
            $content     = array('huid'=>$va['huid'],'hucid'=>$va['hucid'],'huc_number'=>$hpname.'-'.$va['huc_number'],'codetype'=>1,'huc_vaild'=>0,'huc_parent'=>0);
            $pic         = qrcode_arr($content);
            $where       = array(
                    'hucid'      =>$va['hucid'],
                    'huc_codepic'=>$pic
            );
            $code        = M('HracUsercoupon')->save($where);
        }   
    }
    return $code;
}
/**
* 导入csv
* @param $file csv文件路径
**/
function import_csv($file){
    $data = file_get_contents($file);
    $data = explode("\r\n", $data);
    return $data;
}

/********************************************************日志*******************************************************************
    * @param type   1EP 2订单 3VP 4会员
    * @param action 1EP  （0积分提现的提交 1转出 2转入 3支付 4积分提现的审核 5发放） 
    * @param action 2订单 (1生成 2支付 3发货  4审核 5退货 6退款) 
    * @param action 3VP  （1转出 2转入 3商品兑换VP 4vp兑换商品） 
    * @param action 4会员（1注册 2退出 3信息编辑 4会员升级 ）
    **/

    /**
    * 添加积分提取日志
    * iuid 会员id
    * content 日志记录内容
    * create_month 冗余归档日期,如 2017-8
    * create_time  创建时间 存储格式时间戳
    **/
    function addLog($iuid,$content,$action,$type){
        $create_month = date('Y-m',time());    
        $log = array(
                'iuid'        =>$iuid,
                'content'     =>$content,
                'create_month'=>$create_month,
                'create_time' =>time(),
                'type'        =>$type,
                'action'      =>$action
            );
        $add = M('Log')->add($log);
        if($add){
            return "日志记录成功";
        }else{
            return "日志记录失败";
        }
    }


/*********************************************************************畅捷支付********************************************************************/

/*
 * 直接支付请求接口（畅捷前台）   nmg_quick_onekeypay
 */
function rsaSign($args) {
    $args  = array_filter($args);//过滤掉空值
    ksort($args);
    $query = '';
    foreach($args as $k=>$v){
        if($k=='SignType'){
            continue;
        }
        if($query){
            $query  .=  '&'.$k.'='.$v;
        }else{
            $query  =  $k.'='.$v;
        }
    }
    //这地方不能用 http_build_query  否则会urlencode
    //$query=http_build_query($args);
    $path        = "./rsa_private_key.pem";  //私钥地址
    $private_key = file_get_contents($path);
    $pkeyid      = openssl_get_privatekey($private_key);
    openssl_sign($query, $sign, $pkeyid);
    openssl_free_key($pkeyid);
    $sign        = base64_encode($sign);
    return $sign;
}

/**
* 加解密
**/
function rsaEncrypt($content){
    $public_key_path = "./rsa_public_key.pem";  //公钥地址 
    $pubKey          = file_get_contents($public_key_path);
    $res             = openssl_get_publickey($pubKey);
    //把需要加密的内容，按128位拆开解密
    $result  = '';
    for($i = 0; $i < strlen($content)/128; $i++  ) {
        $data = substr($content, $i * 128, 128);
        openssl_public_encrypt ($data, $encrypt, $res);
        $result .= $encrypt;
    }
    $result = base64_encode($result);
    openssl_free_key($res);
    return $result;
}


function httpGet_a($order_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $order_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    curl_close($ch);
    return $json;
}


/**
 * RSA验签
 * $postData 请传入完整$_POST数据
 * 调用方法 rsaVerify($_POST);即可完成验签 
 */
function rsaVerify($postData)
{
    $rsaVerify_arr = $postData;

    unset($rsaVerify_arr['sign']); //移除不参与验签的参数
    unset($rsaVerify_arr['sign_type']); //移除不参与验签的参数
    $rsaVerify_arr = array_filter($rsaVerify_arr); //过滤掉空值
    ksort($rsaVerify_arr);
    $rsaVerify_str = '';
    foreach ($rsaVerify_arr as $k => $v)
    {
        if ($rsaVerify_str)
        {
            $rsaVerify_str .= '&' . $k . '=' . $v;
        } else
        {
            $rsaVerify_str = $k . '=' . $v;
        }
    }
    $public_key_path   =   "./cj_rsa_public_key.pem";  
    $pubKey = file_get_contents($public_key_path);
    $res = openssl_get_publickey($pubKey);
    //print_R($postData['sign']);
    $result = (bool)openssl_verify($rsaVerify_str, base64_decode($postData['sign']), $res);
    openssl_free_key($res);
    if (!$result)
    {
        return "false";
        exit;
    } else
    {
        return "true";
    }
}