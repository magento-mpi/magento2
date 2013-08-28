<?php
/**
 * Test class for Magento_Core_Model_Config_Fieldset
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Config_Modules_Reader
     */
    protected $_configReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Cache_Type_Config
     */
    protected $_cacheTypeMock;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            'Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false
        );
        $this->_cacheTypeMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false);
    }

    protected function tearDown()
    {
        $this->_configReaderMock = null;
        $this->_cacheTypeMock = null;
    }

    public function testConstructorCacheExists()
    {
        $cachedConfig = '<config/>';
        $this->_cacheTypeMock->expects($this->once())
            ->method('load')
            ->with('fieldset_config')
            ->will($this->returnValue($cachedConfig));
        $model = new Magento_Core_Model_Config_Fieldset($this->_configReaderMock, $this->_cacheTypeMock);
        $this->assertInstanceOf('Magento_Simplexml_Element', $model->getNode());
    }

    public function testConstructorNoCacheExists()
    {
        $config = new Magento_Core_Model_Config_Base('<config/>');
        $this->_cacheTypeMock->expects($this->once())
            ->method('load')
            ->with('fieldset_config')
            ->will($this->returnValue(false));
        $this->_configReaderMock->expects($this->once())
            ->method('loadModulesConfiguration')
            ->with('fieldset.xml')
            ->will($this->returnValue($config));
        $this->_cacheTypeMock->expects($this->once())
            ->method('save')
            ->with("<?xml version=\"1.0\"?>\n<config/>\n");
        $model = new Magento_Core_Model_Config_Fieldset($this->_configReaderMock, $this->_cacheTypeMock);
        $this->assertInstanceOf('Magento_Simplexml_Element', $model->getNode());
    }
}
