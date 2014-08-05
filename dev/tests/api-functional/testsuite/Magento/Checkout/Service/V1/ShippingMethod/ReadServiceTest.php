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
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');

        $cartId = $quote->getId();

        $shippingAddress = $quote->getShippingAddress();
        $data = [
            ShippingMethod::KEY_CARRIER_CODE => explode("_", $shippingAddress->getShippingMethod())[0],
            ShippingMethod::KEY_METHOD_CODE => explode("_", $shippingAddress->getShippingMethod())[1],
            ShippingMethod::KEY_DESCRIPTION => $shippingAddress->getShippingDescription(),
            ShippingMethod::KEY_SHIPPING_AMOUNT => $shippingAddress->getShippingAmount(),
        ];

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($data, $this->_webApiCall($this->getServiceInfo($cartId), $requestData));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_virtual_product_and_address.php
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Cart contains virtual product(s) only. Shipping method is not applicable
     */
    public function testGetMethodOfVirtualCart()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $cartId = $quote->load('test_order_with_virtual_product', 'reserved_order_id')->getId();

        $this->_webApiCall($this->getServiceInfo($cartId), ["cartId" => $cartId]);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Shipping method and carrier are not set for the quote
     */
    public function testGetMethodOfCartWithNoShippingMethod()
    {
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $cartId = $quote->load('test_order_1', 'reserved_order_id')->getId();

        $this->_webApiCall($this->getServiceInfo($cartId), ["cartId" => $cartId]);
    }

    /**
     * @param string $cartId
     * @return array
     */
    protected function getServiceInfo($cartId)
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
}
