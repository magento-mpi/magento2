<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_InitialTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Initial
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configCacheMock;

    protected function setUp()
    {
        $this->_initialReaderMock = $this->getMock('Magento_Core_Model_Config_Initial_Reader', array(), array(), '', false);
        $this->_configCacheMock = $this->getMock('Magento_Core_Model_Cache_Type_Config', array(), array(), '', false);
        $serializedData = serialize(array(
            'data' => array(
                'default' => array(
                    'key' => 'default_value',
                ),
                'stores' => array(
                    'default' => array('key' => 'store_value'),
                ),
                'websites' => array(
                    'default' => array('key' => 'website_value'),
                ),
            ),
            'metadata' => array('metadata'),
        ));
        $this->_configCacheMock->expects($this->any())
            ->method('load')
            ->with('initial_config')
            ->will($this->returnValue($serializedData));

        $this->_model = new Magento_Core_Model_Config_Initial($this->_initialReaderMock, $this->_configCacheMock);
    }

    public function testGetDefault()
    {
        $expectedResult = array('key' => 'default_value');
        $this->assertEquals($expectedResult, $this->_model->getDefault());
    }

    public function testGetStore()
    {
        $expectedResult = array('key' => 'store_value');
        $this->assertEquals($expectedResult, $this->_model->getStore('default'));
    }

    public function testGetWebsite()
    {
        $expectedResult = array('key' => 'website_value');
        $this->assertEquals($expectedResult, $this->_model->getWebsite('default'));
    }

    public function testGetMetadata()
    {
        $expectedResult = array('metadata');
        $this->assertEquals($expectedResult, $this->_model->getMetadata());
    }
}
