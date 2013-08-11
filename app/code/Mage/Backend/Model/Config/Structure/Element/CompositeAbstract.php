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
     * @param Mage_Core_Model_App $application
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
    ) {
        parent::__construct($application);
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
        $children = array_key_exists('children', $this->_data) && is_array($this->_data['children']) ?
            $this->_data['children'] :
            array();
        $this->_childrenIterator->setElements($children, $scope);
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

    /**
     * Check whether element is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        if (parent::isVisible()) {
            return $this->hasChildren() || $this->getFrontendModel();
        }
        return false;
    }
}

