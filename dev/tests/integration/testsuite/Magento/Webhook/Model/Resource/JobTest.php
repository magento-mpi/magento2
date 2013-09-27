<?php
/**
 * \Magento\Webhook\Model\Resource\Job
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Webhook\Model\Resource\Job::init
     */
    public function testInit()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $resource = $objectManager->create('Magento\Core\Model\Resource', array('tablePrefix' => 'prefix_'));

        /** @var \Magento\Webhook\Model\Resource\Job $jobResource */
        $jobResource = $objectManager->create('Magento\Webhook\Model\Resource\Job', array('resource' => $resource));

        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}
