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
            $check_msg = A('Common/Account')->check_reg_info($_POST['email'], $_POST['password'], $_POST['confirm_password'], $_POST['verify']);
            if($check_msg['errno'] !== 0){
                $return_msg = array(
                    'errno' => 1,
                    'msg' => $check_msg['msg'],
                );
                $this->ajaxReturn($return_msg);
            }
            //注册入库
            $insert_msg = A('Common/Account')->insert_reg_data($_POST['email'],$_POST['password']);
            if($insert_msg !== true){
                $return_msg = array(
                    'errno' => 2,        
                );
                $this->ajaxReturn($return_msg);
            }
            $return_msg = array(
                'errno' => 0,
            );
            $this->ajaxReturn($return_msg);
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
