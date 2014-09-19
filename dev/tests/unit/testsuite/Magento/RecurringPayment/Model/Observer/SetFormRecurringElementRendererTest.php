<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\RecurringPayment\Model\Observer;
 
class SetFormRecurringElementRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  SetFormRecurringElementRenderer
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $elementFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutInterfaceElementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $recurringPaymentBlockMock;
    
    protected function setUp()
    {
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->eventMock =
            $this->getMock('Magento\Framework\Event', ['getForm', 'getLayout', '__wakeup'], [], '', false);
        $this->elementFactoryMock =
            $this->getMock('Magento\Framework\Data\Form\Element\Factory', ['setRenderer', '__wakeup'], [], '', false);
        $this->formMock = $this->getMock('Magento\Framework\Data\Form', ['getElement', '__wakeup'], [], '', false);
        $this->layoutInterfaceElementMock = $this->getMock('Magento\Framework\View\LayoutInterface');
        $this->recurringPaymentBlockMock =
            $this->getMock(
                'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring',
                [],
                [],
                '',
                false);

        $this->model = new SetFormRecurringElementRenderer();
    }

    public function testExecuteWithoutRecurringPaymentElement()
    {
        $this->observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getForm')->will($this->returnValue($this->formMock));
        $this->formMock->expects($this->once())
            ->method('getElement')->with('recurring_payment')->will($this->returnValue(null));
        $this->eventMock->expects($this->once())->method('getLayout')
            ->will($this->returnValue($this->layoutInterfaceElementMock));
        $this->layoutInterfaceElementMock->expects($this->once())
            ->method('createBlock')
            ->with('Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring')
            ->will($this->returnValue($this->recurringPaymentBlockMock));
        $this->elementFactoryMock->expects($this->never())->method('setRenderer');

        $this->model->execute($this->observerMock);
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())->method('getForm')->will($this->returnValue($this->formMock));
        $this->formMock->expects($this->once())
            ->method('getElement')
            ->with('recurring_payment')
            ->will($this->returnValue($this->elementFactoryMock));
        $this->eventMock->expects($this->once())
            ->method('getLayout')->will($this->returnValue($this->layoutInterfaceElementMock));
        $this->layoutInterfaceElementMock->expects($this->once())
            ->method('createBlock')
            ->with('Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring')
            ->will($this->returnValue($this->recurringPaymentBlockMock));
        $this->elementFactoryMock->expects($this->once())
            ->method('setRenderer')->with($this->recurringPaymentBlockMock);

        $this->model->execute($this->observerMock);
    }
}
