<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Export_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var string
     */
    protected $_cacheId = 'some_id';

    /**
     * @var Magento_ImportExport_Model_Export_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock
            = $this->getMock('Magento_ImportExport_Model_Export_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_CacheInterface');
    }

    /**
     * @param array $value
     * @param null|string $expected
     * @dataProvider getEntitiesDataProvider
     */
    public function testGetEntities($value, $expected)
    {
        $this->_configScopeMock->expects($this->any())
            ->method('load')->with($this->_cacheId)->will($this->returnValue(false));
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue($value));
        $this->_model = new Magento_ImportExport_Model_Export_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheId
        );
        $this->assertEquals($expected, $this->_model->getEntities('entities'));
    }

    public function getEntitiesDataProvider()
    {
        return array(
            'entities_key_exist' => array(array('entities' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), null),
        );
    }

    /**
     * @param array $value
     * @param null|string $expected
     * @dataProvider getProductTypesDataProvider
     */
    public function testGetProductTypes($value, $expected)
    {
        $this->_configScopeMock->expects($this->any())
            ->method('load')->with($this->_cacheId)->will($this->returnValue(false));
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue($value));
        $this->_model = new Magento_ImportExport_Model_Export_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheId
        );
        $this->assertEquals($expected, $this->_model->getProductTypes('productTypes'));
    }

    public function getProductTypesDataProvider()
    {
        return array(
            'productTypes_key_exist' => array(array('productTypes' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), null),
        );
    }

    /**
     * @param array $value
     * @param null|string $expected
     * @dataProvider getFileFormatsDataProvider
     */
    public function testGetFileFormats($value, $expected)
    {
        $this->_configScopeMock->expects($this->any())
            ->method('load')->with($this->_cacheId)->will($this->returnValue(false));
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue($value));
        $this->_model = new Magento_ImportExport_Model_Export_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheId
        );
        $this->assertEquals($expected, $this->_model->getFileFormats('fileFormats'));
    }

    public function getFileFormatsDataProvider()
    {
        return array(
            'fileFormats_key_exist' => array(array('fileFormats' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), null),
        );
    }
}