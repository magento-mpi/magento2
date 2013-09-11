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
        /** @todo Implement test logic here */
        
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

    public function testGetCallbacks()
    {
        /** @todo Implement test logic here */
        
        $groupName = null;
        
        $this->_model->getCallbacks($groupName);
    }

    public function testGetDeniedAclResources()
    {
        /** @todo Implement test logic here */
        
        $level = null;
        
        $this->_model->getDeniedAclResources($level);
    }
}