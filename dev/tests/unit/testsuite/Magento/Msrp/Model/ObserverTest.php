<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Model;

use Magento\Framework\Object;
use Magento\TestFramework\Helper\ObjectManager;
use Magento\Sales\Model\Quote\Address;

/**
 * Tests Magento\Msrp\Model\Observer
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Msrp\Model\Observer
     */
    protected $observer;

    /**
     * @var \Magento\Msrp\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockBuilder('Magento\Msrp\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->observer = (new ObjectManager($this))->getObject(
            'Magento\Msrp\Model\Observer',
            ['config' => $this->configMock]
        );
    }

    /**
     * @param bool $isMsrpEnabled
     * @param bool $canApplyMsrp
     * @dataProvider setQuoteCanApplyMsrpDataProvider
     */
    public function testSetQuoteCanApplyMsrp($isMsrpEnabled, $canApplyMsrp)
    {
        $eventMock = $this->getMockBuilder('Magento\Framework\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote'])
            ->getMock();
        $quoteMock = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'setCanApplyMsrp', 'getAllAddresses'])
            ->getMock();
        $observerMock = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));
        $eventMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));
        $this->configMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue($isMsrpEnabled));
        $quoteMock->expects($this->once())
            ->method('setCanApplyMsrp')
            ->with($canApplyMsrp);
        $addressMock1 = $this->getMockBuilder('Magento\Customer\Model\Address\AbstractAddress')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMockForAbstractClass();
        $addressMock1->setCanApplyMsrp($canApplyMsrp);
        $addressMock2 = $this->getMockBuilder('Magento\Customer\Model\Address\AbstractAddress')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMockForAbstractClass();
        $addressMock2->setCanApplyMsrp(false);
        $quoteMock->expects($this->any())
            ->method('getAllAddresses')
            ->will($this->returnValue([$addressMock1, $addressMock2]));
        $this->observer->setQuoteCanApplyMsrp($observerMock);
    }

    public function setQuoteCanApplyMsrpDataProvider()
    {
        return [
            [false, false],
            [true, true],
            [true, false]
        ];
    }
}
