<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-18
 * Time: 下午2:45
 */

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/vendor/autoload.php';

$wheel = new \Evolution\WheelTimer\WheelTimer(5,1,1);
$wheelWorker = new \Evolution\WheelTimer\WheelWorker($wheel);
$productSlot = new \Evolution\WheelTimer\ProductSlot($wheel);
//
$productSlot->start();
//$productSlot->join();
$wheelWorker->start();
//$wheelWorker->join();

//print_r('slot_'.$productSlot->join());
//print_r('worker_'.$wheelWorker->join());

//$queue = new \Evolution\WheelTimer\Storage\Queue\Redis(['host'=>'127.0.0.1', 'port'=>6379, '_unixsock' => '/tmp/redis.sock']);
//while (1){
////                $this->wheel->p('wplock');
//    $data = $queue->rpop('delayqueue');
//    $wheel->add($data);
//    echo '111\n';
////    $this->show(\Thread::getCurrentThreadId());
////                $this->wheel->v('wplock');
//    sleep(1);
//}

while (1) {
    echo "==========start=========\n";
    print_r($wheel);
    echo "==========end=========\n";
    sleep(1);
}




