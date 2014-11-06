<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Api;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use \Magento\Catalog\Api\Data\ProductLinkInterface;

class ProductLinkRepositoryTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductLinkRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_new.php
     * @magentoApiDataFixture Magento/GroupedProduct/_files/product_grouped.php
     */
    public function testSave()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $repository = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
        $product = $repository->get('simple');

        $productSku = 'grouped-product';
        $linkType = 'associated';
        $productData = [
            ProductLinkInterface::PRODUCT_SKU => $productSku,
            ProductLinkInterface::LINK_TYPE => $linkType,
            ProductLinkInterface::LINKED_PRODUCT_SKU => 'simple',
            ProductLinkInterface::LINKED_PRODUCT_TYPE => 'simple',
            ProductLinkInterface::POSITION => 3,
            ProductLinkInterface::CUSTOM_ATTRIBUTES => [
                'qty' => ['attribute_code' => 'qty', 'value' => (float) 300.0000]
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
                'operation' => self::SERVICE_NAME . 'Save'
            ]
        ];

        /** @var \Magento\Catalog\Api\ProductLinkManagementInterface $service */
        $service = $objectManager->get('Magento\Catalog\Api\ProductLinkManagementInterface');

        $this->_webApiCall($serviceInfo, ['entity' => $productData]);

        $actual = $service->getLinkedItemsByType($productSku, $linkType);
        array_walk($actual, function (&$item){
                $item = $item->__toArray();
            });
        $this->assertEquals($productData, $actual[2]);
    }
}
