<?php
/**
 * Test class for Magento_Core_Model_Config_Cache
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Cache
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_baseFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configSectionsMock;

    protected function setUp()
    {
        $this->_cacheMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false, false);
        $this->_configSectionsMock = $this->getMock('Magento_Core_Model_Config_Sections',
            array(), array(), '', false, false);
        $this->_contFactoryMock = $this->getMock('Magento_Core_Model_Config_ContainerFactory',
            array(), array(), '', false, false);
        $this->_baseFactoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Config_Cache(
            $this->_cacheMock,
            $this->_configSectionsMock,
            $this->_contFactoryMock,
            $this->_baseFactoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_cacheMock);
        unset($this->_configSectionsMock);
        unset($this->_contFactoryMock);
        unset($this->_baseFactoryMock);
        unset($this->_model);
    }


    public function testCacheLifetime()
    {
        $lifetime = 10;
        $this->_model->setCacheLifetime($lifetime);
        $this->assertEquals($lifetime, $this->_model->getCacheLifeTime());
    }

    public function testLoadWithoutConfig()
    {
        $this->assertFalse($this->_model->load());
    }

    public function testLoadWithConfig()
    {
        $this->_cacheMock->expects($this->at(0))
            ->method('load')
            ->will($this->returnValue(false));
        $this->_cacheMock->expects($this->at(1))
            ->method('load')
            ->will($this->returnValue('test_config'));
        $this->_contFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array('sourceData' => 'test_config')))
            ->will($this->returnValue('some_instance'));

        $this->assertEquals('some_instance', $this->_model->load());
    }

    public function testSave()
    {
        $config = new Magento_Core_Model_Config_Base(
            '<config><section1>section 1</section1><section2>section 2</section2></config>'
        );
        $this->_cacheMock->expects($this->at(0))
            ->method('load')
            ->with('config_global.lock')
            ->will($this->returnValue(false));
        $this->_configSectionsMock->expects($this->once())
            ->method('getSections')
            ->will($this->returnValue(array('section1' => 1, 'section2' => 2)));

        $this->_cacheMock->expects($this->at(1))
            ->method('save')
            ->with($this->anything(), 'config_global.lock');
        $this->_cacheMock->expects($this->once())
            ->method('clean');
        $this->_cacheMock->expects($this->at(3))
            ->method('save')
            ->with('<section1>section 1</section1>', 'config_global_section1');
        $this->_cacheMock->expects($this->at(4))
            ->method('save')
            ->with('<section2>section 2</section2>', 'config_global_section2');
        $this->_cacheMock->expects($this->once())
            ->method('remove')
            ->with('config_global.lock');
        $this->_model->save($config);
    }

    public function testSaveLocked()
    {
        $this->_cacheMock->expects($this->at(0))
            ->method('load')
            ->with('config_global.lock')
            ->will($this->returnValue(true));
        $this->_cacheMock->expects($this->never())
            ->method('save');
        $this->_model->save(new Magento_Core_Model_Config_Base());
    }

    public function testClean()
    {
        $this->_cacheMock->expects($this->once())
            ->method('clean');
        $this->_model->clean();
    }

    public function testGetSection()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('config_global_section1')
            ->will($this->returnValue('<config/>'));
        $this->_baseFactoryMock->expects($this->once())
            ->method('create')
            ->with('<config/>');
        $this->_model->getSection('section1');
    }

    /**
     * @expectedException Magento_Core_Model_Config_Cache_Exception
     */
    public function testGetSectionNoSectionException()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with('config_global_section1')
            ->will($this->returnValue(false));
        $this->_baseFactoryMock->expects($this->never())
            ->method('create');
        $this->_model->getSection('section1');
    }
}
