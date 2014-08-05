<?php
namespace Home\Model;
use Think\Model;
class ShopModel extends Model {

    protected $trueTableName = 'shop';

    protected $insertFields = array('account_id', 'name', 'description', 'tel', 'lng', 'lat', 'province', 'city', 'district', 'street', 'street_number', 'business', 'add_time', 'status');
    protected $updateFields = array('name', 'description', 'tel', 'lng', 'lat', 'province', 'city', 'district', 'street', 'street_number', 'business', 'status');

    protected $_validate = array(
        array('name', 'require', '“店铺名称”必须填写'),
        array('name', 'validate_name_exist_insert', '“店铺名称”已存在', 0, 'callback', 1),
        array('name', 'validate_name_exist_update', '“店铺名称”已存在', 0, 'callback', 2),
        array('tel', '/^\d*$/', '“店铺电话”格式错误'),
        array('lng', '/^[-\+]?\d+(\.\d+)?$/', '“店铺地址”定位坐标错误'),
        array('lat', '/^[-\+]?\d+(\.\d+)?$/', '“店铺地址”定位坐标错误”'),
        array('status', array(2, 3), '“店铺状态”异常错误', 0, 'in'),
    );

    public function validate_name_exist_insert(){
        $name = $_POST['name'];
        $account_id = intval($_SESSION['account']['id']);
        if(!$name || !$account_id){
            return false;
        }
        $map = array(
            'name' => $name,
            'account_id' => $account_id,
        );
        $count = $this->where($map)->count();
        if($count > 0){
            return false;
        }
        return true;
    }

    public function validate_name_exist_update(){
        $id = intval($_POST['id']);
        $name = $_POST['name'];
        $account_id = intval($_SESSION['account']['id']);
        if(!$id || !$name || !$account_id){
            return false;
        }
        $map = array(
            'id' => array('neq', $id),
            'name' => $name,
            'account_id' => $account_id,
        );
        $count = $this->where($map)->count();
        if($count > 0){
            return false;
        }
        return true;
    }

    public function find_data(){
        $map = array(
            'account_id' => intval($_SESSION['account']['id']),
            'status' => array('in', array(2, 3)),
        );
        $res = $this->find($_GET['id']);
        return $res;
    }

    /*
     *  添加商铺
     *
     *  return:
            array(
                'errno' => 0,       //0:成功 !0:失败
                'errmsg' => '',
                'data' => array()
            )
     */
    public function insert_data($data){
        $return = array('errno' => 0, 'errmsg' => '', 'data' => array());
        $create = $this->create($data);
        if(!$create){
            $return['errno'] = 1;    
            $return['errmsg'] = $this->getError();
            return $return;
        }
        $this->account_id = intval($_SESSION['account']['id']);
        $this->add_time = NOW_TIME;
        $res = $this->add();
        if(!$res){
            $return['errno'] = 2;    
            $return['errmsg'] = $this->getError();
            return $return;
        }
        $return['data'] = array('id' => $res);
        return $return;
    }

    /*
     *  编辑商铺
     *
     *  return:
            array(
                'errno' => 0,       //0:成功 !0:失败
                'errmsg' => '',
                'data' => array()
            )
     */
    public function update_data($data){
        $id = intval($data['id']);
        $account_id = intval($_SESSION['account']['id']);
        $return = array('errno' => 0, 'errmsg' => '', 'data' => array());
        $create = $this->create($data);
        if(!$create){
            $return['errno'] = 1;    
            $return['errmsg'] = $this->getError();
            return $return;
        }
        $map = array(
            'id' => $id,
            'account_id' => $account_id,
        );
        $res = $this->where($map)->save();
        if($res === false){
            $return['errno'] = 2;    
            $return['errmsg'] = $this->getError();
            return $return;
        }
        $return['data'] = array('id' => $_POST['id']);
        return $return;
    }
    
    public function findall_img_data($fid = 0){
        $fid = intval($fid);
        if(!$fid){
            return false;
        }
        $map = array(
            'shop_id' => $fid,
        );
        $res = D()->table('shop_img')->where($map)->order('queue DESC')->select();
        return $res;
    }
    public function insertall_img_data($fid = 0, $imgs = array()){
        $fid = intval($fid);
        if(!$fid || !is_array($imgs)){
            return false;
        }
        $queue = $count = count($imgs);
        if($count <= 0){
            return false;
        }
        $data = array();
        foreach($imgs as $img){
            $data[] = array(
                'shop_id'   => $fid,        
                'img'       => $img,
                'queue'     => $queue,
            );
            $queue--;
        }
        D()->table('shop_img')->where(array('shop_id' => $fid))->delete();
        D()->table('shop_img')->addall($data);
    }
}
