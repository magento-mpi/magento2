<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link;

use \Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\TestFramework\Helper\Bootstrap;

class WriteServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'bundleProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testRemoveChild()
    {
        $productSku = 'bundle-product';
        $childSku = 'simple';
        $optionIds = $this->getProductOptions(3);
        $optionId = array_shift($optionIds);
        $this->assertTrue($this->removeChild($productSku, $optionId, $childSku));
    }

    protected function removeChild($productSku, $optionId, $childSku)
    {
        $resourcePath = self::RESOURCE_PATH . '%s/option/%s/child/%s';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => sprintf($resourcePath, $productSku, $optionId, $childSku),
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'removeChild'
            ]
        ];
        $requestData = array('productSku' => $productSku, 'optionId' => $optionId, 'childSku' => $childSku);
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    protected function getProductOptions($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = Bootstrap::getObjectManager()->get('\Magento\Catalog\Model\Product');
        $product->load($productId);
        /** @var  \Magento\Bundle\Model\Product\Type $type */
        $type = Bootstrap::getObjectManager()->get('\Magento\Bundle\Model\Product\Type');
        return $type->getOptionsIds($product);
    }

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     */
    public function testAddChild()
    {
        $productSku = 'bundle-product';
        $children = $this->getChildren($productSku);

        $optionId = $children[0]['option_id'];

        $linkedProduct = [
            'sku' => 'virtual-product',
            'option_id' => $optionId,
            'position' => '1',
            'default' => 1,
            'priceType' => 2,
            'price' => 151.34,
            'qty' => 8,
            'canChangeQuantity' => 1
        ];

        $childId = $this->addChild($productSku, $optionId, $linkedProduct);

        $this->assertGreaterThan(0, $childId);
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @param array $linkedProduct
     * @return string
     */
    private function addChild($productSku, $optionId, $linkedProduct)
    {
        $resourcePath = self::RESOURCE_PATH . ':productSku/links/:optionId';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(
                    [':productSku', ':optionId'],
                    [$productSku, $optionId],
                    $resourcePath
                ),
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addChild'
            ]
        ];
        return $this->_webApiCall(
            $serviceInfo,
            ['productSku' => $productSku, 'optionId' => $optionId, 'linkedProduct' => $linkedProduct]
        );
    }

    /**
     * @param string $productSku
     * @return string
     */
    private function getChildren($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productId', $productSku, '/V1/bundle-products/:productId/children'),
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => 'bundleProductLinkReadServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'bundleProductLinkReadServiceV1getChildren'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku]);
    }
}
