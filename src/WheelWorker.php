<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:21
 */

namespace Evolution\WheelTimer;


class WheelWorker extends \Thread {
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

    public function show($id)
    {
        echo "\n 消费者　threadid is {$id}\n　tick=".$this->wheel->current_tick."\r\n";
    }

    function run(){
        try{
            while (1){
                $params = $this->wheel->get();
                $this->wheel->tickIncr();
                if(!empty($params)){
                    $this->shareData->add($params);
                    $this->workerpool->dispatch();
                }
                sleep($this->wheel->tic_interval);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }finally{
            echo "end\n";
        }
    }
}
