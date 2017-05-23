<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:15
 */

namespace Evolution\WheelTimer;


class LNode
{
    public $value;
    public $next;
    public $cycle;

    public function __construct()
    {
        $this->value = null;
        $this->next = null;
        $this->cycle = 0;
    }
}