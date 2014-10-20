<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'bundleProductOptionReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/bundle-products/:productSku/option';

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testGetList()
    {
        $productSku = 'bundle-product';
        $expected = [
            [
                'required' => true,
                'position' => 0,
                'type' => 'select',
                'title' => 'Bundle Product Items',
                'sku' => $productSku,
                'product_links' => [
                    [
                        'sku' => 'simple',
                        'qty' => 1,
                        'position' => 0,
                        'defined' => true,
                        'default' => false,
                        'price' => null,
                        'price_type' => null,
                        'can_change_quantity' => null,
                    ]
                ]
            ]
        ];
        $result = $this->getList($productSku);

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey('id', $result[0]);
        $expected[0]['product_links'][0]['option_id'] = $result[0]['id'];
        unset($result[0]['id']);

        ksort($expected[0]);
        ksort($result[0]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @magentoApiDataFixture Magento/Bundle/_files/product.php
     */
    public function testGet()
    {
        $productSku = 'bundle-product';
        $expected = [
            'required' => true,
            'position' => 0,
            'type' => 'select',
            'title' => 'Bundle Product Items',
            'sku' => $productSku,
            'product_links' => [
                [
                    'sku' => 'simple',
                    'qty' => 1,
                    'position' => 0,
                    'defined' => true,
                    'default' => false,
                    'price' => null,
                    'price_type' => null,
                    'can_change_quantity' => null,
                ]
            ]
        ];
        $optionId = $this->getList($productSku)[0]['id'];
        $result = $this->get($productSku, $optionId);

        $this->assertArrayHasKey('id', $result);
        $expected['product_links'][0]['option_id'] = $result['id'];
        unset($result['id']);

        ksort($expected);
        ksort($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @param string $productSku
     * @return string
     */
    protected function getList($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . '/all',
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @return string
     */
    protected function get($productSku, $optionId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . '/' . $optionId,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'get'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
    }
}
