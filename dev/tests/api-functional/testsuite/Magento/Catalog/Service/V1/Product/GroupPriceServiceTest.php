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
                'resourcePath' => '/V1/products/' . $productSku . '/group-prices',
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

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_group_prices.php
     */
    public function testDelete()
    {
        $productSku = 'simple_with_group_price';
        $customerGroupId = \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/$productSku/group-prices/$customerGroupId",
                'httpMethod' => 'DELETE',
            ),
            'soap' => array(
                'service' => 'catalogProductGroupPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductGroupPriceServiceV1Delete',
            ),
        );
        $requestData = array('productSku' => $productSku, 'customerGroupId' => $customerGroupId);
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
    }
}
