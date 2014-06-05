<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class ProductTypeServiceTest
 */
class ProductTypeServiceTest extends WebapiAbstract
{
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
                'service' => 'catalogProductTypeServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTypeServiceV1GetProductTypes',
            ),
        );

        $productTypes = $this->_webApiCall($serviceInfo);

        foreach ($expectedProductTypes as $expectedProductType) {
            $this->assertContains($expectedProductType, $productTypes);
        }
    }
}

