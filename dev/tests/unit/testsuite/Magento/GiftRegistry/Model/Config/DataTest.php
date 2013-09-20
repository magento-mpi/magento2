<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
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
        $this->_configScopeMock = $this->getMock('Magento_Core_Model_Config_Scope');
        $this->_cacheMock = $this->getMockBuilder('Magento_Core_Model_Cache_Type_Config')
                                 ->disableOriginalConstructor()->getMock();
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
