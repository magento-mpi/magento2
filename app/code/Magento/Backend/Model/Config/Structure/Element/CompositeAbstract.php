<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Backend_Model_Config_Structure_Element_CompositeAbstract
    extends Magento_Backend_Model_Config_Structure_ElementAbstract
{
    /**
     * Child elements iterator
     *
     * @var Magento_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_childrenIterator;

    /**
     * @param Magento_Core_Model_App $application
     * @param Magento_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
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
     * @return Magento_Backend_Model_Config_Structure_Element_Iterator
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

