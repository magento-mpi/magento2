<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

class ProductTypeListTest extends WebapiAbstract
{
    public function testGetProductTypes()
    {
        $expectedProductTypes = array(
            array(
                'key' => 'simple',
                'value' => 'Simple Product',
            ),
            array(
                'key' => 'virtual',
                'value' => 'Virtual Product',
            ),
            array(
                'key' => 'downloadable',
                'value' => 'Downloadable Product',
            ),
            array(
                'key' => 'bundle',
                'value' => 'Bundle Product',
            ),
            array(
                'key' => 'configurable',
                'value' => 'Configurable Product',
            ),
        );

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                // @todo fix this configuration after SOAP test framework is functional
                'operation' => 'catalogProductTypeListGetProductTypes',
            ),
        );

        $productTypes = $this->_webApiCall($serviceInfo);

        foreach ($expectedProductTypes as $expectedProductType) {
            $this->assertContains($expectedProductType, $productTypes);
        }
    }
}

