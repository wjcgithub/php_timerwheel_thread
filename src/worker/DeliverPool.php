<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 上午11:54
 */

namespace Evolution\WheelTimer\worker;


use Evolution\WheelTimer\Storage\Log\LogTrait;

class DeliverPool
{
    use LogTrait;

    public $size;
    public $pool=[];
    public $shareData;

    public function __construct($size, $shareData)
    {
        $this->size = $size;
        $this->shareData = $shareData;
        $this->checkPool();
    }

    public function push($worker)
    {
        if($this->getPoolLen()){
            $this->pool[] = $worker;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 调度任务去执行
     * @param $params array
     */
    public function dispatch()
    {
        $this->checkPool();
        foreach ($this->pool as $worker) {
            if($worker->getStatus()==0){
                $worker->notify();
                return true;
            }
        }

        return false;
    }

    public function checkPool()
    {
        $curNum = $this->getPoolLen();
        if ($curNum) {
            for ($i=0; $i<$curNum; $i++) {
                $len = count($this->pool);
                $name = 'send task worker-'.($len+1);
                self::info('分发工作线程任务：'.$name.'--创建成功');
                $worker = new DeliverWorker($name,$this->shareData);
                $this->pool[$len+1] = $worker;
                $this->pool[$len+1]->start();
                unset($worker);
            }
        }
    }

    public function getPoolLen()
    {
        return $this->size - count($this->pool);
    }

    public function run(){}
}