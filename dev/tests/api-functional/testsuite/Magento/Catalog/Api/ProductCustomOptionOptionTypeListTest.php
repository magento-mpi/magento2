<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract;

class ProductCustomOptionOptionTypeListTest extends WebapiAbstract
{
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
                // @todo fix this configuration after SOAP test framework is functional
            ]
        ];
        $types = $this->_webApiCall($serviceInfo);
        $excepted = [
            'label' => __('Drop-down'),
            'code' => 'drop_down',
            'group' => __('Select'),
        ];
        $this->assertGreaterThanOrEqual(10, count($types));
        $this->assertContains($excepted, $types);
    }
}
