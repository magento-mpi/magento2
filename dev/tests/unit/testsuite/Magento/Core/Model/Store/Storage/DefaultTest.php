<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Store\Storage\DefaultStorage
 */
class Magento_Core_Model_Store_Storage_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Store\Storage\DefaultStorage
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

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_groupMock;

    protected function setUp()
    {
        $this->_websiteMock = $this->getMock('Magento\Core\Model\Website',
            array('getCode', 'getId'), array(), '', false, false);
        $this->_groupMock = $this->getMock('Magento\Core\Model\Store\Group',
            array('getCode', 'getId'), array(), '', false, false);
        $this->_storeFactoryMock = $this->getMock('Magento\Core\Model\StoreFactory',
            array('create'), array(), '', false, false);
        $this->_websiteFactoryMock = $this->getMock('Magento\Core\Model\Website\Factory',
            array('create'), array(), '', false, false);
        $this->_websiteFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_websiteMock));
        $this->_groupFactoryMock = $this->getMock('Magento\Core\Model\Store\Group\Factory',
            array('create'), array(), '', false, false);
        $this->_groupFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_groupMock));
        $this->_storeMock = $this->getMock('Magento\Core\Model\Store',
            array('setId', 'setCode', 'getCode', '__sleep', '__wakeup'),
            array(), '', false, false);
        $this->_storeFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_storeMock));
        $this->_model = new \Magento\Core\Model\Store\Storage\DefaultStorage(
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
        $this->assertInstanceOf('Magento\Core\Model\Store', $this->_model->getStore($storeId));
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
        $this->assertInstanceOf('Magento\Core\Model\Website', $this->_model->getWebsite($websiteId));
    }

    public function testGetWebsitesWithDefault()
    {
        $withDefault = true;
        $codeKey = 'someKey';
        $this->_websiteMock->expects($this->once())->method('getCode')->will($this->returnValue(0));
        $this->_websiteMock->expects($this->never())->method('getId');
        $result = $this->_model->getWebsites($withDefault, $codeKey);
        $this->assertInstanceOf('Magento\Core\Model\Website', $result[0]);
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

    public function testGetGroup()
    {
        $groupId = 'testGroup';
        $this->assertInstanceOf('Magento\Core\Model\Store\Group', $this->_model->getGroup($groupId));
    }

    public function testGetGroupsWithDefault()
    {
        $withDefault = true;
        $codeKey = 'someKey';
        $this->_groupMock->expects($this->once())->method('getCode')->will($this->returnValue(0));
        $this->_groupMock->expects($this->never())->method('getId');
        $result = $this->_model->getGroups($withDefault, $codeKey);
        $this->assertInstanceOf('Magento\Core\Model\Store\Group', $result[0]);
    }

    public function testGetGroupsWithoutDefault()
    {
        $withDefault = false;
        $codeKey = 'someKey';
        $this->_groupMock->expects($this->never())->method('getCode');
        $this->_groupMock->expects($this->never())->method('getId');
        $result = $this->_model->getGroups($withDefault, $codeKey);
        $this->assertEquals(array(), $result);
    }

    public function testGetDefaultStoreView()
    {
        $this->assertNull($this->_model->getDefaultStoreView());
    }

    public function testGetCurrentStore()
    {
        $this->_storeMock->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('result'));
        $result = $this->_model->getCurrentStore();
        $this->assertEquals('result', $result);
    }
}
