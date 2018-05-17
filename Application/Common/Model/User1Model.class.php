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
        array('LastName','require','请填写中文姓'), 
        array('FirstName','require','请填写中文名'), 
        array('EnLastName','require','请填写英文姓'), 
        array('EnFirstName','require','请填写英文名'), 
        array('PassWord','/^[0-9]{8}/','密码最少8位数'), 
        array('ConfirmPassWord','PassWord','两次输入密码不一致',0,'confirm'), 
        array('EnrollerID','require','推荐人不能为空！'), 
        array('Email','/^\w+([.]\w+)?[@]\w+[.]\w+([.]\w+)?$/','请输入正确的电子邮箱'),
        array('Phone','/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[358]\d{9})$)/','请输入正确的电话号码'),
        array('Phone','','该号码已被注册',0,'unique'),
        array('ShopProvince','require','请填写所在省'), 
        array('ShopCity','require','请填写所在市'), 
        array('ShopArea','require','请填写所在区'), 
        array('ShopAddress1','require','请填写详细地址'), 
        array('BankName','require','银行名称不能为空'), 
        array('BankAccount','require','银行账号不能为空'), 
        // array('BankNum','/^([1-9]{1})(\d{14}|\d{18})$/','请填写有效的银行账号'), 
        array('BankProvince','require','请填写银行所在省'), 
        array('BankCity','require','请填写银行所在市'), 
        array('BankArea','require','请填写银行所在区'), 
        array('SubName','require','支行名称不能为空'), 
        // array('Idcard','/(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)|[A-Za-z]{1}\d{6}[(\d)]{3}/','请输入有效身份证号码'),
        array('Idcard','/[^\w\s]+/','存在非法字符'),
   );
}