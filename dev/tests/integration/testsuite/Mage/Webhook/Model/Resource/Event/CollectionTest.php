<?php
/**
 * Mage_Webhook_Model_Resource_Event_Collection
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        /** @var Mage_Webhook_Model_Resource_Event_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Event_Collection');
        $this->assertEquals('Mage_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Mage_Webhook_Model_Event', $collection->getModelName());
    }
}