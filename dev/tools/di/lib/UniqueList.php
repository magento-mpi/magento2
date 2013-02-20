<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 2/18/13
 * Time: 11:43 PM
 * To change this template use File | Settings | File Templates.
 */

class UniqueList
{
    protected $_itemsPerNumber = array();

    public function getNumber($item)
    {
        if (in_array($item, $this->_itemsPerNumber)) {
            return array_search($item, $this->_itemsPerNumber);
        } else {
            $this->_itemsPerNumber[] = $item;
            return count($this->_itemsPerNumber)-1;
        }
    }

    public function asArray()
    {
        return $this->_itemsPerNumber;
    }
}
