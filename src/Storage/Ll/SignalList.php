<?php
/**
 * Created by PhpStorm.
 * User: evolution
 * Date: 17-5-23
 * Time: 下午2:17
 */

namespace Evolution\WheelTimer\Storage\Ll;


class SignalList
{
    public $head;
    public $linkLen = 0;
    public function __construct()
    {
        $this->head = NULL;
    }

    /**
     * 链表长度加１
     */
    private function increateLinkLen()
    {
        $this->linkLen++;
    }
    /**
     * 链表长减1
     */
    private function decreateLinkLen()
    {
        $this->linkLen--;
    }
    /**
     * 获取链表长度
     *
     * @return int
     */
    public function getLinkLen()
    {
        return $this->linkLen;
    }
    /**
     * 多数据头插法
     */
    public function multiHeadInsert(Array $varr = [])
    {
        if (empty($varr)) {
            return false;
        } else {
            foreach ($varr as $value) {
                $this->headInsert($value);
            }
        }
    }
    /**
     * 单数据头插法
     *
     * @param $value
     */
    public function headInsert($cycle, $value = '')
    {
        try{
            if (empty($value)) {
                return false;
            }
            $node = new LNode();
            $node->value = $value;
            $node->cycle = $cycle;
            $this->increateLinkLen();
            if (empty($this->head)) {
                $this->head = $node;
            } else {
                $node->next = $this->head;
                $this->head = $node;
            }
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            die;
        }
    }
    /**
     * 批量删除
     *
     * @param array $varr
     * @return bool
     */
    public function multiDelElem(Array $varr = [])
    {
        if (empty($varr)) {
            return false;
        } else {
            foreach ($varr as $value) {
                $this->delElem($value);
            }
        }
    }
    /**
     * 删除指定值的节点 (对象本身传递的就是引用)
     *
     * @param $value
     * @return bool
     */
    public function delElem($value)
    {
        //判断是否有头节点
        if ($this->linkLen < 1) {
            return false;
        }
        if ($this->head->value == $value) {
            $this->head = $this->head->next;
            $this->decreateLinkLen();
        } else {
            $thead = $this->head;
            while ($thead->next) {
                if ($thead->next->value == $value) {
                    //todo 常驻内存注意删除的元素如何回收内存
                    $tmpNext = $thead->next->next;
                    unset($thead->next);
                    $thead->next = $tmpNext;
                    unset($tmpNext);
                    $this->decreateLinkLen();
                    break;
                } else {
                    $thead = $thead->next;
                }
            }
        }
    }

    public function updateLinkElem($oldValue, $newValue)
    {
        if($this->linkLen < 1){
            return false;
        }
        if ($this->head->value == $oldValue) {
            $this->head->value = $newValue;
        } else {
            $thead = $this->head;
            while ($thead->next) {
                if ($thead->next->value == $oldValue) {
                    $thead->next->value = $newValue;
                    break;
                } else {
                    $thead = $thead->next;
                }
            }
        }
    }
    /**
     * 获取指定位置的
     * @param $pos
     * @return bool
     */
    public function getNodeForPos($pos)
    {
        $linkLen = $this->getLinkLen();
        if ($pos > $linkLen || $pos < 1) {
            return false;
        } else {
            $j = 1;
            $thead = $this->head;
            while ($j < $pos) {
                $thead = $thead->next;
                $j++;
            }
            return $thead->value;
        }
    }
    /**
     * 获取链表内容
     * @return string
     */
    public function getLinkAllContent()
    {
        $result = [];

        if ($this->linkLen > 0) {
            $result = [];
            $thead = $this->head;
            while (isset($thead->value)) {
                $result[] = $thead->value;
                $thead = $thead->next;
            }
        }

        return $result;
    }

    public function getLinkContent()
    {
        $result = [];
        if ($this->linkLen > 0) {
            $thead = $this->head;
            while ($thead){
                if($thead->cycle<=0){
                    $result[] = $thead->value;
                    $this->delElem($thead->value);
                } else {
                    $thead->cycle--;
                }
                $thead = $thead->next;
            }
        }
        return $result;
    }
}