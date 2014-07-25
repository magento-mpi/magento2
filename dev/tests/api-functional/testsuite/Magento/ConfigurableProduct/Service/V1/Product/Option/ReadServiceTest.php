<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'configurableProductProductOptionReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products/:productSku/option/';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGet()
    {
        $productSku = 'configurable';
        $attributeId = $this->getAttribute()->getId();

        /** @var array $result */
        $result = $this->get($productSku, $attributeId);

        $this->assertNotEmpty($result);
        $this->assertEquals($attributeId, $result['attribute_id']);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetList()
    {
        $productSku = 'configurable';

        /** @var array $result */
        $result = $this->getList($productSku);

        $this->assertNotEmpty($result);
        $this->assertEquals($this->getAttribute()->getId(), $result[0]['attribute_id']);
    }

    /**
     * @param $productSku
     * @param $optionId
     * @return array
     */
    protected function get($productSku, $optionId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . $optionId,
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'get'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku, 'optionId' => $optionId]);
    }

    /**
     * @param $productSku
     * @return array
     */
    protected function getList($productSku)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku', $productSku, self::RESOURCE_PATH) . 'all',
                'httpMethod' => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku]);
    }

    protected function getAttribute($attributeCode = 'test_configurable')
    {
        /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Eav\Attribute'
        );
        return $attribute->load($attributeCode, 'attribute_code');
    }
}
