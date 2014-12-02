<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Config\Source\Group
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    protected function setUp()
    {
        $this->groupServiceMock = $this->getMock('\Magento\Customer\Api\GroupManagementInterface');
        $this->converterMock = $this->getMock('\Magento\Framework\Convert\Object', array(), array(), '', false);
        $this->model =
            new \Magento\Customer\Model\Config\Source\Group($this->groupServiceMock, $this->converterMock);
    }

    public function testToOptionArray()
    {
        $expectedValue = array('General', 'Retail');
        $this->groupServiceMock->expects($this->once())
            ->method('getLoggedInGroups')
            ->will($this->returnValue($expectedValue));
        $this->converterMock->expects($this->once())->method('toOptionArray')
            ->with($expectedValue, 'id', 'code')->will($this->returnValue($expectedValue));
        array_unshift($expectedValue, array('value' => '', 'label' => __('-- Please Select --')));
        $this->assertEquals($expectedValue, $this->model->toOptionArray());
    }
}
