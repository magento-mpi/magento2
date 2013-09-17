<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_ProductTypes_ConfigTest extends PHPUnit_Framework_TestCase
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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var Magento_Catalog_Model_ProductTypes_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento_Catalog_Model_ProductTypes_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_model = new Magento_Catalog_Model_ProductTypes_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            'cache_id'
        );
    }

    /**
     * @dataProvider getTypeDataProvider
     */
    public function testGetType($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue($value));
        $this->assertEquals($expected, $this->_model->getType('global'));
    }

    public function getTypeDataProvider()
    {
        return array(
            'global_key_exist' => array(array('global' => 'value'), 'value'),
            'return_default_value' => array(array('some_key' => 'value'), array())
        );
    }

    public function testGetAll()
    {
        $expected = array('Expected Data');
        $this->_cacheMock->expects($this->once())->method('get')->with('global')->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->_model->getAll());
    }
}