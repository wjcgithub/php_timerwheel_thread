<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-18
 * Time: 下午2:45
 */

$startMpu = memory_get_peak_usage();
$startMu = memory_get_usage();
date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/vendor/autoload.php';
$config = parse_ini_file('./config.ini', true);

$shareData = new \Evolution\WheelTimer\worker\ShareData();
$workerpool = new \Evolution\WheelTimer\worker\DeliverPool($config['worker_pool']['num'],$shareData);
$wheel = new \Evolution\WheelTimer\WheelTimer($config);
$wheelWorker = new \Evolution\WheelTimer\WheelWorker($wheel,$workerpool,$shareData);
$productSlot = new \Evolution\WheelTimer\ProductSlot($wheel,$config);
$productSlot->start();
$wheelWorker->start();

function ex($v){
    return ($v/1000/1000) .'M';
}

while (1) {
    $endMpu = memory_get_peak_usage();
    $endMu = memory_get_usage();
//    echo "==========start=========\n";
//    print_r($wheel->slots);
//    echo "==========end=========\n";
    echo '当前使用最大内存：'.ex($endMu).'当前使用最大内存差值：'.ex($endMu-$startMu)."\r\n";
    echo '当前使用峰值内存：'.ex($endMpu).'当前使用峰值内存差值：'.ex($endMpu-$startMpu)."\r\n";
    sleep(3);
}




