<?php
/**
 * \Magento\Webhook\Model\Resource\Event\Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource\Event;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        /** @var \Magento\Core\Model\Event\Manager $eventManager */
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        /** @var \Magento\Data\Collection\Db\FetchStrategyInterface $mockFetchStrategy */
        $mockFetchStrategy = $this->getMockBuilder('Magento\Data\Collection\Db\FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Magento\Core\Model\EntityFactory $entityFactory */
        $entityFactory = $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false);

        $mockDBAdapter = $this->getMockBuilder('Magento\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento\Webhook\Model\Resource\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
            ->will($this->returnValue($mockResourceEvent));
    }

    public function tearDown()
    {
        // Unsets object manager
        \Mage::reset();
    }

        $collection = new \Magento\Webhook\Model\Resource\Event\Collection(
            $eventManager, $logger, $mockFetchStrategy, $entityFactory, $mockResourceEvent
        );
        $this->assertInstanceOf('Magento\Webhook\Model\Resource\Event\Collection', $collection);
        $this->assertEquals('Magento\Webhook\Model\Resource\Event', $collection->getResourceModelName());
        $this->assertEquals('Magento\Webhook\Model\Event', $collection->getModelName());
    }
}
