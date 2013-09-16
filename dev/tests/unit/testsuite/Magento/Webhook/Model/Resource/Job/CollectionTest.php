<?php
/**
 * Magento_Webhook_Model_Resource_Job_Collection
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
    protected function setUp()
    {
        $mockDBAdapter = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Magento_Webhook_Model_Resource_Job')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->once())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $mockObjectManager = $this->_setMageObjectManager();
        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_Webhook_Model_Resource_Job'))
            ->will($this->returnValue($mockResourceEvent));
    }

    protected function tearDown()
    {
        // Unsets object manager
        Mage::reset();
    }

    public function testConstructor()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $collection = new Magento_Webhook_Model_Resource_Job_Collection($eventManager, $mockFetchStrategy);
        $this->assertInstanceOf('Magento_Webhook_Model_Resource_Job_Collection', $collection);
        $this->assertEquals('Magento_Webhook_Model_Resource_Job', $collection->getResourceModelName());
    }

    /**
     * Makes sure that Mage has a mock object manager set, and returns that instance.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _setMageObjectManager()
    {
        Mage::reset();
        $mockObjectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        Mage::setObjectManager($mockObjectManager);

        return $mockObjectManager;
    }
}
