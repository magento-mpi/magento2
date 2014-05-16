<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflineShipping\Model\SalesRule;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMock(
            'Magento\OfflineShipping\Model\SalesRule\Calculator',
            array('_getRules', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_model->expects($this->any())->method('_getRules')->will($this->returnValue(array()));
    }

    public function testProcessFreeShipping()
    {
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array('getAddress', '__wakeup'), array(), '', false);
        $item->expects($this->once())->method('getAddress')->will($this->returnValue(true));

        $this->assertInstanceOf(
            'Magento\OfflineShipping\Model\SalesRule\Calculator',
            $this->_model->processFreeShipping($item)
        );

        return true;
    }
}
