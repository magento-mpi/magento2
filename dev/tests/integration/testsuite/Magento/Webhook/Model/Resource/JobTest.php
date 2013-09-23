<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

/**
 * \Magento\Webhook\Model\Resource\Job
 */
class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        $this->markTestSkipped("MAGETWO-11929 uncaught exception");
        /** @var \Magento\Webhook\Model\Resource\Job $jobResource */
        $jobResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Job');
        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}
