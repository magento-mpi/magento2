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
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_simple_product.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testAddItem()
    {
        $checkoutSession = $this->objectManager->create('Magento\Checkout\Model\Session');
        $product= $this->objectManager->create('Magento\Catalog\Model\Product');
        $productSku = $product->load(1)->getSku();
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $checkoutSession->getQuote();
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
                "qty" => 5
            ]
        ];
        $this->assertEquals(true, $this->_webApiCall($serviceInfo, $requestData));
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote')->load($cartId);
        $this->assertTrue($quote->hasProductId(1));

    }
}
