<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_ConfigTest extends PHPUnit_Framework_TestCase
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
     * @var Magento_FullPageCache_Model_Placeholder_Config
     */
    protected $_model;

    protected function setUp()
    {
        /** @todo Implement test logic here */
        
        $this->_readerMock = $this->getMock(
            'Magento_FullPageCache_Model_Placeholder_Config_Reader',
            array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $cacheId = null;
        
        $this->_model = new Magento_FullPageCache_Model_Placeholder_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $cacheId
        );
    }

    public function testGetPlaceholders()
    {
        /** @todo Implement test logic here */
        
        $name = null;
        
        $this->_model->getPlaceholders($name);
    }
}