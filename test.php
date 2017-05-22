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






class cacheRecord {
    public $currentPos;
    public $currentRoom;
    public $someOtherData;
}

class cache extends Threaded {
    public function run() {}
}

class socketThread extends Thread {

    public function __construct($myCache) {
        $this->cacheData = $myCache;
    }

    public function run() {

        // This will be in a loop, waiting for sockets, and then responding to them, indefinitely.

        // At some point, add a record to the cache
        $c = new cacheRecord;
        $c->currentPos = '1,2,4';
        $c->currentRoom = '2';
        $this->cacheData['record1'] = $c;

        var_dump($this);

        // Later on, update the cache record, but this doesnt work
        $this->cacheData['record1']->currentRoom = '3';

        var_dump($this);

        // However this does work, but is this the correct way? Seems like more code to execute, than a simple assign, and obviously, I would need to use synchronized to keep integrity, which would further slow it down.

        $tmp = $this->cacheData['record1'];
        $tmp->currentRoom = '3';
        $this->cacheData['record1'] = $tmp;

        var_dump($this);

        // Later on some more, remove the record
        unset($this->cacheData['record1']);

        var_dump($this);

        // Also will be using ->synchronized to enforce integrity of certain other operations
        // Just an example of how I might use it

        /*
                $this->cacheData->synchronized(function() {
                    if ($this->cacheData['record1']->currentRoom == '3') {
                        $this->cacheData['record1']->Pos = '0,0,0'; // Obviously this wont work as above.
                        $this->cacheData['record1']->currentRoom = '4';
                    }
                });
        */
    }
}

// Main

$myCache = new cache;

for ($th=0;$th<1;$th++) { // Just 1 thread for testing
    $socketThreads[$th] = new socketThread($myCache);
    $socketThreads[$th]->start();
}