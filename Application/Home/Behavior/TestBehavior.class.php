<?php
namespace Home\Behavior;
class TestBehavior {
    // 行为扩展的执行入口必须是run
    public function run(&$params){
        if(C('TEST_PARAM')) {
            echo 'RUNTEST BEHAVIOR '.$params;
        }
        else{
            echo 321;
        }
    }
}
