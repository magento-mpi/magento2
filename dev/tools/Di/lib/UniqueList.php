<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class UniqueList
{
    /**
     * List of stored items
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Add item to list and retrieve auto-incremented item position
     *
     * @param mixed $item
     * @return int|bool
     */
    public function getNumber($item)
    {
        if (in_array($item, $this->_items)) {
            return array_search($item, $this->_items);
        } else {
            $this->_items[] = $item;
            return count($this->_items)-1;
        }
    }

    /**
     * Represent list as array
     *
     * @return array
     */
    public function asArray($serializer)
    {
        foreach ($this->_items as &$item) {
            $item = $serializer($item);
        }
        return $this->_items;
    }
}
