<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Resource\Grid;

class TypesArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Resource\Grid\TypesArray
     */
    protected $typesArrayModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $this->typesArrayModel = new \Magento\Payment\Model\Resource\Grid\TypesArray($this->helperMock);
    }

    public function testToOptionArray()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('getPaymentMethodList')
            ->with(true)
            ->will($this->returnValue(array('group data')));
        $this->assertEquals(array('group data'), $this->typesArrayModel->toOptionArray());
    }
}
