<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\TestFramework\TestCase\WebapiAbstract;

class ReadServiceTest extends WebapiAbstract
{
    const SERVICE_NAME = 'catalogProductCustomOptionsReadServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/options/';

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

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            /** Unset dynamic data */
            $unset = function($item){
                unset($item['option_id']);
                return $item;
            };
        } else {
            /** Unset dynamic data */
            $unset = function ($item) {
                unset($item['option_id']);

                /** Format output data */
                $format = function ($element) {
                    $element['price'] = intval($element['price']);
                    if (isset($element['custom_attributes'])) {
                        $element['customAttributes'] = $element['custom_attributes'];
                        unset($element['custom_attributes']);
                    }
                    return $element;
                };
                $item['value'] = array_map($format, $item['value']);
                return $item;
            };
        }

        $options = array_map($unset, $options);

        $excepted = include '_files/product_options.php';
        $this->assertEquals(count($excepted), count($options));

        //in order to make assertion result readable we need to check each element separately
        foreach ($excepted as $index => $value) {
            $this->assertEquals($value, $options[$index]);
        }
    }
}
