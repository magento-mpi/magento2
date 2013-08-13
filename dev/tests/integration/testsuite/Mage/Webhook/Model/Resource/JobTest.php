<?php
/**
 * Mage_Webhook_Model_Resource_Job
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_JobTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var Mage_Webhook_Model_Resource_Job $jobResource */
        $jobResource = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Webhook_Model_Resource_Job');
        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}