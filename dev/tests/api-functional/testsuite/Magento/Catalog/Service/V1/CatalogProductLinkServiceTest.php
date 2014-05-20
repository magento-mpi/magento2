<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

use Magento\Webapi\Model\Rest\Config as RestConfig;

class CatalogProductLinkServiceTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    const SERVICE_NAME = 'catalogCatalogProductLinkServiceV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/catalogProductLink/types';

    public function testGetList()
    {
        $serviceInfo = [
            'rest' => ['resourcePath' => self::RESOURCE_PATH, 'httpMethod' => RestConfig::HTTP_METHOD_GET],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'GetProductLinkTypes'
            ]
        ];

        $haystack = $this->_webApiCall($serviceInfo);

        /**
         * Validate that product type links provided by Magento_Catalog module are present
         */
        $expectedItems = [
            ['type' => 'links_related', 'code' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED],
            ['type' => 'links_crosssell', 'code' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL],
            ['type' => 'links_upsell', 'code' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL],
        ];

        foreach ($expectedItems as $item) {
            $this->assertContains($item, $haystack);
        }
    }
} 
