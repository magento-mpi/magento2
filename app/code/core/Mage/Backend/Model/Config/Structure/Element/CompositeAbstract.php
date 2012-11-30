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
     * @param Mage_Core_Model_App $application
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_App $application,
        Mage_Core_Model_Authorization $authorization,
        Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
    ) {
        parent::__construct($helperFactory, $application, $authorization);
        $this->_childrenIterator = $childrenIterator;
    }

    /**
     * Set flyweight data
     *
     * @param array $data
     * @param string $scope
     */
    public function setData(array $data, $scope)
    {
        parent::setData($data, $scope);
        if (isset($this->_data['children'])) {
            $this->_childrenIterator->setElements($this->_data['children'], $scope);
        }
    }

    /**
     * Check whether element has visible child elements
     *
     * @return bool
     */
    public function hasChildren()
    {
        foreach ($this->getChildren() as $child) {
            return (bool)$child;
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
        return $this->_childrenIterator;
    }
}

