<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 第三方用户
 */
class User1Model extends BaseModel{
    // 批量验证
    protected $patchValidate = true;
    // 验证规则
    protected $_validate = array(
        array('Placement','require','密码不能为空！'), //默认情况下用正则进行验证
        array('CustomerID','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('CustomerID','require','用户名不能为空！'), // 在新增的时候验证name字段是否唯一
        array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
        array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
        array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
        array('Idcard','/(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)|[A-Za-z]{1}\d{6}[(\d)]{3}/','请输入有效身份证号码'),
        array('Email','/^\w+([.]\w+)?[@]\w+[.]\w+([.]\w+)?$/','请输入正确的电子邮箱'),
        array('Phone','/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[358]\d{9})$)/','请输入正确的电话号码'),
   );
}