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
use Magento\Store\Model\ScopeInterface;

/**
 * Test class for \Magento\Sales\Model\Order
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Catalog\Model\Product |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Framework\Object\Factory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactoryMock;

    /**
     * @var \Magento\Sales\Model\Resource\Quote\Item\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemCollectionFactoryMock;

    /**
     * @var \Magento\Framework\App\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressRepositoryMock;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * @var \Magento\Framework\Api\FilterBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressBuilderMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilderMock;

    protected function setUp()
    {
        $this->quoteAddressFactoryMock = $this->getMock(
            'Magento\Sales\Model\Quote\AddressFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->quoteAddressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            [
                'isDeleted', 'getCollection', 'getId', 'getCustomerAddressId',
                '__wakeup', 'getAddressType', 'getDeleteImmediately', 'validateMinimumAmount'
            ],
            [],
            '',
            false
        );
        $this->quoteAddressCollectionMock = $this->getMock(
            'Magento\Sales\Model\Resource\Quote\Address\Collection',
            array(),
            array(),
            '',
            false
        );
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->objectFactoryMock = $this->getMock('\Magento\Framework\Object\Factory', ['create'], [], '', false);
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
        $this->eventManagerMock = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Quote')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder('Magento\Framework\Model\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerFactoryMock = $this->getMockBuilder('Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->addressConverterMock = $this->getMockBuilder('Magento\Customer\Model\Address\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->groupRepositoryMock = $this->getMockBuilder('Magento\Customer\Api\GroupRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($this->eventManagerMock));
        $this->quoteItemCollectionFactoryMock = $this->getMock(
            'Magento\Sales\Model\Resource\Quote\Item\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressRepositoryMock = $this->getMockForAbstractClass('Magento\Customer\Api\AddressRepositoryInterface',
            [],
            '',
            false
        );

        $this->criteriaBuilderMock = $this->getMockBuilder('Magento\Framework\Api\SearchCriteriaBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBuilderMock = $this->getMockBuilder('Magento\Framework\Api\FilterBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressBuilderMock = $this->getMockBuilder('Magento\Customer\Api\Data\AddressInterfaceBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['mergeDataObjectWithArray'])
            ->getMock();

        $this->customerBuilderMock = $this->getMockBuilder('Magento\Customer\Api\Data\CustomerInterfaceBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->quote = (
        new ObjectManager(
            $this
        )
        )->getObject(
                'Magento\Sales\Model\Quote',
                [
                    'quoteAddressFactory' => $this->quoteAddressFactoryMock,
                    'storeManager' => $this->storeManagerMock,
                    'resource' => $this->resourceMock,
                    'context' => $this->contextMock,
                    'customerFactory' => $this->customerFactoryMock,
                    'addressConverter' => $this->addressConverterMock,
                    'groupRepository' => $this->groupRepositoryMock,
                    'objectFactory' => $this->objectFactoryMock,
                    'addressRepository' => $this->addressRepositoryMock,
                    'criteriaBuilder' => $this->criteriaBuilderMock,
                    'filterBuilder' => $this->filterBuilderMock,
                    'addressBuilder' => $this->addressBuilderMock,
                    'customerBuilder' => $this->customerBuilderMock,
                    'quoteItemCollectionFactory' => $this->quoteItemCollectionFactoryMock,
                    'scopeConfig' => $this->scopeConfig
                ]
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

    public function testGetStoreIdNoId()
    {
        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $result = $this->quote->getStoreId();
        $this->assertNull($result);
    }

    public function testGetStoreId()
    {
        $storeId = 1;

        $result = $this->quote->setStoreId($storeId)->getStoreId();
        $this->assertEquals($storeId, $result);
    }

    public function testGetStore()
    {
        $storeId = 1;

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $this->quote->setStoreId($storeId);
        $result = $this->quote->getStore();
        $this->assertInstanceOf('Magento\Store\Model\Store', $result);
    }

    public function testSetStore()
    {
        $storeId = 1;

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $result = $this->quote->setStore($storeMock);
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    public function testGetSharedWebsiteStoreIds()
    {
        $sharedIds = null;
        $storeIds = [1, 2, 3];

        $websiteMock = $this->getMockBuilder('Magento\Store\Model\Website')
            ->disableOriginalConstructor()
            ->getMock();
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));

        $this->quote->setData('shared_store_ids', $sharedIds);
        $this->quote->setWebsite($websiteMock);
        $result = $this->quote->getSharedStoreIds();
        $this->assertEquals($storeIds, $result);
    }

    public function testGetSharedStoreIds()
    {
        $sharedIds = null;
        $storeIds = [1, 2, 3];
        $storeId = 1;

        $websiteMock = $this->getMockBuilder('Magento\Store\Model\Website')
            ->disableOriginalConstructor()
            ->getMock();
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->will($this->returnValue($storeIds));

        $storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getWebsite')
            ->will($this->returnValue($websiteMock));

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->will($this->returnValue($storeMock));

        $this->quote->setData('shared_store_ids', $sharedIds);
        $this->quote->setStoreId($storeId);
        $result = $this->quote->getSharedStoreIds();
        $this->assertEquals($storeIds, $result);
    }

    public function testLoadActive()
    {
        $quoteId = 1;

        $this->resourceMock->expects($this->once())
            ->method('loadActive')
            ->with($this->quote, $quoteId);

        $this->eventManagerMock->expects($this->any())
            ->method('dispatch');

        $result = $this->quote->loadActive($quoteId);
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    public function testloadByIdWithoutStore()
    {
        $quoteId = 1;

        $this->resourceMock->expects($this->once())
            ->method('loadByIdWithoutStore')
            ->with($this->quote, $quoteId);

        $this->eventManagerMock->expects($this->any())
            ->method('dispatch');

        $result = $this->quote->loadByIdWithoutStore($quoteId);
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    public function testSetCustomerAddressData()
    {
        $customerId = 1;

        $addressMock = $this->getMockBuilder('Magento\Customer\Api\Data\AddressInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $addresses = [$addressMock];


        $this->addressRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($addressMock);
        $customerMock = $this->getMockBuilder('Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($customerMock));
        $customerMock->expects($this->once())
            ->method('load')
            ->with($customerId);
        $customerMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(false));
        $this->quote->setCustomerId($customerId);
        $this->addressBuilderMock->expects($this->once())
            ->method('mergeDataObjectWithArray')
            ->with($addressMock, [\Magento\Customer\Api\Data\AddressInterface::CUSTOMER_ID => null]);

        $result = $this->quote->setCustomerAddressData($addresses);
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    public function testGetCustomerTaxClassId()
    {
        $groupId = 1;
        $taxClassId = 1;
        $groupMock = $this->getMockForAbstractClass('Magento\Customer\Api\Data\GroupInterface', [], '', false);
        $groupMock->expects($this->once())
            ->method('getTaxClassId')
            ->willReturn($taxClassId);
        $this->groupRepositoryMock->expects($this->once())
            ->method('get')
            ->with($groupId)
            ->will($this->returnValue($groupMock));
        $this->quote->setData('customer_group_id', $groupId);
        $result = $this->quote->getCustomerTaxClassId();
        $this->assertEquals($taxClassId, $result);
    }

    public function testGetAllAddresses()
    {
        $id = 1;
        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->once())
            ->method('isDeleted')
            ->will($this->returnValue(false));

        $iterator = new \ArrayIterator([$this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);
        $result = $this->quote->getAllAddresses();
        $this->assertEquals([$this->quoteAddressMock], $result);
    }

    /**
     * @dataProvider dataProviderGetAddress
     */
    public function testGetAddressById($addressId, $expected)
    {
        $id = 1;
        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $iterator = new \ArrayIterator([$this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);
        $result = $this->quote->getAddressById($addressId);

        $this->assertEquals((bool)$expected, (bool)$result);
    }

    public static function dataProviderGetAddress()
    {
        return [
            [1, true],
            [2, false]
        ];
    }

    /**
     * @param $isDeleted
     * @param $customerAddressId
     * @param $expected
     *
     * @dataProvider dataProviderGetAddressByCustomer
     */
    public function testGetAddressByCustomerAddressId($isDeleted, $customerAddressId, $expected)
    {
        $id = 1;
        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->once())
            ->method('isDeleted')
            ->will($this->returnValue($isDeleted));
        $this->quoteAddressMock->expects($this->once())
            ->method('getCustomerAddressId')
            ->will($this->returnValue($customerAddressId));

        $iterator = new \ArrayIterator([$this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);
        $result = $this->quote->getAddressByCustomerAddressId($id);

        $this->assertEquals((bool)$expected, (bool)$result);
    }

    public static function dataProviderGetAddressByCustomer()
    {
        return [
            [false, 1, true],
            [false, 2, false]
        ];
    }

    /**
     * @param $isDeleted
     * @param $addressType
     * @param $customerAddressId
     * @param $expected
     *
     * @dataProvider dataProviderShippingAddress
     */
    public function testGetShippingAddressByCustomerAddressId($isDeleted, $addressType, $customerAddressId, $expected)
    {
        $id = 1;

        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->once())
            ->method('isDeleted')
            ->will($this->returnValue($isDeleted));
        $this->quoteAddressMock->expects($this->once())
            ->method('getCustomerAddressId')
            ->will($this->returnValue($customerAddressId));
        $this->quoteAddressMock->expects($this->once())
            ->method('getAddressType')
            ->will($this->returnValue($addressType));

        $iterator = new \ArrayIterator([$this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);

        $result = $this->quote->getShippingAddressByCustomerAddressId($id);
        $this->assertEquals($expected, (bool)$result);
    }

    public static function dataProviderShippingAddress()
    {
        return [
            [false, \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING, 1, true],
            [false, \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING, 2, false],
        ];
    }

    public function testRemoveAddress()
    {
        $id = 1;

        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->once())
            ->method('isDeleted')
            ->with(true);
        $this->quoteAddressMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $iterator = new \ArrayIterator([$this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);

        $result = $this->quote->removeAddress($id);
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    public function testRemoveAllAddresses()
    {
        $id = 1;

        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->with($id)
            ->will($this->returnSelf());

        $this->quoteAddressMock->expects($this->any())
            ->method('getAddressType')
            ->will($this->returnValue(\Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING));
        $this->quoteAddressMock->expects($this->any())
            ->method('isDeleted')
            ->will($this->returnValue(false));
        $this->quoteAddressMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));
        $this->quoteAddressMock->expects($this->once())
            ->method('getDeleteImmediately')
            ->will($this->returnValue(true));

        $iterator = new \ArrayIterator([$id => $this->quoteAddressMock]);
        $this->quoteAddressCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));
        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('removeItemByKey')
            ->with($id)
            ->will($this->returnValue($iterator));

        $this->quote->setId($id);

        $result = $this->quote->removeAllAddresses();
        $this->assertInstanceOf('Magento\Sales\Model\Quote', $result);
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testAddProductException()
    {
        $this->quote->addProduct($this->productMock, 'test');
    }

    public function testAddProductNoCandidates()
    {
        $expectedResult = 'test_string';
        $requestMock = $this->getMock(
            '\Magento\Framework\Object'
        );
        $this->objectFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(['qty' => 1]))
            ->will($this->returnValue($requestMock));

        $typeInstanceMock = $this->getMock(
            'Magento\Catalog\Model\Product\Type\Simple',
            [
                'prepareForCartAdvanced'
            ],
            [],
            '',
            false
        );
        $typeInstanceMock->expects($this->once())
            ->method('prepareForCartAdvanced')
            ->will($this->returnValue($expectedResult));
        $this->productMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstanceMock));

        $result = $this->quote->addProduct($this->productMock, null);
        $this->assertEquals($expectedResult, $result);
    }


    public function testAddProductItemPreparation()
    {
        $itemMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Item',
            [],
            [],
            '',
            false
        );

        $expectedResult = $itemMock;
        $requestMock = $this->getMock(
            '\Magento\Framework\Object'
        );
        $this->objectFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo(['qty' => 1]))
            ->will($this->returnValue($requestMock));

        $typeInstanceMock = $this->getMock(
            'Magento\Catalog\Model\Product\Type\Simple',
            [
                'prepareForCartAdvanced'
            ],
            [],
            '',
            false
        );

        $productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            [
                'getParentProductId',
                'setStickWithinParent',
                '__wakeup'
            ],
            [],
            '',
            false
        );

        $collectionMock = $this->getMock(
            'Magento\Sales\Model\Resource\Quote\Item\Collection',
            [],
            [],
            '',
            false
        );


        $itemMock->expects($this->any())
            ->method('representProduct')
            ->will($this->returnValue(true));

        $iterator = new \ArrayIterator([$itemMock]);
        $collectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->quoteItemCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $typeInstanceMock->expects($this->once())
            ->method('prepareForCartAdvanced')
            ->will($this->returnValue([$productMock]));
        $this->productMock->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($typeInstanceMock));

        $result = $this->quote->addProduct($this->productMock, null);
        $this->assertEquals($expectedResult, $result);
    }

    public function testValidateMiniumumAmount()
    {
        $storeId = 1;
        $this->quote->setStoreId($storeId);

        $valueMap = [
            ['sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/multi_address', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/amount', ScopeInterface::SCOPE_STORE, $storeId, 20],
            ['sales/minimum_order/tax_including', ScopeInterface::SCOPE_STORE, $storeId, true]
        ];
        $this->scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->will($this->returnValueMap($valueMap));

        $this->quoteAddressMock->expects($this->once())
            ->method('validateMinimumAmount')
            ->willReturn(true);

        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->willReturn([$this->quoteAddressMock]);

        $this->assertTrue($this->quote->validateMinimumAmount());
    }

    public function testValidateMiniumumAmountNegative()
    {
        $storeId = 1;
        $this->quote->setStoreId($storeId);

        $valueMap = [
            ['sales/minimum_order/active', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/multi_address', ScopeInterface::SCOPE_STORE, $storeId, true],
            ['sales/minimum_order/amount', ScopeInterface::SCOPE_STORE, $storeId, 20],
            ['sales/minimum_order/tax_including', ScopeInterface::SCOPE_STORE, $storeId, true]
        ];
        $this->scopeConfig->expects($this->any())
            ->method('isSetFlag')
            ->will($this->returnValueMap($valueMap));

        $this->quoteAddressMock->expects($this->once())
            ->method('validateMinimumAmount')
            ->willReturn(false);

        $this->quoteAddressCollectionMock->expects($this->once())
            ->method('setQuoteFilter')
            ->willReturn([$this->quoteAddressMock]);

        $this->assertFalse($this->quote->validateMinimumAmount());
    }
}
