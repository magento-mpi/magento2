<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Dependency_FieldFactory
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
     * @return Mage_Backend_Model_Config_Structure_Element_Dependency_Field
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager
            ->create('Mage_Backend_Model_Config_Structure_Element_Dependency_Field', $arguments);
    }
}
