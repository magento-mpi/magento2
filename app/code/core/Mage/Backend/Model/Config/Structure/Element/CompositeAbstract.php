<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
    extends Mage_Backend_Model_Config_Structure_ElementAbstract
    implements IteratorAggregate, RecursiveIterator
{
    /**
     * The name of children array identifier in data array
     *
     * @var string
     */
    protected $_childrenKey;

    /**
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_childrenIterator;

    /**
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getIterator()
    {
        $this->_childrenIterator->setElements($this->_data[$this->_childrenKey]);
        return $this->_childrenIterator;
    }
}
