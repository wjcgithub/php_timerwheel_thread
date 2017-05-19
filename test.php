<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-19
 * Time: 下午2:32
 */
class workerThread extends Thread {
    public function __construct($i){
        $this->i=$i;
    }

    public function run(){
        while(true){
            echo $this->i;
            sleep(1);
        }
    }
}

for($i=0;$i<50;$i++){
    $workers[$i]=new workerThread($i);
    $workers[$i]->start();
}