<?php
/**
 * Base config model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_BaseFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create config model
     *
     * @param string|\Magento\Simplexml\Element $sourceData
     * @return Magento_Core_Model_Config_Base
     */
    public function create($sourceData = null)
    {
        return $this->_objectManager->create('Magento_Core_Model_Config_Base', array('sourceData' => $sourceData));
    }
}
