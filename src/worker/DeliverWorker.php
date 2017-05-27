<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 上午10:16
 */

namespace Evolution\WheelTimer\worker;


use Evolution\WheelTimer\Storage\Log\LogTrait;

class DeliverWorker extends \Thread
{
    use LogTrait;

    public $params;
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

    public function run()
    {
        while (1){
            try {
                 $this->synchronized(function($thread){
                    $thread->wait();
                 }, $this);

                $this->setStatus(1);
                if(count($this->shareData)>0){
                    $data = $this->shareData->shift();
                    self::info("线程[{$this->name}]收到任务数据：{$data}, 开始执行！");
                }
                $this->setStatus(0);
            } catch (\Exception $e) {
                self::error('任务：'.json_encode($this->params).'执行失败, 失败信息为：'.$e->getMessage().'file:'.$e->getFile().'line:'.$e->getLine()."\r\n");
            }
        }
    }


}