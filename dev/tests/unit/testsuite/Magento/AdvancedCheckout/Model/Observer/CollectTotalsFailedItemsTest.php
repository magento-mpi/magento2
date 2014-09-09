<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\AdvancedCheckout\Model\Observer;
 
class CollectTotalsFailedItemsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectTotalsFailedItems
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemProcessorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;
    
    protected function setUp()
    {
        $this->cartMock = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $this->itemProcessorMock =
            $this->getMock('Magento\AdvancedCheckout\Model\FailedItemProcessor', [], [], '', false);
        $this->eventMock =
            $this->getMock('Magento\Framework\Event', ['getFullActionName', '__wakeup'], [], '', false);
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $this->model = new CollectTotalsFailedItems($this->cartMock, $this->itemProcessorMock);
    }

    public function testExecuteWhenActionNameIsNotCheckoutCartItem()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getFullActionName')->will($this->returnValue('some value'));
        $this->cartMock->expects($this->never())->method('getFailedItems');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteWithEmptyAffectedItems()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())
            ->method('getFullActionName')->will($this->returnValue('checkout_cart_index'));
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue([]));
        $this->itemProcessorMock->expects($this->never())->method('process');

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())
            ->method('getFullActionName')->will($this->returnValue('checkout_cart_index'));
        $this->cartMock->expects($this->once())->method('getFailedItems')->will($this->returnValue(['not empty']));
        $this->itemProcessorMock->expects($this->once())->method('process');

        $this->model->execute($this->observerMock);
    }
}
