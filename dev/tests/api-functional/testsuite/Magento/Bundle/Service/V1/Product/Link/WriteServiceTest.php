<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'bundleProductLinkWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundleProduct/:productSku/links/:optionId';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_virtual.php
     */
    public function testAddChild()
    {
        $productSku = 'bundle';
        $children = $this->getChildren($productSku);

        $optionId = $children[0]['option_id'];

        $linkedProduct = [
            'sku' => 'virtual-product',
            'position' => '1',
            'isDefault' => 1,
            'priceType' => 2,
            'priceValue' => 151.34,
            'quantity' => 8,
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
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(
                    [':productSku', ':optionId'],
                    [$productSku, $optionId],
                    self::RESOURCE_PATH
                ),
                'httpMethod' => Config::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'addChild'
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
                'httpMethod' => Config::HTTP_METHOD_GET
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
