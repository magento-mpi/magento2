<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutShippingMethodReadServiceV1';
    const RESOURCE_PATH = '/V1/carts/';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_shipping_method.php
     */
    public function testGetMethod()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');

        $cartId = $quote->getId();

        $shippingAddress = $quote->getShippingAddress();
        list($carrierCode, $methodCode) = explode('_', $shippingAddress->getShippingMethod());
        list($carrierTitle, $methodTitle) = explode(' - ', $shippingAddress->getShippingDescription());
        $data = [
            ShippingMethod::CARRIER_CODE => $carrierCode,
            ShippingMethod::METHOD_CODE => $methodCode,
            ShippingMethod::CARRIER_TITLE => $carrierTitle,
            ShippingMethod::METHOD_TITLE => $methodTitle,
            ShippingMethod::SHIPPING_AMOUNT => $shippingAddress->getShippingAmount(),
            ShippingMethod::BASE_SHIPPING_AMOUNT => $shippingAddress->getBaseShippingAmount(),
            ShippingMethod::AVAILABLE => true,
        ];

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($data, $this->_webApiCall($this->getSelectedMethodServiceInfo($cartId), $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     */
    public function testGetMethodOfVirtualCart()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $cartId = $quote->load('test_order_with_virtual_product', 'reserved_order_id')->getId();

        $result = $this->_webApiCall($this->getSelectedMethodServiceInfo($cartId), ["cartId" => $cartId]);
        $this->assertEquals([], $result);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetMethodOfCartWithNoShippingMethod()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $cartId = $quote->load('test_order_1', 'reserved_order_id')->getId();

        $result = $this->_webApiCall($this->getSelectedMethodServiceInfo($cartId), ["cartId" => $cartId]);
        $this->assertEquals([], $result);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     *
     */
    public function testGetListForVirtualCart()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $cartId = $quote->load('test_order_with_virtual_product', 'reserved_order_id')->getId();

        $this->assertEquals([], $this->_webApiCall($this->getListServiceInfo($cartId), ["cartId" => $cartId]));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testGetList()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load(72);
        $cartId = $quote->getId();
        if (!$cartId) {
            $this->fail('quote fixture failed');
        }
        $quote->getShippingAddress()->requestShippingRates();
        $expectedRates = $quote->getShippingAddress()->getAllShippingRates();
        $expectedData = $this->convertRates($expectedRates, $quote->getQuoteCurrencyCode());

        $requestData = ["cartId" => $cartId];

        $returnedRates = $this->_webApiCall($this->getListServiceInfo($cartId), $requestData);
        $this->assertEquals($expectedData, $returnedRates);
    }

    /**
     * @param string $cartId
     * @return array
     */
    protected function getSelectedMethodServiceInfo($cartId)
    {
        return $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/selected-shipping-method',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetMethod',
            ],
        ];
    }

    /**
     * Service info
     *
     * @param int $cartId
     * @return array
     */
    protected function getListServiceInfo($cartId)
    {
        return [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/shipping-methods',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
    }


    /**
     * Convert rate models array to data array
     *
     * @param string $currencyCode
     * @param \Magento\Sales\Model\Quote\Address\Rate[] $rates
     * @return array
     */
    protected function convertRates($rates, $currencyCode)
    {
        $result = [];
        /** @var \Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter $converter */
        $converter = $this->objectManager->create('\Magento\Checkout\Service\V1\Data\Cart\ShippingMethodConverter');
        foreach ($rates as $rate) {
            $result[] = $converter->modelToDataObject($rate, $currencyCode)->__toArray();
        }
        return $result;
    }

}
