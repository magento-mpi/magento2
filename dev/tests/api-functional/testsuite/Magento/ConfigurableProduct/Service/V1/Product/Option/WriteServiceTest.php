<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;
use Magento\Webapi\Exception as HTTPExceptionCodes;
use Magento\TestFramework\Helper\Bootstrap;

class WriteServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'configurableProductProductOptionWriteServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products';

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/configurable_attribute.php
     */
    public function testAdd()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku . '/options',
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Add'
            ]
        ];

        $option = [
            'attribute_id' => 'test_configurable',
            'label' => 'Test',
            'values' => []
        ];

        $option = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'option' => $option]);

        $this->assertArrayHasKey('id', $option);
        $this->assertArrayHasKey('attribute_id', $option);
        $this->assertArrayHasKey('label', $option);

        $this->assertEquals('Test', $option['label']);
        $this->assertGreaterThan(0, $option['id']);
        $this->assertGreaterThan(0, $option['attribute_id']);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testRemove()
    {
        $productSku = 'configurable';
        $optionId = 333; //TODO: need fix it (use get service)

        $result = $this->remove($productSku, $optionId);

        $this->assertTrue($result);
    }

    /**
     * @param string $productSku
     * @param int $optionId
     * @return bool
     */
    private function remove($productSku, $optionId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku . '/options/' . $optionId,
                'httpMethod' => RestConfig::HTTP_METHOD_POST
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
    }
}
