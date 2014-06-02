<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;

class WriteServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_new.php
     * @magentoApiDataFixture Magento/GroupedProduct/_files/product_grouped.php
     */
    public function testAssign()
    {
        $productSku = 'grouped-product';
        $linkType = 'associated';
        $productData = [
            ProductLink::TYPE => 'simple',
            ProductLink::SKU => 'simple',
            ProductLink::POSITION => 3,
            ProductLink::CUSTOM_ATTRIBUTES_KEY => [
                'qty' => ['attribute_code' => 'qty', 'value' => 300]
            ]
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $productSku . '/links/' . $linkType,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Assign'
            ]
        ];

        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface $service */
        $service = $objectManager->get('Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface');

        $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'assignedProducts' => [$productData], 'type' => $linkType]
        );

        $actual = $service->getLinkedProducts($productSku, $linkType);
        array_walk($actual, function (&$item){
            $item = $item->__toArray();
        });
        $this->assertEquals([$productData], $actual);
    }
}
