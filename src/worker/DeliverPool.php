<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 上午11:54
 */

namespace Evolution\WheelTimer\worker;


class DeliverPool extends \Thread
{
    use Worker;

    public $size;
    public $pool=[];
    public $shareToWorker;

    public function __construct($size, $shareData)
    {
        $this->size = $size;
        $this->shareToWorker = $shareData;
//        $this->shareToWorker->a[]=2;
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
    public function dispatch($params)
    {
        $this->checkPool();
        $this->shareToWorker->a[] = $params;

        foreach ($this->pool as $worker) {
            echo __CLASS__." worker status : ".$worker->getStatus()."\r\n";
            print_r($this->shareToWorker);
            if($worker->getStatus()==0){
                $this->shareToWorker->a[] = $params;
                $worker->send();
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
                echo '分发工作线程任务：'.$name.'--创建成功'."\r\n";
                self::info('分发工作线程任务：'.$name.'--创建成功');
                $this->pool[] = new DeliverWorker($name,$this->shareToWorker);
            }
        }
    }

    public function getPoolLen()
    {
        return $this->size - count($this->pool);
    }

    public function run(){
        $this->shareToWorker->a[]=1;
    }
}