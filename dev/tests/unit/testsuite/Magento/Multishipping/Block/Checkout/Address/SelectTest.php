<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Multishipping\Block\Checkout\Address;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Multishipping\Block\Checkout\Address\Select
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $multishippingMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->multishippingMock =
            $this->getMock('Magento\Multishipping\Model\Checkout\Type\Multishipping', [], [], '', false);
        $this->addressMock = $this->getMock('Magento\Customer\Api\Data\AddressInterface');
        $this->customerMock = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $this->filterBuilderMock = $this->getMock('Magento\Framework\Api\FilterBuilder', [], [], '', false);
        $this->searchCriteriaBuilderMock =
            $this->getMock('Magento\Framework\Api\SearchCriteriaBuilder', [], [], '', false);
        $this->addressRepositoryMock = $this->getMock('Magento\Customer\Api\AddressRepositoryInterface');
        $this->filterMock = $this->getMock('Magento\Framework\Api\Filter', [], [], '', false);
        $this->searchCriteriaMock = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $this->block = $this->objectManager->getObject('Magento\Multishipping\Block\Checkout\Address\Select', [
                'multishipping' => $this->multishippingMock,
                'addressRepository' => $this->addressRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock
            ]
        );
    }

    /**
     * @param string $id
     * @param bool $expectedValue
     * @dataProvider isDefaultAddressDataProvider
     */
    public function testIsAddressDefaultBilling($id, $expectedValue)
    {
        $this->addressMock->expects($this->once())->method('getId')->willReturn(1);
        $this->multishippingMock->expects($this->once())->method('getCustomer')->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())->method('getDefaultBilling')->willReturn($id);
        $this->assertEquals($expectedValue, $this->block->isAddressDefaultBilling($this->addressMock));
    }

    /**
     * @param string $id
     * @param bool $expectedValue
     * @dataProvider isDefaultAddressDataProvider
     */
    public function testIsAddressDefaultShipping($id, $expectedValue)
    {
        $this->addressMock->expects($this->once())->method('getId')->willReturn(1);
        $this->multishippingMock->expects($this->once())->method('getCustomer')->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())->method('getDefaultShipping')->willReturn($id);
        $this->assertEquals($expectedValue, $this->block->isAddressDefaultShipping($this->addressMock));
    }

    public function isDefaultAddressDataProvider()
    {
        return [
            'yes' => [1, true],
            'no' => [2, false],
        ];
    }

    public function testGetAddress()
    {
        $searchResultMock = $this->getMock('Magento\Customer\Api\Data\AddressSearchResultsInterface');
        $this->multishippingMock->expects($this->once())->method('getCustomer')->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())->method('getId')->willReturn(1);
        $this->filterBuilderMock->expects($this->once())->method('setField')->with('parent_id')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setValue')->with(1)->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setConditionType')->with('eq')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('create')->willReturn($this->filterMock);
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')
            ->with([$this->filterMock])
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->addressRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())->method('getItems')->willReturn([$this->addressMock]);
        $this->assertEquals([$this->addressMock], $this->block->getAddress());
        $this->assertEquals([$this->addressMock], $this->block->getData('address_collection'));
    }

    public function testGetAlreadyExistingAddress()
    {
        $this->block = $this->objectManager->getObject('Magento\Multishipping\Block\Checkout\Address\Select', [
                'addressRepository' => $this->addressRepositoryMock,
                'filterBuilder' => $this->filterBuilderMock,
                'data' => [
                    'address_collection' => [$this->addressMock
                    ]
                ]
            ]
        );
        $this->filterBuilderMock->expects($this->never())->method('setField');
        $this->addressRepositoryMock
            ->expects($this->never())
            ->method('getList');
        $this->assertEquals([$this->addressMock], $this->block->getAddress());
    }

    public function testGetAddressWhenItNotExistInCustomer()
    {
        $searchResultMock = $this->getMock('Magento\Customer\Api\Data\AddressSearchResultsInterface');
        $this->multishippingMock->expects($this->once())->method('getCustomer')->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())->method('getId')->willReturn(1);
        $this->filterBuilderMock->expects($this->once())->method('setField')->with('parent_id')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setValue')->with(1)->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setConditionType')->with('eq')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('create')->willReturn($this->filterMock);
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')
            ->with([$this->filterMock])
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);
        $this->addressRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())->method('getItems')->willThrowException(new NoSuchEntityException());
        $this->assertEquals([], $this->block->getAddress());
    }
}
