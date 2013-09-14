<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Plugin_LogTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Plugin_Log
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_compareItemMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logResourceMock;

    protected function setUp()
    {
        $this->_logResourceMock = $this->getMock('Magento_Log_Model_Resource_Log', array(), array(), '', false);
        $this->_compareItemMock = $this->getMock(
            'Magento_Catalog_Model_Product_Compare_Item', array(), array(), '', false
        );
        $this->_model = new Magento_Catalog_Model_Plugin_Log($this->_compareItemMock);
    }

    /**
     * @covers Magento_Catalog_Model_Plugin_Log::afterClean
     */
    public function testAfterClean()
    {
        $this->_compareItemMock->expects($this->once())
            ->method('clean');

        $this->assertEquals($this->_logResourceMock, $this->_model->afterClean($this->_logResourceMock));
    }
}
