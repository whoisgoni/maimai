<?php
namespace Common\Controller;
use Think\Controller;
class AccountController extends Controller {
    //判断是否登陆
    public function is_login(){
        if($_SESSION['account']['is_login'] !== true){
            $this->error('未登录',C('DOMAIN.WWW').'?m=Home&c=Login&a=index');
        }
    }
    //验证注册信息是否有效
    /*
     *  params:
            $reg_data => array(
                'email'             => '',
                'password'          => '',
                'confirm_password'  => '',
                'verify'            => '',
            )
     *  return: 
            array(
                'errno' => 0,                   // 0合法 !0不合法
                'msg'   => array(
                    'email'             => 0,   // 0合法 1空 2格式错误
                    'password'          => 0,   // 0合法 1空 2格式错误 
                    'confirm_password'  => 0,   // 0合法 1空 2与password不同
                    'verify'            => 0,   // 0合法 1空 2长度错误 3错误
                ),
            )
     */
    public function check_reg_info($reg_data){
        $email = $reg_data['email'] ? $reg_data['email'] : '';
        $password = $reg_data['password'] ? $reg_data['password'] : '';
        $confirm_password = $reg_data['confirm_password'] ? $reg_data['confirm_password'] : '';
        $verify = $reg_data['verify'] ? $reg_data['verify'] : '';
        //判断email（0合法 1空 2格式错误 3数据库已存在）
        $email_errno = 0;
        if($email == ''){
            $email_errno = 1;
        }
        if($email_errno == 0){
            if(!preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$email)){
                $email_errno = 2;
            }
        }
        if($email_errno == 0){
            $email_exist = D()->query("SELECT id FROM `account` WHERE `email` = '{$email}'");
            if(count($email_exist) > 0){
                $email_errno = 3;
            }
        }
        //判断password（0合法 1空 2格式错误）
        $password_errno = 0;
        if($password == ''){
            $password_errno = 1;
        }
        if($password_errno == 0){
            if(!preg_match("/^\w{6,16}$/",$password)){
                $password_errno = 2;
            }
        }
        //判断confirm_password（0合法 1空 2与password不同）
        $confirm_password_errno = 0;
        if($confirm_password == ''){
            $confirm_password_errno = 1;
        }
        if($confirm_password_errno == 0){
            if($confirm_password != $password){
                $confirm_password_errno = 2;
            }
        }
        //判断verify（0合法 1空 2长度错误 3错误）
        $verify_errno = 0;
        if($verify == ''){
            $verify_errno = 1;
        }
        if($verify_errno == 0){
            if(strlen($verify) != 5){
                $verify_errno = 2;
            }
        }
        if($verify_errno == 0){
            $Verify = new \Think\Verify();
            $res = $Verify->check($verify);
            if($res !== true){
                $verify_errno = 3;
            }   
        }
        $errno = 0;
        if(($email_errno !== 0) || ($password_errno !== 0) || ($confirm_password_errno !== 0) || ($verify_errno !== 0)){
            $errno = 1;
        }
        return array(
            'errno' => $errno,
            'msg'   => array(
                'email'             => $email_errno,
                'password'          => $password_errno,
                'confirm_password'  => $confirm_password_errno,
                'verify'            => $verify_errno,
            ),
        );
    }
}
