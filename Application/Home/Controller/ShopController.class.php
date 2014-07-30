<?php
namespace Home\Controller;
use Think\Controller;
class ShopController extends Controller {
    //public function _initialize(){
    public function _before_index(){
        $is_login =A('Common/Account')->is_login();
        if($is_login !== true){
            $this->error("未登录", U('Login/index'), 3);
        }
    }
    public function index(){
        $sql = "SELECT * FROM `shop` WHERE `account_id` = {$_SESSION['account']['id']} AND `status` IN (2,3)";
        $list = D()->query($sql);
        $this->assign('list', $list);
        $this->display();
    }
    public function add(){
        $this->display();
    }
    public function upload(){
        $rand1 = rand(10, 99);
        $rand2 = rand(10, 99);
        $upd_rootpath = C('PATH.UPLOAD');
        $upd_savepath = 'shop' . __DS__;
        $upd_subname = $rand1 . __DS__ . $rand2 . __DS__;
        $url_rootpath = C('DOMAIN.UPLOAD');
        $url_savepath = 'shop/';
        $url_subname = $rand1 . '/' . $rand2 . '/';

        $upload = new \Think\Upload();
        $upload->maxSize   = 4096000;
        $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath  = $upd_rootpath;
        $upload->savePath  = $upd_savepath;
        $upload->subName   = $upd_subname;
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $msg = array(
                'errno' => 1,
                'msg' => $upload->getError(),
            );
            $this->ajaxReturn($msg);
        }else{// 上传成功
            $msg = array(
                'errno' => 0,
                'rootpath' => $url_rootpath,
                'savepath' => $url_savepath,
                'subname' => $url_subname,
                'savename' => $info[0]['savename'],
            );
            $this->ajaxReturn($msg);
        }
    }
}
