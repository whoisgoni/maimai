<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //public function _initialize(){
    public function _before_index(){
        $is_login =A('Common/Account')->is_login();
        if($is_login !== true){
            $this->error("未登录", U('Login/index'), 3);
        }
    }
    public function index(){
        $this->display();
    }
}
