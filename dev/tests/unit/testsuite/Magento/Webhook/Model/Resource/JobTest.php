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
    public function testConstruct()
    {
        $tableName = 'webhook_dispatch_job_table';
        $idFieldName = 'dispatch_job_id';
        $resourceMock = $this->getMockBuilder('Magento_Core_Model_Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('webhook_dispatch_job')
            ->will($this->returnValue($tableName));

        $job = new Magento_Webhook_Model_Resource_Job ($resourceMock);
        $this->assertEquals($tableName, $job->getMainTable() );
        $this->assertEquals($idFieldName, $job->getIdFieldName());
    }
}
