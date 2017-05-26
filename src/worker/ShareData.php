<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 下午6:12
 */

namespace Evolution\WheelTimer\worker;


class ShareData extends \Threaded
{
    public function add($params){
        $this->synchronized(function($thread) use ($params) {
            $thread[] = json_encode($params);
        }, $this);
    }

    public function get(){
        $this->synchronized(function($thread) {
            return $thread->shift();
        }, $this);
    }

    public function run(){}
}