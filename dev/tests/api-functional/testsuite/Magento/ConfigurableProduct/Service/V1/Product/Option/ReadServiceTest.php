<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\Catalog\Model\Resource\Eav\Attribute as EavAttribute;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_READ_NAME = 'configurableProductProductOptionReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/configurable-products/:productSku/options/';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGet()
    {
        $productSku = 'configurable';
        $attribute = $this->getAttribute();
        $configurableAttribute = $this->getConfigurableAttribute($attribute);

        /** @var array $result */
        $result = $this->get($productSku, $configurableAttribute->getId());

        $this->assertNotEmpty($result);
        $this->assertEquals($attribute->getId(), $result['attribute_id']);
        $this->assertEquals($configurableAttribute->getId(), $result['id']);

    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetList()
    {
        $productSku = 'configurable';
        $attribute = $this->getAttribute();
        $configurableAttribute = $this->getConfigurableAttribute($attribute);

        /** @var array $result */
        $result = $this->getList($productSku);

        $this->assertNotEmpty($result);
        $this->assertEquals($attribute->getId(), $result[0]['attribute_id']);
        $this->assertEquals($configurableAttribute->getId(), $result[0]['id']);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @expectedException \Exception
     * @expectedExceptionCode 404
     * @expectedExceptionMessage "Requested product doesn't exist: product_not_exist"
     */
    public function testGetUndefinedProduct()
    {
        $productSku = 'product_not_exist';
        $this->getList($productSku);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @expectedException \Exception
     * @expectedExceptionCode 404
     * @expectedExceptionMessage "Requested option doesn't exist: option_not_exist"
     */
    public function testGetUndefinedOption()
    {
        $productSku = 'configurable';
        $attributeId = 'option_not_exist';
        $this->get($productSku, $attributeId);
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
                'httpMethod'   => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service'        => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation'      => self::SERVICE_READ_NAME . 'get'
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
                'httpMethod'   => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service'        => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation'      => self::SERVICE_READ_NAME . 'getList'
            ]
        ];
        return $this->_webApiCall($serviceInfo, ['productId' => $productSku]);
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
     * @param EavAttribute $attribute
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute
     */
    protected function getConfigurableAttribute(EavAttribute $attribute)
    {
        /** @var $attribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
        $configurableAttribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        );
        return $configurableAttribute->load($attribute->getId(), 'attribute_id');
    }
}
