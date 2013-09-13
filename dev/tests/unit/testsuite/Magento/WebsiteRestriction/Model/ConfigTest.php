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
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
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

    public function testGetGenericActions()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getGenericActions();
    }

    public function testGetRegisterActions()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getRegisterActions();
    }

    public function testIsRestrictionEnabled()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $store = null;
        
        $this->_model->isRestrictionEnabled($store);
    }

    public function testGetMode()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getMode();
    }

    public function testGetHTTPStatusCode()
    {
        /** @todo Implement test logic here */
        
        $this->_model->getHTTPStatusCode();
    }

    public function testGetHTTPRedirectCode()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getHTTPRedirectCode();
    }

    public function testGetLandingPageCode()
    {
        $this->markTestIncomplete('MAGETWO-14185');
        /** @todo Implement test logic here */
        
        $this->_model->getLandingPageCode();
    }
}