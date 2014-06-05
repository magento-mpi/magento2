<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

use Magento\TestFramework\TestCase\WebapiAbstract;

class GroupPriceServiceTest extends WebapiAbstract
{

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_group_prices.php
     */
    public function testGetList()
    {
        $productSku = 'simple_with_group_price';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/grouped-prices',
                'httpMethod' => 'GET',
            ),
            'soap' => array(
                'service' => 'catalogProductGroupPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductGroupPriceServiceV1GetList',
            ),
        );

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $groupPriceList = $this->_webApiCall($serviceInfo, ['productSku' => $productSku]);
        } else {
            $groupPriceList = $this->_webApiCall($serviceInfo);
        }

        $this->assertCount(2, $groupPriceList);
        $this->assertEquals(9, $groupPriceList[0]['value']);
        $this->assertEquals(7, $groupPriceList[1]['value']);
    }
} 
