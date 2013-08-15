<?php
/**
 * Magento_Webhook_Model_Resource_Event_Collection
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals('Magento_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Magento_Webhook_Model_Event', $collection->getModelName());
    }
}
