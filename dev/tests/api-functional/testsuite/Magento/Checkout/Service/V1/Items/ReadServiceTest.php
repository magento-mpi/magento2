<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Items;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Checkout\Service\V1\Data\Items as Items;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutItemsReadServiceV1';
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
     */
    public function testGetProductTypes()
    {
        $checkoutSession = $this->objectManager->create('Magento\Checkout\Model\Session');
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $checkoutSession->getQuote();
        $cartId = $quote->getId();
        $output = [];
        foreach ($quote->getAllItems() as $item) {
            $data = [
                Items::SKU => $item->getSku(),
                Items::NAME => $item->getName(),
                Items::PRICE => $item->getPrice(),
                Items::QTY => $item->getQty(),
                Items::TYPE => $item->getProductType()
            ];

            $output[] = $data;
        }
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/items',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'ItemsList',
            ),
        );

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($output, $this->_webApiCall($serviceInfo, $requestData));
    }
}
