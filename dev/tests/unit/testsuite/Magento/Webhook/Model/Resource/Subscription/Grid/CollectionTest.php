<?php
/**
 * Magento_Webhook_Model_Resource_Subscription_Grid_Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Subscription_Grid_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);

        $fetchStrategyMock = $this->_makeMock('Magento_Data_Collection_Db_FetchStrategyInterface');
        $endpointResMock = $this->_makeMock('Magento_Webhook_Model_Resource_Endpoint');

        $configMock = $this->_makeMock('Magento_Webhook_Model_Subscription_Config');
        $configMock->expects($this->once())
            ->method('updateSubscriptionCollection');

        $selectMock = $this->_makeMock('Zend_Db_Select');
        $selectMock->expects($this->any())
            ->method('from')
            ->with(array('main_table' => null));
        $connectionMock = $this->_makeMock('Magento_DB_Adapter_Pdo_Mysql');
        $connectionMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $resourceMock = $this-> _makeMock('Magento_Webhook_Model_Resource_Subscription');
        $resourceMock->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connectionMock));
        /** @var Magento_Core_Model_EntityFactory $entityFactory */
        $entityFactory = $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false);
        new Magento_Webhook_Model_Resource_Subscription_Grid_Collection(
            $configMock, $endpointResMock, $eventManager, $logger, $fetchStrategyMock, $entityFactory, $resourceMock);
    }

    /**
     * Generates a mock object of the given class
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function _makeMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

}
