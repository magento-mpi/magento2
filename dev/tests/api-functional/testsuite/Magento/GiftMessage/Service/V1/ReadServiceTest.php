<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\GiftMessage;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\Webapi\Model\Rest\Config as RestConfig;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'giftMessageReadServiceV1';
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
     * @magentoApiDataFixture Magento/GiftMessage/_files/quote_with_message.php
     */
    public function testGet()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('message_order_21', 'reserved_order_id');

        $cartId = $quote->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/gift-message',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $expectedMessage = array (
            'recipient' => 'Mercutio',
            'sender' => 'Romeo',
            'message' => 'I thought all for the best.',
        );

        $requestData = ["cartId" => $cartId];
        $resultMessage = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertCount(4, $resultMessage);
        unset($resultMessage['id']);
        $this->assertEquals($expectedMessage, $resultMessage);
    }

    /**
     * @magentoApiDataFixture Magento/GiftMessage/_files/quote_with_item_message.php
     */
    public function testGetItemMessage()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test_order_item_with_message', 'reserved_order_id');
        $sku = $quote->getAllItems()[0]->getSku();
        /** @var  \Magento\Catalog\Model\Product $product */
        $cartId = $quote->getId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/gift-message/' . $sku,
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getItemMessage',
            ],
        ];

        $expectedMessage = array (
            'recipient' => 'Jane Roe',
            'sender' => 'John Doe',
            'message' => 'Gift Message Text',
        );

        $requestData = ["cartId" => $cartId, "itemSku" => $sku];
        $resultMessage = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertCount(4, $resultMessage);
        unset($resultMessage['id']);
        $this->assertEquals($expectedMessage, $resultMessage);
    }
}
