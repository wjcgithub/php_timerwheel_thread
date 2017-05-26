<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-18
 * Time: 下午2:45
 */

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/vendor/autoload.php';

$shareData = new \Evolution\WheelTimer\worker\ShareData();
$workerpool = new \Evolution\WheelTimer\worker\DeliverPool(3,$shareData);
$wheel = new \Evolution\WheelTimer\WheelTimer(5,1,1);
$wheelWorker = new \Evolution\WheelTimer\WheelWorker($wheel,$workerpool,$shareData);
$productSlot = new \Evolution\WheelTimer\ProductSlot($wheel);
$productSlot->start();
$wheelWorker->start();

while (1) {
    echo "==========start=========\n";
    print_r($wheel);
    echo "==========end=========\n";
    sleep(1);
}




