<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午7:00
 */

namespace Evolution\WheelTimer;


class StackableArray extends \Threaded {

    /*
    * Always think about caching these types of objects, don't waste the run method or your workers
    */
    public function run() {}
}