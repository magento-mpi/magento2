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
        $options = $this->getConfigurableOptions();

        /** @var array $result */
        $result = $this->get($productSku, $options['items'][0]['option_id']);

        $this->assertNotEmpty($result);
        //TODO Add more asserts
        //$this->assertEquals($options, $result);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGetList()
    {
        $productSku = 'configurable';
        $options = $this->getConfigurableOptions();

        /** @var array $result */
        $result = $this->getList($productSku);

        $this->assertNotEmpty($result);
        //TODO Add more asserts
        //$this->assertEquals($options, $result);
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

    /**
     * @return array
     */
    protected function getConfigurableOptions()
    {
        /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Resource\Eav\Attribute'
        );
        //Id from fixture
        $attribute->load('test_configurable', 'attribute_code');
        /** @var $options \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection */
        $options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection'
        );
        $options->setAttributeFilter($attribute->getId());
        return $options->load()->toArray();
    }
}
