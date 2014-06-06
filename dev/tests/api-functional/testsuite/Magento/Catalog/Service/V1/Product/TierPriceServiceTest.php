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

class TierPriceServiceTest extends WebapiAbstract
{
    /**
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider getListDataProvider
     */
    public function testGetList($customerGroupId, $count, $value, $qty)
    {
        $productSku = 'simple';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/products/' . $productSku . '/grouped-prices/' . $customerGroupId . '/tiers',
                'httpMethod' => 'GET',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1GetList',
            ),
        );

        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
            $groupPriceList = $this->_webApiCall(
                $serviceInfo, ['productSku' => $productSku, 'customerGroupId' => $customerGroupId]
            );
        } else {
            $groupPriceList = $this->_webApiCall($serviceInfo);
        }

        $this->assertCount($count, $groupPriceList);
        if ($count) {
            $this->assertEquals($value, $groupPriceList[0]['value']);
            $this->assertEquals($qty, $groupPriceList[0]['qty']);
        }
    }

    public function getListDataProvider()
    {
        return array(
            array(0, 1, 5, 3),
            array(1, 0, null, null),
            array('all', 2, 8, 2),
        );
    }

    /**
     * @param string|int $customerGroupId
     * @param int $qty
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider deleteDataProvider
     */
    public function testDelete($customerGroupId, $qty)
    {
        $productSku = 'simple';
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => "/V1/products/$productSku/group-prices/$customerGroupId/tiers/$qty",
                'httpMethod' => 'DELETE',
            ),
            'soap' => array(
                'service' => 'catalogProductTierPriceServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'catalogProductTierPriceServiceV1Delete',
            ),
        );
        $requestData = array('productSku' => $productSku, 'customerGroupId' => $customerGroupId, 'qty' => $qty);
        $this->assertTrue( $this->_webApiCall($serviceInfo, $requestData));
    }


    public function deleteDataProvider()
    {
        return array(
            'delete_tier_price_for_specific_customer_group' => array(0, 3),
            'delete_tier_price_for_all_customer_group' => array('all', 5)
        );
    }
} 
