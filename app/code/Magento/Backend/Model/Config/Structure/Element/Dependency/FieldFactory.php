<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Dependency_FieldFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create dependency field model instance.
     *
     * @param array $arguments
     * @return Magento_Backend_Model_Config_Structure_Element_Dependency_Field
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager
            ->create('Magento_Backend_Model_Config_Structure_Element_Dependency_Field', $arguments);
    }
}
