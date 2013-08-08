<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ObjectManager_ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_ObjectManager_ConfigLoader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_modulesReaderMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader',
            array(), array(), '', false
        );

        $this->_factoryMock = $this->getMock('Magento_ObjectManager_Config_Reader_DomFactory',
            array('create'), array(), '', false
        );

        $this->_cacheMock = $this->getMock('Magento_Cache_FrontendInterface');
        $this->_model = new Magento_Core_Model_ObjectManager_ConfigLoader(
            $this->_cacheMock, $this->_modulesReaderMock, $this->_factoryMock
        );
    }

    /**
     * @param $configFileName
     * @param $area
     * @dataProvider loadDataProvider
     */
    public function testLoad($configFileName, $area)
    {
        $configFiles = array('path/to/config.xml');
        $configData = array('some' => 'config', 'data' => 'value');

        $configReaderMock = $this->getMock('Magento_ObjectManager_Config_Reader_Dom', array(), array(), '', false);
        $this->_modulesReaderMock->expects($this->once())
            ->method('getModuleConfigurationFiles')->with($configFileName)->will($this->returnValue($configFiles));
        $this->_factoryMock->expects($this->once())->method('create')
            ->with(array('configFiles' => $configFiles,'isRuntimeValidated' => false))
            ->will($this->returnValue($configReaderMock));

        $configReaderMock->expects($this->once())->method('read')->will($this->returnValue($configData));
        $this->assertEquals($configData, $this->_model->load($area));
    }

    public function loadDataProvider()
    {
        return array(
            'global files' => array('di.xml', 'global'),
            'adminhtml files' => array('adminhtml' . DIRECTORY_SEPARATOR . 'di.xml', 'adminhtml'),
            'any area files' => array('any' . DIRECTORY_SEPARATOR . 'di.xml', 'any'),
        );
    }
}
