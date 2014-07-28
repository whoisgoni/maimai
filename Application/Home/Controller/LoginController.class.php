<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        if(IS_POST){

        }
        $this->display();
    }
    //注册
    public function reg(){
        if(IS_POST){
            //验证注册信息是否有效
            $check_msg = A('Common/Account')->check_reg_info($_POST);
            if($check_msg['errno'] !== 0){
                $this->error($check_msg['msg']);
            }
            //注册入库

            var_dump($check_msg);
            exit;
        }
        $this->display();
    }
    public function check_verify(){
        $code = $_POST['code'];
        $Verify = new \Think\Verify();
        $res = $Verify->check($code);
        if($res === true){
            $this->success();
        }
        $this->error();
    }
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }
}
