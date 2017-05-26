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
    public $slots;

    public function __construct(&$wheel)
    {
        $this->wheel = $wheel;
    }

    public function show($id)
    {
        echo "\n 生产者　threadid is {$id}\n　tick=".$this->wheel->current_tick."\r\n";
//        print_r($this->wheel->slots);
    }

    function run(){
        try{
            $queue = new Redis(['host'=>'127.0.0.1', 'port'=>6379, '_unixsock' => '/tmp/redis.sock']);
            while (1){
                $data = $queue->rpop('delayqueue');
                if(!empty($data)){
                    $this->wheel->add($data);
                }
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