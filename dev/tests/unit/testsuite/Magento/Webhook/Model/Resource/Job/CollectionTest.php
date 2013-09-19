<?php
/**
 * \Magento\Webhook\Model\Resource\Job\Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Job_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $mockFetchStrategy = $this->getMockBuilder('Magento\Data\Collection\Db\FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $mockDBAdapter = $this->getMockBuilder('Magento\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento\Webhook\Model\Resource\Job')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $collection = new \Magento\Webhook\Model\Resource\Job\Collection(
            $eventManager, $mockFetchStrategy, $mockResourceEvent
        );
        $this->assertInstanceOf('Magento\Webhook\Model\Resource\Job\Collection', $collection);
        $this->assertEquals('Magento\Webhook\Model\Resource\Job', $collection->getResourceModelName());
    }
}
