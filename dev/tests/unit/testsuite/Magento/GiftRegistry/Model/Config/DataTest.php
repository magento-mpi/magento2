<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Config\Data
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\GiftRegistry\Model\Config\Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_cacheMock = $this->getMockBuilder(
            'Magento\App\Cache\Type\Config'
        )->disableOriginalConstructor()->getMock();
        $this->_model = new \Magento\GiftRegistry\Model\Config\Data(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock
        );
    }

    public function testGet()
    {
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue('global'));
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(false));
        $this->_readerMock->expects($this->any())->method('read')->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->_model->get());
    }
}
