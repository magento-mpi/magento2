<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model;

class Item extends \Magento\Object
{
    /** @var bool */
    protected $_isEmpty  = false;

    /** @var array */
    protected $_children = array();

    /**
     * Set is empty indicator
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsEmpty($flag = true)
    {
        $this->_isEmpty = $flag;
        return $this;
    }

    /**
     * Get is empty indicator
     *
     * @return bool
     */
    public function getIsEmpty()
    {
        return $this->_isEmpty;
    }

    /**
     * @return void
     */
    public function hasIsEmpty()
    {}

    /**
     * Get children
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Set children
     *
     * @param $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->_children = $children;
        return $this;
    }

    /**
     * Indicator of whether or not children are present
     *
     * @return bool
     */
    public function hasChildren()
    {
        return (count($this->_children) > 0) ? true : false;
    }

    /**
     * Add child to array of items
     *
     * @param array $child
     * @return $this
     */
    public function addChild($child)
    {
        $this->_children[] = $child;
        return $this;
    }
}
