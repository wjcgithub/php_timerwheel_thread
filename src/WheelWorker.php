<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:21
 */

namespace Evolution\WheelTimer;


class WheelWorker extends \Thread {
    public function __construct($wheel)
    {
        $this->wheel = $wheel;
    }

    public function show($id)
    {
        echo "\n 消费者　threadid is {$id}\n　tick=".$this->wheel->current_tick."\r\n";
//        print_r($this->wheel->slots);
    }

    function run(){
        try{
            while (1){
//                $this->wheel->p('wplock');
//                $this->wheel->del();
                $this->wheel->current_tick++;
                $this->show(\Thread::getCurrentThreadId());
                sleep($this->wheel->tic_interval);
//                $this->wheel->v('wplock');
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }finally{
            echo "end\n";
        }
    }
}
