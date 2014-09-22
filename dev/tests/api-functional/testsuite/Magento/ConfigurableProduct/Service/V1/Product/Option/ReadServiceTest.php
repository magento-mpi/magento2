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
    const RESOURCE_PATH = '/V1/configurable-products/:productSku/options/';

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     */
    public function testGet()
    {
        $productSku = 'configurable';

        $options = $this->getList($productSku);
        $this->assertTrue(is_array($options));
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            /** @var array $result */
            $result = $this->get($productSku, $option['id']);

            $this->assertTrue(is_array($result));
            $this->assertNotEmpty($result);

            $this->assertArrayHasKey('id', $result);
            $this->assertEquals($option['id'], $result['id']);

            $this->assertArrayHasKey('attribute_id', $result);
            $this->assertEquals($option['attribute_id'], $result['attribute_id']);

            $this->assertArrayHasKey('label', $result);
            $this->assertEquals($option['label'], $result['label']);

            $this->assertArrayHasKey('values', $result);
            $this->assertTrue(is_array($result['values']));
            $this->assertEquals($option['values'], $result['values']);
        }
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
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey(0, $result);

        $option = $result[0];

        $this->assertNotEmpty($option);
        $this->assertTrue(is_array($option));

        $this->assertArrayHasKey('label', $option);
        $this->assertEquals($option['label'], 'Test Configurable');

        $this->assertArrayHasKey('values', $option);
        $this->assertTrue(is_array($option));
        $this->assertNotEmpty($option);

        $expectedValues = array(
            ['price' => 5, 'percent' => 0],
            ['price' => 5, 'percent' => 0]
        );

        $this->assertCount(count($expectedValues), $option['values']);

        foreach ($option['values'] as $key => $value) {
            $this->assertTrue(is_array($value));
            $this->assertNotEmpty($value);

            $this->assertArrayHasKey($key, $expectedValues);
            $expectedValue = $expectedValues[$key];

            $this->assertArrayHasKey('price', $value);
            $this->assertEquals($expectedValue['price'], $value['price']);

            $this->assertArrayHasKey('percent', $value);
            $this->assertEquals($expectedValue['percent'], $value['percent']);
        }
    }

    public function testGetTypes()
    {
        $expectedTypes = array('multiselect', 'select');
        $result = $this->getTypes();
        $this->assertEquals($expectedTypes, $result);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Requested product doesn't exist
     */
    public function testGetUndefinedProduct()
    {
        $productSku = 'product_not_exist';
        $this->getList($productSku);
    }

    /**
     * @magentoApiDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @expectedException \Exception
     * @expectedExceptionMessage Requested option doesn't exist: -42
     */
    public function testGetUndefinedOption()
    {
        $productSku = 'configurable';
        $attributeId = -42;
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
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);
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
        return $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
    }

    /**
     * @return array
     */
    protected function getTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => str_replace(':productSku/', '', self::RESOURCE_PATH) . 'types',
                'httpMethod'   => Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service'        => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation'      => self::SERVICE_READ_NAME . 'getTypes'
            ]
        ];
        return $this->_webApiCall($serviceInfo);
    }
}
