<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Model_Report_Item extends \Magento\Object
{
    protected $_isEmpty  = false;
    protected $_children = array();

    public function setIsEmpty($flag = true)
    {
        $this->_isEmpty = $flag;
        return $this;
    }

    public function getIsEmpty()
    {
        return $this->_isEmpty;
    }

    public function hasIsEmpty()
    {}

    public function getChildren()
    {
        return $this->_children;
    }

    public function setChildren($children)
    {
        $this->_children = $children;
        return $this;
    }

    public function hasChildren()
    {
        return (count($this->_children) > 0) ? true : false;
    }

    public function addChild($child)
    {
        $this->_children[] = $child;
        return $this;
    }
}
