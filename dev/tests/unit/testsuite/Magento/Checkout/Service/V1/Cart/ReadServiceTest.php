<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Cart;

use \Magento\Checkout\Service\V1\Data\Cart;
use \Magento\Checkout\Service\V1\Data\Cart\Totals;
use \Magento\Checkout\Service\V1\Data\Cart\Customer;
use \Magento\Framework\Service\V1\Data\SearchCriteria;
use \Magento\Checkout\Service\V1\Data\Cart\Currency;
use \Magento\Checkout\Service\V1\Data\Cart\Totals\Item as ItemTotals;

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
    protected $storeManagerMock;

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
    protected $currencyBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemTotalBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->quoteFactoryMock =
            $this->getMock('\Magento\Sales\Model\QuoteFactory', ['create'], [], '', false);
        $methods = [
            'getId', 'getStoreId', 'getCreatedAt', 'getUpdatedAt', 'getConvertedAt',
            'getIsActive', 'getIsVirtual', 'getItemsCount', 'getItemsQty', 'getCheckoutMethod', 'getReservedOrderId',
            'getOrigOrderId', 'getBaseGrandTotal', 'getBaseSubtotal', 'getSubtotal', 'getBaseSubtotalWithDiscount',
            'getSubtotalWithDiscount', 'getCustomerId', 'getCustomerEmail', 'getCustomerGroupId',
            'getCustomerTaxClassId', 'getCustomerPrefix', 'getCustomerFirstname', 'getCustomerMiddlename',
            'getCustomerLastname', 'getCustomerSuffix', 'getCustomerDob', 'getCustomerNote', 'getCustomerNoteNotify',
            'getCustomerIsGuest', 'getCustomerGender', 'getCustomerTaxvat', '__wakeup', 'load', 'getGrandTotal',
            'getGlobalCurrencyCode', 'getBaseCurrencyCode', 'getStoreCurrencyCode', 'getQuoteCurrencyCode',
            'getStoreToBaseRate', 'getStoreToQuoteRate', 'getBaseToGlobalRate', 'getBaseToQuoteRate', 'setStoreId',
            'getShippingAddress', 'getAllItems'
        ];
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote', $methods, [], '', false);
        $this->quoteCollectionMock = $objectManager->getCollectionMock(
            '\Magento\Sales\Model\Resource\Quote\Collection', [$this->quoteMock]);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->cartBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\CartBuilder', [], [], '', false);
        $this->searchResultsBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\CartSearchResultsBuilder', [], [], '', false);
        $this->totalsBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\TotalsBuilder', [], [], '', false);
        $this->customerBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\CustomerBuilder', [], [], '', false);
        $this->currencyBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\CurrencyBuilder', [], [], '', false);
        $this->itemTotalBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\Totals\ItemBuilder', [], [], '', false);

        $this->service = new ReadService(
            $this->quoteFactoryMock,
            $this->quoteCollectionMock,
            $this->storeManagerMock,
            $this->cartBuilderMock,
            $this->searchResultsBuilderMock,
            $this->totalsBuilderMock,
            $this->customerBuilderMock,
            $this->currencyBuilderMock,
            $this->itemTotalBuilderMock
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
        $this->currencyBuilderMock->expects($this->once())->method('populateWithArray');
        $this->cartBuilderMock->expects($this->once())->method('setCustomer');
        $this->cartBuilderMock->expects($this->once())->method('setTotals');
        $this->cartBuilderMock->expects($this->once())->method('setCurrency');
        $this->cartBuilderMock->expects($this->once())->method('create');

        $this->setCartTotalsExpectations();
        $this->setCartDataExpectations();
        $this->setCurrencyDataExpectations();
        $this->setCustomerDataExpectations();
        $this->setCartItemTotalsExpectations();

        $this->service->getCart($cartId);
    }

    /**
     * @param int $direction
     * @param string $expected
     * @dataProvider getCartListSuccessDataProvider
     */
    public function testGetCartListSuccess($direction, $expected)
    {
        $searchResult = $this->getMock('\Magento\Checkout\Service\V1\Data\CartSearchResults', [], [], '', false);
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        $customerMock = $this->getMock('Magento\Customer\Model\Customer', [], [], '', false);
        $totalMock = $this->getMock('Magento\Sales\Model\Order\Total', [], [], '', false);
        $cartMock = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
        $currencyMock = $this->getMock('Magento\Checkout\Service\V1\Data\Cart\Currency', [], [], '', false);
        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->will($this->returnValue($searchCriteriaMock));
        $filterGroupMock = $this->getMock('\Magento\Framework\Service\V1\Data\Search\FilterGroup', [], [], '', false);
        $searchCriteriaMock
            ->expects($this->any())
            ->method('getFilterGroups')
            ->will($this->returnValue([$filterGroupMock]));
        $filterMock = $this->getMock('\Magento\Framework\Service\V1\Data\Filter', [], [], '', false);
        $filterGroupMock->expects($this->any())->method('getFilters')->will($this->returnValue([$filterMock]));
        $filterMock->expects($this->once())->method('getField')->will($this->returnValue('store_id'));
        $filterMock->expects($this->any())->method('getConditionType')->will($this->returnValue('eq'));
        $filterMock->expects($this->once())->method('getValue')->will($this->returnValue('filter_value'));
        $this->quoteCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['store_id'], [0 => ['eq' => 'filter_value']]);

        $this->quoteCollectionMock->expects($this->once())->method('getSize')->will($this->returnValue(10));
        $this->searchResultsBuilderMock->expects($this->once())->method('setTotalCount')->with(10);

        $searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->will($this->returnValue(['id' => $direction]));
        $this->quoteCollectionMock->expects($this->once())->method('addOrder')->with('entity_id', $expected);
        $searchCriteriaMock->expects($this->once())->method('getCurrentPage')->will($this->returnValue(1));
        $searchCriteriaMock->expects($this->once())->method('getPageSize')->will($this->returnValue(10));

        $this->setCartTotalsExpectations();
        $this->setCartDataExpectations();
        $this->setCustomerDataExpectations();
        $this->setCurrencyDataExpectations();
        $this->setCartItemTotalsExpectations();

        $this->currencyBuilderMock->expects($this->once())->method('create')->will($this->returnValue($currencyMock));
        $this->cartBuilderMock->expects($this->once())->method('setCurrency')->with($currencyMock);

        $this->customerBuilderMock->expects($this->once())->method('create')->will($this->returnValue($customerMock));
        $this->cartBuilderMock->expects($this->once())->method('setCustomer')->with($customerMock);
        $this->totalsBuilderMock->expects($this->once())->method('create')->will($this->returnValue($totalMock));
        $this->cartBuilderMock->expects($this->once())->method('setTotals')->will($this->returnValue($totalMock));
        $this->cartBuilderMock->expects($this->once())->method('create')->will($this->returnValue($cartMock));
        $this->searchResultsBuilderMock->expects($this->once())->method('setItems')->with([$cartMock]);
        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($searchResult));
        $this->assertEquals($searchResult, $this->service->getCartList($searchCriteriaMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Field 'any_value' cannot be used for search.
     */
    public function testGetCartListWithNotExistingField()
    {
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->will($this->returnValue($searchCriteriaMock));

        $filterGroupMock = $this->getMock('\Magento\Framework\Service\V1\Data\Search\FilterGroup', [], [], '', false);
        $searchCriteriaMock
            ->expects($this->any())
            ->method('getFilterGroups')
            ->will($this->returnValue([$filterGroupMock]));
        $filterMock = $this->getMock('\Magento\Framework\Service\V1\Data\Filter', [], [], '', false);
        $filterGroupMock->expects($this->any())->method('getFilters')->will($this->returnValue([$filterMock]));
        $filterMock->expects($this->once())->method('getField')->will($this->returnValue('any_value'));
        $filterMock->expects($this->never())->method('getConditionType');
        $this->service->getCartList($searchCriteriaMock);
    }

    /**
     * @return array
     */
    public function getCartListSuccessDataProvider()
    {
        return [
            'asc' => [SearchCriteria::SORT_ASC, 'ASC'],
            'desc' => [SearchCriteria::SORT_DESC, 'DESC']
        ];
    }

    /**
     * Set expectations for cart general data processing
     */
    protected function setCartDataExpectations()
    {
        $this->quoteMock->expects($this->any())->method('getId')->will($this->returnValue(12));

        $expected = [
            Cart::ID => 12,
            Cart::STORE_ID => 1,
            Cart::CREATED_AT => '2014-04-02 12:28:50',
            Cart::UPDATED_AT => '2014-04-02 12:28:50',
            Cart::CONVERTED_AT => '2014-04-02 12:28:50',
            Cart::IS_ACTIVE => true,
            Cart::IS_VIRTUAL => false,
            Cart::ITEMS_COUNT => 10,
            Cart::ITEMS_QUANTITY => 15,
            Cart::CHECKOUT_METHOD => 'check mo',
            Cart::RESERVED_ORDER_ID => 'order_id',
            Cart::ORIG_ORDER_ID => 'orig_order_id'
        ];
        $expectedMethods = [
            'getStoreId' => 1,
            'getCreatedAt' => '2014-04-02 12:28:50',
            'getUpdatedAt' => '2014-04-02 12:28:50',
            'getConvertedAt' => '2014-04-02 12:28:50',
            'getIsActive' => true,
            'getIsVirtual' => false,
            'getItemsCount' => 10,
            'getItemsQty' => 15,
            'getCheckoutMethod' => 'check mo',
            'getReservedOrderId' => 'order_id',
            'getOrigOrderId' => 'orig_order_id'
        ];
        foreach ($expectedMethods as $method => $value) {
            $this->quoteMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }
        $this->cartBuilderMock->expects($this->once())->method('populateWithArray')->with($expected);
    }

    /**
     * Set expectations for totals processing
     */
    protected function setCartTotalsExpectations()
    {
        $methods = [
            'getDiscountAmount', 'getBaseDiscountAmount', 'getShippingAmount', 'getBaseShippingAmount',
            'getShippingDiscountAmount', 'getBaseShippingDiscountAmount', 'getTaxAmount', 'getBaseTaxAmount',
            'getShippingTaxAmount', 'getBaseShippingTaxAmount', 'getSubtotalInclTax', 'getBaseSubtotalTotalInclTax',
            'getShippingInclTax', 'getBaseShippingInclTax', 'getId'
        ];

        $shippingAddressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', $methods, [], '', false);
        $shippingAddressMock->expects($this->any())->method('getId')->will($this->returnValue('AddressId'));

        $this->quoteMock->expects($this->any())->method('getShippingAddress')
            ->will($this->returnValue($shippingAddressMock));

        $expected = [
            Totals::BASE_GRAND_TOTAL => 100,
            Totals::GRAND_TOTAL => 150,
            Totals::BASE_SUBTOTAL => 150,
            Totals::SUBTOTAL => 150,
            Totals::BASE_SUBTOTAL_WITH_DISCOUNT => 120,
            Totals::SUBTOTAL_WITH_DISCOUNT => 120,
            Totals::DISCOUNT_AMOUNT => 110,
            Totals::BASE_DISCOUNT_AMOUNT => 110,
            Totals::SHIPPING_AMOUNT => 20,
            Totals::BASE_SHIPPING_AMOUNT => 20,
            Totals::SHIPPING_DISCOUNT_AMOUNT => 5,
            Totals::BASE_SHIPPING_DISCOUNT_AMOUNT => 5,
            Totals::TAX_AMOUNT => 3,
            Totals::BASE_TAX_AMOUNT => 3,
            Totals::SHIPPING_TAX_AMOUNT => 1,
            Totals::BASE_SHIPPING_TAX_AMOUNT => 1,
            Totals::SUBTOTAL_INCL_TAX => 153,
            Totals::BASE_SUBTOTAL_INCL_TAX => 153,
            Totals::SHIPPING_INCL_TAX => 21,
            Totals::BASE_SHIPPING_INCL_TAX => 21,
            Totals::BASE_CURRENCY_CODE => 'EUR',
            Totals::QUOTE_CURRENCY_CODE => 'BR',
        ];
        $expectedQuoteMethods = [
            'getBaseGrandTotal' => 100,
            'getGrandTotal' => 150,
            'getBaseSubtotal' => 150,
            'getSubtotal' => 150,
            'getBaseSubtotalWithDiscount' => 120,
            'getSubtotalWithDiscount' => 120,
        ];

        $addressMethods = [
            'getDiscountAmount' => 110,
            'getBaseDiscountAmount' => 110,
            'getShippingAmount' => 20,
            'getBaseShippingAmount' => 20,
            'getShippingDiscountAmount' => 5,
            'getBaseShippingDiscountAmount' => 5,
            'getTaxAmount' => 3,
            'getBaseTaxAmount' => 3,
            'getShippingTaxAmount' => 1,
            'getBaseShippingTaxAmount' => 1,
            'getSubtotalInclTax' => 153,
            'getBaseSubtotalTotalInclTax' => 153,
            'getShippingInclTax' => 21,
            'getBaseShippingInclTax' => 21
        ];

        $this->quoteMock->expects($this->atLeastOnce())->method('getBaseCurrencyCode')->will($this->returnValue('EUR'));
        $this->quoteMock->expects($this->atLeastOnce())->method('getQuoteCurrencyCode')->will($this->returnValue('BR'));

        foreach ($expectedQuoteMethods as $method => $value) {
            $this->quoteMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }
        foreach ($addressMethods as $method => $value) {
            $shippingAddressMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }

        $this->totalsBuilderMock->expects($this->once())->method('populateWithArray')->with($expected)
            ->will($this->returnSelf());
    }

    /**
     * Set expectations for totals item data processing
     *
     * @return array
     */
    protected function setCartItemTotalsExpectations()
    {
        $itemMethods = [
            'getPrice', 'getBasePrice', 'getQty', 'getRowTotal', 'getBaseRowTotal', 'getRowTotalWithDiscount',
            'getTaxAmount', 'getBaseTaxAmount', 'getTaxPercent', 'getDiscountAmount', 'getBaseDiscountAmount',
            'getDiscountPercent', 'getPriceInclTax', 'getBasePriceInclTax', 'getRowTotalInclTax',
            'getBaseRowTotalInclTax'
        ];

        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', $itemMethods, [], '', false);
        $items = [$quoteItemMock];
        $this->quoteMock->expects($this->any())->method('getAllItems')->will($this->returnValue($items));

        $expected = [
            ItemTotals::PRICE => 100,
            ItemTotals::BASE_PRICE => 150,
            ItemTotals::QTY => 2,
            ItemTotals::ROW_TOTAL => 200,
            ItemTotals::BASE_ROW_TOTAL => 300,
            ItemTotals::ROW_TOTAL_WITH_DISCOUNT => 180,
            ItemTotals::TAX_AMOUNT => 20,
            ItemTotals::BASE_TAX_AMOUNT => 30,
            ItemTotals::TAX_PERCENT => 10,
            ItemTotals::DISCOUNT_AMOUNT => 1,
            ItemTotals::BASE_DISCOUNT_AMOUNT => 2,
            ItemTotals::DISCOUNT_PERCENT => 1,
            ItemTotals::PRICE_INCL_TAX => 120,
            ItemTotals::BASE_PRICE_INCL_TAX => 180,
            ItemTotals::ROW_TOTAL_INCL_TAX => 240,
            ItemTotals::BASE_ROW_TOTAL_INCL_TAX => 360,
        ];
        $expectedMethods = [
            'getPrice' => 100,
            'getBasePrice' => 150,
            'getQty' => 2,
            'getRowTotal' => 200,
            'getBaseRowTotal' => 300,
            'getRowTotalWithDiscount' => 180,
            'getTaxAmount' => 20,
            'getBaseTaxAmount' => 30,
            'getTaxPercent' => 10,
            'getDiscountAmount' => 1,
            'getBaseDiscountAmount' => 2,
            'getDiscountPercent' => 1,
            'getPriceInclTax' => 120,
            'getBasePriceInclTax' => 180,
            'getRowTotalInclTax' => 240,
            'getBaseRowTotalInclTax' => 360
        ];

        foreach ($expectedMethods as $method => $value) {
            $quoteItemMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }

        $this->itemTotalBuilderMock->expects($this->once())->method('populateWithArray')->with($expected)
            ->will($this->returnSelf());

        $this->itemTotalBuilderMock->expects($this->once())->method('create')->will($this->returnSelf());

    }

    /**
     * Set expectations for cart customer data processing
     */
    protected function setCustomerDataExpectations()
    {
        $expected = [
            Customer::ID => 10,
            Customer::EMAIL => 'customer@example.com',
            Customer::GROUP_ID => '4',
            Customer::TAX_CLASS_ID => 10,
            Customer::PREFIX => 'prefix_',
            Customer::FIRST_NAME => 'First Name',
            Customer::MIDDLE_NAME => 'Middle Name',
            Customer::LAST_NAME => 'Last Name',
            Customer::SUFFIX => 'suffix',
            Customer::DOB => '1/1/1989',
            Customer::NOTE => 'customer_note',
            Customer::NOTE_NOTIFY => 'note_notify',
            Customer::IS_GUEST => false,
            Customer::GENDER => 'male',
            Customer::TAXVAT => 'taxvat',
            ];
        $expectedMethods = [
            'getCustomerId' => 10,
            'getCustomerEmail' => 'customer@example.com',
            'getCustomerGroupId' => 4,
            'getCustomerTaxClassId' => 10,
            'getCustomerPrefix' => 'prefix_',
            'getCustomerFirstname' => 'First Name',
            'getCustomerMiddlename' => 'Middle Name',
            'getCustomerLastname' => 'Last Name',
            'getCustomerSuffix' => 'suffix',
            'getCustomerDob' => '1/1/1989',
            'getCustomerNote' => 'customer_note',
            'getCustomerNoteNotify' => 'note_notify',
            'getCustomerIsGuest' => false,
            'getCustomerGender' => 'male',
            'getCustomerTaxvat' => 'taxvat',
        ];
        foreach ($expectedMethods as $method => $value) {
            $this->quoteMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }
        $this->customerBuilderMock->expects($this->once())->method('populateWithArray')->with($expected);
    }

    /**
     * Set expectations for currency data processing
     */
    protected function setCurrencyDataExpectations()
    {
        $expected = [
            Currency::GLOBAL_CURRENCY_CODE => 'USD',
            Currency::BASE_CURRENCY_CODE => 'EUR',
            Currency::STORE_CURRENCY_CODE => 'USD',
            Currency::QUOTE_CURRENCY_CODE => 'BR',
            Currency::STORE_TO_BASE_RATE => 1,
            Currency::STORE_TO_QUOTE_RATE => 2,
            Currency::BASE_TO_GLOBAL_RATE => 3,
            Currency::BASE_TO_QUOTE_RATE => 4,
        ];
        $expectedMethods = [
            'getGlobalCurrencyCode' => 'USD',
            'getStoreCurrencyCode' => 'USD',
            'getStoreToBaseRate' => 1,
            'getStoreToQuoteRate' => 2,
            'getBaseToGlobalRate' => 3,
            'getBaseToQuoteRate' => 4,
        ];

        $this->quoteMock->expects($this->atLeastOnce())->method('getBaseCurrencyCode')->will($this->returnValue('EUR'));
        $this->quoteMock->expects($this->atLeastOnce())->method('getQuoteCurrencyCode')->will($this->returnValue('BR'));

        foreach ($expectedMethods as $method => $value) {
            $this->quoteMock->expects($this->once())->method($method)->will($this->returnValue($value));
        }
        $this->currencyBuilderMock->expects($this->once())->method('populateWithArray')->with($expected);
    }

    public function testGetTotals()
    {
        $cartId = 12;
        $storeId = 1;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('setStoreId')->with($storeId)->will($this->returnSelf());
        $this->quoteMock->expects($this->once())->method('load')->with($cartId)->will($this->returnSelf());

        $this->quoteMock->expects($this->any())->method('getId')->will($this->returnValue($cartId));
        $this->quoteMock->expects($this->any())->method('getIsActive')->will($this->returnValue(true));

        $this->setCartTotalsExpectations();
        $this->setCartItemTotalsExpectations();

        $this->service->getTotals($cartId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with cartId = 12
     */
    public function testGetTotalsWithNoSuchEntityException()
    {
        $cartId = 12;
        $storeId = 1;

        $storeMock = $this->getMock('\Magento\Store\Model\Store', ['getId'], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $this->quoteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('setStoreId')->with($storeId)->will($this->returnSelf());
        $this->quoteMock->expects($this->once())->method('load')->with($cartId)->will($this->returnSelf());

        $this->quoteMock->expects($this->any())->method('getId')->will($this->returnValue(false));

        $this->service->getTotals($cartId);
    }
}
