<?php
namespace Evolution\WheelTimer\worker;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-25
 * Time: 下午4:23
 */
trait Worker
{
    static public function __callStatic($name, $arguments)
    {
        \SeasLog::setLogger('task_worker');
        call_user_func_array(['\SeasLog',$name], $arguments);
    }
}