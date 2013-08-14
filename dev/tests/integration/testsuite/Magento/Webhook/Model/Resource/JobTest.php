<?php
/**
 * Magento_Webhook_Model_Resource_Job
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var Magento_Webhook_Model_Resource_Job $jobResource */
        $jobResource = Mage::getObjectManager()->create('Magento_Webhook_Model_Resource_Job');
        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}
