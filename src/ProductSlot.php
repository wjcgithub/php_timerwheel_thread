<?php
namespace Evolution\WheelTimer;
use Evolution\WheelTimer\Storage\Queue\Redis;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:13
 */

class ProductSlot extends \Thread {
    public $wheel;

    public function __construct($wheel)
    {
        $this->wheel = $wheel;
    }

    public function show($id)
    {
        echo "\n 生产者　threadid is {$id}\n　tick=".$this->wheel->current_tick."\r\n";
//        print_r($this->wheel->slots);
    }

    public function add($info)
    {
        $ret = $this->calcSlot($info);
//        $this->p($this->current_tick.LOCK_SUFFIX);
        $this->wheel->slots[$ret['slot']]->headInsert($ret['cycle'], $info);
        print_r($this->wheel->slots[$ret['slot']]);
        echo "\r\n insert to slot {$ret['slot']} success \r\n";
//        $this->v($this->current_tick.LOCK_SUFFIX);
    }

    private function calcSlot($data)
    {
        $info = json_decode($data,true);
        if($info['type']=='delay'){
            $delayTime = $info['exectime'];
            //计算周期
            $ptr = $this->wheel->current_tick;
            $offsetDelayTime = $delayTime+$ptr;
            $cycle = floor($delayTime / $this->wheel->wheel_size);
            $solt = $offsetDelayTime % $this->wheel->wheel_size;

            return ['cycle'=>$cycle, 'slot'=>$solt];
        }
    }

    function run(){
        try{
            $queue = new Redis(['host'=>'127.0.0.1', 'port'=>6379, '_unixsock' => '/tmp/redis.sock']);
            while (1){
//                $this->wheel->p('wplock');
                $data = $queue->rpop('delayqueue');
                $this->wheel->add($data);
                $this->show(\Thread::getCurrentThreadId());
//                $this->wheel->v('wplock');
                sleep($this->wheel->product_tick);
            }
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString();
        }finally{
            echo "end\n";
        }
    }
}