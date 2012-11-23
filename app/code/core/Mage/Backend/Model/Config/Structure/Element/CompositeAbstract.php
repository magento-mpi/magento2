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
{
    /**
     * Child elements iterator
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_childrenIterator;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     */
    function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Authorization $authorization,
        Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
    ) {
        parent::__construct($helperFactory, $authorization);
        $this->_childrenIterator = $childrenIterator;
    }

    /**
     * Check whether element has visible child elements
     *
     * @return bool
     */
    public function hasChildren()
    {
        foreach ($this->getChildren() as $child) {
            return (bool) $child;
        };
        return false;
    }

    /**
     * Retrieve children iterator
     *
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getChildren()
    {
        $this->_childrenIterator->setElements($this->_data['children']);
        return $this->_childrenIterator;
    }
}

