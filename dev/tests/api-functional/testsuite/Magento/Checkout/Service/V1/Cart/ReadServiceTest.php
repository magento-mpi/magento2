<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Cart;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use \Magento\Framework\Service\V1\Data\FilterBuilder;
use \Magento\TestFramework\ObjectManager;
use \Magento\Checkout\Service\V1\Data\Cart;
use \Magento\Framework\Service\V1\Data\SearchCriteria;
use \Magento\Checkout\Service\V1\Data\Cart\Totals;
use \Magento\Checkout\Service\V1\Data\Cart\Totals\Item as ItemTotals;

class ReadServiceTest extends WebapiAbstract
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->searchBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder'
        );
        $this->filterBuilder = $this->objectManager->create(
            'Magento\Framework\Service\V1\Data\FilterBuilder'
        );
    }

    /**
     * Retrieve quote by given reserved order ID
     *
     * @param string $reservedOrderId
     * @return \Magento\Sales\Model\Quote
     * @throws \InvalidArgumentException
     */
    protected function getCart($reservedOrderId)
    {
        /** @var $cart \Magento\Sales\Model\Quote */
        $cart = $this->objectManager->get('Magento\Sales\Model\Quote');
        $cart->load($reservedOrderId, 'reserved_order_id');
        if (!$cart->getId()) {
            throw new \InvalidArgumentException('There is no quote with provided reserved order ID.');
        }
        return $cart;
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     */
    public function testGetCart()
    {
        $cart = $this->getCart('test01');
        $cartId = $cart->getId();

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => 'checkoutCartReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartReadServiceV1GetCart',
            ),
        );

        $requestData = array('cartId' => $cartId);
        $cartData = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($cart->getId(), $cartData['id']);
        $this->assertEquals($cart->getCreatedAt(), $cartData['created_at']);
        $this->assertEquals($cart->getUpdatedAt(), $cartData['updated_at']);
        $this->assertEquals($cart->getStoreId(), $cartData['store_id']);
        $this->assertEquals($cart->getIsActive(), $cartData['is_active']);
        $this->assertEquals($cart->getIsVirtual(), $cartData['is_virtual']);
        $this->assertEquals($cart->getOrigOrderId(), $cartData['orig_order_id']);
        $this->assertEquals($cart->getItemsCount(), $cartData['items_count']);
        $this->assertEquals($cart->getItemsQty(), $cartData['items_qty']);

        $this->assertContains('customer', $cartData);
        $this->assertEquals(1, $cartData['customer']['is_guest']);
        $this->assertContains('totals', $cartData);
        $this->assertEquals($cart->getSubtotal(), $cartData['totals']['subtotal']);
        $this->assertEquals($cart->getGrandTotal(), $cartData['totals']['grand_total']);
        $this->assertContains('currency', $cartData);
        $this->assertEquals($cart->getGlobalCurrencyCode(), $cartData['currency']['global_currency_code']);
        $this->assertEquals($cart->getBaseCurrencyCode(), $cartData['currency']['base_currency_code']);
        $this->assertEquals($cart->getQuoteCurrencyCode(), $cartData['currency']['quote_currency_code']);
        $this->assertEquals($cart->getStoreCurrencyCode(), $cartData['currency']['store_currency_code']);
        $this->assertEquals($cart->getBaseToGlobalRate(), $cartData['currency']['base_to_global_rate']);
        $this->assertEquals($cart->getBaseToQuoteRate(), $cartData['currency']['base_to_quote_rate']);
        $this->assertEquals($cart->getStoreToBaseRate(), $cartData['currency']['store_to_base_rate']);
        $this->assertEquals($cart->getStoreToQuoteRate(), $cartData['currency']['store_to_quote_rate']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No such entity with
     */
    public function testGetCartThrowsExceptionIfThereIsNoCartWithProvidedId()
    {
        $cartId = 9999;

        $serviceInfo = array(
            'soap' => array(
                'service' => 'checkoutCartReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartReadServiceV1GetCart',
            ),
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $cartId,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
        );

        $requestData = array('cartId' => $cartId);
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @magentoApiDataFixture Magento/Sales/_files/quote.php
     */
    public function testGetCartList()
    {
        $cart = $this->getCart('test01');

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/carts',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'checkoutCartReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartReadServiceV1GetCartList',
            ),
        );

        // The following two filters are used as alternatives. The target cart does not match the first one.
        $grandTotalFilter = $this->filterBuilder->setField('grand_total')
            ->setConditionType('gteq')
            ->setValue(15)
            ->create();
        $subtotalFilter = $this->filterBuilder->setField('subtotal')
            ->setConditionType('eq')
            ->setValue($cart->getSubtotal())
            ->create();

        $yesterdayDate = (new \DateTime())->sub(new \DateInterval('P1D'))->format('Y-m-d');
        $tomorrowDate = (new \DateTime())->add(new \DateInterval('P1D'))->format('Y-m-d');
        $minCreatedAtFilter = $this->filterBuilder->setField(Cart::CREATED_AT)
            ->setConditionType('gteq')
            ->setValue($yesterdayDate)
            ->create();
        $maxCreatedAtFilter = $this->filterBuilder->setField(Cart::CREATED_AT)
            ->setConditionType('lteq')
            ->setValue($tomorrowDate)
            ->create();

        $this->searchBuilder->addFilter(array($grandTotalFilter, $subtotalFilter));
        $this->searchBuilder->addFilter(array($minCreatedAtFilter));
        $this->searchBuilder->addFilter(array($maxCreatedAtFilter));
        $this->searchBuilder->setSortOrders([
                [
                    \Magento\Framework\Service\V1\Data\SortOrder::FIELD => 'subtotal',
                    \Magento\Framework\Service\V1\Data\SortOrder::DIRECTION => SearchCriteria::SORT_ASC
                ]
            ]
        );
        $searchCriteria = $this->searchBuilder->create()->__toArray();

        $requestData = array('searchCriteria' => $searchCriteria);
        $searchResult = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertArrayHasKey('total_count', $searchResult);
        $this->assertEquals(1, $searchResult['total_count']);
        $this->assertArrayHasKey('items', $searchResult);
        $this->assertCount(1, $searchResult['items']);

        $cartData = $searchResult['items'][0];
        $this->assertEquals($cart->getId(), $cartData['id']);
        $this->assertEquals($cart->getCreatedAt(), $cartData['created_at']);
        $this->assertEquals($cart->getUpdatedAt(), $cartData['updated_at']);
        $this->assertEquals($cart->getIsActive(), $cartData['is_active']);
        $this->assertEquals($cart->getStoreId(), $cartData['store_id']);

        $this->assertContains('totals', $cartData);
        $this->assertEquals($cart->getBaseSubtotal(), $cartData['totals']['base_subtotal']);
        $this->assertEquals($cart->getBaseGrandTotal(), $cartData['totals']['base_grand_total']);
        $this->assertContains('customer', $cartData);
        $this->assertEquals(1, $cartData['customer']['is_guest']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Field 'invalid_field' cannot be used for search.
     */
    public function testGetCartListThrowsExceptionIfProvidedSearchFieldIsInvalid()
    {
        $serviceInfo = array(
            'soap' => array(
                'service' => 'checkoutCartReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutCartReadServiceV1GetCartList',
            ),
            'rest' => array(
                'resourcePath' => '/V1/carts',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
        );

        $invalidFilter = $this->filterBuilder->setField('invalid_field')
            ->setConditionType('eq')
            ->setValue(0)
            ->create();

        $this->searchBuilder->addFilter(array($invalidFilter));
        $searchCriteria = $this->searchBuilder->create()->__toArray();
        $requestData = array('searchCriteria' => $searchCriteria);
        $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Adjust response details for SOAP protocol
     *
     * @param array $data
     * @return array
     */
    protected function formatTotalsData($data)
    {
        foreach ($data as $key => $field) {
            if (is_numeric($field)) {
                $data[$key] = round($field, 1);
                if ($data[$key] === null) {
                    $data[$key] = 0.0;
                }
            }
        }

        unset($data[Totals::BASE_SUBTOTAL_INCL_TAX]);

        return $data;
    }

    /**
     * Fetch quote item totals data from quote
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return array
     */
    protected function getQuoteItemTotalsData(\Magento\Sales\Model\Quote $quote)
    {
        $items = $quote->getAllItems();
        $item = array_shift($items);

        return [
            ItemTotals::PRICE => $item->getPrice(),
            ItemTotals::BASE_PRICE => $item->getBasePrice(),
            ItemTotals::QTY => $item->getQty(),
            ItemTotals::ROW_TOTAL => $item->getRowTotal(),
            ItemTotals::BASE_ROW_TOTAL => $item->getBaseRowTotal(),
            ItemTotals::ROW_TOTAL_WITH_DISCOUNT => $item->getRowTotalWithDiscount(),
            ItemTotals::TAX_AMOUNT => $item->getTaxAmount(),
            ItemTotals::BASE_TAX_AMOUNT => $item->getBaseTaxAmount(),
            ItemTotals::TAX_PERCENT => $item->getTaxPercent(),
            ItemTotals::DISCOUNT_AMOUNT => $item->getDiscountAmount(),
            ItemTotals::BASE_DISCOUNT_AMOUNT => $item->getBaseDiscountAmount(),
            ItemTotals::DISCOUNT_PERCENT => $item->getDiscountPercent(),
            ItemTotals::PRICE_INCL_TAX => $item->getPriceInclTax(),
            ItemTotals::BASE_PRICE_INCL_TAX => $item->getBasePriceInclTax(),
            ItemTotals::ROW_TOTAL_INCL_TAX => $item->getRowTotalInclTax(),
            ItemTotals::BASE_ROW_TOTAL_INCL_TAX => $item->getBaseRowTotalInclTax(),
        ];
    }
}
