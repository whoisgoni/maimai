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
    public function check_reg_info($reg_data){
        var_dump($reg_data);
    }
}
