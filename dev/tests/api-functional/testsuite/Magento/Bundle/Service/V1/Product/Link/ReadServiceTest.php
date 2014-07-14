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

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'bundleProductLinkReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/:productId/children';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testGetChildren()
    {
        $productSku = 'bundle-product';
        $expected = [
            [
                'sku' => 'simple',
                'position' => '0',
                'is_default' => false,
                'qty' => '1.0000',
                'defined' => true,
            ]
        ];

        $result = $this->getChildren($productSku);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('option_id', $result[0]);
        unset($result[0]['option_id']);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param string $productSku
     * @return string
     */
    protected function getChildren($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productId', $productSku, self::RESOURCE_PATH),
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getChildren'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku]);
    }
}
