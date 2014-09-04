<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Model;

use Magento\TestFramework\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Wishlist\Model\Observer */
    protected $observerModel;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->observerModel = $objectManager->getObject('Magento\Wishlist\Model\Observer');
    }

    public function testProcessCartUpdateBefore()
    {
        $quoteMock = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $cartMock = $this->getMockBuilder('Magento\Checkout\Model\Cart')->setMethods(['getQuote'])
            ->disableOriginalConstructor()->getMock();
        $cartMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $infoDataMock = $this->getMock('Magento\Framework\Object', [], [], '', false);
        $observer = $this->getMockBuilder(
            'Magento\Framework\Event\Observer'
        )->setMethods(['getEvent', 'getCart', 'getInfo'])
            ->disableOriginalConstructor()->getMock();
        $observer->expects($this->exactly(2))->method('getEvent')->will($this->returnSelf());
        $observer->expects($this->once())->method('getInfo')->will($this->returnValue($infoDataMock));
        $observer->expects($this->once())->method('getCart')->will($this->returnValue($cartMock));
        $result = $this->observerModel->processCartUpdateBefore($observer);
        $this->assertInstanceOf('Magento\Wishlist\Model\Observer', $result);
    }
}
