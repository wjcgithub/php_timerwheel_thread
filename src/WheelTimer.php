<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:20
 */

namespace Evolution\WheelTimer;


use Evolution\WheelTimer\Storage\Ll\LNode;
use Evolution\WheelTimer\Storage\Ll\SignalList;
use Evolution\WheelTimer\Storage\Queue\Redis;

class WheelTimer extends \Threaded {
    const LOCK_SUFFIX='_slot';

    public $wheel_size;
    public $slots;
    public $product_tick;
    public $current_tick=1;
    public $tic_interval=1;
    public $lastPid=0;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->tic_interval=$config['time_wheel']['tick_interval'];
        $this->wheel_size=$config['time_wheel']['wheel_size'];
        $this->product_tick=$config['time_wheel']['product_tick'];
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
        new Redis(
            [
                'host'=>$this->config['queue']['redis']['host'],
                'port'=>$this->config['queue']['redis']['6379'],
                '_unixsock' => $this->config['queue']['redis']['unixsock']
            ]
        );
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
                unset($tmp);
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
}