<?php
/**
 * \Magento\Webhook\Model\Resource\Subscription\Grid\Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource\Subscription\Grid;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);

        $fetchStrategyMock = $this->_makeMock('Magento\Data\Collection\Db\FetchStrategyInterface');
        $endpointResMock = $this->_makeMock('Magento\Webhook\Model\Resource\Endpoint');

        $configMock = $this->_makeMock('Magento\Webhook\Model\Subscription\Config');
        $configMock->expects($this->once())
            ->method('updateSubscriptionCollection');

        $selectMock = $this->_makeMock('Zend_Db_Select');
        $selectMock->expects($this->any())
            ->method('from')
            ->with(array('main_table' => null));
        $connectionMock = $this->_makeMock('Magento\DB\Adapter\Pdo\Mysql');
        $connectionMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $resourceMock = $this-> _makeMock('Magento\Webhook\Model\Resource\Subscription');
        $resourceMock->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connectionMock));
        /** @var \Magento\Core\Model\EntityFactory $entityFactory */
        $entityFactory = $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false);
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        new \Magento\Webhook\Model\Resource\Subscription\Grid\Collection(
            $configMock, $endpointResMock, $eventManager, $logger, $fetchStrategyMock, $entityFactory, $resourceMock);
    }

    /**
     * Generates a mock object of the given class
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _makeMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

}
