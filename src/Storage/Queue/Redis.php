<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-4-7
 * Time: 上午10:03
 */

namespace Evolution\WheelTimer\Storage\Queue;


use Evolution\WheelTimer\Storage\Log\LogTrait;

class Redis extends IQueue
{
    use LogTrait;

    public $redis = null;
    const PREFIX = 'djobs:';

    public function __construct(array $config)
    {
        $this->redis = new \Redis();
        try{
            if(isset($config['unixsock'])){
                $this->redis->connect($config['unixsock']);
            } else {
                $this->redis->connect($config['host'], $config['port']);
            }
        }catch (\Exception $e){
            self::error('Redis连接失败' . $e->getMessage().'--'.$e->getTraceAsString());
        }
    }

    public function push($key, $value)
    {
        return $this->redis->rPush(self::PREFIX.$key, $value);
    }

    public function pop($key)
    {
        return $this->redis->lPop(self::PREFIX.$key);
    }

    public function getAll($key)
    {
        $json = $this->redis->lRange(self::PREFIX.$key,0,-1);
        if(!$this->redis->del(self::PREFIX.$key)){
            \SeasLog::error('删除键'.self::PREFIX.$key.'失败'.'----数据：'.$json);
        }
        return $json;
    }

    public function __call($name, $arguments)
    {
        $arguments[0] = self::PREFIX.$arguments[0];
        return call_user_func_array([$this->redis, $name], $arguments);
    }
}