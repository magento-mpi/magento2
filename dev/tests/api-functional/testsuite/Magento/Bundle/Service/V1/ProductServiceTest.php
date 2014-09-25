<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1;

use Magento\Catalog\Service\V1\Data\Product;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';

    private function getSimpleProductData()
    {
        return [
            Product::SKU => uniqid('sku-', true),
            Product::NAME => uniqid('sku-', true),
            Product::VISIBILITY => 4,
            Product::TYPE_ID => 'simple',
            Product::PRICE => 3.62,
            Product::STATUS => 1,
            Product::TYPE_ID => 'simple'
        ];
    }

    protected function createProduct($product)
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_POST],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'create'
            ],
        ];
        $requestData = ['product' => $product];
        $product[Product::SKU] = $this->_webApiCall($serviceInfo, $requestData);
        return $product;
    }

    public function testCreateBundle()
    {
        $simpleProduct = $this->getSimpleProductData();
        $response = $this->createProduct($simpleProduct);
        $this->assertArrayHasKey(Product::SKU, $response);
        $simpleProductSku = $response[Product::SKU];

        $product = [
            "sku" => uniqid('sku-', true),
            "name" => uniqid('sku-', true),
            "type_id" => "bundle",
            "price" => 50,
            "custom_attributes" => [
                "bundle_product_options" => [
                    "attribute_code" => "bundle_product_options",
                    "value" => [
                        [
                            "product_links" => [
                                [
                                    "sku" => $simpleProductSku
                                ]
                            ]
                        ]
                    ]
                ],
                "price_view" => [
                    "attribute_code" => "price_view",
                    "value" => "test"
                ]
            ]
        ];

        $response = $this->createProduct($product);
        $this->assertArrayHasKey(Product::SKU, $response);
    }

}
