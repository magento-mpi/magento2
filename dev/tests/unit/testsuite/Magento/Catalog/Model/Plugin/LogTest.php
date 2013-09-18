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
     * @var \Magento\Catalog\Model\Plugin\Log
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
        $this->_logResourceMock = $this->getMock('Magento\Log\Model\Resource\Log', array(), array(), '', false);
        $this->_compareItemMock = $this->getMock(
            'Magento\Catalog\Model\Product\Compare\Item', array(), array(), '', false
        );
        $this->_model = new \Magento\Catalog\Model\Plugin\Log($this->_compareItemMock);
    }

    /**
     * @covers \Magento\Catalog\Model\Plugin\Log::afterClean
     */
    public function testAfterClean()
    {
        $this->_compareItemMock->expects($this->once())
            ->method('clean');

        $this->assertEquals($this->_logResourceMock, $this->_model->afterClean($this->_logResourceMock));
    }
}
