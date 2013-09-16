<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_WebsiteRestriction_Model_ConfigTest extends PHPUnit_Framework_TestCase
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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var Magento_WebsiteRestriction_Model_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento_WebsiteRestriction_Model_Config_Reader',
            array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $cacheId = null;
        
        $this->_model = new Magento_WebsiteRestriction_Model_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $this->_storeConfigMock,
            $cacheId
        );
    }

    /**
     * @dataProvider getGenericActionsDataProvider
     */
    public function testGetGenericActions($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue($value));

        $this->assertEquals($expected, $this->_model->getGenericActions());
    }

    public function getGenericActionsDataProvider()
    {
        return array(
            'generic_key_exist' => array(array('generic' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), array()),
        );
    }

    /**
     * @dataProvider getRegisterActionsDataProvider
     */
    public function testGetRegisterActions($value, $expected)
    {
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue($value));
        
        $this->assertEquals($expected, $this->_model->getRegisterActions());
    }

    public function getRegisterActionsDataProvider()
    {
        return array(
            'register_key_exist' => array(array('register' => 'value'), 'value'),
            'return_default_value' => array(array('key_one' =>'value'), array()),
        );
    }

    public function testIsRestrictionEnabled()
    {
        $store = null;
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')->with('general/restriction/is_active', $store)->will($this->returnValue(false));

        $this->assertEquals(false, $this->_model->isRestrictionEnabled($store));
    }

    public function testGetMode()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')->with('general/restriction/mode')->will($this->returnValue(false));
        $this->assertEquals(0, $this->_model->getMode());
    }

    public function testGetHTTPStatusCode()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')->with('general/restriction/http_status')->will($this->returnValue(false));
        $this->assertEquals(0, $this->_model->getHTTPStatusCode());
    }

    public function testGetHTTPRedirectCode()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')->with('general/restriction/http_redirect')->will($this->returnValue(true));
        $this->assertEquals(1, $this->_model->getHTTPRedirectCode());
    }

    public function testGetLandingPageCode()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')->with('general/restriction/cms_page')->will($this->returnValue('config'));
        $this->assertEquals('config', $this->_model->getLandingPageCode());
    }
}