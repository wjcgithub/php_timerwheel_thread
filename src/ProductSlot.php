<?php
namespace Evolution\WheelTimer;
use Evolution\WheelTimer\Storage\Log\LogTrait;
use Evolution\WheelTimer\Storage\Queue\Redis;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:13
 */

class ProductSlot extends \Thread {

    use LogTrait;

    public $wheel;
    public $config;

    public function __construct(&$wheel, $config)
    {
        $this->wheel = $wheel;
        $this->config = $config;
    }

    function run(){
        try{
            $queue = new Redis(
                [
                    'host'=>$this->config['queue']['redis']['host'],
                    'port'=>$this->config['queue']['redis']['port'],
                    '_unixsock' => $this->config['queue']['redis']['unixsock']
                ]
            );
            while (1){
//                echo "product one\r\n";
//                $this->synchronized(function($thread){
//                    $thread->wait();
//                }, $this);

                $data = $queue->rpop('delayqueue');
                if(!empty($data)){
                    $this->wheel->add($data);
                }

                $this->synchronized(function($thread){
                    $thread->wait($this->wheel->product_tick);
                }, $this);
            }
        } catch (\Exception $e) {
            self::error('追加任务失败, 失败信息为：'.$e->getMessage().'file:'.$e->getFile().'line:'.$e->getLine()."\r\n");
        }
    }
}