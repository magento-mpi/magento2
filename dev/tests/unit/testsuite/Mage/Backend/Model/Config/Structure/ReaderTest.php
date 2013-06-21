<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Reader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converterMock;

    /**
     * @var string directory path to the _files directory
     */
    private $_filesPath;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Mage_Core_Model_Cache_Type_Config', array(), array(), '', false);
        $this->_converterMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Converter', array(), array(), '', false
        );
        $this->_filesPath = dirname(__DIR__) . '/../_files';
    }

    public function testGetConfigurationLoadsConfigFromCacheWhenCacheIsEnabled()
    {
        $cachedObject = new StdClass();
        $cachedObject->foo = 'bar';
        $cachedData = serialize($cachedObject);

        $this->_cacheMock->expects($this->once())->method('load')
            ->with(Mage_Backend_Model_Config_Structure_Reader::CACHE_SYSTEM_CONFIGURATION_STRUCTURE)
            ->will($this->returnValue($cachedData));

        $model = new Mage_Backend_Model_Config_Structure_Reader(
            $this->_cacheMock, $this->_configMock, $this->_converterMock
        );
        $this->assertEquals($cachedObject, $model->getData());
    }

    public function testGetConfigurationLoadsConfigFromFilesAndCachesIt()
    {
        $expected = array('var' => 'val');
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));

        $this->_converterMock->expects($this->once())->method('convert')->will($this->returnValue(
            array('config' => array('system' => $expected))
        ));
        
        $this->_configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array($this->_filesPath . '/system_2.xml')));

        $this->_cacheMock->expects($this->once())->method('save')->with(
            serialize($expected)
        );

        $model = new Mage_Backend_Model_Config_Structure_Reader(
            $this->_cacheMock, $this->_configMock, $this->_converterMock, false
        );
        $this->assertEquals($expected, $model->getData());
    }

    public function testGetConfigurationLoadsConfigFromFilesAndMergeIt()
    {
        $expected = array('var' => 'val');
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));

        $this->_converterMock->expects($this->once())->method('convert')->will($this->returnValue(
            array('config' => array('system' => $expected))
        ));
        $this->_configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                $this->_filesPath . '/system_config_options_1.xml',
                $this->_filesPath . '/system_config_options_2.xml')));

        $this->_cacheMock->expects($this->once())->method('save')->with(
            serialize($expected)
        );

        $model = new Mage_Backend_Model_Config_Structure_Reader(
            $this->_cacheMock, $this->_configMock, $this->_converterMock, false
        );
        $this->assertEquals($expected, $model->getData());
    }

    public function testGetConfigurationLoadsConfigFromFilesAndMergeUnknownAttribute()
    {
        $this->_configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                $this->_filesPath . '/system_unknown_attribute_1.xml',
                $this->_filesPath . '/system_unknown_attribute_2.xml')));

        $this->setExpectedException('Magento_Exception', "More than one node matching the query: " .
        "/config/system/section[@id='customer']/group[@id='create_account']" .
        "/field[@id='tax_calculation_address_type']/options/option");
        new Mage_Backend_Model_Config_Structure_Reader(
            $this->_cacheMock, $this->_configMock, $this->_converterMock, false
        );
    }

    public function testGetConfigurationLoadsConfigFromFilesAndMergeUnknownAttributeValidate()
    {
        $this->_configMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue(array(
                                           $this->_filesPath . '/system_unknown_attribute_1.xml',
                                           $this->_filesPath . '/system_unknown_attribute_2.xml')));

        // setup real path to schema in config
        $this->_configMock->expects($this->any())
            ->method('getModuleDir')
            ->with('etc', 'Mage_Backend')
            ->will(
                $this->returnValue(
                    realpath(__DIR__ . '/../../../../../../../../../app/code/Mage/Backend/etc')
                )
        );

        $this->setExpectedException('Magento_Exception',
            "Element 'option', attribute 'unknown': The attribute 'unknown' is not allowed.");
        new Mage_Backend_Model_Config_Structure_Reader(
            $this->_cacheMock, $this->_configMock, $this->_converterMock, true
        );
    }
}
