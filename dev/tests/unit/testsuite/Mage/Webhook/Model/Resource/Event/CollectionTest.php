<?php
/**
 * Mage_Webhook_Model_Resource_Event_Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $mockDBAdapter = $this->getMockBuilder('Magento_DB_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('_connect', '_quote'))
            ->getMockForAbstractClass();
        $mockResourceEvent = $this->getMockBuilder('Mage_Webhook_Model_Resource_Event')
            ->disableOriginalConstructor()
            ->getMock();
        $mockResourceEvent->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($mockDBAdapter));

        $mockObjectManager = $this->_setMageObjectManager();
        $mockObjectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Mage_Webhook_Model_Resource_Event'))
            ->will($this->returnValue($mockResourceEvent));
    }

    public function tearDown()
    {
        // Unsets object manager
        Mage::reset();
    }

    public function testConstructor()
    {
        $mockFetchStrategy = $this->getMockBuilder('Magento_Data_Collection_Db_FetchStrategyInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $collection = new Mage_Webhook_Model_Resource_Event_Collection($mockFetchStrategy);
        $this->assertInstanceOf('Mage_Webhook_Model_Resource_Event_Collection', $collection);
        $this->assertEquals('Mage_Webhook_Model_Resource_Event', $collection->getResourceModelName());
        $this->assertEquals('Mage_Webhook_Model_Event', $collection->getModelName());
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
