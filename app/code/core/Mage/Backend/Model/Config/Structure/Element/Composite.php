<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Backend_Model_Config_Structure_Element_Composite
    extends Mage_Backend_Model_Config_Structure_ElementAbstract
{
    /**
     * The name of children array identifier in data array
     *
     * @var string
     */
    protected $_childrenKey;

    /**
     * @var M
     */
    protected $_childrenIterator;

    /**
     * Check whether element has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return array_key_exists($this->_childrenKey, $this->_data) && count($this->_data[$this->_childrenKey]);
    }

    /**
     * Retrieve children iterator
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->hasChildren() ? $this->_data[$this->_childrenKey] : new ArrayIterator(array());
    }
}
