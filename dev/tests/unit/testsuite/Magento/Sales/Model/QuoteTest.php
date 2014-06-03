<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Sales\Model\Quote\Address;

/**
 * Test class for \Magento\Sales\Model\Order
 */
class QuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressFactoryMock;

    /**
     * @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \Magento\Sales\Model\Resource\Quote\Address\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressCollectionMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $quote;

    protected function setUp()
    {
        $this->quoteAddressFactoryMock = $this->getMock(
            'Magento\Sales\Model\Quote\AddressFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->quoteAddressMock = $this->getMock('Magento\Sales\Model\Quote\Address', array(), array(), '', false);
        $methods = array('isDeleted', 'setQuoteFilter', 'getIterator', 'validateMinimumAmount');
        $this->quoteAddressCollectionMock = $this->getMock(
            'Magento\Sales\Model\Resource\Quote\Address\Collection',
            $methods,
            array(),
            '',
            false
        );

        $this->quoteAddressFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->quoteAddressMock)
        );
        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getCollection'
        )->will(
            $this->returnValue($this->quoteAddressCollectionMock)
        );

        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->quote = (new ObjectManager(
            $this
        ))->getObject(
                'Magento\Sales\Model\Quote',
                array('quoteAddressFactory' => $this->quoteAddressFactoryMock,
                      'storeManager' => $this->storeManagerMock,
                      'scopeConfig' => $this->scopeConfigMock)
            );
    }

    /**
     * @param array $addresses
     * @param bool $expected
     * @dataProvider dataProviderForTestIsMultipleShippingAddresses
     */
    public function testIsMultipleShippingAddresses($addresses, $expected)
    {
        $this->quoteAddressCollectionMock->expects(
            $this->any()
        )->method(
            'setQuoteFilter'
        )->will(
            $this->returnValue($this->quoteAddressCollectionMock)
        );
        $this->quoteAddressCollectionMock->expects(
            $this->once()
        )->method(
            'getIterator'
        )->will(
            $this->returnValue(new \ArrayIterator($addresses))
        );

        $this->assertEquals($expected, $this->quote->isMultipleShippingAddresses());
    }

    /**
     * Customer group ID is not set to quote object and customer data is not available.
     */
    public function testGetCustomerGroupIdNotSet()
    {
        $this->assertEquals(
            \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
            $this->quote->getCustomerGroupId(),
            "Customer group ID is invalid"
        );
    }

    /**
     * Customer group ID is set to quote object.
     */
    public function testGetCustomerGroupId()
    {
        /** Preconditions */
        $customerGroupId = 33;
        $this->quote->setCustomerGroupId($customerGroupId);

        /** SUT execution */
        $this->assertEquals($customerGroupId, $this->quote->getCustomerGroupId(), "Customer group ID is invalid");
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsMultipleShippingAddresses()
    {
        return array(
            array(
                array($this->getAddressMock(Address::TYPE_SHIPPING), $this->getAddressMock(Address::TYPE_SHIPPING)),
                true
            ),
            array(
                array($this->getAddressMock(Address::TYPE_SHIPPING), $this->getAddressMock(Address::TYPE_BILLING)),
                false
            )
        );
    }

    /**
     * @param string $type One of \Magento\Customer\Model\Address\AbstractAddress::TYPE_ const
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAddressMock($type)
    {
        $shippingAddressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            array('getAddressType', '__wakeup'),
            array(),
            '',
            false
        );

        $shippingAddressMock->expects($this->any())->method('getAddressType')->will($this->returnValue($type));
        $shippingAddressMock->expects($this->any())->method('isDeleted')->will($this->returnValue(false));
        return $shippingAddressMock;
    }

    public function testValidateMinimumAmount()
    {

        $this->storeMock->expects($this->any())->method('getId')->will($this->returnValue(false));
        $valueMap = array(
            array('sales/minimum_order/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, false, true),
            array('sales/minimum_order/multi_address',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, false, true)
        );
        $this->scopeConfigMock->expects($this->any())->method('isSetFlag')->will($this->returnValueMap($valueMap));
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('sales/minimum_order/amount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, false)
            ->will($this->returnValue(150));
        $this->quoteAddressCollectionMock
            ->expects($this->any())
            ->method('setQuoteFilter')
            ->will($this->returnValue(array($this->quoteAddressCollectionMock)));
        $this->quoteAddressCollectionMock->expects($this->never())->method('validateMinimumAmount');
        $this->assertEquals(true, $this->quote->validateMinimumAmount(true));
    }
}
