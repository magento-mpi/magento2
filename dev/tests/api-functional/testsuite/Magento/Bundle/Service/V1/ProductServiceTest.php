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

    /**
     * Create product
     *
     * @param array $product
     * @return array the created product data
     */
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

    /**
     * Delete Product
     *
     * @param string $sku
     * @return boolean
     */
    protected function deleteProduct($sku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $sku,
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'delete'
            ]
        ];

        return (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['id' => $sku]) : $this->_webApiCall($serviceInfo);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testCreateBundle()
    {
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
                                    "sku" => "simple"
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
        $this->deleteProduct($response[Product::SKU]);
    }

}
