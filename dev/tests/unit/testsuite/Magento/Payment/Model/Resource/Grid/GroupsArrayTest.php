<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Resource\Grid;

class GroupsArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Resource\Grid\GroupsArray
     */
    protected $groupArrayModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    protected function setUp()
    {
        $this->helperMock = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $this->groupArrayModel = new \Magento\Payment\Model\Resource\Grid\GroupsArray($this->helperMock);
    }

    public function testToOptionArray()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('getPaymentMethodList')
            ->with(true, true, true)
            ->will($this->returnValue(array('group data')));
        $this->assertEquals(array('group data'), $this->groupArrayModel->toOptionArray());
    }
}
