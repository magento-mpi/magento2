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
     * @covers Magento_Webhook_Model_Resource_Job::init
     */
    public function testInit()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $resource = $objectManager->create('Magento_Core_Model_Resource', array('tablePrefix' => 'prefix_'));

        /** @var Magento_Webhook_Model_Resource_Job $jobResource */
        $jobResource = $objectManager->create('Magento_Webhook_Model_Resource_Job', array('resource' => $resource));

        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}
