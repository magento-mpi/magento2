<?php
/**
 * Magento_Webhook_Model_Resource_Job_Collection
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $this->assertEquals('Magento_Webhook_Model_Resource_Job', $collection->getResourceModelName());
        $this->assertEquals('Magento_Webhook_Model_Job', $collection->getModelName());
    }

    public function testSetPageLimit()
    {
        /** @var Magento_Webhook_Model_Resource_Job_Collection $collection */
        $collection = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job_Collection');
        $this->assertInstanceOf('Magento_Webhook_Model_Resource_Job_Collection', $collection->setPageLimit());
    }
}