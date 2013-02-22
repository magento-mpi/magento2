<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
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
