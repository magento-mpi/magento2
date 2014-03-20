<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Source\Group;

class MultiselectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Config\Source\Group\Multiselect
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
        $this->groupServiceMock = $this->getMock('\Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $this->converterMock = $this->getMock('\Magento\Convert\Object', array(), array(), '', false);
        $this->model =
            new \Magento\Customer\Model\Config\Source\Group\Multiselect($this->groupServiceMock, $this->converterMock);
    }

    public function testToOptionArray()
    {
        $expectedValue = array('General', 'Retail');
        $this->groupServiceMock->expects($this->once())->method('getGroups')
            ->with(false)->will($this->returnValue($expectedValue));
        $this->converterMock->expects($this->once())->method('toOptionArray')
            ->with($expectedValue, 'id', 'code')->will($this->returnValue($expectedValue));
        $this->assertEquals($expectedValue, $this->model->toOptionArray());
    }
}
