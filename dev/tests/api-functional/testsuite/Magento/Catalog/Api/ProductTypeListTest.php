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
    const SERVICE_NAME = 'catalogProductTypeListV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/products/';

    public function testGetProductTypes()
    {
        $expectedProductTypes = array(
            array(
                'name' => 'simple',
                'label' => 'Simple Product',
            ),
            array(
                'name' => 'virtual',
                'label' => 'Virtual Product',
            ),
            array(
                'name' => 'downloadable',
                'label' => 'Downloadable Product',
            ),
            array(
                'name' => 'bundle',
                'label' => 'Bundle Product',
            ),
            array(
                'name' => 'configurable',
                'label' => 'Configurable Product',
            ),
        );

        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/types',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ),
            'soap' => array(
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetProductTypes'
            ),
        );

        $productTypes = $this->_webApiCall($serviceInfo);

        foreach ($expectedProductTypes as $expectedProductType) {
            $this->assertContains($expectedProductType, $productTypes);
        }
    }
}

