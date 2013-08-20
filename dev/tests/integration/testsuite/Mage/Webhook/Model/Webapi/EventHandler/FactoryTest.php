<?php
/**
 * Mage_Webhook_Model_Webapi_EventHandler_Factory
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Webapi_EventHandler_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = Mage::getObjectManager()->create('Mage_Webhook_Model_Webapi_EventHandler_Factory');
        $eventHandler = $factory->create();
        $this->assertInstanceOf('Mage_Webhook_Model_Webapi_EventHandler', $eventHandler);
    }
}