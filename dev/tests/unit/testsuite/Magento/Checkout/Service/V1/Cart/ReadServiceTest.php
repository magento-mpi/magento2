<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $totalsBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    protected function setUp()
    {
        $this->quoteFactoryMock =
            $this->getMock('\Magento\Sales\Model\QuoteFactory', ['create'], [], '', false);
        $this->quoteCollectionMock =
            $this->getMock('\Magento\Sales\Model\Resource\Quote\Collection', [], [], '', false);
        $this->cartBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\CartBuilder', [], [], '', false);
        $this->searchResultsBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\CartSearchResultsBuilder', [], [], '', false);
        $this->totalsBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\TotalsBuilder', [], [], '', false);
        $this->customerBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\CustomerBuilder', [], [], '', false);
        
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        

        $this->service = new ReadService(
            $this->quoteFactoryMock,
            $this->quoteCollectionMock,
            $this->cartBuilderMock,
            $this->searchResultsBuilderMock,
            $this->totalsBuilderMock,
            $this->customerBuilderMock
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage There is no cart with provided ID.
     */
    public function testGetCartWithNoSuchEntityException()
    {
        $cartId = 12;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('load')->with($cartId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('getId')->will($this->returnValue(13));
        $this->cartBuilderMock->expects($this->never())->method('populateWithArray');

        $this->service->getCart($cartId);
    }

    public function testGetCartSuccess()
    {
        $cartId = 12;
        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())
            ->method('load')->with($cartId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->any())->method('getId')->will($this->returnValue($cartId));
        $this->cartBuilderMock->expects($this->once())->method('populateWithArray');
        $this->totalsBuilderMock->expects($this->once())->method('populateWithArray');
        $this->customerBuilderMock->expects($this->once())->method('populateWithArray');
        $this->cartBuilderMock->expects($this->once())->method('setCustomer');
        $this->cartBuilderMock->expects($this->once())->method('setTotals');
        $this->cartBuilderMock->expects($this->once())->method('create');

        $this->service->getCart($cartId);
    }

//    public function testGetCartList1()
//    {
//        $searchCriteriaMock = $this->getMock('\Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
//
//        $this->searchResultsBuilderMock->expects($this->once())->method('setSearchCriteria')->will($this->returnValue($searchCriteriaMock));
//
//        $filterGroupMock = $this->getMock('\Magento\Framework\Service\V1\Data\Search\FilterGroup', [], [], '', false);
//        $searchCriteriaMock
//            ->expects($this->any())
//            ->method('getFilterGroups')
//            ->will($this->returnValue([$filterGroupMock]));
//        $filterMock = $this->getMock('\Magento\Framework\Service\V1\Data\Filter', [], [], '', false);
//        $filterGroupMock->expects($this->any())->method('getFilters')->will($this->returnValue([$filterMock]));
//
//
//
//
//        $this->service->getCartList($searchCriteriaMock);
//    }
}
 