<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1;

use Magento\Catalog\Service\V1\Data\Product;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;
use \Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductServiceTest for testing Bundle Product API
 */
class ProductServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products';

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $productCollection;

    /**
     * Execute per test initialization
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->productCollection = $objectManager->get('Magento\Catalog\Model\Resource\Product\Collection');
    }

    /**
     * Execute per test cleanup
     */
    public function tearDown()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $this->productCollection->addFieldToFilter(
            'sku',
            ['in' => ['sku-test-product', 'sku-test-product-bundle']]
        )->delete();
        unset($this->productCollection);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }

    public function testCreateBundle()
    {
        $response = $this->createProduct($this->getSimpleProductData());
        $simpleProductSku = $response[ProductInterface::SKU];

        $bundleProductOptions = [
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
        ];

        $uniqueId = 'sku-test-product-bundle';
        $product = [
            "sku" => $uniqueId,
            "name" => $uniqueId,
            "type_id" => "bundle",
            "price" => 50,
            'attribute_set_id' => 4,
            "custom_attributes" => [
                "bundle_product_options" => $bundleProductOptions,
                "price_view" => [
                    "attribute_code" => "price_view",
                    "value" => "test"
                ]
            ]
        ];

        $response = $this->createProduct($product);

        $this->assertEquals($uniqueId, $response[ProductInterface::SKU]);
        $this->assertEquals(
            $bundleProductOptions,
            $response[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY]["bundle_product_options"]
        );

        $response = $this->getProduct($uniqueId);
        $foundBundleProductOptions = false;
        foreach ($response[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY] as $customAttribute) {
            if ($customAttribute["attribute_code"] === 'bundle_product_options') {
                $this->assertEquals($simpleProductSku, $customAttribute["value"][0]["product_links"][0]["sku"]);
                $foundBundleProductOptions = true;
            }
        }
        $this->assertTrue($foundBundleProductOptions);

        $this->deleteProduct($response[ProductInterface::SKU]);
        $this->deleteProduct($simpleProductSku);
    }

    /**
     * Get product
     *
     * @param string $productSku
     * @return array the product data
     */
    protected function getProduct($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku,
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'get'
            ]
        ];

        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['id' => $productSku]) : $this->_webApiCall($serviceInfo);

        return $response;
    }

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
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $product[ProductInterface::SKU] = $response[ProductInterface::SKU];
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
                'operation' => self::SERVICE_NAME . 'DeleteById'
            ]
        ];

        return (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, ['productSku' => $sku]) : $this->_webApiCall($serviceInfo);
    }

    /**
     * Creates simple product data.
     *
     * @return array
     */
    private function getSimpleProductData()
    {
        return [
            \Magento\Catalog\Api\Data\ProductInterface::SKU => 'sku-test-product',
            \Magento\Catalog\Api\Data\ProductInterface::NAME => 'sku-test-product',
            \Magento\Catalog\Api\Data\ProductInterface::VISIBILITY => 4,
            \Magento\Catalog\Api\Data\ProductInterface::PRICE => 3.62,
            \Magento\Catalog\Api\Data\ProductInterface::STATUS => 1,
            \Magento\Catalog\Api\Data\ProductInterface::TYPE_ID => 'simple',
            \Magento\Catalog\Api\Data\ProductInterface::ATTRIBUTE_SET_ID => 4,
        ];
    }
}
