<?php
namespace Home\Controller;
use Think\Controller;
class ShopController extends Controller {
    public function _initialize(){
        $is_login =A('Common/Account')->is_login();
        if($is_login !== true){
            if(IS_AJAX){
                $this->ajaxReturn(array('errno' => 9999, 'errmsg' => '未登录'));
            }
            $this->error("未登录", U('Login/index'), 3);
        }
    }
    public function index(){
        $map['account_id'] = $_SESSION['account']['id'];
        $map['status'] = array('in', array(2,3));
        $count = D('Shop')->where($map)->count();
        import("@.Library.Page");
        $Page = new \Home\Library\Page($count,15);
        $show = $Page->show();
        $list = D('Shop')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            //插入数据库
            $insert_msg = D('Shop')->insert_data($_POST);
            if($insert_msg['errno'] !== 0){
                $this->ajaxReturn($insert_msg);
            }
            $insert_id = $insert_msg['data']['id'];
            //图片
            D('Shop')->insertall_img_data($insert_id,$_POST['img']);
            $return = array('errno' => 0);
            $this->ajaxReturn($return);
        }
        $this->display();
    }
    public function edit(){
        if(IS_POST){
            //更新数据库
            $update_msg = D('Shop')->update_data($_POST);
            if($update_msg['errno'] !== 0){
                $this->ajaxReturn($update_msg);
            }
            $update_id = $update_msg['data']['id'];
            //图片
            D('Shop')->insertall_img_data($update_id,$_POST['img']);
            $return = array('errno' => 0);
            $this->ajaxReturn($return);
        }
        $res = D('Shop')->find_data();
        if(!$res){
            $this->error("访问错误", U('index'), 3);
        }
        $res['img_res'] = D('Shop')->findall_img_data($res['id']);
        $this->assign('res', $res);
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
