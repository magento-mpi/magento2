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
            'type' => 'select',
            'label' => 'Test',
            'values' => [
                [
                    'index' => 1,
                    'price' => '3',
                    'percent' => 0
                ]
            ],
        ];

        /** @var int $result */
        $result = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'option' => $option]);

        $this->assertGreaterThan(0, $result);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testUpdate()
    {
        $productSku = 'configurable';
        $configurableAttribute = $this->getConfigurableAttribute($productSku);
        $optionId = $configurableAttribute[0]['id'];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku . '/options' . '/' . $optionId,
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'update'
            ]
        ];

        $option = [
            'label' => 'Update Test Configurable'
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo,
            [
                'productSku' => $productSku,
                'optionId' => $optionId,
                'option' => $option
            ]
        ));
        $configurableAttribute = $this->getConfigurableAttribute($productSku);
        $this->assertEquals($option['label'], $configurableAttribute[0]['label']);
    }

    /**
     * @param string $productSku
     * @return array
     */
    protected function getConfigurableAttribute($productSku)
    {
        $readServiceName = 'configurableProductProductOptionReadServiceV1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $productSku . '/options/all',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => $readServiceName,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => $readServiceName . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testRemove()
    {
        $productSku = 'configurable';

        $optionList = $this->getList($productSku);
        $optionId = $optionList[0]['id'];
        $resultRemove = $this->remove($productSku, $optionId);
        $optionListRemoved = $this->getList($productSku);

        $this->assertTrue($resultRemove);
        $this->assertEquals(count($optionList) - 1, count($optionListRemoved));
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
                'httpMethod' => RestConfig::HTTP_METHOD_DELETE
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'remove'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
    }

    /**
     * @param string $productSku
     * @return array
     */
    private function getList($productSku)
    {
        $serviceName = 'configurableProductProductOptionReadServiceV1';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/configurable-products/' . $productSku . '/options/all',
                'httpMethod' => RestConfig::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => $serviceName,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => $serviceName . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
    }
}
