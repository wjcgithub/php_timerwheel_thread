<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-18
 * Time: 下午2:45
 */

class GlobalWheel extends Stackable
{
    public $shareObj;
    static public $instance=null;

    private function __construct($obj)
    {
        $this->shareObj=$obj;
        $this->shareObj->queue=[];
        $this->shareObj->tick=0;

        print_r($this->shareObj);
    }

    static public function getInstance($obj)
    {
        if(empty(self::$instance)){
            self::$instance = new self($obj);
        }

        return self::$instance;
    }

    public function del()
    {
//        $this->shareObj->queue[]=time();
    }

    public function add()
    {
        $this->shareObj->tick++;
        $this->shareObj->queue[$this->shareObj->tick] = $this->shareObj->tick;
    }

    public function show($id)
    {
        echo "\n threadid is {$id}\n　print queue"."\r\n";
        print_r($this->shareObj->queue);
        echo "\n threadid is {$id}\n　tick=".$this->shareObj->tick."\r\n";

    }
}


//通过继承Thread类来实现自己的线程类MyThread
class MyThread extends Thread{
    private $glo;

    //重写构造函数
    function __construct(&$glo){
        $this->glo = $glo;
    }
    //重写run方法（运行的是子线程需要执行的任务）
    function run(){
        try{
            while (1){
                $this->glo->add();
                $this->glo->show(Thread::getCurrentThreadId());
                sleep(1);
                Threaded::unlock();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }finally{
            echo "end\n";
        }
    }
}

class MyThread2 extends Thread{
    private $w;
    public $glo;

    //重写构造函数
    function __construct(&$glo){
        $this->glo = $glo;
    }
    //重写run方法（运行的是子线程需要执行的任务）
    function run(){
        try{
            while (1){
                $this->glo->add();
                $this->glo->show(Thread::getCurrentThreadId());
                sleep(1);
                Threaded::unlock();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }finally{
            echo "end\n";
        }
    }
}

$stackObj = new Stackable();
$wheelObj = GlobalWheel::getInstance($stackObj);
$t1 = new MyThread($wheelObj);
$t2 = new MyThread2($wheelObj);
$t1->start();
$t2->start();

sleep(5);
$wheelObj->shareObj->tick=200;