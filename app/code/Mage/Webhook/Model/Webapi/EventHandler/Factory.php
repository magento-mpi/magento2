<?php
/**
 * Factory for Mage_Webhook_Model_Webapi_EventHandler objects
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Webapi_EventHandler_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Mage_Webhook_Model_Webapi_EventHandler
     *
     * @param array $arguments Fed into constructor
     * @return Mage_Webhook_Model_Webapi_EventHandler
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Webhook_Model_Webapi_EventHandler', $arguments);
    }
}