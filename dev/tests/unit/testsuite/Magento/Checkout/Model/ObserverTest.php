<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model;

use Magento\TestFramework\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var Observer */
    protected $object;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $checkoutSession;

    /** @var \Magento\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $messageManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->checkoutSession = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->messageManager = $this->getMock('Magento\Message\ManagerInterface', [], [], '', false);
        $this->object = $this->objectManager->getObject('Magento\Checkout\Model\Observer', [
            'checkoutSession' => $this->checkoutSession,
            'messageManager' => $this->messageManager,
        ]);
    }

    public function testUnsetAll()
    {
        $this->checkoutSession->expects($this->once())->method('clearQuote')->will($this->returnSelf());
        $this->checkoutSession->expects($this->once())->method('clearStorage')->will($this->returnSelf());

        $this->object->unsetAll();
    }

    public function testLoadCustomerQuoteThrowingCoreException()
    {
        $this->checkoutSession->expects($this->once())->method('loadCustomerQuote')->will(
            $this->throwException(new \Magento\Framework\Model\Exception('Message'))
        );
        $this->messageManager->expects($this->once())->method('addError')->with('Message');

        $this->object->loadCustomerQuote();
    }

    public function testLoadCustomerQuoteThrowingException()
    {
        $exception = new \Exception('Message');
        $this->checkoutSession->expects($this->once())->method('loadCustomerQuote')->will(
            $this->throwException($exception)
        );
        $this->messageManager->expects($this->once())->method('addException')
            ->with($exception, 'Load customer quote error');

        $this->object->loadCustomerQuote();
    }

    public function testSalesQuoteSaveAfter()
    {
        $observer = $this->getMock('Magento\Framework\Event\Observer', [], [], '', false);
        $observer->expects($this->once())->method('getEvent')->will(
            $this->returnValue(new \Magento\Object(
                ['quote' => new \Magento\Object(['is_checkout_cart' => 1, 'id' => 7])]
            ))
        );
        $this->checkoutSession->expects($this->once())->method('getQuoteId')->with(7);

        $this->object->salesQuoteSaveAfter($observer);
    }
}
