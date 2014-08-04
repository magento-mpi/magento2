<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Item;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Checkout\Service\V1\Data\Cart\Item as Item;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutItemWriteServiceV1';
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
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testAddItem()
    {
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load(2);
        $productSku = $product->getSku();
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/items',
                'httpMethod' => RestConfig::HTTP_METHOD_POST,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'AddItem',
            ),
        );

        $requestData = [
            "cartId" => $cartId,
            "data" => [
                "sku" => $productSku,
                "qty" => 7
            ]
        ];
        $this->assertEquals(true, $this->_webApiCall($serviceInfo, $requestData));
        $this->assertTrue($quote->hasProductId(2));
        $this->assertEquals(7, $quote->getItemByProduct($product)->getQty());
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testRemoveItem()
    {
        $product = $this->objectManager->create('Magento\Catalog\Model\Product');
        $productSku = $product->load(1)->getSku();
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/items/' . $productSku,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'RemoveItem',
            ),
        );

        $requestData = [
            "cartId" => $cartId,
            "itemSku" => $productSku
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $this->assertFalse($quote->hasProductId(1));
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testUpdateItem()
    {
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load(1);
        $productSku = $product->getSku();
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $cartId = $quote->getId();
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/items/' . $productSku,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'UpdateItem',
            ),
        );

        $requestData = [
            "cartId" => $cartId,
            "itemSku" => $productSku,
            "data" => [
                "qty" => 5
            ]
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $this->assertTrue($quote->hasProductId(1));
        $this->assertEquals(5, $quote->getItemByProduct($product)->getQty());
    }
}
