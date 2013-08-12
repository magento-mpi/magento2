<?php
/**
 * Mage_Webhook_Model_Resource_Job_Collection
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
class Mage_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals('Mage_Webhook_Model_Resource_Job', $collection->getResourceModelName());
        $this->assertEquals('Mage_Webhook_Model_Job', $collection->getModelName());
    }

    public function testSetPageLimit()
    {
        /** @var Mage_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Mage_Webhook_Model_Resource_Job_Collection');
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Job_Collection', $collection->setPageLimit());
    }
}