<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Store_Storage_Default
 */
class Mage_Core_Model_Store_Storage_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Store_Storage_Default
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_groupFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteMock;

    protected function setUp()
    {
        $this->_websiteMock = $this->getMock('Mage_Core_Model_Website',
            array('getCode', 'getId'), array(), '', false, false);
        $this->_storeFactoryMock = $this->getMock('Mage_Core_Model_StoreFactory',
            array('create'), array(), '', false, false);
        $this->_websiteFactoryMock = $this->getMock('Mage_Core_Model_Website_Factory',
            array('create'), array(), '', false, false);
        $this->_websiteFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_websiteMock));
        $this->_groupFactoryMock = $this->getMock('Mage_Core_Model_Store_Group_Factory',
            array(), array(), '', false, false);
        $this->_storeMock = $this->getMock('Mage_Core_Model_Store', array('setId', 'setCode'),
            array(), '', false, false);
        $this->_storeFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_storeMock));
        $this->_model = new Mage_Core_Model_Store_Storage_Default(
            $this->_storeFactoryMock,
            $this->_websiteFactoryMock,
            $this->_groupFactoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_storeFactoryMock);
        unset($this->_websiteFactoryMock);
        unset($this->_groupFactoryMock);
        unset($this->_storeMock);
        unset($this->_model);
    }

    public function testHasSingleStore()
    {
        $this->assertEquals(false, $this->_model->hasSingleStore());
    }

    public function testGetStore()
    {
        $storeId = 'testStore';
        $this->assertInstanceOf('Mage_Core_Model_Store', $this->_model->getStore($storeId));
    }

    public function testGetStores()
    {
        $withDefault = true;
        $codeKey = true;
        $this->assertEquals(array(), $this->_model->getStores($withDefault, $codeKey));
    }

    public function testGetWebsite()
    {
        $websiteId = 'testWebsite';
        $this->assertInstanceOf('Mage_Core_Model_Website', $this->_model->getWebsite($websiteId));
    }

    public function testGetWebsitesWithDefault()
    {
        $withDefault = true;
        $codeKey = 'someKey';
        $this->_websiteMock->expects($this->once())->method('getCode')->will($this->returnValue(0));
        $this->_websiteMock->expects($this->never())->method('getId');
        $result = $this->_model->getWebsites($withDefault, $codeKey);
        $this->assertInstanceOf('Mage_Core_Model_Website', $result[0]);
    }

    public function testGetWebsitesWithoutDefault()
    {
        $withDefault = false;
        $codeKey = 'someKey';
        $this->_websiteMock->expects($this->never())->method('getCode');
        $this->_websiteMock->expects($this->never())->method('getId');
        $result = $this->_model->getWebsites($withDefault, $codeKey);
        $this->assertEquals(array(), $result);
    }
}
