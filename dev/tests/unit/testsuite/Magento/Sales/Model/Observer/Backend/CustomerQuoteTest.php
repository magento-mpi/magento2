<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

class CustomerQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Backend\CustomerQuote
     */
    protected $customerQuote;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Model\Config\Share
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\\Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Event\Observer
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Event
     */
    protected $eventMock;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder('Magento\Customer\Model\Config\Share')
            ->disableOriginalConstructor()
            ->getMock();
        $this->quoteFactoryMock = $this->getMockBuilder('\Magento\Sales\Model\QuoteFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->observerMock = $this->getMockBuilder('Magento\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventMock = $this->getMockBuilder('Magento\Event')
            ->disableOriginalConstructor()
            ->setMethods(['getOrigCustomerDataObject', 'getCustomerDataObject'])
            ->getMock();
        $this->observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($this->eventMock));
        $this->customerQuote = new \Magento\Sales\Model\Observer\Backend\CustomerQuote(
            $this->storeManagerMock,
            $this->configMock,
            $this->quoteFactoryMock
        );
    }

    public function testDispatchNoCustomerGroupChange()
    {
        $customerDataObjectMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerDataObjectMock->expects($this->any())
            ->method('getGroupId')
            ->will($this->returnValue(1));
        $origCustomerDataObjectMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $origCustomerDataObjectMock->expects($this->any())
            ->method('getGroupId')
            ->will($this->returnValue(1));
        $this->eventMock->expects($this->any())
            ->method('getCustomerDataObject')
            ->will($this->returnValue($customerDataObjectMock));
        $this->eventMock->expects($this->any())
            ->method('getOrigCustomerDataObject')
            ->will($this->returnValue($origCustomerDataObjectMock));
        $this->quoteFactoryMock->expects($this->never())
            ->method('create');

        $this->customerQuote->dispatch($this->observerMock);
    }

    /**
     * @param bool $isWebsiteScope
     * @param array $websites
     * @param int $quoteId
     * @dataProvider dispatchDataProvider
     */
    public function testDispatch($isWebsiteScope,$websites, $quoteId)
    {
        $this->configMock->expects($this->once())
            ->method('isWebsiteScope')
            ->will($this->returnValue($isWebsiteScope));
        $customerDataObjectMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $customerDataObjectMock->expects($this->any())
            ->method('getGroupId')
            ->will($this->returnValue(1));
        $customerDataObjectMock->expects($this->any())
            ->method('getWebsiteId')
            ->will($this->returnValue(2));
        if ($isWebsiteScope) {
            $websites = $websites[0];
            $this->storeManagerMock->expects($this->once())
                ->method('getWebsite')
                ->with(2)
                ->will($this->returnValue($websites));
        } else {
            $this->storeManagerMock->expects($this->once())
                ->method('getWebsites')
                ->will($this->returnValue($websites));
        }
        $origCustomerDataObjectMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $origCustomerDataObjectMock->expects($this->any())
            ->method('getGroupId')
            ->will($this->returnValue(2));
        $this->eventMock->expects($this->any())
            ->method('getCustomerDataObject')
            ->will($this->returnValue($customerDataObjectMock));
        $this->eventMock->expects($this->any())
            ->method('getOrigCustomerDataObject')
            ->will($this->returnValue($origCustomerDataObjectMock));
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote $quoteMock */
        $quoteMock = $this->getMockBuilder(
            'Magento\Sales\Model\Quote'
        )->setMethods(
                array(
                    'setWebsite',
                    'loadByCustomer',
                    'getId',
                    'setCustomerGroupId',
                    'collectTotals',
                    'save',
                    '__wakeup'
                )
            )->disableOriginalConstructor(
            )->getMock();
        $websiteCount = count($websites);
        $this->quoteFactoryMock->expects($this->exactly($websiteCount))
            ->method('create')
            ->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->exactly($websiteCount))
            ->method('setWebsite');
        $quoteMock->expects($this->exactly($websiteCount))
            ->method('loadByCustomer');
        $quoteMock->expects($this->exactly($websiteCount))
            ->method('getId')
            ->will($this->returnValue($quoteId));
        if ($quoteId) {
            $quoteMock->expects($this->exactly($websiteCount))
                ->method('setCustomerGroupId');
            $quoteMock->expects($this->exactly($websiteCount))
                ->method('collectTotals');
            $quoteMock->expects($this->exactly($websiteCount))
                ->method('save');
        } else {
            $quoteMock->expects($this->never())
                ->method('setCustomerGroupId');
            $quoteMock->expects($this->never())
                ->method('collectTotals');
            $quoteMock->expects($this->never())
                ->method('save');
        }
        $this->customerQuote->dispatch($this->observerMock);
    }

    public function dispatchDataProvider()
    {
        return [
            [true, ['website1'], 3],
            [true, ['website1', 'website2'], 3],
            [false, ['website1'], 3],
            [false, ['website1', 'website2'], 3],
            [true, ['website1'], null],
            [true, ['website1', 'website2'], null],
            [false, ['website1'], null],
            [false, ['website1', 'website2'], null],
        ];
    }
}
