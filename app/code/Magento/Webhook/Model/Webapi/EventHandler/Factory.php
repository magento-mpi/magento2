<?php
/**
 * Factory for Magento_Webhook_Model_Webapi_EventHandler objects
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_EventHandler_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Magento_Webhook_Model_Webapi_EventHandler
     *
     * @param array $arguments Fed into constructor
     * @return Magento_Webhook_Model_Webapi_EventHandler
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Webhook_Model_Webapi_EventHandler', $arguments);
    }
}