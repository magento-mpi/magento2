<?php
/**
 * Magento_Webhook_Model_Job_QueueReader
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_QueueReaderTest extends PHPUnit_Framework_TestCase
{

    /** @var Magento_Webhook_Model_Job_QueueReader */
    private $_jobQueue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockCollection;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $_mockIterator;

    public function setUp()
    {
        $this->_mockCollection = $this->getMockBuilder('Magento_Webhook_Model_Resource_Job_Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockIterator = $this->getMockBuilder('ArrayIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_mockCollection->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($this->_mockIterator));
        $this->_jobQueue = new Magento_Webhook_Model_Job_QueueReader($this->_mockCollection);
    }

    public function testPollNothing()
    {
        $this->_mockIterator->expects($this->once())
            ->method('valid')
            ->will($this->returnValue(false));
        $this->assertNull($this->_jobQueue->poll());
    }

    public function testPollIteratorJob()
    {
        $this->_mockIterator->expects($this->once())
            ->method('valid')
            ->will($this->returnValue(true));

        $job = 'TEST_JOB';
        $this->_mockIterator->expects($this->once())
            ->method('current')
            ->will($this->returnValue($job));

        $this->_mockIterator->expects($this->once())
            ->method('next');

        $this->assertSame($job, $this->_jobQueue->poll());
    }
}
