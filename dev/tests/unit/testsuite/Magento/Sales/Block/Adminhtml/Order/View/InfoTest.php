<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Framework\Exception\NoSuchEntityException;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAddressEditLink()
    {
        $contextMock = $this->getMock('Magento\Backend\Block\Template\Context', ['getAuthorization'], [], '', false);
        $authorizationMock = $this->getMock('Magento\Framework\AuthorizationInterface', [], [], '', false);
        $contextMock->expects($this->any())->method('getAuthorization')->will($this->returnValue($authorizationMock));
        $arguments = ['context' => $contextMock];

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Sales\Block\Adminhtml\Order\View\Info $block */
        $block = $helper->getObject('Magento\Sales\Block\Adminhtml\Order\View\Info', $arguments);

        $authorizationMock->expects($this->atLeastOnce())
            ->method('isAllowed')
            ->with('Magento_Sales::actions_edit')
            ->will($this->returnValue(false));

        $address = new \Magento\Framework\Object();
        $this->assertEmpty($block->getAddressEditLink($address));
    }

    public function testGetCustomerGroupName()
    {
        $contextMock = $this->getMock('Magento\Backend\Block\Template\Context', ['getAuthorization'], [], '', false);
        $authorizationMock = $this->getMock('Magento\Framework\AuthorizationInterface', [], [], '', false);
        $contextMock->expects($this->any())->method('getAuthorization')->will($this->returnValue($authorizationMock));
        $groupServiceMock = $this->getMock('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $methods = ['getCustomerGroupId', '__wakeUp'];
        $orderMock = $this->getMock('\Magento\Sales\Model\Order', $methods, [], '', false);
        $groupMock = $this->getMock('Magento\Customer\Service\V1\Data\CustomerGroup', [], [], '', false);
        $arguments = [
            'context' => $contextMock,
            'groupService' => $groupServiceMock,
            'registry' => $coreRegistryMock
        ];

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Sales\Block\Adminhtml\Order\View\Info $block */
        $block = $helper->getObject('Magento\Sales\Block\Adminhtml\Order\View\Info', $arguments);
        $coreRegistryMock
            ->expects($this->any())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($orderMock));
        $orderMock->expects($this->once())->method('getCustomerGroupId')->will($this->returnValue(4));
        $groupServiceMock->expects($this->once())->method('getGroup')->with(4)->will($this->returnValue($groupMock));
        $groupMock
            ->expects($this->once())
            ->method('getCode')
            ->will($this->throwException(new NoSuchEntityException()));
        $this->assertEquals('', $block->getCustomerGroupName());
    }
}
