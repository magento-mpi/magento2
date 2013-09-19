<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_ConfigTest extends PHPUnit_Framework_TestCase
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
     * @var Magento_AdminGws_Model_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_AdminGws_Model_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $cacheId = null;
        
        $this->_model = new Magento_AdminGws_Model_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $cacheId
        );
    }

    /**
     * @dataProvider getCallbacksDataProvider
     */
    public function testGetCallbacks($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue($value));

        $this->assertEquals($expected, $this->_model->getCallbacks('group'));
    }

    public function getCallbacksDataProvider()
    {
        return array(
            'generic_key_exist' => array(array('callbacks' => array('group' => 'value')), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), array()),
        );
    }

    /**
     * @dataProvider getDeniedAclResourcesDataProvider
     */
    public function testGetDeniedAclResources($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue($value));
        
        $this->assertEquals($expected, $this->_model->getDeniedAclResources('level'));
    }

    public function getDeniedAclResourcesDataProvider()
    {
        return array(
            'generic_key_exist' => array(array('acl' => array('level' => 'value')), 'value'),
            'return_default_value' => array(array('key_one' => 'value'), array()),
        );
    }
}