<?php
/**
 * Configuration model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Base_Factory
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
     * Create config model
     *
     * @param string|Varien_Simplexml_Element $sourceData
     * @return mixed
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Mage_Core_Model_Config_Base', array($sourceData));
    }
}
