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
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testInit()
    {
        /** @var \Magento\Webhook\Model\Resource\Job $jobResource */
        $jobResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\Resource\Job');
        $this->assertEquals('prefix_webhook_dispatch_job', $jobResource->getMainTable());
        $this->assertEquals('dispatch_job_id', $jobResource->getIdFieldName());
    }
}
