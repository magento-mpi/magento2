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

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'checkoutItemReadServiceV1';
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
    public function testGetList()
    {
        $checkoutSession = $this->objectManager->create('Magento\Checkout\Model\Session');
        /** @var \Magento\Sales\Model\Quote  $quote */
        $quote = $checkoutSession->getQuote();
        $cartId = $quote->getId();
        $output = [];
        foreach ($quote->getAllItems() as $item) {
            $data = [
                Item::SKU => $item->getSku(),
                Item::NAME => $item->getName(),
                Item::PRICE => $item->getPrice(),
                Item::QTY => $item->getQty(),
                Item::TYPE => $item->getProductType()
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
                'operation' => self::SERVICE_NAME . 'GetList',
            ),
        );

        $requestData = ["cartId" => $cartId];
        $this->assertEquals($output, $this->_webApiCall($serviceInfo, $requestData));
    }
}
