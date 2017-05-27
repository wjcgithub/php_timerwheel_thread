<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:21
 */

namespace Evolution\WheelTimer;

use Evolution\WheelTimer\Storage\Log\LogTrait;

class WheelWorker extends \Thread {

    use LogTrait;

    public $wheel;
    //执行任务的线程池
    public $workerpool;
    //和线程池共享数据
    public $shareData;

    public function __construct(&$wheel,$workerpool,$shareData)
    {
        $this->wheel = $wheel;
        $this->workerpool = $workerpool;
        $this->shareData = $shareData;
    }

    function run(){
        try{
            while (1){
//                echo "tick one {$this->wheel->current_tick}\r\n";
                $params = $this->wheel->get();
                $this->wheel->tickIncr();
                if(!empty($params)){
                    $this->shareData->add($params);
                    $this->workerpool->dispatch();
                }
                $this->synchronized(function($thread){
                    $thread->wait($this->wheel->tic_interval);
                }, $this);
            }
        } catch (\Exception $e) {
            self::error('时间轮线程发生错误:'.$e->getTraceAsString().'File: '.$e->getFile().'Line:'.$e->getLine());
        }
    }
}
