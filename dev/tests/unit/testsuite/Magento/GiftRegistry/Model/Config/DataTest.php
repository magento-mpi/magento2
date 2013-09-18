<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftRegistry_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_GiftRegistry_Model_Config_Data
     */
    protected $_model;

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

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_GiftRegistry_Model_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_model = new Magento_GiftRegistry_Model_Config_Data(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock
        );
    }

    public function testGet()
    {
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue('global'));
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->_model->get());
    }
}
