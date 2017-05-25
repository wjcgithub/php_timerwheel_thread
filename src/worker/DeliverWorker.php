<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 上午10:16
 */

namespace Evolution\WheelTimer\worker;


class DeliverWorker extends \Thread
{
    use Worker;

    public $params = [];
    public $name = '';
    public $res = '';
    public $lastRunTime;
    //记录执行状态0 等待执行，　１运行中　　2执行成功，　-1执行失败
    public $status=0;
    public $shareData;

    public function __construct($name, $shareData)
    {
        $this->name = $name;
        $this->lastRunTime = time();
        $this->start();
        $this->shareData = $shareData;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 发送消息
     * @param $params array
     */
    public function send()
    {
//        print_r($this->shareData->shift()); die;
//        $this->$params = $params;

        $this->notify();
    }

    public function run()
    {
        while (1){
            try {
                 $this->synchronized(function($thread){
                    $thread->wait();
                 }, $this);
                $this->setStatus(1);
                if(!empty($this->params)){
                    $timeout = rand(1,5);
                    $str = json_encode($this->params);
                    echo "线程[{$this->name}]收到任务数据：{$str}, 执行时间为{$timeout}.\n";
                    self::info("线程[{$this->name}]收到任务数据：{$str}, 执行时间为{$timeout}");
                    sleep($timeout);
                }
                $this->setStatus(0);
            } catch (\Exception $e) {
                self::error('任务：'.json_encode($this->params).'执行失败, 失败信息为：'.$e->getMessage().'file:'.$e->getFile().'line:'.$e->getLine()."\r\n");
            }
        }
    }


}