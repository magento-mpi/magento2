<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Session;

/**
 * Class QuoteTest
 */
class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Sales\Model\QuoteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteRepositoryMock;

    /**
     * @var \Magento\Backend\Model\Session\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quote;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupManagementMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->customerRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Customer\Api\CustomerRepositoryInterface',
            [],
            '',
            false,
            true,
            true,
            ['getCustomer']
        );
        $this->groupManagementMock = $this->getMockForAbstractClass(
            'Magento\Customer\Api\GroupManagementInterface',
            [],
            '',
            false,
            true,
            true,
            ['getDefaultGroup']
        );

        $this->scopeConfigMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            [],
            '',
            false,
            true,
            true,
            ['getValue']
        );
        $this->quoteRepositoryMock = $this->getMock(
            'Magento\Sales\Model\QuoteRepository',
            ['create', 'save', 'get'],
            [],
            '',
            false
        );

        $requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $sidResolverMock = $this->getMockForAbstractClass('Magento\Framework\Session\SidResolverInterface');
        $sessionConfigMock = $this->getMockForAbstractClass('Magento\Framework\Session\Config\ConfigInterface');
        $saveHandlerMock = $this->getMockForAbstractClass('Magento\Framework\Session\SaveHandlerInterface');
        $validatorMock = $this->getMockForAbstractClass('Magento\Framework\Session\ValidatorInterface');
        $storageMock = $this->getMockForAbstractClass('Magento\Framework\Session\StorageInterface');
        $cookieManagerMock = $this->getMock('Magento\Framework\Stdlib\CookieManagerInterface');
        $cookieMetadataFactoryMock = $this->getMock(
            'Magento\Framework\Stdlib\Cookie\CookieMetadataFactory',
            [],
            [],
            '',
            false
        );
        $orderFactoryMock = $this->getMock('Magento\Sales\Model\OrderFactory', [], [], '', false);
        $storeManagerMock = $this->getMockForAbstractClass('Magento\Framework\StoreManagerInterface');

        $this->quote = $this->getMock(
            'Magento\Backend\Model\Session\Quote',
            ['getStoreId', 'getQuoteId', 'setQuoteId', 'hasCustomerId', 'getCustomerId'],
            [
                'request' => $requestMock,
                'sidResolver' => $sidResolverMock,
                'sessionConfig' => $sessionConfigMock,
                'saveHandler' => $saveHandlerMock,
                'validator' => $validatorMock,
                'storage' => $storageMock,
                'cookieManager' => $cookieManagerMock,
                'cookieMetadataFactory' => $cookieMetadataFactoryMock,
                'quoteRepository' => $this->quoteRepositoryMock,
                'customerRepository' => $this->customerRepositoryMock,
                'orderFactory' => $orderFactoryMock,
                'storeManager' => $storeManagerMock,
                'groupManagement' => $this->groupManagementMock
            ],
            '',
            true
        );
    }

    /**
     * Run test getQuote method
     *
     * @return void
     */
    public function testGetQuote()
    {
        $storeId = 10;
        $quoteId = 22;
        $customerGroupId = 77;
        $customerId = 66;

        $quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            [
                'setStoreId',
                'setCustomerGroupId',
                'setIsActive',
                'getId',
                'assignCustomer',
                'setIgnoreOldQty',
                'setIsSuperMode',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $defaultGroup = $this->getMockBuilder('Magento\Customer\Api\Data\GroupInterface')
            ->getMock();
        $defaultGroup->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($customerGroupId));
        $this->groupManagementMock->expects($this->any())
            ->method('getDefaultGroup')
            ->will($this->returnValue($defaultGroup));

        $this->quoteRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($quoteMock));
        $this->quote->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $quoteMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId);
        $this->quote->expects($this->any())
            ->method('getQuoteId')
            ->will($this->returnValue(null));
        $quoteMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->will($this->returnSelf());
        $quoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(false)
            ->will($this->returnSelf());
        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quoteMock);
        $quoteMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($quoteId));
        $this->quote->expects($this->any())
            ->method('setQuoteId')
            ->with($quoteId);
        $this->quote->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->will($this->returnValue('customer-result'));
        $quoteMock->expects($this->once())
            ->method('assignCustomer')
            ->with('customer-result');
        $quoteMock->expects($this->once())
            ->method('setIgnoreOldQty')
            ->with(true);
        $quoteMock->expects($this->once())
            ->method('setIsSuperMode')
            ->with(true);

        $this->assertEquals($quoteMock, $this->quote->getQuote());
    }

    /**
     * Run test getQuote method
     *
     * @return void
     */
    public function testGetQuoteGet()
    {
        $storeId = 10;
        $quoteId = 22;
        $customerId = 66;

        $quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            [
                'setStoreId',
                'setCustomerGroupId',
                'setIsActive',
                'getId',
                'assignCustomer',
                'setIgnoreOldQty',
                'setIsSuperMode',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $this->quoteRepositoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($quoteMock));
        $this->quote->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $quoteMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId);
        $this->quote->expects($this->any())
            ->method('getQuoteId')
            ->will($this->returnValue($quoteId));
        $this->quoteRepositoryMock->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);
        $this->quote->expects($this->any())
            ->method('setQuoteId')
            ->with($quoteId);
        $this->quote->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->will($this->returnValue('customer-result'));
        $quoteMock->expects($this->once())
            ->method('assignCustomer')
            ->with('customer-result');
        $quoteMock->expects($this->once())
            ->method('setIgnoreOldQty')
            ->with(true);
        $quoteMock->expects($this->once())
            ->method('setIsSuperMode')
            ->with(true);

        $this->assertEquals($quoteMock, $this->quote->getQuote());
    }
}
