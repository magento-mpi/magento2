<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Menu_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheInstanceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_directorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuBuilderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var Magento_Backend_Model_Menu_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_cacheInstanceMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(),
            '', false);

        $this->_directorMock = $this->getMock('Magento_Backend_Model_Menu_DirectorAbstract', array(), array(),
            '', false);

        $this->_menuFactoryMock = $this->getMock('Magento_Backend_Model_MenuFactory', array('create'), array(),
            '', false);

        $this->_configReaderMock = $this->getMock('Magento_Backend_Model_Menu_Config_Reader', array(), array(),
            '', false);

        $this->_eventManagerMock = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '',
            false, false);

        $this->_logger = $this->getMock(
            'Magento_Core_Model_Logger', array('addStoreLog', 'log', 'logException'), array(), '', false
        );

        $this->_menuMock = $this->getMock('Magento_Backend_Model_Menu', array(), array(), '', false);

        $this->_menuBuilderMock = $this->getMock('Magento_Backend_Model_Menu_Builder', array(), array(), '', false);

        $this->_menuFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_menuMock));

        $storeManagerMock = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);

        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);

        $storeManagerMock->expects($this->atLeastOnce())->method('getStore')->will($this->returnValue($storeMock));

        $this->_configReaderMock->expects($this->any())->method('read')->will($this->returnValue(array()));

        $this->_model = new Magento_Backend_Model_Menu_Config(
            $this->_menuBuilderMock,
            $this->_directorMock,
            $this->_menuFactoryMock,
            $this->_configReaderMock,
            $this->_cacheInstanceMock,
            $this->_eventManagerMock,
            $this->_logger,
            $storeManagerMock
        );
    }

    public function testGetMenuWithCachedObjectReturnsUnserializedObject()
    {
        $this->_cacheInstanceMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(Magento_Backend_Model_Menu_Config::CACHE_MENU_OBJECT))
            ->will($this->returnValue('menu_cache'));

        $this->_menuMock->expects($this->once())
            ->method('unserialize')
            ->with('menu_cache');

        $this->assertEquals($this->_menuMock, $this->_model->getMenu());
    }

    public function testGetMenuWithNotCachedObjectBuidlsObject()
    {
        $this->_cacheInstanceMock->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(Magento_Backend_Model_Menu_Config::CACHE_MENU_OBJECT))
            ->will($this->returnValue(false));

        $this->_configReaderMock->expects($this->once())->method('read')->will($this->returnValue(array()));

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->returnValue($this->_menuMock));

        $this->assertEquals($this->_menuMock, $this->_model->getMenu());
    }

    /**
     * @param string $expectedException
     *
     * @dataProvider getMenuExceptionLoggedDataProvider
     */
    public function testGetMenuExceptionLogged($expectedException)
    {
        $this->setExpectedException($expectedException);
        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->throwException(new $expectedException()));

        $this->_model->getMenu();
    }

    public function getMenuExceptionLoggedDataProvider()
    {
        return array(
            'InvalidArgumentException' => array(
                'InvalidArgumentException'
            ),
            'BadMethodCallException' => array(
                'BadMethodCallException'
            ),
            'OutOfRangeException' => array(
                'OutOfRangeException'
            )
        );
    }

    public function testGetMenuGenericExceptionIsNotLogged()
    {
        $this->_logger->expects($this->never())->method('logException');

        $this->_menuBuilderMock->expects($this->exactly(1))
            ->method('getResult')
            ->will($this->throwException(new Exception()));
        try {
            $this->_model->getMenu();
        } catch (Exception $e) {
            return;
        }
        $this->fail("Generic Exception was not throwed");
    }
}
