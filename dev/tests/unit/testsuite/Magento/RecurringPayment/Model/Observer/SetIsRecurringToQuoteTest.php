<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\RecurringPayment\Model\Observer;
 
class SetIsRecurringToQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SetIsRecurringToQuote
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventMock;
    
    protected function setUp()
    {
        $this->observerMock = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $this->quoteItemMock =
            $this->getMock('Magento\Sales\Model\Quote\Item', ['setIsRecurring', '__wakeup'], [], '', false);
        $this->eventMock =
            $this->getMock('Magento\Framework\Event', ['getQuoteItem', 'getProduct', '__wakeup'], [], '', false);
        $this->productMock =
            $this->getMock('Magento\Catalog\Model\Product', ['getIsRecurring', '__wakeup'], [], '', false);

        $this->model = new SetIsRecurringToQuote();
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->exactly(2))
            ->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->eventMock->expects($this->once())
            ->method('getQuoteItem')->will($this->returnValue($this->quoteItemMock));
        $this->eventMock->expects($this->once())->method('getProduct')->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->once())->method('getIsRecurring')->will($this->returnValue(true));
        $this->quoteItemMock->expects($this->once())->method('setIsRecurring')->with(true);

        $this->model->execute($this->observerMock);
    }
}
