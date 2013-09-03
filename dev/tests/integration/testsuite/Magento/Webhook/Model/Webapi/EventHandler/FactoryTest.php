<?php
/**
 * Magento_Webhook_Model_Webapi_EventHandler_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_EventHandler_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webhook_Model_Webapi_EventHandler_Factory');
        $eventHandler = $factory->create();
        $this->assertInstanceOf('Magento_Webhook_Model_Webapi_EventHandler', $eventHandler);
    }
}
