<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:20
 */

namespace Evolution\WheelTimer;


use Evolution\WheelTimer\Storage\Queue\Redis;

class WheelTimer extends \Threaded {
    public $queue;
    public $wheel_size;
    public $slots;
    public $product_tick;
    public $current_tick=1;
    public $tic_interval=1;
    public $lockfile=0;
    public $lastPid=0;
    public $lockArr=[];

    const LOCK_SUFFIX='_slot';

    public function __construct($wheel_size, $tic_interval, $product_tick)
    {
        $this->lockfile=0;
        $this->tic_interval=$tic_interval;
        $this->wheel_size=$wheel_size;
        $this->product_tick=$product_tick;
        $this->slots = [];
        $this->init();
    }

    public function run(){}

    /**
     * 初始化空的时间槽
     */
    public function init()
    {
	    for($i=1; $i <= $this->wheel_size; $i++){
		    $this->slots[$i] = NULL;
        }

        new SignalList();
        new Redis(['host'=>'127.0.0.1', 'port'=>6379, '_unixsock' => '/tmp/redis.sock']);
        new LNode();
    }

    public function get()
    {
        return $this->getAt($this->current_tick);
    }

    public function getAt($index)
    {
        $result = [];
        $this->checkIndex($index);
        $result = $this->synchronized(function($thread) use ($index) {
            $result=[];
            if(!empty($thread->slots[$index])){
                $tmp = $thread->slots[$index];
                $result = $tmp->getLinkContent();
                $thread->slots[$index]=$tmp;
            }
            return $result;
        },$this);

        return $result;
    }

    private function checkIndex($index)
    {
        if(!is_numeric($index) || $index > $this->wheel_size){
            throw new IllegalArgumentException('索引错误 value:'.$index);
        }

        return true;
    }

    /**
     * 时间指针++
     */
    public function tickIncr()
    {
        $this->current_tick++;
        $this->current_tick = ($this->current_tick % $this->wheel_size);
        if ($this->current_tick == 0)
            $this->current_tick++;
    }

    public function add($info)
    {
        $this->synchronized(function($thread) use ($info) {
            $ret = $thread->calcSlot($info);
            if(empty($thread->slots[$ret['slot']])){
                $signalListObj = new SignalList();
            } else {
                $signalListObj = $thread->slots[$ret['slot']];
            }
            $signalListObj->headInsert($ret['cycle'], $info);
            $thread->slots[$ret['slot']] = $signalListObj;
        }, $this);
    }

    /**
     * caculation slot's position and task's cycle
     * @param $data
     * @return array
     */
    private function calcSlot($data)
    {
        $info = json_decode($data,true);
        if($info['type']=='delay'){
            $delayTime = $info['exectime'];
            //计算周期
            $ptr = $this->current_tick;
            $offsetDelayTime = $delayTime+$ptr;
            $cycle = floor($delayTime / $this->wheel_size);
            $slot = $offsetDelayTime % $this->wheel_size;
            $slot = $slot ? $slot : 1;

            return ['cycle'=>$cycle, 'slot'=>$slot];
        }
    }

    public function del()
    {
        $this->current_tick--;
        unset($this->slots[$this->current_tick]);
    }

    public function pop()
    {
        $data = isset($this->slots[$this->current_tick]) ? $this->slots[$this->current_tick] : [];
        unset($this->slots[$this->current_tick]);
        return $data;
    }

    public function p($lockName)
    {
        while(true) {
            if( in_array($lockName, (array)$this->lockArr) ) {
                usleep(100);
            } else {
                $this->lockArr[$lockName]=$lockName;
            }
        }

        return True;
    }

    public function v($lockName)
    {
        $this->lockArr[$lockName]=NULL;
        unset($this->lockArr[$lockName]);
        return True;
    }

}