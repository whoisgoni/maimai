<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    //登陆
    public function index(){
        if(IS_POST){
            //验证登陆信息是否有效
            $check_msg = A('Common/Account')->check_login_info($_POST['email'], $_POST['password']);
            if($check_msg['errno'] !== 0){
                $return_msg = array('errno' => $check_msg['errno']);
                $this->ajaxReturn($return_msg);
            }
            //set session
            $is_set = A('Common/Account')->account_set_session($check_msg['account_info']['id']);
            if(!$is_set){
                $return_msg = array('errno' => 9);
                $this->ajaxReturn($return_msg);
            }
            $return_msg = array('errno' => 0);
            $this->ajaxReturn($return_msg);
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
    //登出
    public function logout(){
        A('Common/Account')->account_unset_session();
        $this->success('成功退出登陆！跳转至登陆页面...',U('index'),1);
    }
    public function verify(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }
}
