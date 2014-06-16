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
}
