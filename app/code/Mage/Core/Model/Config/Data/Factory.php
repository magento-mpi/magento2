<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Data_Factory
{
    /**
     * Object manager
     *
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
     * Create config data object
     *
     * @param array $arguments
     * @return Mage_Core_Model_Config_Data
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Config_Data', $arguments);
    }
}
