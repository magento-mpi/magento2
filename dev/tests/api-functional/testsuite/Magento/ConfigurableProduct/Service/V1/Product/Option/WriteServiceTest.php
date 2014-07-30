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
    public function testUpdate()
    {
        $productSku = 'configurable';
        $attribute = $this->getAttribute();
        $configurableAttribute = $this->getConfigurableAttribute($attribute->getId())->getData();
        $optionId = $configurableAttribute['product_super_attribute_id'];
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
        $configurableAttribute = $this->getConfigurableAttribute($attribute->getId())->getData();
        $this->assertEquals($option['label'], $configurableAttribute['label']);
    }

    /**
     * @param string $attributeCode
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected function getAttribute($attributeCode = 'test_configurable')
    {
        /** @var $attribute EavAttribute */
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Eav\Attribute'
        );
        return $attribute->load($attributeCode, 'attribute_code');
    }

    /**
     * @param int $attributeId
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute
     */
    protected function getConfigurableAttribute($attributeId)
    {
        /** @var $attribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
        $configurableAttribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        );
        return $configurableAttribute->load($attributeId, 'attribute_id');
    }
}
