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
    public function testConstruct()
    {
        $tableName = 'webhook_dispatch_job_table';
        $idFieldName = 'dispatch_job_id';
        $resourceMock = $this->getMockBuilder('Magento\Core\Model\Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('webhook_dispatch_job')
            ->will($this->returnValue($tableName));

        $job = new \Magento\Webhook\Model\Resource\Job ($resourceMock);
        $this->assertEquals($tableName, $job->getMainTable() );
        $this->assertEquals($idFieldName, $job->getIdFieldName());
    }
}
