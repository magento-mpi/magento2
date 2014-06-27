<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductCustomOptionsReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/options/';
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetTypes()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "types",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetTypes'
            ]
        ];
        $types = $this->_webApiCall($serviceInfo);
        $excepted = [
            Data\OptionType::LABEL => __('Drop-down'),
            Data\OptionType::CODE => 'drop_down',
            Data\OptionType::GROUP => __('Select'),
        ];
        $this->assertGreaterThanOrEqual(10, count($types));
        $this->assertContains($excepted, $types);
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testGetList()
    {
        $productSku = 'simple';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options",
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetList'
            ]
        ];
        $options = $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);

        /** Unset dynamic data */
        $unset = $this->buildUnsetFunction();

        $options = array_map($unset, $options);

        $excepted = include '_files/product_options.php';
        $this->assertEquals(count($excepted), count($options));

        //in order to make assertion result readable we need to check each element separately
        foreach ($excepted as $index => $value) {
            $this->assertEquals($value, $options[$index]);
        }
    }

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_with_options.php
     * @magentoAppIsolation enabled
     */
    public function testGet()
    {
        $productSku = 'simple';
        $service = Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface');
        $options = $service->getList('simple');
        $optionId = $options[0]->getOptionId();
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/products/' . $productSku . "/options/" . $optionId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get'
            ]
        ];
        $option = $this->_webApiCall($serviceInfo, ['productSku' => $productSku, 'optionId' => $optionId]);

        $unset = $this->buildUnsetFunction();

        $option = $unset($option);

        $excepted = include '_files/product_options.php';
        $this->assertEquals($excepted[0], $option);
    }

    /**
     * Create unset function for options. It will be used to bring options returned from service to comparative format
     *
     * @return callable
     */
    protected function buildUnsetFunction()
    {
        return function ($item) {
            unset($item['option_id']);

            /** Format output data */
            $format = function ($element) {
                if (isset($element['custom_attributes'])) {
                    $attributes = [];
                    foreach ($element['custom_attributes'] as $attribute) {
                        if ($attribute['attribute_code'] == 'option_type_id') {
                            continue;
                        }
                        $attributes[] = $attribute;
                    }
                    $element['custom_attributes'] = $attributes;
                }
                return $element;
            };
            $item['metadata'] = array_map($format, $item['metadata']);
            return $item;
        };
    }
}
