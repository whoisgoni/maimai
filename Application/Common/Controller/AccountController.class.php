<?php
namespace Common\Controller;
use Think\Controller;
class AccountController extends Controller {
    //判断是否登陆
    public function is_login(){
        if($_SESSION['account']['is_login'] !== true){
            $this-> account_unset_session();
            return false;
        }
        return true;
    }
    //set account session
    public function account_set_session($account_id){
        $this-> account_unset_session();
        $account_id = intval($account_id);
        if(!$account_id){
            return false;
        }
        $info_sql = "SELECT `id`,`email` FROM `account` WHERE `id` = {$account_id} AND `status` = 3";
        $account_info = D()->query($info_sql);
        if(!$account_info){
            return false;
        }
        $_SESSION['account'] = $account_info[0];
        $_SESSION['account']['is_login'] = true;
        return true;
    }
    public function account_unset_session(){
        unset($_SESSION['account']);
    }
    /*
     *  验证登陆信息是否有效
     *
     *  params:
            $email = ''
            $password = ''
     *
     *  return:
            array(
                'errno' => 0,   // 0:成功 1:账号为空 2:密码为空 3:没有这个账号 4:密码错误
                'account_info' => array(),  //账号信息
            )
     */
    public function check_login_info($email, $password){
        $email = $email ? $email : '';
        $password = $password ? $password : '';
        if($email == ''){
            return array('errno' => 1);
        }
        if($password == ''){
            return array('errno' => 2);
        }
        $sql = "SELECT `id`,`email`,`password` FROM `account` WHERE `email` = '{$email}' AND `status` = 3";
        $res = D()->query($sql);
        if(!$res){
            return array('errno' => 3);
        }
        $account_info = $res[0];
        if($account_info['password'] !== md5(md5($password,true).sha1($password,true))){
            return array('errno' => 4);
        }
        return array('errno' => 0, 'account_info' => $account_info);
    }
    /*
     *  验证注册信息是否有效
     *
     *  params:
            $email = ''
            $password = ''
            $confirm_password = ''
            $verify = ''
     *  return: 
            array(
                'errno' => 0,                   // 0合法 !0不合法
                'msg'   => array(
                    'email'             => 0,   // 0合法 1空 2格式错误 3已被注册
                    'password'          => 0,   // 0合法 1空 2格式错误 
                    'confirm_password'  => 0,   // 0合法 1空 2与password不同
                    'verify'            => 0,   // 0合法 1空 2长度错误 3错误
                ),
            )
     */
    public function check_reg_info($email, $password, $confirm_password, $verify){
        $email = $email ? $email : '';
        $password = $password ? $password : '';
        $confirm_password = $confirm_password ? $confirm_password : '';
        $verify = $verify ? $verify : '';
        //判断email（0合法 1空 2格式错误 3已被注册）
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

    /*
     *  注册通行证
     *
     *  params:
            $email = ''
            $password = ''
     *  return: 
            bool    // true:注册成功 false:注册失败 
     */
    public function insert_reg_data($email, $password){
        $email = $email ? $email : '';
        $password = $password ? $password : '';
        $time = NOW_TIME;
        $ip = get_client_ip();
        
        $password = md5(md5($password,true).sha1($password,true));
        $ip = explode('.', $ip); 
        $ip = array_reverse($ip); 
        $ipnum = 0; 
        for($i=0,$j=count($ip); $i<$j; $i++){ 
            $ipnum += $ip[$i] * pow(256, $i); 
        }

        $sql = "INSERT INTO `account` (`email`,`password`,`reg_time`,`reg_ip`,`status`) VALUES ('{$email}','{$password}','{$time}','{$ipnum}',3)";
        $res = D()->execute($sql);
        if($res !== 1){
            return false;
        }
        return true;
    }
}
