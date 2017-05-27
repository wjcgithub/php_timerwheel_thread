<?php
namespace Evolution\WheelTimer\Storage\Log;

/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-27
 * Time: 下午2:31
 */
trait LogTrait
{
    static public function __callStatic($name, $arguments)
    {
        \SeasLog::setLogger('task_worker');
        call_user_func_array(['\SeasLog',$name], $arguments);
    }
}